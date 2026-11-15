<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds conservative security headers to every API response.
 *
 * These are defence-in-depth: they do not replace output encoding, but they
 * stop sniffing, framing, referrer leakage and accidental script execution if
 * a response is ever rendered directly by a browser.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'"
        );
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
