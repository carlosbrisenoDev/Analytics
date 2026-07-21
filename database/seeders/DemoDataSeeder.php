<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Site;
use App\Models\AnalyticsTraffic;
use App\Models\AnalyticsSeo;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $site = Site::firstOrCreate(
            ['domain' => 'demo.com'],
            ['name' => 'Demo Site', 'api_key' => 'test_api_key_123']
        );

        $today = Carbon::today();
        
        AnalyticsTraffic::updateOrCreate(
            ['site_id' => $site->id, 'date' => $today],
            ['users' => 24800, 'sessions' => 28000, 'conversions' => 890, 'avg_time_seconds' => 102]
        );

        AnalyticsSeo::updateOrCreate(
            ['site_id' => $site->id, 'date' => $today],
            ['impressions' => 125000, 'clicks' => 4500, 'ctr' => 3.6, 'position' => 11.2]
        );
    }
}
