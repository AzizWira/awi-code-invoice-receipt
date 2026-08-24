<?php

namespace App\Models;

use App\Models\Concerns\HasOpaqueRouteKey;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasOpaqueRouteKey;
    protected $fillable = ['transaction_id','label','method','custom_method','amount','note'];
    protected function casts(): array { return ['amount'=>'decimal:2']; }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function methodLabel(): string { return $this->custom_method ?: $this->method; }
}
