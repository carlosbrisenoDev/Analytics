<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AnalyticsTraffic;
use App\Models\AnalyticsSeo;
use App\Models\AnalyticsPerformance;
use App\Models\AnalyticsMeta;

class IngestionController extends Controller
{
    public function storeTraffic(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'users' => 'nullable|integer',
            'sessions' => 'nullable|integer',
            'conversions' => 'nullable|integer',
            'avg_time_seconds' => 'nullable|integer',
            'funnel_data' => 'nullable|array',
        ]);
        
        $data['site_id'] = $request->site->id;
        
        $record = AnalyticsTraffic::updateOrCreate(
            ['site_id' => $data['site_id'], 'date' => $data['date']],
            $data
        );

        return response()->json(['message' => 'Traffic data stored', 'data' => $record]);
    }

    public function storeSeo(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'impressions' => 'nullable|integer',
            'clicks' => 'nullable|integer',
            'ctr' => 'nullable|numeric',
            'position' => 'nullable|numeric',
        ]);
        
        $data['site_id'] = $request->site->id;
        
        $record = AnalyticsSeo::updateOrCreate(
            ['site_id' => $data['site_id'], 'date' => $data['date']],
            $data
        );

        return response()->json(['message' => 'SEO data stored', 'data' => $record]);
    }

    public function storePerformance(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'performance_score' => 'nullable|integer',
            'core_web_vitals' => 'nullable|array',
        ]);
        
        $data['site_id'] = $request->site->id;
        
        $record = AnalyticsPerformance::updateOrCreate(
            ['site_id' => $data['site_id'], 'date' => $data['date']],
            $data
        );

        return response()->json(['message' => 'Performance data stored', 'data' => $record]);
    }
    
    public function storeMeta(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'events' => 'nullable|integer',
            'conversions' => 'nullable|integer',
        ]);
        
        $data['site_id'] = $request->site->id;
        
        $record = AnalyticsMeta::updateOrCreate(
            ['site_id' => $data['site_id'], 'date' => $data['date']],
            $data
        );

        return response()->json(['message' => 'Meta data stored', 'data' => $record]);
    }

    public function track(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|in:pageview,time,conversion',
            'value' => 'nullable|numeric',
            'url' => 'nullable|string',
        ]);

        $today = date('Y-m-d');
        $siteId = $request->site->id;

        $record = AnalyticsTraffic::firstOrCreate(
            ['site_id' => $siteId, 'date' => $today],
            ['users' => 0, 'sessions' => 0, 'conversions' => 0, 'avg_time_seconds' => 0]
        );

        if ($data['type'] === 'pageview') {
            $record->increment('sessions', 1);
            $record->increment('users', 1); // Simplification for now

            if (!empty($data['url'])) {
                // Record specific page hits for funnel and friction tracking
                $funnel = $record->funnel_data ?? [];
                $url = $data['url'];
                $funnel[$url] = ($funnel[$url] ?? 0) + 1;
                
                // Have to manually save the array cast back
                $record->funnel_data = $funnel;
                $record->save();
            }
        } elseif ($data['type'] === 'conversion') {
            $record->increment('conversions', 1);
        } elseif ($data['type'] === 'time') {
            // Keep a running total of time, we can approximate average later
            $record->increment('avg_time_seconds', $data['value'] ?? 0); 
        }

        return response()->json(['message' => 'Tracked successfully', 'type' => $data['type']]);
    }
}
