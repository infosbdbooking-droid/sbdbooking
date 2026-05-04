<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class orders extends Model
{
   
    protected $table = 'orders';
    
    protected $fillable = [
        'order_number',
        'user_id',
        'vendor_id',
        'category',
        'sub_category',
        'service_type',
        'service_frequency',
        'no_of_services',
        'scheduled_day',
        'price',
        'tax',
        'order_total',
        'discount_amount',
        'site_request',
        'service_center_type',
        'employee_name',
        'billing',
        'business_region',
        'business_sub_region',
        'branch_codes',
        'customer_type',
        'business_lead',
        'mobile_number',
        'customer_legal_name',
        'customer_trade_name',
        'contact_person',
        'designation',
        'address',
        'landmark',
        'city',
        'state',
        'pincode',
        'country',
        'phone_1',
        'phone_2',
        'email_1',
        'email_2',
        'gstnNum',
        'others',
        'clatlon',
        'bill_customer_legal_name',
        'bill_customer_trade_name',
        'bill_phone',
        'bill_email',
        'bill_address',
        'bill_city',
        'bill_pincode',
        'bill_landmark',
        'bill_country',
        'audit_requirement',
        'desired_date',
        'desired_time',
        'order_status',
        'sez',
    ];

}
