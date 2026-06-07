<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict a route to requests originating from localhost / private IP ranges.
 * Used for infrastructure endpoints like /up that should not be publicly exposed (VULN-19).
 */
class InternalOnly
{
    private const ALLOWED = [
        '127.0.0.1',
        '::1',
        '10.',
        '172.16.', '172.17.', '172.18.', '172.19.',
        '172.20.', '172.21.', '172.22.', '172.23.',
        '172.24.', '172.25.', '172.26.', '172.27.',
        '172.28.', '172.29.', '172.30.', '172.31.',
        '192.168.',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip() ?? '';

        foreach (self::ALLOWED as $prefix) {
            if (str_starts_with($ip, $prefix)) {
                return $next($request);
            }
        }

        abort(403, 'Access restricted to internal networks.');
    }
}
