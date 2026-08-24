<?php

namespace App\Models;

use App\Models\Concerns\HasOpaqueRouteKey;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasOpaqueRouteKey;
    protected $fillable = [
        'internal_code','customer_id','service_type_id','custom_service_type','project_name','description',
        'transaction_date','work_status','progress_percent','subtotal','discount_type','discount_value',
        'discount_amount','grand_total','total_paid','remaining_amount','payment_status','transaction_status',
        'internal_note','finalized_at'
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'finalized_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function serviceType(): BelongsTo { return $this->belongsTo(ServiceType::class); }
    public function items(): HasMany { return $this->hasMany(TransactionItem::class)->orderBy('sort_order'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class)->orderBy('id'); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }

    public function serviceLabel(): string
    {
        return $this->custom_service_type ?: ($this->serviceType?->name ?? 'Lainnya');
    }

    public function transactionStatusLabel(): string
    {
        return match ($this->transaction_status) {
            'draft' => 'Draft',
            'final' => 'Final',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', (string) $this->transaction_status)),
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'belum_dibayar' => 'Belum Dibayar',
            'sebagian' => 'Dibayar Sebagian',
            'lunas' => 'Lunas',
            default => ucwords(str_replace('_', ' ', (string) $this->payment_status)),
        };
    }

    public function workStatusLabel(): string
    {
        return match ($this->work_status) {
            'menunggu' => 'Menunggu',
            'dikerjakan' => 'Dikerjakan',
            'revisi' => 'Revisi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucwords(str_replace('_', ' ', (string) $this->work_status)),
        };
    }

    public function isPaid(): bool { return (float) $this->grand_total > 0 && (float) $this->total_paid >= (float) $this->grand_total; }
    public function isFinal(): bool { return $this->transaction_status === 'final'; }
}
