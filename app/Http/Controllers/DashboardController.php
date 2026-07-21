<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AnalyticsTraffic;
use App\Models\AnalyticsSeo;
use App\Models\AnalyticsPerformance;
use App\Models\AnalyticsMeta;

class DashboardController extends Controller
{
    public function overview(Request $request)
    {
        $siteId = $request->site->id;
        
        $today = \Carbon\Carbon::today();
        $last7 = $today->copy()->subDays(7);
        $prev7 = $today->copy()->subDays(14);
        
        // Fetch raw data from Express Backend
        $viewsRes = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:3000/api/v1/analytics/logs?type=view');
        $timeRes = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:3000/api/v1/analytics/logs?type=time');
        $convRes = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:3000/api/v1/analytics/logs?type=conversion');
        $navRes = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:3000/api/v1/analytics/logs?type=navigation');
        
        $viewsData = $viewsRes->successful() ? $viewsRes->json()['data'] ?? [] : [];
        $timeData = $timeRes->successful() ? $timeRes->json()['data'] ?? [] : [];
        $convData = $convRes->successful() ? $convRes->json()['data'] ?? [] : [];
        $navData = $navRes->successful() ? $navRes->json()['data'] ?? [] : [];

        $currentTraffic = 0;
        $prevTraffic = 0;
        $aggregatedFunnel = [];

        foreach ($viewsData as $view) {
            $date = \Carbon\Carbon::parse($view['timestamp']);
            if ($date >= $last7) {
                $currentTraffic++;
                $url = $view['url'];
                $aggregatedFunnel[$url] = ($aggregatedFunnel[$url] ?? 0) + 1;
            } elseif ($date >= $prev7 && $date < $last7) {
                $prevTraffic++;
            }
        }

        // Conversions
        $currentConversions = 0;
        $prevConversions = 0;
        foreach ($convData as $conv) {
            $date = \Carbon\Carbon::parse($conv['timestamp']);
            if ($date >= $last7) $currentConversions++;
            elseif ($date >= $prev7 && $date < $last7) $prevConversions++;
        }

        // Time / Retention
        $currentTimeSum = 0; $currentCount = 0;
        $prevTimeSum = 0; $prevCount = 0;
        foreach ($timeData as $t) {
            $date = \Carbon\Carbon::parse($t['timestamp']);
            $spent = $t['metadata']['timeSpent'] ?? 0;
            if ($date >= $last7) { $currentTimeSum += $spent; $currentCount++; }
            elseif ($date >= $prev7 && $date < $last7) { $prevTimeSum += $spent; $prevCount++; }
        }
        $currentAvgTime = $currentCount > 0 ? round($currentTimeSum / $currentCount) : 0;
        $prevAvgTime = $prevCount > 0 ? round($prevTimeSum / $prevCount) : 0;
        
        // Friction Logic
        // Calculate exit rates or drop-offs based on navigation
        $pagesFrom = [];
        $pagesTo = [];
        foreach ($navData as $nav) {
            $date = \Carbon\Carbon::parse($nav['timestamp']);
            if ($date >= $last7) {
                $from = $nav['metadata']['from'] ?? 'unknown';
                $to = $nav['metadata']['to'] ?? 'unknown';
                $pagesFrom[$from] = ($pagesFrom[$from] ?? 0) + 1;
                $pagesTo[$to] = ($pagesTo[$to] ?? 0) + 1;
            }
        }
        
        $frictionAlerts = [];
        foreach ($pagesFrom as $page => $departures) {
            $arrivals = $pagesTo[$page] ?? 0;
            // If many arrived, but fewer departed (they just closed the tab/dropped off)
            if ($arrivals > 0) {
                $dropOffRate = round((($arrivals - $departures) / $arrivals) * 100);
                if ($dropOffRate > 70 && $arrivals > 5) {
                    $frictionAlerts[] = [
                        'type' => 'danger',
                        'message' => "Abandono superior al {$dropOffRate}% en $page"
                    ];
                } elseif ($dropOffRate > 50 && $arrivals > 5) {
                    $frictionAlerts[] = [
                        'type' => 'warning',
                        'message' => "Fricción moderada en $page ({$dropOffRate}% abandonan)"
                    ];
                }
            }
        }
        if (empty($frictionAlerts)) {
            $frictionAlerts[] = ['type' => 'success', 'message' => 'El flujo de usuarios es estable. No se detectan bloqueos graves.'];
        }

        // Helper to calc trend percentage
        $calcTrend = function($current, $prev) {
            if ($prev == 0) {
                return $current > 0 ? 100 : 0;
            }
            return round((($current - $prev) / $prev) * 100, 1);
        };

        $trafficTrend = $calcTrend($currentTraffic, $prevTraffic);
        $conversionTrend = $calcTrend($currentConversions, $prevConversions);
        $timeTrend = $calcTrend($currentAvgTime, $prevAvgTime);

        // Simple Health Score (0-100)
        $healthScore = 50; 
        if ($currentTraffic > 0) $healthScore += 20;
        if ($trafficTrend > 0) $healthScore += 10;
        if ($conversionTrend > 0) $healthScore += 10;
        if ($currentConversions > 0) $healthScore += 10;
        
        // Sort pages by most visited
        arsort($aggregatedFunnel);
        $topPages = array_slice($aggregatedFunnel, 0, 5, true);
        
        // Total visits for funnel percentages
        $totalVisits = $currentTraffic ?: 1;
        
        return response()->json([
            'site' => $request->site->name,
            'week' => \Carbon\Carbon::now()->weekOfYear,
            'health_score' => min(100, $healthScore),
            'traffic_total' => $currentTraffic,
            'traffic_trend' => ($trafficTrend > 0 ? '+' : '') . $trafficTrend . '%',
            'conversion_total' => $currentConversions,
            'conversion_trend' => ($conversionTrend > 0 ? '+' : '') . $conversionTrend . '%',
            'avg_time' => $currentAvgTime . 's',
            'time_trend' => ($timeTrend > 0 ? '+' : '') . $timeTrend . '%',
            'pages' => $topPages,
            'total_visits' => $totalVisits,
            'friction_alerts' => $frictionAlerts
        ]);
    }

    public function traffic(Request $request)
    {
        $siteId = $request->site->id;
        $data = AnalyticsTraffic::where('site_id', $siteId)->orderBy('date', 'desc')->take(7)->get();
        return response()->json(['data' => $data]);
    }

    public function seo(Request $request)
    {
        $siteId = $request->site->id;
        $data = AnalyticsSeo::where('site_id', $siteId)->orderBy('date', 'desc')->take(7)->get();
        return response()->json(['data' => $data]);
    }

    public function performance(Request $request)
    {
        $siteId = $request->site->id;
        $data = AnalyticsPerformance::where('site_id', $siteId)->orderBy('date', 'desc')->take(7)->get();
        return response()->json(['data' => $data]);
    }

    public function aiInsights(Request $request)
    {
        // For the "Próximamente" banner test, we intentionally return a 501 Not Implemented
        return response()->json(['error' => 'AI insights not ready yet', 'status' => 'coming_soon'], 501);
    }
}
