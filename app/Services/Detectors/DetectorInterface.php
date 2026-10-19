<?php

namespace App\Services\Detectors;

interface DetectorInterface
{
    /**
     * Unique machine identifier for the detector (e.g. 'ga4', 'gtm', 'meta_pixel').
     */
    public function key(): string;

    /**
     * Human-readable label (e.g. 'Google Analytics 4').
     */
    public function name(): string;

    /**
     * Inspect HTML content and return structured detection evidence.
     */
    public function detect(string $html): DetectionResult;
}
