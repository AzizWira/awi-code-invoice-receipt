<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasOpaqueRouteKey
{
    public static function bootHasOpaqueRouteKey(): void
    {
        static::creating(function (Model $model): void {
            if (! empty($model->route_key)) {
                return;
            }

            do {
                $key = bin2hex(random_bytes(10));
            } while ($model->newQueryWithoutScopes()->where('route_key', $key)->exists());

            $model->route_key = $key;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'route_key';
    }
}
