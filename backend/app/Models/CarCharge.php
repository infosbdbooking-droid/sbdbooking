<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarCharge extends Model
{
    use HasFactory;

    protected $table = 'car_charges';

    protected $fillable = [
        'car_id',
        'charges_type_id',
        'title',
        'amount',
        'charge_unit',
        'free_wait_minutes',
        'wait_charge_unit',
        'min_km',
        'max_km',
        'status',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function chargeType()
    {
        return $this->belongsTo(ChargesType::class, 'charges_type_id');
    }
}
