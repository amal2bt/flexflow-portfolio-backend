<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * A fixed bcrypt hash used when the email does not exist. Checking against
     * it keeps the response time close to a real failed login, so an attacker
     * cannot use timing to enumerate valid accounts.
     */
    private const DUMMY_HASH = '$2y$12$1rWya.588pi.n2EAGIYacOdzX5bQmI1RY4aikkcz.BsuJfG02yiEO';

    /**
     * Failed attempts allowed per account+IP and per IP before lockout, plus
     * the strict lockout duration. The failure window and the lockout both use
     * a 10-minute decay, and the timer is reset when the lock triggers, so
     * `Retry-After` is always exactly LOCKOUT_SECONDS (not the remainder of a
     * partially elapsed window).
     */
    private const MAX_ATTEMPTS_PER_ACCOUNT = 5;

    private const MAX_ATTEMPTS_PER_IP = 20;

    private const LOCKOUT_SECONDS = 600;

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email:rfc|max:255',
            'password' => 'required|string|max:255',
        ]);

        // The database collation is case-insensitive; normalise here as well
        // so the throttling keys and the lookup always refer to the same value.
        $email = Str::lower(trim($credentials['email']));
        $ip = (string) $request->ip();

        // Keys are hashed so an email address never ends up in a cache key.
        $accountKey = 'login:'.sha1($email.'|'.$ip);
        $ipKey = 'login-ip:'.sha1($ip);
        $accountLockKey = 'login-lock:'.sha1($email.'|'.$ip);
        $ipLockKey = 'login-ip-lock:'.sha1($ip);

        // An active lockout is checked before hashing so blocked requests are
        // rejected cheaply and the response is deterministic.
        if ($this->isLockedOut($accountLockKey) || $this->isLockedOut($ipLockKey)) {
            return $this->lockoutResponse();
        }

        $user = User::where('email', $email)->first();

        $passwordMatches = Hash::check(
            $credentials['password'],
            (string) ($user?->password ?? self::DUMMY_HASH)
        );

        // One generic message for unknown user, wrong password and disabled
        // account, so the response cannot be used to enumerate accounts.
        if (! $user || ! $passwordMatches || $user->status !== 'active') {
            $accountAttempts = RateLimiter::hit($accountKey, self::LOCKOUT_SECONDS);
            $ipAttempts = RateLimiter::hit($ipKey, self::LOCKOUT_SECONDS);

            if ($accountAttempts > self::MAX_ATTEMPTS_PER_ACCOUNT || $ipAttempts > self::MAX_ATTEMPTS_PER_IP) {
                // Start a fresh, fixed-length lockout from this moment instead
                // of leaving the caller with the remainder of the failure window.
                RateLimiter::clear($accountKey);
                RateLimiter::clear($ipKey);
                RateLimiter::hit($accountLockKey, self::LOCKOUT_SECONDS);

                if ($ipAttempts > self::MAX_ATTEMPTS_PER_IP) {
                    RateLimiter::hit($ipLockKey, self::LOCKOUT_SECONDS);
                }

                return $this->lockoutResponse();
            }

            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // A valid sign-in clears this account's failure counter. The per-IP
        // counter is deliberately left to decay on its own so one valid login
        // cannot reset a spray across other accounts from the same host.
        RateLimiter::clear($accountKey);

        $token = User::issueToken($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ])->header('Cache-Control', 'no-store, private');
    }

    private function isLockedOut(string $key): bool
    {
        return RateLimiter::tooManyAttempts($key, 1);
    }

    /**
     * The single 429 response shape for every login lockout. `Retry-After` is
     * always the full 10-minute window so clients can run a fixed countdown.
     */
    private function lockoutResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Too many failed login attempts. Please try again after 10 minutes.',
        ], 429)
            ->header('Retry-After', (string) self::LOCKOUT_SECONDS)
            ->header('Cache-Control', 'no-store, private');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->forceFill([
                'api_token' => null,
                'last_activity_at' => null,
                'token_expires_at' => null,
            ])->save();
        }

        return response()->json(['message' => 'Logged out.'])
            ->header('Cache-Control', 'no-store, private');
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user())
            ->header('Cache-Control', 'no-store, private');
    }
}
