<?php

namespace App\Services\Detectors;

class GtmDetector implements DetectorInterface
{
    public function key(): string
    {
        return 'gtm';
    }

    public function name(): string
    {
        return 'Google Tag Manager';
    }

    public function detect(string $html): DetectionResult
    {
        $evidence = [];
        $containerIds = [];

        // 1. Script snippet loading gtm.js
        if (preg_match_all('/googletagmanager\.com\/gtm\.js\?id=(GTM-[A-Z0-9]+)/i', $html, $matches)) {
            $evidence[] = "Script gtm.js cargado con contenedor: " . implode(', ', array_unique($matches[1]));
            $containerIds = array_merge($containerIds, $matches[1]);
        }

        // 2. Noscript iframe fallback
        if (preg_match_all('/googletagmanager\.com\/ns\.html\?id=(GTM-[A-Z0-9]+)/i', $html, $matches)) {
            $evidence[] = "Iframe noscript de GTM detectado con contenedor: " . implode(', ', array_unique($matches[1]));
            $containerIds = array_merge($containerIds, $matches[1]);
        }

        // 3. Standard GTM tracking snippet pattern: (w,d,s,l,i) ... 'GTM-XXXXXX'
        if (preg_match_all('/[\'"](GTM-[A-Z0-9]{4,10})[\'"]/i', $html, $matches)) {
            $containerIds = array_merge($containerIds, $matches[1]);
        }

        $containerIds = array_values(array_unique($containerIds));
        $found = count($evidence) > 0 || count($containerIds) > 0;

        if ($found && empty($evidence) && count($containerIds) > 0) {
            $evidence[] = "Identificador de contenedor GTM detectado en el código: " . implode(', ', $containerIds);
        }

        return new DetectionResult(
            found: $found,
            evidence: $evidence,
            metadata: [
                'container_ids' => $containerIds,
            ]
        );
    }
}
