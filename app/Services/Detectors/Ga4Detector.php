<?php

namespace App\Services\Detectors;

class Ga4Detector implements DetectorInterface
{
    public function key(): string
    {
        return 'ga4';
    }

    public function name(): string
    {
        return 'Google Analytics 4';
    }

    public function detect(string $html): DetectionResult
    {
        $evidence = [];
        $measurementIds = [];

        // 1. Script tag loading gtag.js with a G- measurement ID
        if (preg_match_all('/googletagmanager\.com\/gtag\/js\?id=(G-[A-Z0-9]+)/i', $html, $matches)) {
            $evidence[] = "Script gtag.js cargado con ID: " . implode(', ', array_unique($matches[1]));
            $measurementIds = array_merge($measurementIds, $matches[1]);
        }

        // 2. gtag('config', 'G-XXXXXXXX') call
        if (preg_match_all('/gtag\s*\(\s*[\'"]config[\'"]\s*,\s*[\'"](G-[A-Z0-9]+)[\'"]/i', $html, $matches)) {
            $evidence[] = "Llamada gtag('config') detectada con ID: " . implode(', ', array_unique($matches[1]));
            $measurementIds = array_merge($measurementIds, $matches[1]);
        }

        // 3. Fallback: Google Analytics inline gtag initialization + dataLayer
        if (stripos($html, 'window.dataLayer') !== false && stripos($html, 'function gtag()') !== false) {
            $evidence[] = "Inicialización de dataLayer y helper function gtag()";
        }

        // 4. Any direct G- measurement ID inside <script> blocks
        if (preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $html, $scriptMatches)) {
            foreach ($scriptMatches[1] as $scriptBody) {
                if (preg_match_all('/[\'"](G-[A-Z0-9]{5,15})[\'"]/', $scriptBody, $gMatches)) {
                    $measurementIds = array_merge($measurementIds, $gMatches[1]);
                }
            }
        }

        $measurementIds = array_values(array_unique($measurementIds));
        $found = count($evidence) > 0 || count($measurementIds) > 0;

        return new DetectionResult(
            found: $found,
            evidence: $evidence,
            metadata: [
                'measurement_ids' => $measurementIds,
            ]
        );
    }
}
