<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Rutas de suscripción y pagos con Stripe
    Route::get('/subscribe', [SubscriptionController::class, 'showNotice'])->name('subscribe.notice');
    Route::post('/subscribe/free-trial', [SubscriptionController::class, 'startFreeTrial'])->name('subscribe.free_trial');
    Route::post('/subscribe/checkout', [SubscriptionController::class, 'createCheckoutSession'])->name('subscribe.checkout');
    Route::get('/subscribe/success', [SubscriptionController::class, 'success'])->name('subscribe.success');
    Route::get('/subscribe/cancel', [SubscriptionController::class, 'cancel'])->name('subscribe.cancel');
    Route::get('/subscribe/portal', [SubscriptionController::class, 'customerPortal'])->name('subscribe.portal');

    // Rutas principales del dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas para administración de usuarios (solo master)
    Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
    
    // Rutas para eliminación de eventos (editor o master)
    Route::delete('/events/site/{site_name}', [DashboardController::class, 'clearSiteEvents'])->name('events.clear');

    // Rutas para dominios CORS permitidos (solo master)
    Route::post('/domains', [DashboardController::class, 'storeDomain'])->name('domains.store');
    Route::delete('/domains/{id}', [DashboardController::class, 'destroyDomain'])->name('domains.destroy');

    // Ruta de guardado para configuración de Stripe en la BD MySQL
    Route::post('/admin/stripe-config', [DashboardController::class, 'saveStripeConfig'])->name('admin.stripe.save');
});
Route::get('/test-dashboard', function () {
    Auth::loginUsingId(1);
    $controller = app(App\Http\Controllers\DashboardController::class);
    $request = request();
    $request->merge(['site' => 'Test']);
    return $controller->index($request);
});
