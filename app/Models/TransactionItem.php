<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id','name','description','quantity','unit','unit_price','line_total','sort_order'];
    protected function casts(): array { return ['quantity'=>'decimal:2','unit_price'=>'decimal:2','line_total'=>'decimal:2']; }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
}
