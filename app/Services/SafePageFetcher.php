<?php

namespace App\Services;

class SafePageFetcher
{
    protected const MAX_REDIRECTS = 3;
    protected const TIMEOUT_SECONDS = 10;
    protected const CONNECT_TIMEOUT_SECONDS = 4;
    protected const MAX_BODY_BYTES = 2097152; // 2 MB
    protected const USER_AGENT = 'Mozilla/5.0 (compatible; UnlockBrandsBot/1.0; +https://unlockbrands.com)';

    /**
     * Safely fetch the HTML of a URL, ensuring strict protection against SSRF,
     * infinite redirects, oversized payloads, and internal network scanning.
     */
    public function fetch(string $url): FetchResult
    {
        $currentUrl = trim($url);
        $redirectsFollowed = 0;

        while ($redirectsFollowed <= self::MAX_REDIRECTS) {
            $validationError = $this->validateUrl($currentUrl);
            if ($validationError !== null) {
                return new FetchResult(
                    success: false,
                    statusCode: 0,
                    html: null,
                    error: "URL de destino no permitida por seguridad: {$validationError}",
                    finalUrl: $currentUrl
                );
            }

            $curlResult = $this->executeCurl($currentUrl);

            if (!$curlResult['success']) {
                return new FetchResult(
                    success: false,
                    statusCode: $curlResult['status_code'],
                    html: null,
                    error: $curlResult['error'],
                    finalUrl: $currentUrl
                );
            }

            $statusCode = $curlResult['status_code'];

            // Handle HTTP redirects manually to validate each hop against SSRF
            if (in_array($statusCode, [301, 302, 303, 307, 308], true)) {
                $redirectUrl = $curlResult['redirect_url'];
                if (empty($redirectUrl)) {
                    return new FetchResult(
                        success: false,
                        statusCode: $statusCode,
                        html: null,
                        error: "Redirección HTTP {$statusCode} sin cabecera Location válida",
                        finalUrl: $currentUrl
                    );
                }

                // Resolve relative redirect URL if needed
                $currentUrl = $this->resolveRedirectUrl($currentUrl, $redirectUrl);
                $redirectsFollowed++;

                if ($redirectsFollowed > self::MAX_REDIRECTS) {
                    return new FetchResult(
                        success: false,
                        statusCode: $statusCode,
                        html: null,
                        error: "Límite de redirecciones excedido (máximo " . self::MAX_REDIRECTS . ")",
                        finalUrl: $currentUrl
                    );
                }

                continue;
            }

            // Normal response (2xx, 4xx, 5xx)
            if ($statusCode >= 200 && $statusCode < 300) {
                return new FetchResult(
                    success: true,
                    statusCode: $statusCode,
                    html: $curlResult['body'],
                    error: null,
                    finalUrl: $currentUrl
                );
            }

            return new FetchResult(
                success: false,
                statusCode: $statusCode,
                html: $curlResult['body'] ?? null,
                error: "Respuesta HTTP {$statusCode} del servidor remoto",
                finalUrl: $currentUrl
            );
        }

        return new FetchResult(
            success: false,
            statusCode: 0,
            html: null,
            error: "Excedido el número máximo de redirecciones",
            finalUrl: $currentUrl
        );
    }

