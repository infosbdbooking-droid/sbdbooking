<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cab_orders', function (Blueprint $table) {
            $table->id();

            // ─── Order Identity ───────────────────────────────────────────
            $table->string('order_number')->unique(); // SBD-20260502-XXXX
            $table->string('booking_status')->default('pending');
            // pending | confirmed | driver_assigned | on_the_way | started | completed | cancelled

            // ─── Customer ─────────────────────────────────────────────────
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_mobile', 20);

            // ─── Car / Vehicle ────────────────────────────────────────────
            $table->unsignedBigInteger('car_id');
            $table->string('car_name')->nullable();         // snapshot at booking

            // ─── Trip Type ────────────────────────────────────────────────
            $table->enum('trip_type', ['one_way', 'round_trip'])->default('one_way');
            $table->enum('stay_duration', ['short', 'day', 'night'])->default('short');

            // ─── Pickup Location ──────────────────────────────────────────
            $table->text('pickup_address');
            $table->decimal('pickup_lat', 10, 7)->nullable();
            $table->decimal('pickup_lng', 10, 7)->nullable();

            // ─── Drop Location ────────────────────────────────────────────
            $table->text('drop_address');
            $table->decimal('drop_lat', 10, 7)->nullable();
            $table->decimal('drop_lng', 10, 7)->nullable();

            // ─── Return Trip (Round Trip) ─────────────────────────────────
            $table->text('return_pickup_address')->nullable();
            $table->decimal('return_pickup_lat', 10, 7)->nullable();
            $table->decimal('return_pickup_lng', 10, 7)->nullable();

            $table->text('return_drop_address')->nullable();
            $table->decimal('return_drop_lat', 10, 7)->nullable();
            $table->decimal('return_drop_lng', 10, 7)->nullable();

            // ─── Distance ─────────────────────────────────────────────────
            $table->decimal('one_way_km', 8, 2)->default(0);
            $table->decimal('return_km', 8, 2)->default(0);
            $table->decimal('total_km', 8, 2)->default(0);

            // ─── Schedule ─────────────────────────────────────────────────
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->date('return_date')->nullable();
            $table->time('return_time')->nullable();

            // ─── Passengers & Bags ────────────────────────────────────────
            $table->unsignedTinyInteger('passengers')->default(1);
            $table->unsignedTinyInteger('bags')->default(0);

            // ─── Notes / Instructions ─────────────────────────────────────
            $table->text('notes_for_driver')->nullable();

            // ─── Charges Breakdown (JSON snapshot) ───────────────────────
            $table->decimal('per_km_amount', 10, 2)->default(0);
            $table->decimal('driver_allowance', 10, 2)->default(0);
            $table->decimal('platform_charges', 10, 2)->default(0);
            $table->decimal('ac_charges', 10, 2)->default(0);
            $table->decimal('waiting_charges', 10, 2)->default(0);
            $table->decimal('toll_tax', 10, 2)->default(0);        // 0 = "Included" / actual if collected
            $table->json('charges_breakdown')->nullable();          // full JSON snapshot from calculateCharges

            // ─── Coupon ───────────────────────────────────────────────────
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);

            // ─── Amounts ─────────────────────────────────────────────────
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);    // after discount

            // ─── Payment ─────────────────────────────────────────────────
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'online', 'upi', 'card'])->nullable();

            // ─── Estimated Toll (shown on booking page) ───────────────────
            $table->decimal('estimated_toll', 10, 2)->default(0);

            // ─── Driver (assigned later) ──────────────────────────────────
            $table->unsignedBigInteger('driver_id')->nullable();

            // ─── AC flag ──────────────────────────────────────────────────
            $table->boolean('is_ac')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('customer_id');
            $table->index('car_id');
            $table->index('booking_status');
            $table->index('pickup_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cab_orders');
    }
};
