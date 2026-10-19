<?php

namespace App\Services\Detectors;

class MetaPixelDetector implements DetectorInterface
{
    public function key(): string
    {
        return 'meta_pixel';
    }

    public function name(): string
    {
        return 'Meta Pixel';
    }

    public function detect(string $html): DetectionResult
    {
        $evidence = [];
        $pixelIds = [];

        // 1. Script fbevents.js loader
        if (stripos($html, 'connect.facebook.net') !== false && stripos($html, 'fbevents.js') !== false) {
            $evidence[] = "Librería oficial de Meta Pixel (fbevents.js) cargada";
        }

        // 2. fbq('init', '1234567890')
        if (preg_match_all('/fbq\s*\(\s*[\'"]init[\'"]\s*,\s*[\'"]?([0-9]{8,20})[\'"]?/i', $html, $matches)) {
            $evidence[] = "Inicialización fbq('init') detectada con Pixel ID: " . implode(', ', array_unique($matches[1]));
            $pixelIds = array_merge($pixelIds, $matches[1]);
        }

        // 3. Noscript <img> pixel beacon (facebook.com/tr?id=XXXXXXXX)
        if (preg_match_all('/facebook\.com\/tr\?[^"\']*id=([0-9]{8,20})/i', $html, $matches)) {
            $evidence[] = "Baliza de imagen noscript detectada con Pixel ID: " . implode(', ', array_unique($matches[1]));
            $pixelIds = array_merge($pixelIds, $matches[1]);
        }

        // 4. Generic fbq call check
        if (preg_match('/fbq\s*\(\s*[\'"]track[\'"]/i', $html)) {
            $evidence[] = "Llamada de seguimiento de eventos fbq('track') presente";
        }

        $pixelIds = array_values(array_unique($pixelIds));
        $found = count($evidence) > 0 || count($pixelIds) > 0;

        return new DetectionResult(
            found: $found,
            evidence: $evidence,
            metadata: [
                'pixel_ids' => $pixelIds,
            ]
        );
    }
}
