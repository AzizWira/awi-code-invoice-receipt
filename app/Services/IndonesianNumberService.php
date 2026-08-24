<?php

namespace App\Services;

class IndonesianNumberService
{
    private array $words = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

    public function spell(int|float|string $value): string
    {
        $number = (int) round((float) $value);
        if ($number === 0) return 'Nol rupiah';
        if ($number < 0) return 'Minus '.ucfirst($this->convert(abs($number))).' rupiah';
        return ucfirst(trim($this->convert($number))).' rupiah';
    }

    private function convert(int $n): string
    {
        if ($n < 12) return $this->words[$n];
        if ($n < 20) return $this->convert($n - 10).' belas';
        if ($n < 100) return $this->convert(intdiv($n, 10)).' puluh'.($n % 10 ? ' '.$this->convert($n % 10) : '');
        if ($n < 200) return 'seratus'.($n > 100 ? ' '.$this->convert($n - 100) : '');
        if ($n < 1000) return $this->convert(intdiv($n, 100)).' ratus'.($n % 100 ? ' '.$this->convert($n % 100) : '');
        if ($n < 2000) return 'seribu'.($n > 1000 ? ' '.$this->convert($n - 1000) : '');
        if ($n < 1_000_000) return $this->convert(intdiv($n, 1000)).' ribu'.($n % 1000 ? ' '.$this->convert($n % 1000) : '');
        if ($n < 1_000_000_000) return $this->convert(intdiv($n, 1_000_000)).' juta'.($n % 1_000_000 ? ' '.$this->convert($n % 1_000_000) : '');
        if ($n < 1_000_000_000_000) return $this->convert(intdiv($n, 1_000_000_000)).' miliar'.($n % 1_000_000_000 ? ' '.$this->convert($n % 1_000_000_000) : '');
        if ($n < 1_000_000_000_000_000) return $this->convert(intdiv($n, 1_000_000_000_000)).' triliun'.($n % 1_000_000_000_000 ? ' '.$this->convert($n % 1_000_000_000_000) : '');
        return (string) $n;
    }
}
