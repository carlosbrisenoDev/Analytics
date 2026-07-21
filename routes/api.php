<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IngestionController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\ApiKeyAuth;

Route::middleware([ApiKeyAuth::class])->prefix('v1/analytics')->group(function () {
    
    // Ingestion Endpoints
    Route::post('/track', [IngestionController::class, 'track']);
    Route::post('/traffic', [IngestionController::class, 'storeTraffic']);
    Route::post('/seo', [IngestionController::class, 'storeSeo']);
    Route::post('/performance', [IngestionController::class, 'storePerformance']);
    Route::post('/meta', [IngestionController::class, 'storeMeta']);

    // Dashboard Endpoints
    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', [DashboardController::class, 'overview']);
        Route::get('/traffic', [DashboardController::class, 'traffic']);
        Route::get('/seo', [DashboardController::class, 'seo']);
        Route::get('/performance', [DashboardController::class, 'performance']);
        Route::get('/ai-insights', [DashboardController::class, 'aiInsights']);
    });
});
