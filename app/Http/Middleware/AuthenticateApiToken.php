<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves and enforces the Bearer token issued by `User::issueToken()`.
 *
 * Tokens are stored hashed on `users.api_token`, so the incoming plain-text
 * token is SHA-256 hashed before lookup. Requests without a valid token are
 * rejected with a 401 JSON response so the admin/CRUD routes stay protected.
 *
 * Token lifetime is decided here, on the server, so it cannot be bypassed by
 * the client: every authenticated request refreshes the sliding inactivity
 * window, and a token that has been idle for too long — or that outlived its
 * absolute lifetime — is rejected with a 401 and cleared from the database.
 * A valid token populates `$request->user()`.
 */
class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token || ! ($user = User::findByApiToken($token))) {
            return response()->json(['message' => 'Unauthenticated.'], 401)
                ->header('Cache-Control', 'no-store, private');
        }

        if ($user->tokenIsExpired()) {
            // Drop the stale token so it can never be used again.
            $user->forceFill([
                'api_token' => null,
                'last_activity_at' => null,
                'token_expires_at' => null,
            ])->save();

            return response()->json(['message' => 'Session expired.'], 401)
                ->header('Cache-Control', 'no-store, private');
        }

        $this->touchActivity($user);

        Auth::setUser($user);

        $response = $next($request);

        // Authenticated payloads (users, messages, tokens metadata) must never
        // be stored by browsers or intermediary proxies.
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }

    /**
     * Write the last activity back, but only once it actually drifted, so a
     * busy client does not cause a database write on every single request.
     */
    private function touchActivity(User $user): void
    {
        $interval = (int) config('api.token.activity_touch_interval');
        $last = $user->last_activity_at;

        if ($last && $last->gt(now()->subSeconds($interval))) {
            return;
        }

        $user->forceFill(['last_activity_at' => now()])->save();
    }
}
