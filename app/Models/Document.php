<?php

namespace App\Models;

use App\Models\Concerns\HasOpaqueRouteKey;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Document extends Model
{
    use HasOpaqueRouteKey;
    protected $fillable = [
        'transaction_id','type','document_number','public_token_hash','public_token_encrypted',
        'public_token_last4','status','snapshot_json','generated_at','finalized_at','revoked_at'
    ];

    protected function casts(): array
    {
        return ['snapshot_json'=>'array','generated_at'=>'datetime','finalized_at'=>'datetime','revoked_at'=>'datetime'];
    }

    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function publicToken(): string { return Crypt::decryptString($this->public_token_encrypted); }
    public function isActive(): bool { return $this->status === 'active' && $this->revoked_at === null; }
}
