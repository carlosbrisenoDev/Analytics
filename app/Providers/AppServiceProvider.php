<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\AllowedDomain;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('allowed_domains')) {
                $domains = Cache::rememberForever('allowed_domains', function () {
                    return AllowedDomain::pluck('domain')->toArray();
                });

                if (!empty($domains)) {
                    config(['cors.allowed_origins' => $domains]);
                }
            }
        } catch (\Exception $e) {
            // Base de datos no lista, ignorar
        }
    }
}
