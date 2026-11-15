<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_activity_at')->nullable()->after('api_token');
            $table->timestamp('token_expires_at')->nullable()->after('last_activity_at');
        });

        // Tokens that are already issued get a full window instead of being
        // rejected on the very first request after the deployment.
        $absolute = (int) config('api.token.absolute_lifetime');

        DB::table('users')
            ->whereNotNull('api_token')
            ->update([
                'last_activity_at' => now(),
                'token_expires_at' => $absolute > 0 ? now()->addMinutes($absolute) : null,
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_activity_at', 'token_expires_at']);
        });
    }
};
