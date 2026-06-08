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
        // 1. Payments Table
        Schema::create('cab_order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cab_order_id');
            $table->string('receipt_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // Cash, UPI, PhonePe, Google Pay, Paytm, Credit Card, Debit Card, Net Banking, Bank Transfer
            $table->string('transaction_id')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->string('payment_status'); // pending, partially_paid, paid, failed, refunded
            $table->text('notes')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();

            $table->foreign('cab_order_id')->references('id')->on('cab_orders')->onDelete('cascade');
        });

        // 2. Activities/Timeline Table
        Schema::create('cab_order_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cab_order_id');
            $table->string('event'); // e.g. "Booking Created", "Booking Approved", "Advance Payment Received", etc.
            $table->text('description')->nullable();
            $table->string('performed_by')->nullable();
            $table->timestamps();

            $table->foreign('cab_order_id')->references('id')->on('cab_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cab_order_activities');
        Schema::dropIfExists('cab_order_payments');
    }
};
