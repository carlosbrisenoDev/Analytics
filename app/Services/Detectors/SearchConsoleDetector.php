<?php

namespace App\Services\Detectors;

class SearchConsoleDetector implements DetectorInterface
{
    public function key(): string
    {
        return 'search_console';
    }

    public function name(): string
    {
        return 'Google Search Console';
    }

    public function detect(string $html): DetectionResult
    {
        $evidence = [];
        $tokens = [];

        // 1. Meta tag with name="google-site-verification" content="..."
        if (preg_match_all('/<meta\s+[^>]*name=[\'"]google-site-verification[\'"][^>]*content=[\'"]([^\'"]+)[\'"]/i', $html, $matches)) {
            $tokens = array_merge($tokens, $matches[1]);
        }

        // 2. Meta tag with content="..." name="google-site-verification" (reversed attributes)
        if (preg_match_all('/<meta\s+[^>]*content=[\'"]([^\'"]+)[\'"][^>]*name=[\'"]google-site-verification[\'"]/i', $html, $matches)) {
            $tokens = array_merge($tokens, $matches[1]);
        }

        $tokens = array_values(array_unique($tokens));
        $found = count($tokens) > 0;

        if ($found) {
            foreach ($tokens as $token) {
                $evidence[] = "Metaetiqueta de verificación HTML detectada (token: " . substr($token, 0, 10) . "...)";
            }
        }

        return new DetectionResult(
            found: $found,
            evidence: $evidence,
            metadata: [
                'verification_tokens' => $tokens,
                'method' => $found ? 'meta_tag' : 'none_in_html',
                'note' => $found 
                    ? 'Verificado en HTML mediante metaetiqueta google-site-verification' 
                    : 'No se detectó metaetiqueta en HTML (puede estar verificado mediante registro DNS TXT o vinculación GA4)',
            ]
        );
    }
}
