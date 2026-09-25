<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemConfig;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Muestra la pantalla de aviso y cobro de suscripción (Paywall).
     */
    public function showNotice()
    {
        $user = Auth::user();

        // Si el cobro de Stripe está desactivado o el usuario ya tiene suscripción activa, ir al dashboard
        if (!SystemConfig::isStripeEnabled() || ($user && $user->hasActiveSubscription())) {
            return redirect()->route('dashboard');
        }

        // Determinar si ya tuvo un trial anteriormente que haya expirado
        $hasExpiredTrial = ($user->subscription_ends_at && $user->subscription_ends_at->isPast() && in_array($user->subscription_status, ['trialing', 'trialling']));

        $amount = SystemConfig::getValue('subscription_amount', '29.99');
        $currency = strtoupper(SystemConfig::getValue('currency', 'USD'));
        $stripePk = SystemConfig::getValue('pk', '');

        return view('subscribe', [
            'user' => $user,
            'amount' => $amount,
            'currency' => $currency,
            'stripePk' => $stripePk,
            'hasExpiredTrial' => $hasExpiredTrial,
        ]);
    }

    /**
     * Activar prueba gratuita sin tarjeta.
     */
    public function startFreeTrial(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Si ya tiene un trial expirado o una suscripción activa
        if ($user->subscription_ends_at !== null || in_array($user->subscription_status, ['active', 'trialing', 'trialling'])) {
            return redirect()->route('dashboard')->with('error', 'No eres elegible para una prueba gratuita.');
        }

        $user->subscription_status = 'trialing';
        $user->subscription_ends_at = \Carbon\Carbon::now()->addMonths(2);
        $user->save();

        return redirect()->route('dashboard')->with('success', '¡Tus 2 meses de prueba gratis se han activado exitosamente!');
    }

    /**
     * Genera la sesión de Checkout en Stripe en modo suscripción recurrente mensual.
     */
    public function createCheckoutSession(Request $request)
    {
        if (!SystemConfig::isStripeEnabled()) {
            return back()->with('error', 'El sistema de cobros con Stripe se encuentra temporalmente desactivado.');
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $stripeSk = SystemConfig::getValue('sk');
        if (empty($stripeSk)) {
            return back()->with('error', 'El sistema de cobros aún no ha sido configurado con las llaves de Stripe por el Administrador.');
        }

        \Stripe\Stripe::setApiKey($stripeSk);

        // Crear o recuperar el cliente de Stripe
        if (empty($user->stripe_customer_id)) {
            try {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                        'site_name' => $user->site_name ?? 'N/A'
                    ]
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            } catch (\Exception $e) {
                return back()->with('error', 'Error conectando con Stripe: ' . $e->getMessage());
            }
        }

        $amount = floatval(SystemConfig::getValue('subscription_amount', '29.99'));
        $currency = strtolower(SystemConfig::getValue('currency', 'usd'));

        try {
            $session = \Stripe\Checkout\Session::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Suscripción Analítica - ' . ($user->site_name ?: 'Plataforma Unlock Brands'),
                            'description' => 'Incluye 1 mes (30 días) de prueba gratuita sin cobro inmediato.',
                        ],
                        'unit_amount' => intval(round($amount * 100)), // monto en centavos
                        'recurring' => ['interval' => 'month'],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'subscription_data' => [
                    'trial_period_days' => 30,
                ],
                'success_url' => route('subscribe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscribe.cancel'),
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar la sesión de cobro en Stripe: ' . $e->getMessage());
        }
    }

    /**
     * Procesar retorno exitoso desde Stripe Checkout.
     */
    public function success(Request $request)
    {
        $user = Auth::user();
        $stripeSk = SystemConfig::getValue('sk');

        if ($user && $stripeSk && $request->has('session_id')) {
            \Stripe\Stripe::setApiKey($stripeSk);
            try {
                $session = \Stripe\Checkout\Session::retrieve($request->session_id);
                if ($session->status === 'complete' || in_array($session->payment_status, ['paid', 'no_payment_required', 'unpaid'])) {
                    $user->subscription_status = 'trialing'; // Inicia en periodo de prueba por defecto
                    if (!empty($session->subscription)) {
                        $user->stripe_subscription_id = $session->subscription;
                        try {
                            $sub = \Stripe\Subscription::retrieve($session->subscription);
                            if (!empty($sub->status)) {
                                $user->subscription_status = $sub->status;
                            }
                            if (!empty($sub->trial_end)) {
                                $user->subscription_ends_at = \Carbon\Carbon::createFromTimestamp($sub->trial_end);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error verificando suscripción en Stripe: ' . $e->getMessage());
                        }
                    }
                    $user->save();

                    return redirect()->route('dashboard')->with('success', '¡Tu mes de prueba gratis se ha activado exitosamente! Disfrutas de acceso total ilimitado sin cobros iniciales.');
                }
            } catch (\Exception $e) {
                Log::error('Error verificando sesión de Stripe: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard');
    }

    /**
     * Cancelar en el Checkout de Stripe.
     */
    public function cancel()
    {
        return redirect()->route('subscribe.notice')->with('error', 'El proceso de pago fue cancelado. Debes suscribirte para acceder al panel.');
    }

    /**
     * Portal de facturación de Stripe para clientes activos.
     */
    public function customerPortal()
    {
        if (!SystemConfig::isStripeEnabled()) {
            return back()->with('error', 'El portal de facturación se encuentra temporalmente desactivado.');
        }

        $user = Auth::user();
        $stripeSk = SystemConfig::getValue('sk');

        if (!$user || !$user->stripe_customer_id || empty($stripeSk)) {
            return back()->with('error', 'No es posible abrir el portal de facturación en este momento.');
        }

        \Stripe\Stripe::setApiKey($stripeSk);
        try {
            $portalSession = \Stripe\BillingPortal\Session::create([
                'customer' => $user->stripe_customer_id,
                'return_url' => route('dashboard'),
            ]);
            return redirect($portalSession->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al abrir el portal de Stripe: ' . $e->getMessage());
        }
    }

    /**
     * Webhook de Stripe para mantener sincronizados los estados de suscripción en tiempo real.
     */
    public function webhook(Request $request)
    {
        if (!SystemConfig::isStripeEnabled()) {
            return response()->json(['message' => 'Stripe webhook recibido pero el sistema de pagos se encuentra desactivado.'], 200);
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = SystemConfig::getValue('webhook_secret');
        $stripeSk = SystemConfig::getValue('sk');

        if (empty($stripeSk)) {
            return response()->json(['error' => 'Stripe SK no configurado'], 400);
        }

        \Stripe\Stripe::setApiKey($stripeSk);
        $event = null;

        try {
            if (!empty($endpointSecret) && !empty($sigHeader)) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                $event = json_decode($payload);
            }
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Firma inválida o error en webhook'], 400);
        }

        $eventType = is_object($event) ? ($event->type ?? null) : null;
        $dataObject = is_object($event) ? ($event->data->object ?? null) : null;

        if ($eventType === 'checkout.session.completed' && $dataObject) {
            $customerId = $dataObject->customer ?? null;
            $subscriptionId = $dataObject->subscription ?? null;
            if ($customerId) {
                $user = User::where('stripe_customer_id', $customerId)->first();
                if ($user) {
                    $user->subscription_status = 'trialing';
                    $user->stripe_subscription_id = $subscriptionId;
                    $user->save();
                }
            }
        } elseif (in_array($eventType, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted']) && $dataObject) {
            $customerId = $dataObject->customer ?? null;
            $status = $dataObject->status ?? 'inactive';
            if ($customerId) {
                $user = User::where('stripe_customer_id', $customerId)->first();
                if ($user) {
                    $user->subscription_status = in_array($status, ['active', 'trialing']) ? $status : 'inactive';
                    if (!empty($dataObject->trial_end)) {
                        $user->subscription_ends_at = \Carbon\Carbon::createFromTimestamp($dataObject->trial_end);
                    }
                    $user->save();
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
