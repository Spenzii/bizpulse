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
        Schema::create('audit_logs', function (Blueprint $col) {
            $col->id();
            $col->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $col->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $col->string('event');
            $col->nullableMorphs('auditable');
            $col->json('old_values')->nullable();
            $col->json('new_values')->nullable();
            $col->string('ip_address')->nullable();
            $col->text('user_agent')->nullable();
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
