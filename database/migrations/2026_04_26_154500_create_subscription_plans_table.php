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
        Schema::create('subscription_plans', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->decimal('monthly_fee', 10, 2)->default(0);
            $col->decimal('setup_fee', 10, 2)->default(0);
            $col->decimal('training_fee', 10, 2)->default(0);
            $col->integer('user_limit')->default(0);
            $col->json('features')->nullable();
            $col->boolean('is_recommended')->default(false);
            $col->boolean('is_active')->default(true);
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
