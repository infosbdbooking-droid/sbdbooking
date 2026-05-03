<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class carType extends Model
{
    use HasFactory;

    protected $table = 'car_type';  

    protected $fillable = [
        'car_type',
        'status',
    ];
}
