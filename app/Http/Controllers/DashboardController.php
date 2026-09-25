<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Models\AllowedDomain;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Verificar suscripción activa para usuarios que no sean master
        if ($user && !$user->hasActiveSubscription()) {
            // Si es su primer inicio de sesión (no tiene fecha de expiración registrada)
            if (empty($user->subscription_ends_at)) {
                $user->subscription_status = 'trialing';
                $user->subscription_ends_at = \Carbon\Carbon::now()->addMonths(2);
                $user->save();
                
                return redirect()->route('dashboard')->with('success', '¡Se ha activado tu prueba gratuita de 2 meses automáticamente!');
            }

            return redirect()->route('subscribe.notice');
        }
        
        // Determinar qué sitios puede ver
        if ($user->role === 'master') {
            $domains = AllowedDomain::all();
            $eventSites = Event::select('site_name')->distinct()->pluck('site_name');
            // Unir sitios con eventos y dominios conectados para que aparezcan exactamente iguales en los selectores de Admin y creación de usuarios
            $sites = $eventSites->concat($domains->pluck('domain'))->unique()->filter()->values();
            $events = Event::orderBy('created_at', 'desc')->get();
            $users = User::all();
        } else {
            // Editor o Viewer solo ven su sitio
            $sites = collect([$user->site_name]);
            $events = Event::where('site_name', $user->site_name)->orderBy('created_at', 'desc')->get();
            $users = collect(); // viewers/editors don't see users
            $domains = collect();
        }

        $selectedSite = $request->get('site') ?? ($sites->count() > 0 ? $sites->first() : null);
        
        $weekNumber = 1;
        $frictionData = [];
        $captureData = [];
        $regionData = [];
        $heatmapData = [];

        // Inicialización de nuevas variables dinámicas (por defecto si no hay sitio o datos)
        $healthScore = 0;
        $healthReading = "Esperando captura de datos para generar diagnóstico de la marca.";
        $healthDetail = "Una vez que ingresen usuarios reales al sitio, el sistema evaluará la atención, interacción y conversión en tiempo real.";
        $healthTrend = "Sin datos recientes";
        
        $weeklyProgress = 0;
        $weeklyText = "Esperando actividad de usuarios este ciclo.";

        $funnelData = [
            'views' => ['label' => 'Visita página principal', 'count' => 0, 'percent' => 0],
            'interest' => ['label' => 'Interacción / Exploración', 'count' => 0, 'percent' => 0],
            'cta' => ['label' => 'Hace clic en CTA', 'count' => 0, 'percent' => 0],
            'form_start' => ['label' => 'Inicia formulario', 'count' => 0, 'percent' => 0],
            'form_submit' => ['label' => 'Envía datos (Lead)', 'count' => 0, 'percent' => 0],
        ];
        $funnelInsight = "No hay suficientes eventos para analizar pérdidas de conversión.";

        $leadsCount = 0;
        $leadsTrendText = "● Sin actividad";
        $leadsTrendClass = "warn";
        $leadsText = "Los leads se contabilizan en tiempo real al registrar eventos de envío de formulario en el sitio.";

        $activityChart = [];
        $nextDecisionTitle = "Acumular masa crítica de datos antes de actuar.";
        $nextDecisionText = "El sistema requiere un volumen inicial de sesiones para identificar con precisión si el obstáculo es de atención, confianza o usabilidad en formularios.";
        $topClickedContent = "ninguna zona (esperando clics)";

        if ($selectedSite) {
            $siteEvents = clone $events;
            $siteEvents = $siteEvents->where('site_name', $selectedSite);

            $firstEvent = Event::where('site_name', $selectedSite)->orderBy('created_at', 'asc')->first();
            if ($firstEvent) {
                $weekNumber = floor(\Carbon\Carbon::parse($firstEvent->created_at)->diffInDays(now()) / 7) + 1;
            }

            // Capture Data (Top 8 paginas por tiempo de duracion)
            $captureData = $siteEvents->where('type', 'view')
                ->whereNotNull('duration')
                ->groupBy('content')
                ->map(function ($eventsInPage) {
                    $avg = $eventsInPage->avg('duration') ?? 0;
                    if ($avg > 86400) { $avg = $avg / 1000; }
                    if ($avg > 86400) { $avg = 0; }
                    return min($avg, 7200);
                })
                ->sortByDesc(function ($avg) { return $avg; })
                ->take(8)
                ->toArray();

            // Region Data
            $regionData = $siteEvents->whereNotNull('region')
                ->groupBy('region')
                ->map(function ($r) { return $r->count(); })
                ->sortByDesc(function ($c) { return $c; })
                ->take(5)
                ->toArray();

            // Heatmap Data
            $heatmapData = $siteEvents->where('type', 'click')
                ->whereNotNull('x_coord')
                ->whereNotNull('y_coord')
                ->map(function ($event) {
                    return [
                        'x' => $event->x_coord,
                        'y' => $event->y_coord
                    ];
                })
                ->values()
                ->toArray();

            // Friction Data
            $viewsByPage = $siteEvents->where('type', 'view')->groupBy('content')->map->count();
            $clicksByPage = $siteEvents->where('type', 'click')->groupBy('content')->map->count();
            
            foreach ($viewsByPage as $page => $views) {
                $clicks = $clicksByPage->get($page, 0);
                if ($views > 5 && $clicks == 0) {
                    $frictionData[] = ['type' => 'danger', 'message' => "La página {$page} tiene tráfico ({$views} vistas) pero 0 interacción (clics)."];
                }
            }

            $formStarts = $siteEvents->where('type', 'form_start')->groupBy('content')->map->count();
            $formSubmits = $siteEvents->where('type', 'form_submit')->groupBy('content')->map->count();
            
            foreach ($formStarts as $form => $starts) {
                $submits = $formSubmits->get($form, 0);
                if ($starts > 0) {
                    $dropoff = round((($starts - $submits) / $starts) * 100);
                    if ($dropoff > 50) {
                        $frictionData[] = ['type' => 'danger', 'message' => "Formulario '{$form}' con abandono del {$dropoff}%."];
                    }
                }
            }

            // --- CÁLCULOS DINÁMICOS DE CARDS REALES ---
            $totalViews = $siteEvents->where('type', 'view')->count();
            $totalSessions = $siteEvents->pluck('session_id')->unique()->count();
            $baseCount = max($totalViews, $totalSessions, 1);

            $interestCount = $siteEvents->whereIn('type', ['click', 'reload', 'external_link'])->pluck('session_id')->unique()->count();
            if ($interestCount == 0 && $totalViews > 0) {
                $interestCount = $siteEvents->where('type', 'click')->count();
            }
            
            $ctaCount = $siteEvents->where('type', 'cta_click')->count();
            if ($ctaCount == 0) {
                $ctaCount = $siteEvents->where('type', 'click')->filter(function($e) {
                    return $e->content && (stripos($e->content, 'cta') !== false || stripos($e->content, 'btn') !== false);
                })->count();
            }

            $formStartCount = $siteEvents->where('type', 'form_start')->count();
            $formSubmitCount = $siteEvents->where('type', 'form_submit')->count();

            $pViews = $totalViews > 0 ? 100 : 0;
            $pInterest = min(100, round(($interestCount / $baseCount) * 100));
            $pCta = min(100, round(($ctaCount / $baseCount) * 100));
            $pFormStart = min(100, round(($formStartCount / $baseCount) * 100));
            $pFormSubmit = min(100, round(($formSubmitCount / $baseCount) * 100));

            $funnelData = [
                'views' => ['label' => 'Tráfico / Vistas del sitio', 'count' => $totalViews, 'percent' => $pViews],
                'interest' => ['label' => 'Interacción y exploración', 'count' => $interestCount, 'percent' => $pInterest],
                'cta' => ['label' => 'Clic en CTA o Botones', 'count' => $ctaCount, 'percent' => $pCta],
                'form_start' => ['label' => 'Inicia formulario', 'count' => $formStartCount, 'percent' => $pFormStart],
                'form_submit' => ['label' => 'Envía datos (Conversión)', 'count' => $formSubmitCount, 'percent' => $pFormSubmit],
            ];

            if ($totalViews > 0) {
                if ($pCta < 10 && $pCta > 0) {
                    $funnelInsight = "<b>Insight:</b> La tasa de clic en CTA es reducida ({$pCta}%). Conviene revisar la jerarquía visual de los botones y la claridad de la promesa principal.";
                } elseif ($formStartCount > 0 && $pFormSubmit < ($pFormStart / 2)) {
                    $drop = round(100 - ($formSubmitCount / max(1, $formStartCount) * 100));
                    $funnelInsight = "<b>Insight:</b> Fuerte abandono en el formulario ({$drop}% de caída). Hay intención pero los campos o la confianza frenan el envío.";
                } elseif ($pInterest < 30) {
                    $funnelInsight = "<b>Insight:</b> Una parte importante del tráfico rebota sin interactuar ({$pInterest}% exploración). Revalidar la relevancia y velocidad del sitio en móviles.";
                } else {
                    $funnelInsight = "<b>Insight:</b> Embudo operando activamente con una conversión global del {$pFormSubmit}%. Se recomienda monitorear de cerca el costo por lead.";
                }
            }

            // Leads
            $leadsCount = $formSubmitCount;
            $now = \Carbon\Carbon::now();
            $last7Days = $siteEvents->where('type', 'form_submit')->where('created_at', '>=', $now->copy()->subDays(7))->count();
            $prev7Days = $siteEvents->where('type', 'form_submit')->where('created_at', '>=', $now->copy()->subDays(14))->where('created_at', '<', $now->copy()->subDays(7))->count();
            
            if ($prev7Days > 0) {
                $leadsDiffPercent = round((($last7Days - $prev7Days) / $prev7Days) * 100, 1);
                if ($leadsDiffPercent >= 0) {
                    $leadsTrendText = "▲ +{$leadsDiffPercent}% vs semana previa";
                    $leadsTrendClass = "";
                } else {
                    $leadsTrendText = "▼ {$leadsDiffPercent}% vs semana previa";
                    $leadsTrendClass = "bad";
                }
            } elseif ($last7Days > 0) {
                $leadsTrendText = "▲ +" . $last7Days . " nuevos esta semana";
                $leadsTrendClass = "";
            } else {
                $leadsTrendText = "● Estable en este ciclo";
                $leadsTrendClass = "warn";
            }

            if ($leadsCount > 0) {
                $leadsText = "La captación acumula {$leadsCount} conversiones reales. Mantener monitoreado el seguimiento comercial de estos prospectos.";
            }

            // Calculate Retention (Average Time on Site by session)
            $retentionSeconds = 0;
            $sessionDurations = $siteEvents->whereNotNull('session_id')->groupBy('session_id')->map(function($eventsForSession) {
                if ($eventsForSession->count() > 1) {
                    $sorted = $eventsForSession->sortBy('created_at')->values();
                    $totalDiff = 0;
                    for ($i = 1; $i < $sorted->count(); $i++) {
                        $diff = $sorted[$i - 1]->created_at->diffInSeconds($sorted[$i]->created_at);
                        // Si la inactividad entre eventos es mayor a 30 minutos (1800s), se ignora ese lapso
                        if ($diff < 1800) {
                            $totalDiff += $diff;
                        }
                    }
                    return min($totalDiff, 7200);
                }
                // Para eventos únicos, usamos la duración enviada por el cliente (a menudo en milisegundos)
                $dur = $eventsForSession->avg('duration') ?? 0;
                if ($dur > 86400) { $dur = $dur / 1000; }
                if ($dur > 86400) { $dur = 0; }
                return min($dur, 7200);
            })->filter(function($dur) { return $dur > 0; });
            
            if ($sessionDurations->count() > 0) {
                $retentionSeconds = $sessionDurations->average();
            }
            $retentionFormatted = sprintf('%02d:%02d', floor($retentionSeconds / 60), $retentionSeconds % 60);

            $scoreInterest = min(35, round($pInterest * 0.35));
            $scoreRetention = min(35, round(($retentionSeconds / 120) * 35));
            $scoreConversion = min(30, round(($pCta * 0.15) + ($pFormSubmit * 0.15)));
            
            $healthScore = min(100, (int) ($scoreInterest + $scoreRetention + $scoreConversion));
            if ($totalViews > 0 && $healthScore < 15) {
                $healthScore = 15;
            }

            if ($healthScore >= 75) {
                $healthReading = "La marca muestra un desempeño sólido convirtiendo atención en decisión.";
                $healthDetail = "El tráfico interactúa constantemente y el tiempo de estancia demuestra alto interés. Mantener la estrategia actual y escalar tráfico.";
                $nextDecisionTitle = "Escalar presupuesto y tráfico publicitario.";
                $nextDecisionText = "El embudo actual convierte de manera óptima y no muestra cuellos de botella críticos. Es el momento de aumentar el volumen de pauta.";
            } elseif ($healthScore >= 45) {
                $healthReading = "La marca está siendo vista, pero hay margen para convertir mejor esa atención.";
                $healthDetail = "El tráfico fluye de manera estable, pero ciertas zonas de conversión y formularios necesitan optimización para no perder leads.";
                $nextDecisionTitle = "Reducir fricción en puntos de conversión antes de escalar.";
                $nextDecisionText = "La atención ya se captura con éxito. Para maximizar el ROI, optimizar la estructura de los formularios y simplificar los llamados a la acción (CTA).";
            } elseif ($totalViews > 0) {
                $healthReading = "El sitio atrae visitas pero enfrenta alta fricción o rebote temprano.";
                $healthDetail = "Los usuarios abandonan el sitio antes de llegar a los llamados a la acción. Es necesario reforzar la propuesta de valor inicial (above the fold).";
                $nextDecisionTitle = "Reestructurar el mensaje principal y jerarquía de CTA.";
                $nextDecisionText = "No se recomienda aumentar inversión en tráfico hasta mejorar la retención inicial y lograr que el usuario interactúe con la oferta central.";
            }
            
            $trafficLast7 = $siteEvents->where('type', 'view')->where('created_at', '>=', $now->copy()->subDays(7))->count();
            $trafficPrev7 = $siteEvents->where('type', 'view')->where('created_at', '>=', $now->copy()->subDays(14))->where('created_at', '<', $now->copy()->subDays(7))->count();
            
            $trDiff = 0;
            if ($trafficPrev7 > 0) {
                $trDiff = round((($trafficLast7 - $trafficPrev7) / $trafficPrev7) * 100, 1);
            } elseif ($trafficLast7 > 0) {
                $trDiff = 100;
            }
            $healthTrend = ($trDiff >= 0 ? "+{$trDiff}% tráfico 7d" : "{$trDiff}% tráfico 7d") . " · Interacción {$pInterest}% · Conversión {$pFormSubmit}%";

            $weeklyProgress = min(100, max(10, $healthScore));
            if ($trDiff > 0) {
                $weeklyText = "En la semana {$weekNumber}, la actividad muestra ritmo activo (" . ($trDiff >= 0 ? "+" : "") . "{$trDiff}% tráfico en 7d). La prioridad es consolidar la retención.";
            } else {
                $weeklyText = "En la semana {$weekNumber}, el tráfico se mantiene en fase de evaluación con un score de salud digital de {$healthScore}/100.";
            }

            // Gráfico de actividad de los últimos 7 días
            for ($i = 6; $i >= 0; $i--) {
                $dDate = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                $dLabel = \Carbon\Carbon::now()->subDays($i)->format('d/m');
                $dCount = $siteEvents->filter(function($e) use ($dDate) {
                    return $e->created_at->format('Y-m-d') === $dDate;
                })->count();
                $activityChart[$dLabel] = $dCount;
            }

            $topClicked = $siteEvents->where('type', 'click')->groupBy('content')->map->count()->sortDesc()->keys()->first();
            if ($topClicked) {
                $topClickedContent = $topClicked;
            }
        }

        $stripeConfigs = [
            'enabled' => SystemConfig::isStripeEnabled(),
            'pk' => SystemConfig::getValue('pk', ''),
            'sk' => SystemConfig::getValue('sk', ''),
            'webhook_secret' => SystemConfig::getValue('webhook_secret', ''),
            'subscription_amount' => SystemConfig::getValue('subscription_amount', '29.99'),
            'currency' => SystemConfig::getValue('currency', 'USD'),
        ];

        return view('dashboard', compact(
            'events', 'sites', 'users', 'user', 'domains', 'selectedSite', 'weekNumber', 
            'frictionData', 'captureData', 'regionData', 'healthScore', 'healthReading', 
            'healthDetail', 'healthTrend', 'weeklyProgress', 'weeklyText', 'funnelData', 
            'funnelInsight', 'leadsCount', 'leadsTrendText', 'leadsTrendClass', 'leadsText', 
            'activityChart', 'nextDecisionTitle', 'nextDecisionText', 'topClickedContent',
            'stripeConfigs', 'heatmapData'
        ));
    }

    public function storeUser(Request $request)
    {
        if (Auth::user()->role !== 'master') {
            return abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:master,editor,viewer',
            'site_name' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        
        User::create($data);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function clearSiteEvents(Request $request, $site_name)
    {
        $user = Auth::user();
        
        // Master puede borrar cualquier sitio. Editor solo su sitio. Viewer no puede.
        if ($user->role === 'viewer') {
            return abort(403);
        }

        if ($user->role === 'editor' && $user->site_name !== $site_name) {
            return abort(403);
        }

        Event::where('site_name', $site_name)->delete();

        return back()->with('success', "Eventos del sitio {$site_name} eliminados.");
    }

    public function storeDomain(Request $request)
    {
        if (Auth::user()->role !== 'master') {
            return abort(403);
        }

        $request->validate([
            'domain' => 'required|string|unique:allowed_domains,domain|url'
        ]);

        // Clean domain if it has trailing slash
        $domain = rtrim($request->domain, '/');

        AllowedDomain::create(['domain' => $domain]);
        Cache::forget('allowed_domains');

        return back()->with('success', 'Dominio registrado correctamente.');
    }

    public function destroyDomain($id)
    {
        if (Auth::user()->role !== 'master') {
            return abort(403);
        }

        AllowedDomain::destroy($id);
        Cache::forget('allowed_domains');

        return back()->with('success', 'Dominio eliminado.');
    }

    public function saveStripeConfig(Request $request)
    {
        if (Auth::user()->role !== 'master') {
            return abort(403);
        }

        $request->validate([
            'payments_enabled' => 'nullable',
            'pk' => 'nullable|string',
            'sk' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'subscription_amount' => 'required|numeric|min:0.5',
            'currency' => 'required|string|max:10',
        ]);

        if ($request->has('payments_enabled_present')) {
            $isEnabled = $request->boolean('payments_enabled');
            SystemConfig::setValue('payments_enabled', $isEnabled ? 'true' : 'false');
        }

        if ($request->has('pk') && !empty(trim($request->pk))) {
            SystemConfig::setValue('pk', trim($request->pk));
        }
        if ($request->has('sk') && !empty(trim($request->sk))) {
            SystemConfig::setValue('sk', trim($request->sk));
        }
        if ($request->has('webhook_secret') && !empty(trim($request->webhook_secret))) {
            SystemConfig::setValue('webhook_secret', trim($request->webhook_secret));
        }
        SystemConfig::setValue('subscription_amount', strval($request->subscription_amount));
        SystemConfig::setValue('currency', strtoupper(trim($request->currency)));

        return back()->with('success', 'Los parámetros de pago y suscripción se han actualizado exitosamente en la plataforma.');
    }
}
