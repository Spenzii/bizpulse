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
        Schema::create('support_tickets', function (Blueprint $col) {
            $col->id();
            $col->foreignId('company_id')->constrained()->onDelete('cascade');
            $col->foreignId('user_id')->constrained()->onDelete('cascade');
            $col->string('subject');
            $col->text('description');
            $col->string('status')->default('Open');
            $col->string('priority')->default('Medium');
            $col->string('category')->nullable();
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
