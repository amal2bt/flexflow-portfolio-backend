<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invest_stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('value')->default(0); // supports negatives (e.g. -80)
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invest_stats');
    }
};
