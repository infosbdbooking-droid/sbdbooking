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
        Schema::table('car_charges', function (Blueprint $table) {
            $table->dropColumn(['free_wait_minutes', 'wait_charge_unit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_charges', function (Blueprint $table) {
            $table->integer('free_wait_minutes')->nullable();
            $table->integer('wait_charge_unit')->nullable();
        });
    }
};
