<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Str;

class DocumentNumberService
{
    public function make(string $type, \DateTimeInterface|string|null $date = null): string
    {
        $date = $date ? \Illuminate\Support\Carbon::parse($date) : now();
        $prefix = $type === 'receipt' ? 'AWI-RCP' : 'AWI-INV';

        do {
            $random = strtoupper(Str::random(4));
            $number = $prefix.'-'.$date->format('ymd').'-'.$random;
        } while (Document::query()->where('document_number', $number)->exists());

        return $number;
    }
}
