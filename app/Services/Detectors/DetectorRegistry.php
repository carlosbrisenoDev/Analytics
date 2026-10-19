<?php

namespace App\Services\Detectors;

class DetectorRegistry
{
    /**
     * @var DetectorInterface[]
     */
    protected array $detectors = [];

    public function __construct()
    {
        $this->registerDefaultDetectors();
    }

    /**
     * Register default detectors.
     */
    protected function registerDefaultDetectors(): void
    {
        $this->register(new Ga4Detector());
        $this->register(new GtmDetector());
        $this->register(new MetaPixelDetector());
        $this->register(new SearchConsoleDetector());
        $this->register(new SchemaOrgDetector());
    }

    /**
     * Register an individual detector.
     */
    public function register(DetectorInterface $detector): self
    {
        $this->detectors[$detector->key()] = $detector;
        return $this;
    }

    /**
     * Get all registered detectors.
     *
     * @return DetectorInterface[]
     */
    public function all(): array
    {
        return $this->detectors;
    }

    /**
     * Execute all detectors on the provided HTML and return a keyed array of DetectionResult.
     *
     * @return array<string, DetectionResult>
     */
    public function detectAll(string $html): array
    {
        $results = [];
        foreach ($this->detectors as $key => $detector) {
            $results[$key] = $detector->detect($html);
        }
        return $results;
    }
}
