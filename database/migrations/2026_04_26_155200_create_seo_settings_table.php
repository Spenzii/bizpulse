<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $col) {
            $col->id();
            $col->string('page_name')->unique();
            $col->string('title')->nullable();
            $col->text('description')->nullable();
            $col->text('keywords')->nullable();
            $col->json('og_data')->nullable(); // title, description, image
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
