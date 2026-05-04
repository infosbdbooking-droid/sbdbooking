<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeakTimeCharge extends Model
{
    use HasFactory;

    protected $table = 'peak_time_charges';

    protected $fillable = [
        'title',
        'start_time',
        'end_time',
        'charge_type',   // 0 = flat, 1 = percent
        'charge_value',
        'status',
    ];
}
