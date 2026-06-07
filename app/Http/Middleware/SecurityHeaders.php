<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Only send HSTS on HTTPS to avoid breaking local HTTP dev
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Restrict sources: allow Bootstrap/Icons from jsdelivr until CDN is fully removed
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; " .
            "style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; " .
            "font-src 'self' https://cdn.jsdelivr.net data:; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'none';"
        );

        return $response;
    }
}
