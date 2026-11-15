<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Safe defaults at the model level as well as the database level: an
     * account created without an explicit role must never become an admin by
     * accident (the previous schema defaulted to "admin").
     */
    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (! $user->role) {
                $user->role = 'employee';
            }

            if (! $user->status) {
                $user->status = 'active';
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'token_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Issue a new personal API token.
     *
     * The plain-text token is returned to the caller (stored client-side in
     * localStorage) while only its SHA-256 hash is persisted on
     * `users.api_token`, so a database leak never exposes usable tokens.
     *
     * The token starts its idle window right away and is given an absolute
     * expiry so it can never outlive that cap, even while it is in use.
     */
    public static function issueToken(self $user): string
    {
        $plainToken = Str::random(80);

        $user->forceFill([
            'api_token' => hash('sha256', $plainToken),
            'last_activity_at' => now(),
            'token_expires_at' => self::absoluteExpiry(),
        ])->save();

        return $plainToken;
    }

    /** Look up the user that owns the given plain-text API token. */
    public static function findByApiToken(string $plainToken): ?self
    {
        return static::where('api_token', hash('sha256', $plainToken))->first();
    }

    /**
     * The point in time a freshly issued token dies, or null when the
     * absolute lifetime is disabled.
     */
    public static function absoluteExpiry(): ?Carbon
    {
        $minutes = (int) config('api.token.absolute_lifetime');

        return $minutes > 0 ? now()->addMinutes($minutes) : null;
    }

    /**
     * Whether the token has outlived the idle window or the absolute lifetime.
     *
     * The idle window slides: every authenticated request refreshes
     * `last_activity_at`, so a token dies only after a period of inactivity.
     */
    public function tokenIsExpired(): bool
    {
        $idleMinutes = (int) config('api.token.lifetime');

        if ($idleMinutes > 0) {
            $lastActivity = $this->last_activity_at;

            // Fail closed: a token without activity data cannot be trusted.
            if (! $lastActivity || $lastActivity->lt(now()->subMinutes($idleMinutes))) {
                return true;
            }
        }

        $absoluteMinutes = (int) config('api.token.absolute_lifetime');

        if ($absoluteMinutes > 0 && $this->token_expires_at && $this->token_expires_at->lt(now())) {
            return true;
        }

        return false;
    }
}
