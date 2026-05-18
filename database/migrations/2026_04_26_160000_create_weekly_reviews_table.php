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
        Schema::create('weekly_reviews', function (Blueprint $col) {
            $col->id();
            $col->foreignId('company_id')->constrained()->onDelete('cascade');
            $col->integer('week_number');
            $col->integer('year');
            $col->text('summary');
            $col->json('highlights')->nullable();
            $col->json('concerns')->nullable();
            $col->foreignId('submitted_by_id')->constrained('users')->onDelete('cascade');
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_reviews');
    }
};
