<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SubscriptionRequest extends Model
{
    use HasFactory;

    protected $appends = [
        'status_html',
        'document_url',
    ];

    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'status_updated_by_id',
        'quantity',
        'transaction_id',
        'document_path',
        'status',
    ];

    protected $casts = [
        'status' => 'integer'
    ];

    public function getDocumentUrlAttribute()
    {
        return Storage::disk('public')->url($this->document_path);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function statusUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_updated_by_id');
    }

    public function getStatusText(): string
    {
        $statusText = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
        ];

        return $statusText[$this->status];
    }

    public function getStatusHtmlAttribute(): string
    {
        $statusBadge = [
            self::STATUS_PENDING => 'badge-info',
            self::STATUS_ACCEPTED => 'badge-success',
            self::STATUS_REJECTED => 'badge-danger',
        ];

        return '<span class="badge '.$statusBadge[$this->status].'">'.$this->getStatusText().'</span>';
    }
}
