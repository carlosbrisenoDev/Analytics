<?php

namespace App\Services\Detectors;

class SchemaOrgDetector implements DetectorInterface
{
    public function key(): string
    {
        return 'schema_org';
    }

    public function name(): string
    {
        return 'Schema.org';
    }

    public function detect(string $html): DetectionResult
    {
        $evidence = [];
        $detectedTypes = [];
        $validJsonLdCount = 0;
        $invalidJsonLdCount = 0;

        // 1. JSON-LD scripts: <script type="application/ld+json">...</script>
        if (preg_match_all('/<script\s+[^>]*type=[\'"]application\/ld\+json[\'"][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $rawJson) {
                $rawJson = trim($rawJson);
                if (empty($rawJson)) {
                    continue;
                }

                $data = json_decode($rawJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    $validJsonLdCount++;
                    $this->extractSchemaTypes($data, $detectedTypes);
                } else {
                    $invalidJsonLdCount++;
                }
            }

            if ($validJsonLdCount > 0) {
                $evidence[] = "Se encontraron {$validJsonLdCount} bloque(s) JSON-LD válidos";
            }
            if ($invalidJsonLdCount > 0) {
                $evidence[] = "Se detectaron {$invalidJsonLdCount} bloque(s) JSON-LD con sintaxis JSON errónea";
            }
        }

        // 2. Microdata attributes: itemscope itemtype="https://schema.org/Type"
        if (preg_match_all('/itemtype=[\'"]https?:\/\/schema\.org\/([a-zA-Z0-9_]+)[\'"]/i', $html, $matches)) {
            $microdataTypes = array_unique($matches[1]);
            $detectedTypes = array_merge($detectedTypes, $microdataTypes);
            $evidence[] = "Microdata Schema.org en HTML: " . implode(', ', $microdataTypes);
        }

        $detectedTypes = array_values(array_unique(array_filter($detectedTypes)));
        $found = ($validJsonLdCount > 0) || (count($detectedTypes) > 0);

        return new DetectionResult(
            found: $found,
            evidence: $evidence,
            metadata: [
                'types' => $detectedTypes,
                'json_ld_valid_count' => $validJsonLdCount,
                'json_ld_invalid_count' => $invalidJsonLdCount,
                'has_microdata' => !empty($microdataTypes),
            ]
        );
    }

    /**
     * Recursively extract @type from JSON-LD data structures (including @graph or arrays).
     */
    protected function extractSchemaTypes(mixed $data, array &$types): void
    {
        if (!is_array($data)) {
            return;
        }

        // Check if there is an @type at the current level
        if (isset($data['@type'])) {
            if (is_string($data['@type'])) {
                $types[] = $data['@type'];
            } elseif (is_array($data['@type'])) {
                foreach ($data['@type'] as $t) {
                    if (is_string($t)) {
                        $types[] = $t;
                    }
                }
            }
        }

        // Check @graph array
        if (isset($data['@graph']) && is_array($data['@graph'])) {
            foreach ($data['@graph'] as $node) {
                $this->extractSchemaTypes($node, $types);
            }
        }

        // Recurse into nested objects/arrays
        foreach ($data as $key => $value) {
            if ($key !== '@graph' && is_array($value)) {
                $this->extractSchemaTypes($value, $types);
            }
        }
    }
}