    /**
     * Validate URL format, scheme, ports, hostname, and resolved IP addresses.
     * Returns null if valid, or an error string if blocked.
     */
    public function validateUrl(string $url): ?string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return "Sintaxis de URL no válida";
        }

        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return "No se pudo extraer el host de la URL";
        }

        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return "Protocolo no permitido (solo http/https)";
        }

        // Port restriction: only standard web ports allowed
        if (isset($parsed['port']) && !in_array((int)$parsed['port'], [80, 443, 8080, 8443], true)) {
            return "Puerto no permitido ({$parsed['port']})";
        }

        $host = strtolower(trim($parsed['host']));

        // Block known dangerous hostnames
        if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true)
            || str_ends_with($host, '.localhost')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.internal')
            || str_ends_with($host, '.corp')
        ) {
            return "Host local o reservado no permitido ({$host})";
        }

        // Resolve DNS and inspect all IP records
        $ips = [];
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips[] = $host;
        } else {
            $records = @dns_get_record($host, DNS_A + DNS_AAAA);
            if (!empty($records) && is_array($records)) {
                foreach ($records as $record) {
                    if (isset($record['ip'])) {
                        $ips[] = $record['ip'];
                    } elseif (isset($record['ipv6'])) {
                        $ips[] = $record['ipv6'];
                    }
                }
            }

            // Fallback resolver
            if (empty($ips)) {
                $fallback = @gethostbynamel($host);
                if (is_array($fallback)) {
                    $ips = array_merge($ips, $fallback);
                }
            }
        }

        if (empty($ips)) {
            return "No se pudo resolver la dirección IP para el host '{$host}'";
        }

        // Verify that NO resolved IP belongs to private/reserved ranges
        foreach ($ips as $ip) {
            if (!$this->isPublicIp($ip)) {
                return "La IP resuelta '{$ip}' pertenece a una red privada o reservada";
            }
        }

        return null;
    }

    /**
     * Check if an IP address is a safe public IP (blocks private, loopback, link-local, cloud metadata).
     */
    protected function isPublicIp(string $ip): bool
    {
        // 1. PHP Native validation for private and reserved ranges
        $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        if (!filter_var($ip, FILTER_VALIDATE_IP, $flags)) {
            return false;
        }

        // 2. Extra explicit checks for cloud metadata and link-local (169.254.0.0/16)
        if (str_starts_with($ip, '169.254.')) {
            return false;
        }

        // 3. Extra check for 0.0.0.0/8
        if (str_starts_with($ip, '0.')) {
            return false;
        }

        // 4. Extra check for Carrier-Grade NAT (100.64.0.0/10: 100.64.0.0 - 100.127.255.255)
        if (preg_match('/^100\.(6[4-9]|[7-9][0-9]|1[01][0-9]|12[0-7])\./', $ip)) {
            return false;
        }

        // 5. IPv6 special checks
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $lower = strtolower($ip);
            if ($lower === '::1' || str_starts_with($lower, 'fc') || str_starts_with($lower, 'fd') || str_starts_with($lower, 'fe80')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute a cURL request with manual size limits and timeout.
     */
    protected function executeCurl(string $url): array
    {
        $ch = curl_init();
        $body = '';
        $bodyLength = 0;
        $tooLarge = false;

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => false, // We control redirects manually
            CURLOPT_TIMEOUT => self::TIMEOUT_SECONDS,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT_SECONDS,
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: es-ES,es;q=0.9,en;q=0.8',
                'Cache-Control: no-cache',
            ],
            // Stream write function to enforce max 2MB size cap
            CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$body, &$bodyLength, &$tooLarge) {
                $len = strlen($data);
                if ($bodyLength + $len > self::MAX_BODY_BYTES) {
                    $tooLarge = true;
                    return 0; // Abort download
                }
                $body .= $data;
                $bodyLength += $len;
                return $len;
            },
            CURLOPT_HEADER => true,
        ]);

        $fullResponse = '';
        // Capture headers as well
        $headerContent = '';
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$headerContent) {
            $headerContent .= $header;
            return strlen($header);
        });

        $execResult = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        if ($tooLarge) {
            return [
                'success' => false,
                'status_code' => $statusCode,
                'body' => null,
                'redirect_url' => null,
                'error' => 'El tamaño de la página excede el límite máximo permitido (2 MB)',
            ];
        }

        if ($curlErrno !== 0) {
            return [
                'success' => false,
                'status_code' => $statusCode,
                'body' => null,
                'redirect_url' => null,
                'error' => "Error de red cURL ({$curlErrno}): {$curlError}",
            ];
        }

        // Extract redirect Location header if any
        $redirectUrl = null;
        if (in_array($statusCode, [301, 302, 303, 307, 308], true)) {
            if (preg_match('/^location:\s*(.+)$/im', $headerContent, $matches)) {
                $redirectUrl = trim($matches[1]);
            }
        }

        return [
            'success' => true,
            'status_code' => $statusCode,
            'body' => $body,
            'redirect_url' => $redirectUrl,
            'error' => null,
        ];
    }

    /**
     * Resolve a redirect URL (handles relative paths vs absolute URLs).
     */
    protected function resolveRedirectUrl(string $baseUrl, string $location): string
    {
        if (preg_match('/^https?:\/\//i', $location)) {
            return $location;
        }

        $baseParsed = parse_url($baseUrl);
        $scheme = $baseParsed['scheme'] ?? 'https';
        $host = $baseParsed['host'] ?? '';
        $port = isset($baseParsed['port']) ? ':' . $baseParsed['port'] : '';

        if (str_starts_with($location, '//')) {
            return "{$scheme}:{$location}";
        }

        if (str_starts_with($location, '/')) {
            return "{$scheme}://{$host}{$port}{$location}";
        }

        $path = $baseParsed['path'] ?? '/';
        $dir = rtrim(dirname($path), '/');
        return "{$scheme}://{$host}{$port}{$dir}/{$location}";
    }
}
