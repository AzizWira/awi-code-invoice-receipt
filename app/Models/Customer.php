<?php

namespace App\Models;

use App\Models\Concerns\HasOpaqueRouteKey;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasOpaqueRouteKey;
    protected $fillable = ['name', 'company_name', 'whatsapp', 'email', 'note', 'archived_at'];
    protected function casts(): array { return ['archived_at' => 'datetime']; }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class); }
    public function scopeActive($query) { return $query->whereNull('archived_at'); }
    public function displayName(): string { return $this->company_name ? $this->company_name.' — '.$this->name : $this->name; }
}
