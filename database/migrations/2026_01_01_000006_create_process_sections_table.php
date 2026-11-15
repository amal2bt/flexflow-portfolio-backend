<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('text1')->nullable();
            $table->text('text2')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_sections');
    }
};
