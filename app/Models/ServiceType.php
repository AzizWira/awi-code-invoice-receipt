<?php

namespace App\Models;

use App\Models\Concerns\HasOpaqueRouteKey;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasOpaqueRouteKey;
    protected $fillable = ['name', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function scopeActive($query) { return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name'); }
}
