<?php

namespace App\Services;

class FetchResult
{
    public function __construct(
        public readonly bool $success,
        public readonly int $statusCode,
        public readonly ?string $html,
        public readonly ?string $error,
        public readonly string $finalUrl
    ) {}
}
