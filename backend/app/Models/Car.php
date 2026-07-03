<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    protected $table = 'car';

    protected $fillable = [
        'vendor_id',
        'car_type_id',
        'car_name',
        'car_seats',
        'car_photos',
        'is_ac',
        'max_passengers',
        'max_bags',
        'min_trip_amount',

        // JSON columns
        'booking_includes',
        'why_book_us',
        'trip_policies',
        'recent_reviews',

        // Rating
        'rating_summary',
        'rating_value',
        'rating_count',

        'status',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function carType()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function charges()
    {
        return $this->hasMany(CarCharge::class, 'car_id');
    }
}
