<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Roles;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use  HasFactory, Notifiable; // HasApiTokens

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'password',
        'mobile',
        'alternate_mobile',
        'aadhaar_number',
        'aadhaar_file',
        'pan_number',
        'pan_file',
        'photo',
        'gst_number',
        'address',
        'city',
        'state',
        'pincode',
        'profile_status',
        'profile_verified_at',
        'approved_by',
        'rejection_reason',
        'commission_type',
        'commission_percentage',
        'flat_commission',
        'company_logo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'vendor_id');
    }

    public function orders()
    {
        return $this->hasMany(CabOrder::class, 'vendor_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function settlements()
    {
        return $this->hasMany(SettlementRequest::class, 'vendor_id');
    }

    public function isVendor()
    {
        return $this->roles && strtolower($this->roles->title) === 'vendor';
    }

    public function isApprovedVendor()
    {
        return $this->isVendor() && $this->profile_status === 'Approved';
    }

}
