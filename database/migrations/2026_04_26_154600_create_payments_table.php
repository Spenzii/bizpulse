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
        Schema::create('payments', function (Blueprint $col) {
            $col->id();
            $col->foreignId('company_id')->constrained()->onDelete('cascade');
            $col->decimal('amount', 10, 2);
            $col->string('currency')->default('PGK');
            $col->date('payment_date');
            $col->string('payment_method');
            $col->string('reference_number')->nullable();
            $col->string('status')->default('Paid'); // Pending, Paid, Failed
            $col->string('type')->default('Subscription');
            $col->text('notes')->nullable();
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
