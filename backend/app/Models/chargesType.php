<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class chargesType extends Model
{
    use HasFactory;

    protected $table = 'charges_type';

    protected $fillable = [
        'charges_type',
        'status',
    ];
}
