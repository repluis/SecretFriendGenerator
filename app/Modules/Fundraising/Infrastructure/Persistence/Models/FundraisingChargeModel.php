<?php

namespace App\Modules\Fundraising\Infrastructure\Persistence\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundraisingChargeModel extends Model
{
    protected $table = 'fundraising_charges';

    protected $fillable = [
        'user_id',
        'type',
        'base_amount',
        'penalty_amount',
        'paid_amount',
        'charge_date',
        'penalty_last_applied_date',
        'is_fully_paid',
        'paid_at',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'charge_date' => 'date',
        'penalty_last_applied_date' => 'date',
        'is_fully_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
