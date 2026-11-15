<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Token Lifetime
    |--------------------------------------------------------------------------
    |
    | The API is stateless: admins authenticate with a Bearer token that is
    | stored hashed on `users.api_token`. These settings decide how long such
    | a token stays valid. They are enforced server-side on every request by
    | the `auth.apikey` middleware, so a stale or leaked token is rejected by
    | Laravel no matter what the client does.
    |
    | - lifetime: idle window in minutes. A token that has not been used for
    |   this long is rejected with a 401 and cleared from the database.
    | - absolute_lifetime: hard cap in minutes on the total age of a token,
    |   even while it is in use. Set to 0 to disable the cap.
    | - activity_touch_interval: how often (in seconds) the last activity is
    |   written back, so a busy client does not cause a write per request.
    |
    */

    'token' => [
        'lifetime' => (int) env('API_TOKEN_LIFETIME', 120),
        'absolute_lifetime' => (int) env('API_TOKEN_ABSOLUTE_LIFETIME', 720),
        'activity_touch_interval' => (int) env('API_TOKEN_ACTIVITY_TOUCH_INTERVAL', 60),
    ],

];
