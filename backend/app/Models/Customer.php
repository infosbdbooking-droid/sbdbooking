<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'customers';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'country_code',
        'mobile',
        'email',
        'profile_photo',
        'password',
        'status',
        'email_verified_at',
        'mobile_verified_at',
        'remember_token',
    ];

    /**
     * Hidden fields (never exposed in API / JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'status'             => 'boolean',
        'email_verified_at'  => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];
}
