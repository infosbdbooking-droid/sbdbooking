<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CabOrder extends Model
{
    use SoftDeletes;

    protected $table = 'cab_orders';

    protected $fillable = [
        'order_number',
        'booking_status',

        // Customer
        'customer_id',
        'customer_name',
        'customer_mobile',

        // Car
        'car_id',
        'car_name',

        // Trip
        'trip_type',
        'stay_duration',
        'is_ac',

        // Pickup
        'pickup_address',
        'pickup_lat',
        'pickup_lng',

        // Drop
        'drop_address',
        'drop_lat',
        'drop_lng',

        // Return
        'return_pickup_address',
        'return_pickup_lat',
        'return_pickup_lng',
        'return_drop_address',
        'return_drop_lat',
        'return_drop_lng',

        // Distance
        'one_way_km',
        'return_km',
        'total_km',

        // Schedule
        'pickup_date',
        'pickup_time',
        'return_date',
        'return_time',

        // Passengers
        'passengers',
        'bags',
        'notes_for_driver',

        // Charges
        'per_km_amount',
        'driver_allowance',
        'platform_charges',
        'ac_charges',
        'waiting_charges',
        'toll_tax',
        'charges_breakdown',
        'estimated_toll',

        // Coupon
        'coupon_code',
        'discount_amount',

        // Totals
        'subtotal',
        'total_amount',

        // Payment
        'payment_status',
        'payment_method',
        'advance_payment',

        // Vendor
        'vendor_id',
        'commission_type',
        'commission_rate',
        'commission_amount',
        'vendor_earnings',

        // Driver
        'driver_id',
        'driver_name',
        'driver_mobile',
    ];

    protected $casts = [
        'charges_breakdown' => 'array',
        'is_ac'             => 'boolean',
        'pickup_lat'        => 'float',
        'pickup_lng'        => 'float',
        'drop_lat'          => 'float',
        'drop_lng'          => 'float',
        'return_pickup_lat' => 'float',
        'return_pickup_lng' => 'float',
        'return_drop_lat'   => 'float',
        'return_drop_lng'   => 'float',
        'pickup_date'       => 'date:Y-m-d',
        'return_date'       => 'date:Y-m-d',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function payments()
    {
        return $this->hasMany(CabOrderPayment::class, 'cab_order_id')->orderBy('created_at', 'desc');
    }

    public function activities()
    {
        return $this->hasMany(CabOrderActivity::class, 'cab_order_id')->orderBy('created_at', 'desc');
    }

    /* ===================== HELPERS ===================== */

    /**
     * Generate a unique order number: SBD-YYYYMMDD-XXXX
     */
    public static function generateOrderNumber(): string
    {
        $date   = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));
        return "SBD-{$date}-{$random}";
    }
}
