<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\AnalyticsTraffic;
use App\Models\AnalyticsSeo;
use App\Models\AnalyticsPerformance;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $site = Site::firstOrCreate(
            ['domain' => 'demo.com'],
            ['name' => 'Sitio Demostración (Mixto)', 'type' => 'mixed']
        );

        $today = Carbon::today();
        
        AnalyticsTraffic::updateOrCreate(
            ['site_id' => $site->id, 'date' => $today],
            [
                'users' => 24800,
                'sessions' => 28000,
                'conversions' => 890,
                'avg_time_seconds' => 102,
                'funnel_data' => [
                    '/' => 14000,
                    '/articulos' => 8200,
                    '/contacto' => 2600
                ]
            ]
        );

        AnalyticsSeo::updateOrCreate(
            ['site_id' => $site->id, 'date' => $today],
            ['impressions' => 125000, 'clicks' => 4500, 'ctr' => 3.6, 'position' => 11.2]
        );

        AnalyticsPerformance::updateOrCreate(
            ['site_id' => $site->id, 'date' => $today],
            ['performance_score' => 94, 'core_web_vitals' => ['LCP' => '1.2s', 'FID' => '18ms', 'CLS' => '0.02']]
        );
    }
}
