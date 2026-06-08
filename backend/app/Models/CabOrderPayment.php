<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CabOrderPayment extends Model
{
    protected $table = 'cab_order_payments';

    protected $fillable = [
        'cab_order_id',
        'receipt_number',
        'amount',
        'payment_method',
        'transaction_id',
        'screenshot_path',
        'payment_status',
        'notes',
        'added_by',
    ];

    /**
     * Get the cab order associated with this payment.
     */
    public function cabOrder(): BelongsTo
    {
        return $this->belongsTo(CabOrder::class, 'cab_order_id');
    }
}
