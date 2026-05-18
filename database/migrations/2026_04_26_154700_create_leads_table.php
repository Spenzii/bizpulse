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
        Schema::create('leads', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->string('email');
            $col->string('phone')->nullable();
            $col->string('company_name')->nullable();
            $col->text('message')->nullable();
            $col->string('status')->default('New');
            $col->string('source')->default('Website');
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
