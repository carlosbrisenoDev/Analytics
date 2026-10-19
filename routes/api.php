<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngestionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DashboardController;

// Ingestion Endpoints sin autenticación (rate limiting por sesión se maneja en el controlador)
Route::post('/activity', [IngestionController::class, 'track']);
Route::post('/track', [IngestionController::class, 'track']);

// Webhook para eventos de Stripe
Route::post('/webhook/stripe', [SubscriptionController::class, 'webhook']);

// Consulta de estado operativo dinámico
Route::get('/operational-status', [DashboardController::class, 'operationalStatus']);
