<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChargesType extends Model
{
    use HasFactory;

    protected $table = 'charges_type';

    protected $fillable = [
        'charges_type',
        'status',
    ];
}
