<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\SafePageFetcher;
use App\Services\Detectors\DetectorRegistry;
use App\Services\Detectors\Ga4Detector;
use App\Services\Detectors\GtmDetector;
use App\Services\Detectors\MetaPixelDetector;
use App\Services\Detectors\SearchConsoleDetector;
use App\Services\Detectors\SchemaOrgDetector;
use App\Services\IntegrationAuditService;

class IntegrationAuditTest extends TestCase
{
    protected SafePageFetcher $fetcher;
    protected DetectorRegistry $registry;
    protected IntegrationAuditService $auditService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fetcher = new SafePageFetcher();
        $this->registry = new DetectorRegistry();
        $this->auditService = new IntegrationAuditService($this->fetcher, $this->registry);
    }

    public function test_ssrf_validator_blocks_private_and_local_addresses(): void
    {
        $blocked = [
            'http://localhost',
            'http://localhost:8080',
            'http://127.0.0.1',
            'http://127.0.0.1:3306',
            'http://169.254.169.254/latest/meta-data',
            'http://192.168.1.1',
            'http://10.0.0.1',
            'http://172.16.0.1',
            'ftp://example.com',
            'file:///etc/passwd',
        ];

        foreach ($blocked as $url) {
            $error = $this->fetcher->validateUrl($url);
            $this->assertNotNull($error, "Expected {$url} to be blocked by SSRF validator");
        }
    }

    public function test_url_path_normalization(): void
    {
        $this->assertEquals('/', $this->auditService->normalizePath('/'));
        $this->assertEquals('/search', $this->auditService->normalizePath('/search?q=iphone&page=2'));
        $this->assertEquals('/producto/iphone-17', $this->auditService->normalizePath('/producto/iphone-17/'));
        $this->assertEquals('/producto/iphone-17', $this->auditService->normalizePath('/PRODUCTO/IPHONE-17'));
        $this->assertEquals('/blog/noticia-1', $this->auditService->normalizePath('https://example.com/blog/noticia-1#comments'));
        $this->assertNull($this->auditService->normalizePath('/assets/logo.png'));
        $this->assertNull($this->auditService->normalizePath('/bundle.min.js'));
        $this->assertNull($this->auditService->normalizePath('/app.css'));
    }

    public function test_ga4_detector(): void
    {
        $detector = new Ga4Detector();

        $htmlWithGtag = '<html><head><script async src="https://www.googletagmanager.com/gtag/js?id=G-ABC12345"></script><script>gtag("config", "G-ABC12345");</script></head></html>';
        $result = $detector->detect($htmlWithGtag);
        $this->assertTrue($result->found);
        $this->assertContains('G-ABC12345', $result->metadata['measurement_ids']);

        $cleanHtml = '<html><body><h1>Clean</h1></body></html>';
        $this->assertFalse($detector->detect($cleanHtml)->found);
    }

    public function test_gtm_detector(): void
    {
        $detector = new GtmDetector();

        $htmlWithGtm = '<html><head><script>(function(w,d,s,l,i){})(window,document,"script","dataLayer","GTM-ZX98765");</script></head><body><noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-ZX98765"></iframe></noscript></body></html>';
        $result = $detector->detect($htmlWithGtm);
        $this->assertTrue($result->found);
        $this->assertContains('GTM-ZX98765', $result->metadata['container_ids']);

        $cleanHtml = '<html><body><h1>Clean</h1></body></html>';
        $this->assertFalse($detector->detect($cleanHtml)->found);
    }

    public function test_meta_pixel_detector(): void
    {
        $detector = new MetaPixelDetector();

        $htmlWithPixel = '<html><head><script>!function(f,b,e,v,n,t,s){}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");fbq("init", "123456789012");fbq("track", "PageView");</script></head><body><noscript><img src="https://www.facebook.com/tr?id=123456789012&ev=PageView" /></noscript></body></html>';
        $result = $detector->detect($htmlWithPixel);
        $this->assertTrue($result->found);
        $this->assertContains('123456789012', $result->metadata['pixel_ids']);

        $cleanHtml = '<html><body><h1>Clean</h1></body></html>';
        $this->assertFalse($detector->detect($cleanHtml)->found);
    }

    public function test_search_console_detector(): void
    {
        $detector = new SearchConsoleDetector();

        $htmlWithVerification = '<html><head><meta name="google-site-verification" content="abcdef1234567890" /></head></html>';
        $result = $detector->detect($htmlWithVerification);
        $this->assertTrue($result->found);
        $this->assertContains('abcdef1234567890', $result->metadata['verification_tokens']);

        $cleanHtml = '<html><body><h1>Clean</h1></body></html>';
        $this->assertFalse($detector->detect($cleanHtml)->found);
    }

    public function test_schema_org_detector(): void
    {
        $detector = new SchemaOrgDetector();

        $htmlWithJsonLd = '<html><head><script type="application/ld+json">{"@context": "https://schema.org", "@type": "Product", "name": "iPhone 17"}</script></head></html>';
        $result = $detector->detect($htmlWithJsonLd);
        $this->assertTrue($result->found);
        $this->assertContains('Product', $result->metadata['types']);
        $this->assertEquals(1, $result->metadata['json_ld_valid_count']);

        $cleanHtml = '<html><body><h1>Clean</h1></body></html>';
        $this->assertFalse($detector->detect($cleanHtml)->found);
    }
}
