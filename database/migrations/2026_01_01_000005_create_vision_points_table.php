<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vision_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vision_section_id')->constrained()->cascadeOnDelete();
            $table->text('point');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vision_points');
    }
};
