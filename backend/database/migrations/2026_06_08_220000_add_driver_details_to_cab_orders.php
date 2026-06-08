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
        Schema::table('cab_orders', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('driver_id');
            $table->string('driver_mobile')->nullable()->after('driver_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cab_orders', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'driver_mobile']);
        });
    }
};
