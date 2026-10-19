<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Unlock Brands | Sistema de Presencia Digital Activa</title>
  <style>
    :root{
      --bg:#080808;
      --panel:#101010;
      --panel-2:#151515;
      --line:#242424;
      --text:#f3f3f0;
      --muted:#9b9b94;
      --acid:#00ff9c;
      --red:#ff4d4d;
      --amber:#f6c85f;
      --blue:#70a7ff;
      --shadow:0 20px 60px rgba(0,0,0,.45);
      --radius:24px;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #555; }

    *{box-sizing:border-box}
    body{
      margin:0;
      background: radial-gradient(circle at 20% 0%, rgba(0,255,156,.09), transparent 28%),
                  radial-gradient(circle at 90% 10%, rgba(112,167,255,.08), transparent 24%),
                  var(--bg);
      color:var(--text);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      letter-spacing:-.02em;
      -webkit-font-smoothing: antialiased;
    }

    .app{
      display:grid;
      grid-template-columns:290px 1fr;
      min-height:100vh;
    }

    aside{
      position:sticky;
      top:0;
      height:100vh;
      padding:28px 22px;
      border-right:1px solid var(--line);
      background:rgba(8,8,8,.6);
      backdrop-filter:blur(24px);
      -webkit-backdrop-filter: blur(24px);
      z-index: 10;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:12px;
      margin-bottom:34px;
    }

    .mark{
      width:42px;height:42px;
      border:1px solid rgba(0,255,156,.6);
      border-radius:14px;
      display:grid;place-items:center;
      box-shadow:0 0 26px rgba(0,255,156,.18) inset;
      color:var(--acid);
      font-weight:800;
    }

    .brand h1{
      font-size:15px;
      margin:0;
      text-transform:uppercase;
      letter-spacing:.08em;
    }

    .brand p{margin:3px 0 0;color:var(--muted);font-size:12px;letter-spacing:0}

    nav{display:flex;flex-direction:column;gap:8px}
    nav a{
      text-decoration:none;
      color:var(--muted);
      padding:13px 14px;
      border-radius:16px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      font-size:14px;
      transition:all .2s ease;
    }
    nav a:hover, nav a.active{
      color:var(--text);
      background:linear-gradient(90deg, rgba(0,255,156,.14), rgba(255,255,255,.035));
      transform: translateX(4px);
    }
    nav span{font-family:ui-monospace, SFMono-Regular, Menlo, monospace;font-size:11px;color:#666; transition:color .2s ease;}
    nav a:hover span, nav a.active span { color: var(--acid); }

    .aside-card{
      margin-top:28px;
      padding:18px;
      border:1px solid var(--line);
      border-radius:22px;
      background:linear-gradient(180deg, rgba(255,255,255,.035), rgba(255,255,255,.015));
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .aside-card b{display:block;font-size:13px;margin-bottom:8px; color:var(--text);}
    .aside-card p{margin:0;color:var(--muted);font-size:12px;line-height:1.5;letter-spacing:0}

    main{padding:34px;overflow:hidden}
    header{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      margin-bottom:28px;
    }
    .eyebrow{color:var(--acid);font-family:ui-monospace, Menlo, monospace;font-size:12px;text-transform:uppercase;letter-spacing:.14em}
    h2{font-size:42px;line-height:1;margin:8px 0 8px;letter-spacing:-.06em}
    .sub{color:var(--muted);max-width:720px;margin:0;line-height:1.5;letter-spacing:0}

    .client-pill{
      border:1px solid var(--line);
      background:rgba(255,255,255,.04);
      padding:10px 16px;
      border-radius:999px;
      color:var(--muted);
      font-size:13px;
      display: flex;
      align-items: center;
      gap: 10px;
      backdrop-filter: blur(10px);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .client-pill strong{color:var(--text)}
    .client-pill select { background: transparent; border: none; color: var(--text); font-family: inherit; font-size: 13px; font-weight: bold; outline: none; cursor: pointer; appearance: none; padding-right: 15px; }

    section{margin-bottom:26px}
    .grid{display:grid;gap:18px}
    .grid-3{grid-template-columns:1.1fr .95fr .95fr}
    .grid-4{grid-template-columns:repeat(4,1fr)}
    .grid-2{grid-template-columns:1fr 1fr}

    .card{
      background:linear-gradient(180deg, rgba(255,255,255,.055), rgba(255,255,255,.022));
      border:1px solid var(--line);
      border-radius:var(--radius);
      padding:22px;
      box-shadow:var(--shadow), inset 0 1px 0 rgba(255,255,255,0.06);
      position:relative;
      overflow:hidden;
      transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-2px);
      border-color: rgba(255,255,255,0.15);
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
    }
    .card:before{
      content:"";
      position:absolute;inset:0;
      background:linear-gradient(120deg, rgba(0,255,156,.07), transparent 28%);
      pointer-events:none;
      opacity:.7;
    }
    .card > *{position:relative;z-index:1}
    .label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:12px}
    .metric{font-family:ui-monospace, SFMono-Regular, Menlo, monospace;font-size:40px;font-weight:700;letter-spacing:-.06em}
    .small{color:var(--muted);font-size:13px;line-height:1.45;letter-spacing:0;word-break:break-word}
    .trend{margin-top:14px;font-size:13px;color:var(--acid);font-family:ui-monospace, Menlo, monospace}
    .bad{color:var(--red)} .warn{color:var(--amber)} .blue{color:var(--blue)}

    .score-wrap{display:grid;grid-template-columns:190px 1fr;gap:24px;align-items:center}
    .score{
      width:174px;height:174px;border-radius:50%;
      background:conic-gradient(var(--acid) 0 78%, #252525 78% 100%);
      display:grid;place-items:center;
      position:relative;
      box-shadow:0 0 42px rgba(0,255,156,.12);
      transition: transform 0.5s ease;
    }
    .card:hover .score { transform: scale(1.02) rotate(2deg); }
    .score:after{content:"";position:absolute;inset:13px;background:var(--panel);border-radius:50%;border:1px solid var(--line)}
    .score div{position:relative;z-index:2;text-align:center}
    .score strong{display:block;font-family:ui-monospace, Menlo, monospace;font-size:42px;letter-spacing:-.08em}
    .score span{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.12em}
    .reading{font-size:25px;line-height:1.18;letter-spacing:-.05em;margin:0 0 14px; font-weight:600;}

    .bar{height:10px;border-radius:999px;background:#262626;overflow:hidden;margin-top:16px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);}
    .bar i{display:block;height:100%;background:var(--acid);border-radius:999px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);}

    .fake-chart{height:185px;display:flex;align-items:stretch;gap:10px;padding-top:14px}
    .fake-chart i{width:100%; flex:none; background:linear-gradient(180deg, rgba(0,255,156,.95), rgba(0,255,156,.14));border-radius:6px 6px 0 0;min-height:6px; transition: height 0.5s ease;}
    .fake-chart div:hover i { filter: brightness(1.2); }

    .heatmap{
      height:290px;
      border-radius:20px;
      background:#0d0d0d;
      border:1px solid var(--line);
      position:relative;
      overflow:hidden;
    }
    .heatmap:before{content:"";position:absolute;inset:24px;border:1px dashed #333;border-radius:16px}
    .hot{position:absolute;border-radius:50%;filter:blur(7px);opacity:.82}
    .h1{width:120px;height:120px;background:rgba(0,255,156,.45);top:35px;left:80px}
    .h2{width:90px;height:90px;background:rgba(255,77,77,.46);top:150px;right:90px}
    .h3{width:68px;height:68px;background:rgba(246,200,95,.55);bottom:45px;left:230px}

    .funnel{display:grid;gap:12px;margin-top:8px}
    .step{padding:14px 16px;border:1px solid var(--line);border-radius:16px;background:rgba(255,255,255,.03);display:flex;justify-content:space-between;color:var(--muted);font-size:14px; transition: background 0.2s;}
    .step:hover { background: rgba(255,255,255,.06); }
    .step strong{color:var(--text);font-family:ui-monospace, Menlo, monospace}

    .alert{display:flex;gap:14px;align-items:flex-start;padding:16px;border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.04);margin-top:12px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);}
    .dot{width:10px;height:10px;border-radius:50%;background:var(--acid);margin-top:5px;box-shadow:0 0 18px var(--acid); flex-shrink:0;}
    .dot.red{background:var(--red);box-shadow:0 0 18px var(--red)}
    .dot.amber{background:var(--amber);box-shadow:0 0 18px var(--amber)}

    .module-title{font-size:22px; font-weight:600; margin:0 0 16px; letter-spacing:-.04em}
    .table{width:100%;border-collapse:separate; border-spacing:0; font-size:14px;color:var(--muted)}
    .table th{text-align:left; padding:12px 6px; color:var(--text); font-weight:600; border-bottom:1px solid var(--line); font-size:12px; text-transform:uppercase; letter-spacing:.05em;}
    .table td{border-bottom:1px solid var(--line);padding:14px 6px; transition: background 0.2s;}
    .table tr:hover td { background: rgba(255,255,255,0.02); }
    .table td:last-child{text-align:right;font-family:ui-monospace, Menlo, monospace;color:var(--text)}

    /* Custom form elements for admin */
    .btn { background: var(--acid); color: #000; border: none; padding: 8px 16px; border-radius: 999px; font-weight: 600; cursor: pointer; font-size: 13px; font-family: inherit; box-shadow: 0 4px 14px rgba(0,255,156,0.25); transition: all 0.2s ease;}
    .btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,255,156,0.35); filter: brightness(1.1); }
    .btn:active { transform: translateY(1px); box-shadow: 0 2px 10px rgba(0,255,156,0.2); }
    .btn-danger { background: transparent; color: var(--red); border: 1px solid var(--red); box-shadow: none; }
    .btn-danger:hover { background: rgba(255,77,77,0.1); border-color: #ff6b6b; box-shadow: none; filter: none; }
    
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; margin-bottom: 6px; color: var(--muted); font-size: 13px; }
    .form-group input, .form-group select { width: 100%; padding: 12px 14px; background: rgba(0,0,0,0.3); border: 1px solid var(--line); color: white; border-radius: 12px; font-family: inherit; font-size:14px; transition: all 0.2s; }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--acid); box-shadow: 0 0 0 3px rgba(0,255,156,0.15); background: rgba(0,0,0,0.5); }

    /* Switch de Pagos en Base de Datos */
    .payment-switch-card {
      background: rgba(255,255,255,0.03);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 16px 18px;
      margin-bottom: 20px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }
    .payment-switch-card.is-active {
      border-color: rgba(0,255,156,0.35);
      background: linear-gradient(135deg, rgba(0,255,156,0.06), rgba(255,255,255,0.02));
      box-shadow: 0 0 24px rgba(0,255,156,0.06);
    }
    .payment-switch-card:not(.is-active) {
      border-color: rgba(255,77,77,0.3);
      background: linear-gradient(135deg, rgba(255,77,77,0.05), rgba(255,255,255,0.02));
    }
    .switch-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    .switch-header { flex: 1; }
    .switch-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 4px;
      flex-wrap: wrap;
    }
    .switch-desc { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.4; }
    
    .status-indicator-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      display: inline-block;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }
    .status-indicator-dot.active {
      background: var(--acid);
      box-shadow: 0 0 12px var(--acid);
    }
    .status-indicator-dot.inactive {
      background: var(--red);
      box-shadow: 0 0 12px var(--red);
    }

    .status-badge {
      font-size: 10px;
      font-family: ui-monospace, Menlo, monospace;
      font-weight: 800;
      letter-spacing: 0.08em;
      padding: 3px 8px;
      border-radius: 6px;
      text-transform: uppercase;
      transition: all 0.3s ease;
    }
    .status-badge.badge-active {
      background: rgba(0,255,156,0.15);
      color: var(--acid);
      border: 1px solid rgba(0,255,156,0.4);
    }
    .status-badge.badge-inactive {
      background: rgba(255,77,77,0.15);
      color: var(--red);
      border: 1px solid rgba(255,77,77,0.4);
    }

    /* Toggle Switch Button */
    .modern-toggle {
      position: relative;
      display: inline-block;
      width: 56px;
      height: 30px;
      flex-shrink: 0;
      cursor: pointer;
    }
    .modern-toggle input {
      opacity: 0;
      width: 0;
      height: 0;
      position: absolute;
    }
    .toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: #1e1e1e;
      border: 1px solid #383838;
      border-radius: 30px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);
    }
    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 22px;
      width: 22px;
      left: 3px;
      bottom: 3px;
      background-color: #888;
      border-radius: 50%;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 2px 6px rgba(0,0,0,0.6);
    }
    .modern-toggle input:checked + .toggle-slider {
      background-color: rgba(0,255,156,0.18);
      border-color: var(--acid);
      box-shadow: 0 0 16px rgba(0,255,156,0.25), inset 0 1px 3px rgba(0,0,0,0.4);
    }
    .modern-toggle input:checked + .toggle-slider:before {
      transform: translateX(26px);
      background-color: var(--acid);
      box-shadow: 0 0 10px var(--acid), 0 2px 6px rgba(0,0,0,0.4);
    }
    .modern-toggle.is-loading {
      opacity: 0.5;
      pointer-events: none;
    }

    footer{padding:18px 0 40px;color:#666;font-size:12px;letter-spacing:0; border-top: 1px solid var(--line); margin-top: 40px;}

    @media(max-width:980px){
      .app{grid-template-columns:1fr}
      aside{position:relative;height:auto; z-index:10;}
      .grid-3,.grid-4,.grid-2,.score-wrap{grid-template-columns:1fr}
      h2{font-size:34px}
      header{flex-direction:column;gap:16px}
    }
  </style>
</head>
<body>
  @php
    $siteEvents = collect();
    $views = 0;
    $clicks = 0;
    $conversionRate = 0;
    $retentionFormatted = $retentionFormatted ?? '00:00';
    
    if ($selectedSite) {
        $siteEvents = $events->where('site_name', $selectedSite);
        $views = $siteEvents->where('type', 'view')->count();
        $clicks = $siteEvents->where('type', 'click')->where('content', '!=', 'heatmap_trace')->count();
        
        $ctaClicks = $siteEvents->where('type', 'cta_click')->count();
        if ($ctaClicks == 0) { // Fallback
            $ctaClicks = $siteEvents->where('type', 'click')->filter(function($e) {
                return $e->content && stripos($e->content, 'cta') !== false;
            })->count();
        }
        
        if ($views > 0) {
            $conversionRate = ($ctaClicks / $views) * 100;
        }

        // Calculate Retention (Average Time on Site by session / continuous visit)
        if (!isset($retentionFormatted) || $retentionFormatted === '00:00') {
            $allVisits = [];
            foreach ($siteEvents->whereNotNull('session_id')->groupBy('session_id') as $sessionId => $eventsForSession) {
                $sorted = $eventsForSession->sortBy('created_at')->values();
                if ($sorted->count() > 1) {
                    $currentVisitStart = $sorted[0]->created_at;
                    $currentVisitLast = $sorted[0]->created_at;

                    for ($i = 1; $i < $sorted->count(); $i++) {
                        $diff = $sorted[$i - 1]->created_at->diffInSeconds($sorted[$i]->created_at);
                        if ($diff >= 1800) {
                            $visitDuration = $currentVisitStart->diffInSeconds($currentVisitLast);
                            if ($visitDuration > 0) {
                                $allVisits[] = min($visitDuration, 1800);
                            }
                            $currentVisitStart = $sorted[$i]->created_at;
                        }
                        $currentVisitLast = $sorted[$i]->created_at;
                    }
                    $visitDuration = $currentVisitStart->diffInSeconds($currentVisitLast);
                    if ($visitDuration > 0) {
                        $allVisits[] = min($visitDuration, 1800);
                    }
                } elseif ($sorted->count() === 1) {
                    $dur = $sorted[0]->duration ?? 0;
                    if ($dur > 1800 && ($dur / 1000) <= 1800) {
                        $dur = round($dur / 1000);
                    }
                    if ($dur > 0) {
                        $allVisits[] = min($dur, 1800);
                    }
                }
            }

            $retentionSeconds = count($allVisits) > 0 ? round(array_sum($allVisits) / count($allVisits)) : 0;
            $retentionFormatted = sprintf('%02d:%02d', floor($retentionSeconds / 60), $retentionSeconds % 60);
        }
    }
  @endphp

  <div class="app">
    <aside>
      <div class="brand">
        <div class="mark">UB</div>
        <div>
          <h1>Unlock Brands</h1>
          <p>Sistema de Presencia Digital Activa</p>
        </div>
      </div>
      <nav>
        <a href="#estado" class="active">Estado de la marca <span>01</span></a>
        <a href="#comportamiento">Comportamiento <span>02</span></a>
        <a href="#friccion">Fricción <span>03</span></a>
        <a href="#visibilidad">Visibilidad <span>04</span></a>
        <a href="#captura">Captura de valor <span>05</span></a>
        <a href="#sistema">Sistema técnico <span>06</span></a>
        @if($user->role === 'master')
        <a href="#admin" style="margin-top: 15px; border: 1px dashed var(--line);">Admin <span>07</span></a>
        <a href="#stripe-settings" style="border: 1px dashed var(--line); border-top: none;">Cobros Stripe <span id="navStripeBadge" style="{{ ($stripeConfigs['enabled'] ?? true) ? '' : 'color: #f87171;' }}">{{ ($stripeConfigs['enabled'] ?? true) ? '08' : 'OFF' }}</span></a>
        @endif
      </nav>
      <div class="aside-card">
        <b>Lectura activa</b>
        <p>Usuario actual: {{ $user->name }} ({{ strtoupper($user->role) }}). Este dashboard no muestra datos aislados. Traduce tráfico y rendimiento técnico en una lectura ejecutiva.</p>
      </div>
      
      <form action="{{ route('logout') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <button type="submit" class="btn btn-danger" style="width:100%;">Cerrar Sesión</button>
      </form>
    </aside>

    <main>
      <header>
        <div>
          <div class="eyebrow">Dashboard ejecutivo / cliente</div>
          <h2>Interfaz de lectura de la marca</h2>
          <p class="sub">Mockup conceptual para entregar a clientes de Cetia Media: una plataforma donde el sitio deja de ser una página estática y se convierte en un sistema medible, interpretable y optimizable.</p>
        </div>
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:10px;">
          <div class="client-pill">
            <span style="font-family:ui-monospace, Menlo, monospace; color:var(--text);">{{ ucfirst(\Carbon\Carbon::now()->locale('es')->monthName) }} {{ \Carbon\Carbon::now()->year }}</span>
            · 
            Cliente: 
            <form action="{{ route('dashboard') }}" method="GET" id="siteForm" style="margin:0; display:flex;">
              @php
                $siteLen = $selectedSite ? strlen($selectedSite) : 10;
              @endphp
              <select name="site" onchange="document.getElementById('siteForm').submit()" style="font-family:ui-monospace, Menlo, monospace; font-size:12px; color:var(--acid); width: {{ $siteLen + 3 }}ch; padding-right:10px;">
                @foreach($sites as $site)
                  <option value="{{ $site }}" {{ request('site') == $site ? 'selected' : '' }}>{{ $site }}</option>
                @endforeach
                @if(count($sites) == 0)
                  <option value="">Sin sitios</option>
                @endif
              </select>
            </form>
          </div>
          @if($selectedSite && ($user->role === 'master' || $user->role === 'editor'))
          <form action="{{ route('events.clear', $selectedSite) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar todos los eventos de {{ $selectedSite }}?');" style="margin:0;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="font-size:11px; padding: 4px 10px;">Purgar Datos del Sitio</button>
          </form>
          @endif
        </div>
      </header>

      @if(session('success'))
        <div class="alert"><span class="dot"></span><p class="small" style="color:var(--acid);">{{ session('success') }}</p></div>
      @endif
      @if($errors->any())
        <div class="alert"><span class="dot red"></span><p class="small" style="color:var(--red);">{{ $errors->first() }}</p></div>
      @endif

      @if(!$selectedSite)
        <div class="alert" style="margin-bottom: 26px;"><span class="dot amber"></span><p class="small" style="color:var(--amber);">Aún no se registran sitios o interacciones activas. Conecta el seguimiento en tu sitio web para comenzar a capturar métricas ejecutivas en vivo.</p></div>
      @endif

      <section id="estado" class="grid grid-3">
        <div class="card" style="grid-column:span 2">
          <div class="label">Estado de la marca</div>
          <div class="score-wrap">
            <div class="score" style="background:conic-gradient(var(--acid) 0 {{ $healthScore }}%, #252525 {{ $healthScore }}% 100%);"><div><strong>{{ $healthScore }}</strong><span>salud digital</span></div></div>
            <div>
              <p class="reading">{{ $healthReading }}</p>
              <p class="small">{{ $healthDetail }}</p>
              <div class="trend">{{ $healthTrend }}</div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="label">Lectura semanal</div>
          <p class="reading" style="font-size:22px">Semana {{ $weekNumber }}</p>
          <p class="small">{{ $weeklyText }}</p>
          <div class="bar"><i style="width:{{ $weeklyProgress }}%"></i></div>
        </div>
      </section>

      <section class="grid grid-4">
        <!-- REAL VIEWS -->
        <div class="card"><div class="label">Tráfico (Vistas)</div><div class="metric">{{ $selectedSite ? number_format($views) : '--' }}</div><p class="trend">▲ Real</p><p class="small">La atención capturada.</p></div>
        <!-- REAL CLICKS -->
        <div class="card"><div class="label">Interacción (Clics)</div><div class="metric">{{ $selectedSite ? number_format($clicks) : '--' }}</div><p class="trend">▲ Real</p><p class="small">El usuario explora más.</p></div>
        <!-- REAL CONVERSION -->
        <div class="card"><div class="label">Conversión (CTA)</div><div class="metric">{{ $selectedSite ? number_format($conversionRate, 1) . '%' : '--' }}</div><p class="trend">▲ Real</p><p class="small">Vistas que terminan en CTA.</p></div>
        <!-- REAL RETENTION -->
        <div class="card"><div class="label">Retención</div><div class="metric">{{ $selectedSite ? $retentionFormatted : '--:--' }}</div><p class="trend warn">● Real</p><p class="small">Tiempo promedio por sesión.</p></div>
      </section>

      <section id="feed-real" class="grid grid-2">
        <div class="card" style="grid-column: span 2">
          <div class="label" style="color:var(--acid);">Telemetría en Tiempo Real</div>
          <h3 class="module-title">Últimos Eventos Capturados ({{ $selectedSite ?? 'Ningún sitio seleccionado' }})</h3>
          <div style="max-height: 250px; overflow-y:auto; border-radius:12px; border:1px solid var(--line); background:var(--panel-2); margin-top: 15px;">
            <table class="table" style="margin:0;">
                @if($selectedSite)
                    @foreach($siteEvents->take(100) as $event)
                    <tr>
                        <td style="font-size:12px; color:#666; width: 140px;">{{ $event->created_at->format('M d, H:i:s') }}</td>
                        <td style="width: 100px;">
                            @php
                                $c = 'white';
                                switch($event->type){
                                    case 'click': $c = 'var(--acid)'; break;
                                    case 'reload': $c = 'var(--blue)'; break;
                                    case 'external_link': $c = 'var(--amber)'; break;
                                    case 'cta_click': $c = '#00ff9c'; break;
                                    case 'form_start': $c = 'lightblue'; break;
                                    case 'form_submit': $c = 'lightgreen'; break;
                                }
                            @endphp
                            <span style="color: {{ $c }}; font-weight:bold; letter-spacing: 1px;">{{ strtoupper($event->type) }}</span>
                        </td>
                        <td style="font-family:monospace; font-size:11px; text-align: left;">Sesión: {{ substr($event->session_id ?? '---', 0, 8) }}</td>
                        <td style="font-size:12px; color:var(--text); text-align: left;">{{ $event->content ?? '' }}</td>
                    </tr>
                    @endforeach
                    @if($siteEvents->count() == 0)
                    <tr><td colspan="4" style="text-align:center; padding: 30px; font-style: italic;">Esperando nuevas interacciones y actividad en tiempo real...</td></tr>
                    @endif
                @else
                    <tr><td colspan="4" style="text-align:center; padding: 30px; font-style: italic;">No hay un sitio seleccionado para mostrar eventos.</td></tr>
                @endif
            </table>
          </div>
        </div>
      </section>

      <section id="comportamiento" class="grid grid-2">
        <div class="card">
          <div class="label">Comportamiento del usuario</div>
          <h3 class="module-title">Zonas de atención</h3>
          <div class="heatmap" id="user-heatmap-container">
            @if(count($heatmapData) == 0)
                <span class="hot h1"></span><span class="hot h2"></span><span class="hot h3"></span>
            @endif
          </div>
          <p class="small">El usuario concentra su mayor atención e interacción real en: <strong style="color:var(--text); font-family:monospace;">{{ $topClickedContent ?? 'bloques principales' }}</strong>.</p>
        </div>
        <div class="card">
          <div class="label">Recorrido real del usuario</div>
          <h3 class="module-title">Del interés a la acción</h3>
          <div class="funnel">
            @foreach($funnelData as $step)
            <div class="step"><span>{{ $step['label'] }} <small style="color:#666; font-family:monospace;">({{ number_format($step['count']) }})</small></span> <strong>{{ $step['percent'] }}%</strong></div>
            @endforeach
          </div>
          <div class="alert"><span class="dot amber"></span><p class="small">{!! $funnelInsight !!}</p></div>
        </div>
      </section>

      <section id="friccion" class="grid grid-3">
        <div class="card">
          <div class="label">Fricción</div>
          <h3 class="module-title">Lugares donde la marca falla</h3>
          @if($selectedSite && count($frictionData) > 0)
              @foreach(array_slice($frictionData, 0, 3) as $alert)
              <div class="alert"><span class="dot {{ $alert['type'] == 'danger' ? 'red' : 'amber' }}"></span><p class="small">{{ $alert['message'] }}</p></div>
              @endforeach
          @else
              <div class="alert"><span class="dot"></span><p class="small">No hay datos suficientes de fricción para este sitio.</p></div>
          @endif
        </div>
        <div class="card" style="grid-column:span 2">
          <div class="label">Regiones</div>
          <h3 class="module-title">Distribución geográfica (Timezone)</h3>
          <table class="table">
            @foreach($regionData as $region => $count)
            <tr><td>{{ $region }}</td><td>{{ $count }} sesiones</td></tr>
            @endforeach
            @if(empty($regionData))
            <tr><td colspan="2">Sin datos de región registrados.</td></tr>
            @endif
          </table>
        </div>
      </section>

      <section id="visibilidad" class="grid grid-2">
        <div class="card">
          <div class="label">Actividad reciente</div>
          <h3 class="module-title">Tráfico diario de los últimos 7 días</h3>
          <div class="fake-chart" style="align-items: stretch; gap: 10px;">
            @php
                $maxDaily = count($activityChart) > 0 ? max($activityChart) : 1;
                if ($maxDaily == 0) $maxDaily = 1;
            @endphp
            @foreach($activityChart as $dLabel => $dCount)
                @php
                    $hPercent = round(($dCount / $maxDaily) * 100);
                    if ($hPercent < 8) $hPercent = 8;
                @endphp
                <div style="display:flex; flex-direction:column; align-items:center; flex:1; min-width:0; height:100%; justify-content:flex-end;">
                  <span style="font-size: 11px; font-family:monospace; color: var(--text); margin-bottom:6px; white-space:nowrap; flex-shrink:0;">{{ $dCount }}</span>
                  <div style="flex:1; width:100%; display:flex; align-items:flex-end; justify-content:center; min-height:0;">
                    <i style="height:{{ $hPercent }}%; width: 100%; background: linear-gradient(180deg, rgba(0,255,156,.95), rgba(0,255,156,.14)); border-radius: 6px 6px 0 0;" title="{{ $dCount }} eventos"></i>
                  </div>
                  <span style="font-size: 10px; color: #666; margin-top:6px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; max-width:100%; flex-shrink:0; text-align:center;">{{ $dLabel }}</span>
                </div>
            @endforeach
            @if(empty($activityChart))
                <p style="font-size: 12px; color: #666; align-self:center;">Sin actividad reciente.</p>
            @endif
          </div>
          <p class="small" style="margin-top:14px;">El consumo diario refleja el volumen real de interacciones registradas y permite proyectar días pico para lanzamiento de campañas.</p>
        </div>
        <div class="card">
          <div class="label">IA-ready / LLM</div>
          <h3 class="module-title">Preguntas que la marca ya puede responder</h3>
          <table class="table">
            <tr><td>¿Qué ofrece la marca?</td><td class="blue">claro</td></tr>
            <tr><td>¿Por qué elegirla?</td><td class="warn">medio</td></tr>
            <tr><td>¿Cuánto cuesta?</td><td class="bad">débil</td></tr>
            <tr><td>¿Cómo iniciar?</td><td class="blue">claro</td></tr>
          </table>
        </div>
      </section>

      <section id="captura" class="grid grid-3">
        <div class="card" style="grid-column:span 2">
          <div class="label">Captura de valor</div>
          <h3 class="module-title">Páginas con más tiempo de retención</h3>
          <div class="fake-chart" style="align-items: stretch; gap: 8px;">
            @php
                $maxDuration = count($captureData) > 0 ? max($captureData) : 1;
                if ($maxDuration == 0) $maxDuration = 1;
            @endphp
            @foreach($captureData as $page => $duration)
                @php
                    $heightPercent = ($duration / $maxDuration) * 100;
                    if ($heightPercent < 5) $heightPercent = 5;
                @endphp
                <div style="display:flex; flex-direction:column; align-items:center; flex:1; min-width:0; height:100%; justify-content:flex-end;">
                  <span style="font-size: 10px; color: var(--text); margin-bottom:6px; white-space:nowrap; flex-shrink:0;">
                    @if($duration >= 3600)
                      {{ floor($duration / 3600) }}h {{ floor(($duration % 3600) / 60) }}m
                    @elseif($duration >= 60)
                      {{ floor($duration / 60) }}m {{ round($duration % 60) }}s
                    @else
                      {{ round($duration) }}s
                    @endif
                  </span>
                  <div style="flex:1; width:100%; display:flex; align-items:flex-end; justify-content:center; min-height:0;">
                    <i style="height:{{ $heightPercent }}%; width: 100%; background: var(--acid); border-radius: 4px 4px 0 0;" title="{{ $page }}"></i>
                  </div>
                  <span style="font-size: 9px; color: #666; margin-top:6px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; max-width: 100%; flex-shrink:0; text-align:center;" title="{{ $page }}">{{ substr(basename(parse_url($page, PHP_URL_PATH) ?? $page), 0, 10) ?: 'home' }}</span>
                </div>
            @endforeach
            @if(empty($captureData))
                <p style="font-size: 12px; color: #666; align-self:center;">Sin datos de captura (tiempo en página) para mostrar.</p>
            @endif
          </div>
        </div>
        <div class="card">
          <div class="label">Leads (Envíos de Formulario)</div>
          <div class="metric">{{ number_format($leadsCount) }}</div>
          <p class="trend {{ $leadsTrendClass }}">{{ $leadsTrendText }}</p>
          <p class="small">{{ $leadsText }}</p>
        </div>
      </section>

      <section id="sistema" class="grid grid-2">
        <div class="card">
          <div class="label">Sistema técnico</div>
          <h3 class="module-title">Estado operativo</h3>
          <table class="table">
            @forelse($operationalStatus ?? [] as $item)
            <tr>
              <td>
                {{ $item['name'] }}
                @if(isset($item['types']) && !empty($item['types']))
                  <br><span style="font-size:10px; color:var(--muted);">Tipos: {{ implode(', ', array_slice($item['types'], 0, 3)) }}</span>
                @endif
              </td>
              <td>
                @if(($item['status'] ?? '') === 'pagespeed')
                  <span class="blue" style="font-weight:600;">{{ $item['status_label'] ?? '--/100' }}</span>
                @elseif(($item['status'] ?? '') === 'active')
                  <span style="color:var(--acid); font-weight:600;">{{ $item['status_label'] ?? 'activo' }}</span>
                  @if(isset($item['pages_detected']) && isset($item['pages_checked']) && $item['pages_checked'] > 0)
                    <small style="color:var(--muted); font-size:11px; font-family:monospace;">({{ $item['pages_detected'] }}/{{ $item['pages_checked'] }})</small>
                  @endif
                @elseif(($item['status'] ?? '') === 'partial')
                  <span class="warn" style="font-weight:600;">{{ $item['status_label'] ?? 'parcial' }}</span>
                  @if(isset($item['pages_detected']) && isset($item['pages_checked']) && $item['pages_checked'] > 0)
                    <small style="color:var(--muted); font-size:11px; font-family:monospace;">({{ $item['pages_detected'] }}/{{ $item['pages_checked'] }})</small>
                  @endif
                @elseif(($item['status'] ?? '') === 'unverifiable')
                  <span class="blue" style="font-weight:600; font-size:12px;">{{ $item['status_label'] ?? 'no verificado en HTML' }}</span>
                @else
                  <span class="bad" style="font-weight:600;">{{ $item['status_label'] ?? 'no detectado' }}</span>
                  @if(isset($item['pages_detected']) && isset($item['pages_checked']) && $item['pages_checked'] > 0)
                    <small style="color:var(--muted); font-size:11px; font-family:monospace;">(0/{{ $item['pages_checked'] }})</small>
                  @endif
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="2" style="text-align:center; padding: 20px; font-style: italic;">
                Esperando captura de datos para auditar integraciones...
              </td>
            </tr>
            @endforelse
          </table>
        </div>
        <div class="card">
          <div class="label">Próxima decisión automática</div>
          <h3 class="module-title">Acción recomendada</h3>
          <p class="reading" style="font-size:22px">{{ $nextDecisionTitle }}</p>
          <p class="small">{{ $nextDecisionText }}</p>
          <div class="alert"><span class="dot"></span><p class="small"><b>Prioridad Unlock:</b> convertir lectura y métricas en intervención directa.</p></div>
        </div>
      </section>
      @if($user->role === 'master')
      <section id="admin" class="grid grid-2" style="margin-top: 60px;">
        <div class="card">
          <div class="label">Gestión de Accesos (Master)</div>
          <h3 class="module-title">Crear Nuevo Usuario</h3>
          <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="role" required>
                <option value="viewer">Viewer (Solo lectura)</option>
                <option value="editor">Editor (Ver y purgar)</option>
                <option value="master">Master (Acceso total)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Sitio Asignado (Ignorado si es Master)</label>
                <select name="site_name">
                  <option value="">-- Sin sitio asignado / Acceso Global (Master) --</option>
                  @foreach($sites as $availableSite)
                    <option value="{{ $availableSite }}">{{ $availableSite }}</option>
                  @endforeach
                </select>
            </div>
            <button type="submit" class="btn">Crear Usuario</button>
          </form>
        </div>
        <div class="card">
          <div class="label">Directorio</div>
          <h3 class="module-title">Usuarios Existentes</h3>
          <table class="table">
            <thead>
                <tr>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Sitio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                <td><strong>{{ $u->name }}</strong><br><span style="font-size:11px;color:#666">{{ $u->email }}</span></td>
                <td><span style="color:{{ $u->role === 'master' ? 'var(--red)' : 'var(--acid)' }}">{{ strtoupper($u->role) }}</span></td>
                <td style="font-family:monospace">{{ $u->site_name ?? 'TODOS' }}</td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
      </section>

      <section id="cors" class="grid grid-2" style="margin-top: 24px;">
        <div class="card">
          <div class="label">Seguridad (Master)</div>
          <h3 class="module-title">Conectar sitio web</h3>
          <p class="small" style="margin-bottom: 15px;">Agrega el dominio de tu sitio (ej. <code>https://mi-sitio.com</code>). Sin '/' al final.</p>
          <form action="{{ route('domains.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>URL del Dominio</label>
                <input type="url" name="domain" placeholder="https://..." required>
            </div>
            <button type="submit" class="btn">Permitir Dominio</button>
          </form>
        </div>
        <div class="card">
          <div class="label">Configuración Activa</div>
          <h3 class="module-title">Dominios Permitidos</h3>
          @if(isset($domains) && $domains->count() > 0)
          <table class="table">
            <thead>
                <tr>
                <th>Dominio</th>
                <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($domains as $d)
                <tr>
                <td style="font-family:monospace">{{ $d->domain }}</td>
                <td>
                    <form action="{{ route('domains.destroy', $d->id) }}" method="POST" style="margin:0;display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding:4px 8px;font-size:11px;" onclick="return confirm('¿Seguro que deseas eliminar este dominio? Dejará de enviar datos.')">Eliminar</button>
                    </form>
                </td>
                </tr>
                @endforeach
            </tbody>
          </table>
          @else
          <p class="small" style="color:var(--amber);margin-top:20px;">No hay dominios autorizados registrados. Añade la dirección oficial de tu sitio para habilitar la recepción segura de datos del tablero.</p>
          @endif
        </div>
      </section>

      <section id="stripe-settings" class="grid grid-2" style="margin-top: 24px;">
        <div class="card">
          <div class="label">Cobros por Suscripción (Master)</div>
          <h3 class="module-title">Configurar Precios y Moneda</h3>
          <p class="small" style="margin-bottom: 18px;">Establece el costo mensual de suscripción exigido a clientes y colaboradores para ingresar a su tablero ejecutivo. Las credenciales y estado guardados aquí se almacenan directamente en la base de datos (<code>system_configs</code>).</p>
          
          <!-- Botón Switch Interactivo para Activar/Desactivar Pagos en BD -->
          <div class="payment-switch-card {{ ($stripeConfigs['enabled'] ?? true) ? 'is-active' : '' }}" id="paymentSwitchCard">
            <div class="switch-content">
              <div class="switch-header">
                <div class="switch-title">
                  <span class="status-indicator-dot {{ ($stripeConfigs['enabled'] ?? true) ? 'active' : 'inactive' }}" id="switchStatusDot"></span>
                  <span>Pasarela de Pagos y Suscripción</span>
                  <span class="status-badge {{ ($stripeConfigs['enabled'] ?? true) ? 'badge-active' : 'badge-inactive' }}" id="switchStatusBadge">
                    {{ ($stripeConfigs['enabled'] ?? true) ? 'ACTIVO' : 'DESACTIVADO' }}
                  </span>
                </div>
                <p class="small switch-desc" id="switchStatusDesc">
                  @if($stripeConfigs['enabled'] ?? true)
                    Cobros <strong style="color:var(--acid);">habilitados</strong> en base de datos. Los usuarios que agoten su prueba gratuita requerirán suscripción para ingresar.
                  @else
                    Cobros <strong style="color:var(--red);">desactivados</strong> en base de datos. Acceso libre concedido a todos los usuarios sin muro de pago.
                  @endif
                </p>
              </div>

              <div class="switch-control-area">
                <label class="modern-toggle" title="Haga clic para alternar el estado en la base de datos">
                  <input type="checkbox" id="paymentsToggleSwitch" {{ ($stripeConfigs['enabled'] ?? true) ? 'checked' : '' }}>
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            <div id="switchToastMessage" style="display:none; margin-top:12px; font-size:12px; font-family:ui-monospace, Menlo, monospace; padding:8px 12px; border-radius:8px;"></div>
          </div>

          <form action="{{ route('admin.stripe.save') }}" method="POST">
            @csrf
            <input type="hidden" name="payments_enabled_present" value="1">
            <input type="hidden" name="payments_enabled" id="hiddenPaymentsEnabled" value="{{ ($stripeConfigs['enabled'] ?? true) ? '1' : '0' }}">
            
            <div style="display:flex;gap:12px;">
              <div class="form-group" style="flex:2;">
                <label>Precio Mensual ($)</label>
                <input type="number" step="0.01" min="0.50" name="subscription_amount" value="{{ $stripeConfigs['subscription_amount'] ?? '29.99' }}" required>
              </div>
              <div class="form-group" style="flex:1;">
                <label>Moneda</label>
                <input type="text" name="currency" value="{{ strtoupper($stripeConfigs['currency'] ?? 'USD') }}" required maxlength="4" style="text-transform:uppercase;">
              </div>
            </div>
            <div class="form-group" style="margin-top:10px;">
                <label>Llave Pública de Stripe (Publishable Key)</label>
                <input type="text" name="pk" value="{{ $stripeConfigs['pk'] }}" placeholder="Ingresar clave pública u omitir para usar predeterminada">
            </div>
            <div class="form-group">
                <label>Llave Secreta de Stripe (Secret Key)</label>
                <input type="password" name="sk" value="{{ $stripeConfigs['sk'] }}" placeholder="Ingresar clave secreta u omitir para usar predeterminada">
            </div>
            <div class="form-group">
                <label>Secreto de Webhook de Stripe</label>
                <input type="password" name="webhook_secret" value="{{ $stripeConfigs['webhook_secret'] }}" placeholder="Ingresar secreto de webhook u omitir para usar predeterminada">
            </div>
            <button type="submit" class="btn">Guardar Parámetros de Pago</button>
          </form>
        </div>
        <div class="card" style="display:flex;flex-direction:column;justify-content:space-between;">
          <div>
            <div class="label">Estatus de Integración</div>
            <h3 class="module-title">Resumen del Módulo de Pago</h3>
            <p class="small" style="line-height:1.6;color:#ccc;">
              Este sistema garantiza que solo los administradores principales con rol <strong style="color:var(--acid);">MASTER</strong> puedan ingresar de forma ilimitada al sistema.
              Cualquier usuario creado como <strong>EDITOR</strong> o <strong>VIEWER</strong> iniciará sesión con <strong>2 meses de prueba gratuita</strong> de forma automática, sin requerir cobro o registro de tarjeta durante los primeros 60 días.
            </p>
            <div style="margin-top:15px;background:rgba(255,255,255,0.02);padding:14px;border-radius:8px;border-left:3px solid var(--acid);">
              <div style="font-size:12px;font-weight:600;margin-bottom:4px;color:var(--acid);">ESTATUS ACTUAL DE LA PLATAFORMA DE COBROS:</div>
              <ul style="font-size:12px;margin:0;padding-left:16px;color:#eee;line-height:1.8;">
                <li><strong>Estado de Cobros:</strong> 
                  <span id="summaryPaymentStatus">
                    @if($stripeConfigs['enabled'] ?? true)
                      <span style="color:var(--acid);font-weight:bold;">● ACTIVO (Muro de pago y suscripciones habilitadas)</span>
                    @else
                      <span style="color:#f87171;font-weight:bold;">○ DESACTIVADO (Acceso libre a todos los usuarios)</span>
                    @endif
                  </span>
                </li>
                <li><strong>Plan Configurado:</strong> ${{ $stripeConfigs['subscription_amount'] ?? '29.99' }} {{ strtoupper($stripeConfigs['currency'] ?? 'USD') }} / mes</li>
                <li><strong>Periodo de Prueba:</strong> <span style="color:var(--acid);font-weight:bold;">60 días (2 meses gratis) activados automáticamente</span></li>
                <li><strong>Llave Pública (PK):</strong> {{ !empty($stripeConfigs['pk']) ? 'Configurada (' . substr($stripeConfigs['pk'], 0, 8) . '...)' : 'Usando predeterminada de sistema' }}</li>
                <li><strong>Llave Secreta (SK):</strong> {{ !empty($stripeConfigs['sk']) ? 'Configurada y activa' : 'Usando predeterminada de sistema' }}</li>
                <li><strong>Firma Webhook:</strong> {{ !empty($stripeConfigs['webhook_secret']) ? 'Sincronización activa' : 'Usando predeterminado de sistema' }}</li>
              </ul>
            </div>
            <p class="small" style="margin-top:15px;color:var(--blue);">
              💡 <strong>Control desde Base de Datos:</strong> Puedes activar o desactivar los cobros al instante usando el botón switch interactivo. El valor se sincroniza en tiempo real en la tabla <code>system_configs</code> sin reiniciar servidores ni modificar variables de entorno.
            </p>
          </div>
        </div>
      </section>

      @endif

      <footer>
        Plataforma ejecutiva de inteligencia comercial desarrollada para Cetia Media / Unlock Brands. Monitoreo de Tráfico, Interacción, Embudo de Conversión, Leads y Salud Digital en tiempo real.
      </footer>
    </main>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/heatmap.js/2.0.0/heatmap.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var heatmapData = @json($heatmapData ?? []);
        var container = document.getElementById('user-heatmap-container');
        
        if (heatmapData.length > 0 && container) {
            if (typeof h337 === 'undefined') {
                console.warn('[Analytics Dashboard] La librería heatmap.js no se cargó correctamente (posible bloqueo por AdBlocker o CSP).');
                return;
            }

            // Remove fallback mockup elements if they exist
            container.innerHTML = '';
            
            var heatmapInstance = h337.create({
              container: container,
              radius: 45,
              maxOpacity: 0.7,
              minOpacity: 0.1,
              blur: 0.8,
              gradient: {
                '0.3': '#70a7ff',
                '0.6': '#f6c85f',
                '0.9': '#ff4d4d',
                '1.0': '#00ff9c'
              }
            });
            var points = [];
            
            var containerW = container.offsetWidth;
            var containerH = container.offsetHeight;

            var maxX = 1920;
            var maxY = 1080;

            heatmapData.forEach(function(p) {
                if (p.x > maxX) maxX = p.x;
                if (p.y > maxY) maxY = p.y;
            });

            heatmapData.forEach(function(p) {
                var scaledX = (p.x / maxX) * containerW;
                var scaledY = (p.y / maxY) * containerH;
                
                points.push({ x: Math.floor(scaledX), y: Math.floor(scaledY), value: 1 });
            });

            // Group close points to increase value and show hotter areas
            var grouped = {};
            var maxVal = 1;
            points.forEach(function(pt) {
                // grid snapping to group them
                var gx = Math.floor(pt.x / 10) * 10;
                var gy = Math.floor(pt.y / 10) * 10;
                var k = gx + ',' + gy;
                if(!grouped[k]) grouped[k] = {x: gx, y: gy, value: 0};
                grouped[k].value += 1;
                if(grouped[k].value > maxVal) maxVal = grouped[k].value;
            });

            var finalData = Object.values(grouped);
            heatmapInstance.setData({ max: maxVal, data: finalData });
        }

        // Manejador interactivo para el botón switch de pagos en BD (system_configs)
        var toggleSwitch = document.getElementById('paymentsToggleSwitch');
        var hiddenInput = document.getElementById('hiddenPaymentsEnabled');
        var switchCard = document.getElementById('paymentSwitchCard');
        var statusDot = document.getElementById('switchStatusDot');
        var statusBadge = document.getElementById('switchStatusBadge');
        var statusDesc = document.getElementById('switchStatusDesc');
        var toastMsg = document.getElementById('switchToastMessage');
        var summaryStatus = document.getElementById('summaryPaymentStatus');
        var navBadge = document.getElementById('navStripeBadge');

        if (toggleSwitch) {
            toggleSwitch.addEventListener('change', function() {
                var isEnabled = this.checked;
                var toggleContainer = this.closest('.modern-toggle');
                if (toggleContainer) toggleContainer.classList.add('is-loading');

                if (hiddenInput) {
                    hiddenInput.value = isEnabled ? '1' : '0';
                }

                fetch("{{ route('admin.payments.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        enabled: isEnabled
                    })
                })
                .then(function(res) {
                    if (!res.ok) throw new Error('Error en el servidor (' + res.status + ')');
                    return res.json();
                })
                .then(function(data) {
                    if (data.success) {
                        applySwitchUi(isEnabled);
                        displayToast(data.message, true);
                    } else {
                        throw new Error(data.message || 'No se pudo guardar la configuración');
                    }
                })
                .catch(function(err) {
                    console.error('Error al actualizar estado de cobros:', err);
                    // Revertir switch
                    toggleSwitch.checked = !isEnabled;
                    if (hiddenInput) hiddenInput.value = (!isEnabled) ? '1' : '0';
                    applySwitchUi(!isEnabled);
                    displayToast('No se pudo actualizar en BD: ' + err.message, false);
                })
                .finally(function() {
                    if (toggleContainer) toggleContainer.classList.remove('is-loading');
                });
            });
        }

        function applySwitchUi(enabled) {
            if (switchCard) {
                if (enabled) {
                    switchCard.classList.add('is-active');
                } else {
                    switchCard.classList.remove('is-active');
                }
            }
            if (statusDot) {
                statusDot.className = 'status-indicator-dot ' + (enabled ? 'active' : 'inactive');
            }
            if (statusBadge) {
                statusBadge.className = 'status-badge ' + (enabled ? 'badge-active' : 'badge-inactive');
                statusBadge.textContent = enabled ? 'ACTIVO' : 'DESACTIVADO';
            }
            if (statusDesc) {
                statusDesc.innerHTML = enabled
                    ? 'Cobros <strong style="color:var(--acid);">habilitados</strong> en base de datos. Los usuarios que agoten su prueba gratuita requerirán suscripción para ingresar.'
                    : 'Cobros <strong style="color:var(--red);">desactivados</strong> en base de datos. Acceso libre concedido a todos los usuarios sin muro de pago.';
            }
            if (summaryStatus) {
                summaryStatus.innerHTML = enabled
                    ? '<span style="color:var(--acid);font-weight:bold;">● ACTIVO (Muro de pago y suscripciones habilitadas)</span>'
                    : '<span style="color:#f87171;font-weight:bold;">○ DESACTIVADO (Acceso libre a todos los usuarios)</span>';
            }
            if (navBadge) {
                navBadge.textContent = enabled ? '08' : 'OFF';
                navBadge.style.color = enabled ? '' : '#f87171';
            }
        }

        function displayToast(msg, isSuccess) {
            if (!toastMsg) return;
            toastMsg.style.display = 'block';
            toastMsg.style.background = isSuccess ? 'rgba(0,255,156,0.1)' : 'rgba(255,77,77,0.1)';
            toastMsg.style.border = '1px solid ' + (isSuccess ? 'rgba(0,255,156,0.3)' : 'rgba(255,77,77,0.3)');
            toastMsg.style.color = isSuccess ? 'var(--acid)' : 'var(--red)';
            toastMsg.textContent = (isSuccess ? '✓ ' : '✗ ') + msg;

            setTimeout(function() {
                toastMsg.style.display = 'none';
            }, 5000);
        }
    });
  </script>
</body>
</html>