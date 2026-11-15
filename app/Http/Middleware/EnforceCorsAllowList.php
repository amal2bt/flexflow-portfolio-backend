<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defence-in-depth wrapper around Laravel's HandleCors.
 *
 * fruitcake/php-cors has a quirk: when the allow-list contains exactly one
 * origin it emits that origin on *every* response, even when the request came
 * from a different Origin. Browsers still block the request (the value does
 * not match their origin), but strict audits expect no CORS headers at all for
 * untrusted origins. This middleware guarantees that, including preflight
 * responses (it wraps HandleCors, so it sees the early-returned OPTIONS reply).
 */
class EnforceCorsAllowList
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $origin = $request->headers->get('Origin');

        if (! $origin) {
            return $response;
        }

        $allowed = (array) config('cors.allowed_origins', []);
        $allowAll = in_array('*', $allowed, true)
            && in_array((string) config('app.env'), ['local', 'testing'], true);

        if ($allowAll || in_array($origin, $allowed, true)) {
            return $response;
        }

        // Honour explicit regex patterns if a deployment adds them.
        foreach ((array) config('cors.allowed_origins_patterns', []) as $pattern) {
            if (is_string($pattern) && @preg_match($pattern, $origin) === 1) {
                return $response;
            }
        }

        foreach ([
            'Access-Control-Allow-Origin',
            'Access-Control-Allow-Credentials',
            'Access-Control-Allow-Methods',
            'Access-Control-Allow-Headers',
            'Access-Control-Max-Age',
            'Access-Control-Expose-Headers',
        ] as $header) {
            $response->headers->remove($header);
        }

        return $response;
    }
}
