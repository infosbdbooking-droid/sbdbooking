<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix two issues:
     * 1. Add missing advance_payment column
     * 2. Change payment_method from a strict enum to a VARCHAR to support
     *    PhonePe, Google Pay, Paytm, Net Banking, Bank Transfer etc.
     */
    public function up(): void
    {
        Schema::table('cab_orders', function (Blueprint $table) {
            // Add advance_payment column (tracks how much has been collected so far)
            $table->decimal('advance_payment', 10, 2)->default(0)->after('total_amount');
        });

        // Change payment_method from enum to varchar (MySQL specific - requires raw ALTER)
        // This preserves existing data
        DB::statement("ALTER TABLE `cab_orders` MODIFY COLUMN `payment_method` VARCHAR(100) NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cab_orders', function (Blueprint $table) {
            $table->dropColumn('advance_payment');
        });

        // Restore original enum - only safe if no incompatible values exist
        DB::statement("ALTER TABLE `cab_orders` MODIFY COLUMN `payment_method` ENUM('cash','online','upi','card') NULL DEFAULT NULL");
    }
};
