<?php

namespace App\Services;

use App\Models\Event;
use App\Models\AllowedDomain;
use App\Models\PageAnalysis;
use App\Services\Detectors\DetectorRegistry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IntegrationAuditService
{
    public const COVERAGE_ACTIVE_THRESHOLD = 0.75;  // >= 75% coverage -> activo
    public const COVERAGE_PARTIAL_THRESHOLD = 0.25; // >= 25% coverage -> parcial
    public const MAX_URLS_TO_INSPECT = 15;          // Sampling cap
    public const CACHE_HOURS = 24;                  // Re-inspection interval

    public function __construct(
        protected SafePageFetcher $fetcher,
        protected DetectorRegistry $detectorRegistry
    ) {}

    /**
     * Compute the full operational status for a site.
     * Cached per site to guarantee instantaneous dashboard rendering.
     */
    public function getOperationalStatus(?string $siteName): array
    {
        if (empty($siteName)) {
            return $this->getEmptyOperationalStatus();
        }

        $cacheKey = 'operational_status_v1_' . md5($siteName);

        try {
            return Cache::remember($cacheKey, now()->addHours(6), function () use ($siteName) {
                return $this->computeOperationalStatus($siteName);
            });
        } catch (\Throwable $e) {
            Log::warning("Error al calcular operational status para {$siteName}: " . $e->getMessage());
            return $this->getEmptyOperationalStatus("Auditoría técnica temporalmente no disponible.");
        }
    }

    /**
     * Compute operational status by discovering URLs, fetching HTML safely,
     * running detectors, storing evidence in DB, and aggregating results.
     */
    public function computeOperationalStatus(string $siteName): array
    {
        $baseDomain = $this->resolveBaseDomain($siteName);
        if (!$baseDomain) {
            return $this->getEmptyOperationalStatus("Dominio base no configurado para '{$siteName}'");
        }

        // 1. Extract, normalize, and sample representative URLs from daily sessions (Events)
        $representativePaths = $this->discoverRepresentativePaths($siteName);

        if (empty($representativePaths)) {
            return $this->getEmptyOperationalStatus("Sin visitas registradas para '{$siteName}'");
        }

        // 2. Inspect pages (leveraging DB cache where available)
        $inspectedAnalyses = [];
        foreach ($representativePaths as $path) {
            $analysis = $this->inspectPath($siteName, $baseDomain, $path);
            if ($analysis) {
                $inspectedAnalyses[] = $analysis;
            }
        }

        if (empty($inspectedAnalyses)) {
            return $this->getEmptyOperationalStatus("No fue posible inspeccionar las URLs descubiertas");
        }

        // 3. Aggregate detections across all inspected pages
        $aggregated = $this->aggregateDetections($inspectedAnalyses);

        // 4. Fetch PageSpeed mobile score independently
        $homepageUrl = rtrim($baseDomain, '/') . '/';
        $pageSpeedScore = $this->getPageSpeedScore($homepageUrl);

        return $this->formatOperationalStatusRows($aggregated, $pageSpeedScore, count($inspectedAnalyses));
    }

    /**
     * Discover representative paths from sessions/events.
     */
    public function discoverRepresentativePaths(string $siteName): array
    {
        $rawContents = Event::where('site_name', $siteName)
            ->where('type', 'view')
            ->whereNotNull('content')
            ->distinct()
            ->pluck('content')
            ->toArray();

        $normalized = [];
        foreach ($rawContents as $raw) {
            $clean = $this->normalizePath((string)$raw);
            if ($clean !== null) {
                $normalized[$clean] = true;
            }
        }

        $uniquePaths = array_keys($normalized);

        // Always guarantee homepage '/' is present
        if (!in_array('/', $uniquePaths, true)) {
            array_unshift($uniquePaths, '/');
        }

        // Group by path section/category for balanced sampling
        $grouped = [];
        foreach ($uniquePaths as $path) {
            $segments = explode('/', trim($path, '/'));
            $prefix = !empty($segments[0]) ? $segments[0] : 'root';
            $grouped[$prefix][] = $path;
        }

        $sampled = ['/'];
        $remainingSlots = self::MAX_URLS_TO_INSPECT - 1;

        // Round-robin selection across different prefixes
        while ($remainingSlots > 0 && !empty($grouped)) {
            $addedInRound = false;
            foreach ($grouped as $prefix => &$paths) {
                while (!empty($paths)) {
                    $candidate = array_shift($paths);
                    if (!in_array($candidate, $sampled, true)) {
                        $sampled[] = $candidate;
                        $remainingSlots--;
                        $addedInRound = true;
                        break;
                    }
                }
                if (empty($paths)) {
                    unset($grouped[$prefix]);
                }
                if ($remainingSlots <= 0) {
                    break;
                }
            }
            if (!$addedInRound) {
                break;
            }
        }

        return $sampled;
    }

    /**
     * Normalize a URL path: strip query params, fragments, lowercase, remove trailing slashes.
     */
    public function normalizePath(string $rawPath): ?string
    {
        $rawPath = trim($rawPath);
        if (empty($rawPath)) {
            return '/';
        }

        // Parse path from potential full URL
        if (preg_match('/^https?:\/\//i', $rawPath)) {
            $parsed = parse_url($rawPath, PHP_URL_PATH);
            $rawPath = $parsed ?: '/';
        }

        // Strip query string and fragments if present
        $rawPath = preg_replace('/[?#].*$/', '', $rawPath);

        // Ignore static assets
        if (preg_match('/\.(png|jpe?g|gif|svg|webp|css|js|ico|pdf|woff2?|ttf|eot)$/i', $rawPath)) {
            return null;
        }

        // Normalize slashes
        $clean = '/' . ltrim($rawPath, '/');
        if (strlen($clean) > 1) {
            $clean = rtrim($clean, '/');
        }

        return strtolower($clean);
    }

    /**
     * Inspect a single path for a given site. Reuses fresh DB records (< 24h).
     */
    protected function inspectPath(string $siteName, string $baseDomain, string $path): ?PageAnalysis
    {
        // Check existing analysis in DB
        $existing = PageAnalysis::where('site_name', $siteName)
            ->where('path', $path)
            ->first();

        $freshThreshold = now()->subHours(self::CACHE_HOURS);
        if ($existing && $existing->analyzed_at && $existing->analyzed_at->gte($freshThreshold)) {
            return $existing;
        }

        // Build full URL
        $fullUrl = rtrim($baseDomain, '/') . $path;

        // Fetch safely with SSRF protection
        $fetchResult = $this->fetcher->fetch($fullUrl);

        if ($fetchResult->success && !empty($fetchResult->html)) {
            $detections = $this->detectorRegistry->detectAll($fetchResult->html);

            $ga4 = $detections['ga4'] ?? null;
            $gtm = $detections['gtm'] ?? null;
            $metaPixel = $detections['meta_pixel'] ?? null;
            $searchConsole = $detections['search_console'] ?? null;
            $schemaOrg = $detections['schema_org'] ?? null;

            $detectionDetails = [
                'ga4' => $ga4?->toArray(),
                'gtm' => $gtm?->toArray(),
                'meta_pixel' => $metaPixel?->toArray(),
                'search_console' => $searchConsole?->toArray(),
                'schema_org' => $schemaOrg?->toArray(),
            ];

            $schemaTypes = $schemaOrg?->metadata['types'] ?? [];

            return PageAnalysis::updateOrCreate(
                ['site_name' => $siteName, 'path' => $path],
                [
                    'url' => $fetchResult->finalUrl,
                    'http_status' => $fetchResult->statusCode,
                    'has_ga4' => $ga4?->found ?? false,
                    'has_gtm' => $gtm?->found ?? false,
                    'has_meta_pixel' => $metaPixel?->found ?? false,
                    'has_search_console' => $searchConsole?->found ?? false,
                    'has_schema_org' => $schemaOrg?->found ?? false,
                    'schema_org_types' => !empty($schemaTypes) ? implode(', ', $schemaTypes) : null,
                    'detection_details' => $detectionDetails,
                    'fetch_error' => null,
                    'analyzed_at' => now(),
                ]
            );
        }

        // Record fetch error
        return PageAnalysis::updateOrCreate(
            ['site_name' => $siteName, 'path' => $path],
            [
                'url' => $fullUrl,
                'http_status' => $fetchResult->statusCode ?: null,
                'has_ga4' => false,
                'has_gtm' => false,
                'has_meta_pixel' => false,
                'has_search_console' => false,
                'has_schema_org' => false,
                'schema_org_types' => null,
                'detection_details' => null,
                'fetch_error' => $fetchResult->error ?? 'Error de conexión desconocido',
                'analyzed_at' => now(),
            ]
        );
    }

    /**
     * Aggregate detections across a collection of PageAnalysis models.
     */
    protected function aggregateDetections(array $analyses): array
    {
        $total = count($analyses);
        if ($total === 0) {
            return [];
        }

        $ga4GtmCount = 0;
        $metaPixelCount = 0;
        $searchConsoleCount = 0;
        $schemaOrgCount = 0;
        $allSchemaTypes = [];

        foreach ($analyses as $analysis) {
            // GA4 / Tag Manager combined detection
            if ($analysis->has_ga4 || $analysis->has_gtm) {
                $ga4GtmCount++;
            }

            if ($analysis->has_meta_pixel) {
                $metaPixelCount++;
            }

            if ($analysis->has_search_console) {
                $searchConsoleCount++;
            }

            if ($analysis->has_schema_org) {
                $schemaOrgCount++;
                if (!empty($analysis->schema_org_types)) {
                    $types = explode(',', $analysis->schema_org_types);
                    foreach ($types as $t) {
                        $trimmed = trim($t);
                        if (!empty($trimmed)) {
                            $allSchemaTypes[$trimmed] = true;
                        }
                    }
                }
            }
        }

        return [
            'total_pages' => $total,
            'ga4_gtm' => [
                'detected_pages' => $ga4GtmCount,
                'coverage' => round($ga4GtmCount / $total, 2),
            ],
            'meta_pixel' => [
                'detected_pages' => $metaPixelCount,
                'coverage' => round($metaPixelCount / $total, 2),
            ],
            'search_console' => [
                'detected_pages' => $searchConsoleCount,
                'coverage' => round($searchConsoleCount / $total, 2),
            ],
            'schema_org' => [
                'detected_pages' => $schemaOrgCount,
                'coverage' => round($schemaOrgCount / $total, 2),
                'types' => array_keys($allSchemaTypes),
            ],
        ];
    }

    /**
     * Map aggregated statistics into standardized UI rows.
     */
    protected function formatOperationalStatusRows(array $aggregated, ?int $pageSpeedScore, int $checkedCount): array
    {
        if (empty($aggregated)) {
            return $this->getEmptyOperationalStatus();
        }

        $total = $aggregated['total_pages'];

        // 1. GA4 / Tag Manager
        $ga4Coverage = $aggregated['ga4_gtm']['coverage'];
        $ga4Status = $this->evaluateCoverageStatus($ga4Coverage);

        // 2. Meta Pixel
        $metaCoverage = $aggregated['meta_pixel']['coverage'];
        $metaStatus = $this->evaluateCoverageStatus($metaCoverage);

        // 3. Search Console
        // Note: Meta tag verification is usually only needed on the homepage
        $scDetected = $aggregated['search_console']['detected_pages'];
        if ($scDetected > 0) {
            $scStatus = 'active'; // verificado
            $scLabel = 'verificado';
        } else {
            $scStatus = 'unverifiable'; // no detectable via HTML
            $scLabel = 'no verificado en HTML';
        }

        // 4. Schema.org
        $schemaCoverage = $aggregated['schema_org']['coverage'];
        $schemaStatus = $this->evaluateCoverageStatus($schemaCoverage);

        return [
            [
                'key' => 'ga4_gtm',
                'name' => 'GA4 / Tag Manager',
                'status' => $ga4Status,
                'status_label' => $this->getStatusLabel($ga4Status),
                'coverage' => $ga4Coverage,
                'pages_detected' => $aggregated['ga4_gtm']['detected_pages'],
                'pages_checked' => $total,
            ],
            [
                'key' => 'meta_pixel',
                'name' => 'Meta Pixel',
                'status' => $metaStatus,
                'status_label' => $this->getStatusLabel($metaStatus),
                'coverage' => $metaCoverage,
                'pages_detected' => $aggregated['meta_pixel']['detected_pages'],
                'pages_checked' => $total,
            ],
            [
                'key' => 'search_console',
                'name' => 'Search Console',
                'status' => $scStatus,
                'status_label' => $scLabel,
                'coverage' => $aggregated['search_console']['coverage'],
                'pages_detected' => $scDetected,
                'pages_checked' => $total,
            ],
            [
                'key' => 'schema_org',
                'name' => 'Schema.org',
                'status' => $schemaStatus,
                'status_label' => $this->getStatusLabel($schemaStatus),
                'coverage' => $schemaCoverage,
                'pages_detected' => $aggregated['schema_org']['detected_pages'],
                'pages_checked' => $total,
                'types' => $aggregated['schema_org']['types'] ?? [],
            ],
            [
                'key' => 'pagespeed',
                'name' => 'PageSpeed móvil',
                'status' => 'pagespeed',
                'status_label' => $pageSpeedScore !== null ? "{$pageSpeedScore}/100" : '--/100',
                'score' => $pageSpeedScore,
            ],
        ];
    }

    /**
     * Evaluate status from coverage percentage.
     */
    protected function evaluateCoverageStatus(float $coverage): string
    {
        if ($coverage >= self::COVERAGE_ACTIVE_THRESHOLD) {
            return 'active';
        }
        if ($coverage >= self::COVERAGE_PARTIAL_THRESHOLD) {
            return 'partial';
        }
        return 'inactive';
    }

    /**
     * Human readable Spanish label.
     */
    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'activo',
            'partial' => 'parcial',
            'inactive' => 'no detectado',
            'unverifiable' => 'no verificado en HTML',
            default => 'sin datos',
        };
    }

    /**
     * Resolve base URL from site_name or allowed_domains table.
     */
    public function resolveBaseDomain(string $siteName): ?string
    {
        $siteName = trim($siteName);

        if (preg_match('/^https?:\/\//i', $siteName)) {
            return rtrim($siteName, '/');
        }

        // Check AllowedDomain database table
        $domainRecord = AllowedDomain::where('domain', 'like', "%{$siteName}%")->first();
        if ($domainRecord && !empty($domainRecord->domain)) {
            $dom = trim($domainRecord->domain);
            if (!preg_match('/^https?:\/\//i', $dom)) {
                $dom = 'https://' . $dom;
            }
            return rtrim($dom, '/');
        }

        // If siteName looks like a valid hostname (e.g. 'unlockbrands.com')
        if (str_contains($siteName, '.')) {
            return 'https://' . $siteName;
        }

        return null;
    }

    /**
     * Fetch mobile PageSpeed score using the Google PageSpeed Insights API.
     */
    public function getPageSpeedScore(string $url): ?int
    {
        $cacheKey = 'pagespeed_score_' . md5($url);

        return Cache::remember($cacheKey, now()->addHours(self::CACHE_HOURS), function () use ($url) {
            try {
                $apiUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
                $response = Http::timeout(15)
                    ->get($apiUrl, [
                        'url' => $url,
                        'strategy' => 'mobile',
                        'category' => 'performance',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $scoreFraction = $data['lighthouseResult']['categories']['performance']['score'] ?? null;
                    if ($scoreFraction !== null) {
                        return (int) round($scoreFraction * 100);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("[PageSpeed API] Error fetching score for {$url}: " . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Default empty structure when no site or traffic is present.
     */
    protected function getEmptyOperationalStatus(?string $reason = null): array
    {
        return [
            ['key' => 'ga4_gtm', 'name' => 'GA4 / Tag Manager', 'status' => 'inactive', 'status_label' => 'sin datos', 'coverage' => 0, 'pages_detected' => 0, 'pages_checked' => 0],
            ['key' => 'meta_pixel', 'name' => 'Meta Pixel', 'status' => 'inactive', 'status_label' => 'sin datos', 'coverage' => 0, 'pages_detected' => 0, 'pages_checked' => 0],
            ['key' => 'search_console', 'name' => 'Search Console', 'status' => 'inactive', 'status_label' => 'sin datos', 'coverage' => 0, 'pages_detected' => 0, 'pages_checked' => 0],
            ['key' => 'schema_org', 'name' => 'Schema.org', 'status' => 'inactive', 'status_label' => 'sin datos', 'coverage' => 0, 'pages_detected' => 0, 'pages_checked' => 0],
            ['key' => 'pagespeed', 'name' => 'PageSpeed móvil', 'status' => 'pagespeed', 'status_label' => '--/100', 'score' => null],
        ];
    }
}
