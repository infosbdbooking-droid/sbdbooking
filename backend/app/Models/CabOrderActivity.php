<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CabOrderActivity extends Model
{
    protected $table = 'cab_order_activities';

    protected $fillable = [
        'cab_order_id',
        'event',
        'description',
        'performed_by',
    ];

    /**
     * Get the cab order associated with this activity.
     */
    public function cabOrder(): BelongsTo
    {
        return $this->belongsTo(CabOrder::class, 'cab_order_id');
    }
}
