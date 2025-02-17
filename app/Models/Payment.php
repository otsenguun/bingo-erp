<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'subscription_id',
        'quantity',
        'method',
        'status',
        'gateway_trx_id',
        'currency',
        'amount',
        'default_amount_rate',
        'data',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->system_trx_id = Str::of(Str::random(10))->upper();
        });
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'data' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}