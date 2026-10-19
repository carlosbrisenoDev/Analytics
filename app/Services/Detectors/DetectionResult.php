<?php

namespace App\Services\Detectors;

class DetectionResult
{
    /**
     * @param bool $found Whether the integration was detected
     * @param string[] $evidence List of evidence strings found in the HTML
     * @param array<string, mixed> $metadata Key-value pairs of metadata (e.g. IDs, types, attributes)
     */
    public function __construct(
        public readonly bool $found,
        public readonly array $evidence = [],
        public readonly array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'found' => $this->found,
            'evidence' => $this->evidence,
            'metadata' => $this->metadata,
        ];
    }
}
