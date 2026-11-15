<?php

/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS) Configuration
|--------------------------------------------------------------------------
|
| The API is consumed by the React admin/marketing frontend. Origins are an
| explicit allow-list; there is deliberately no implicit "*" fallback.
|
| Authentication uses an `Authorization: Bearer ...` header (not cookies), so
| `supports_credentials` stays false. If the project ever moves to cookie /
| Sanctum session auth, set it to true AND keep the allow-list explicit —
| browsers reject `Access-Control-Allow-Origin: *` together with credentials.
|
*/

$appEnv = (string) env('APP_ENV', 'production');
$allowed = (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173');

$origins = array_values(array_filter(array_map('trim', explode(',', $allowed))));

// A wildcard is only tolerated for local development/testing. In any other
// environment it fails closed (no cross-origin access) instead of silently
// exposing the API to every website on the internet.
$allowAll = in_array('*', $origins, true);
if ($allowAll && ! in_array($appEnv, ['local', 'testing'], true)) {
    $origins = [];
}

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $origins,

    // Regex patterns are intentionally empty: a loose pattern is just as
    // dangerous as a wildcard.
    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'Origin',
        'X-Requested-With',
        'X-XSRF-TOKEN',
    ],

    // Throttling metadata must be readable by the SPA (cross-origin responses
    // hide non-simple headers by default) so the login screen can show an
    // accurate 429 lockout countdown instead of guessing.
    'exposed_headers' => [
        'Retry-After',
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
    ],

    'max_age' => 3600,

    'supports_credentials' => false,

];
