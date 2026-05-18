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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('name');
            $table->string('primary_color')->default('#4f46e5')->after('logo_path'); // Indigo-600
            $table->string('secondary_color')->default('#1e293b')->after('primary_color'); // Slate-800
            $table->string('address_line_1')->nullable()->after('secondary_color');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('tax_id')->nullable()->after('address_line_2'); // TIN for PNG
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 
                'primary_color', 
                'secondary_color', 
                'address_line_1', 
                'address_line_2', 
                'tax_id'
            ]);
        });
    }
};
