<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('short')->nullable();
            $table->string('icon')->default('globe');
            $table->string('color')->default('#20d6b3');
            $table->string('side_title')->nullable();
            $table->text('side_copy')->nullable();
            $table->string('heading')->nullable();
            $table->text('intro')->nullable();
            $table->text('closing')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
