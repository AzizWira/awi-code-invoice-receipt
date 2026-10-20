<?php

use App\Models\Transaction;
use App\Services\TransactionCalculator;
use Illuminate\Support\Facades\Artisan;

Artisan::command('awicode:about', function () {
    $this->info('Awi Code Invoice & Receipt Manager v1.0.4');
})->purpose('Lihat versi aplikasi');

// Perbaiki total yang sempat tertinggal akibat bug item relation cache versi lama.
// Tanpa --apply hanya menampilkan jumlah transaksi yang berpotensi tidak sinkron.
Artisan::command('awicode:recalculate-totals {--apply : Simpan ulang total yang tidak sinkron}', function (TransactionCalculator $calculator) {
    $affected = 0;
    $fixed = 0;
    Transaction::query()->with(['items', 'payments'])->chunkById(100, function ($transactions) use (&$affected, &$fixed, $calculator) {
        foreach ($transactions as $transaction) {
            $subtotal = round((float) $transaction->items->sum(fn ($item) => (float) $item->line_total), 2);
            $value = max(0, (float) $transaction->discount_value);
            $discount = match ($transaction->discount_type) {
                'percent' => round($subtotal * min(100, $value) / 100, 2),
                'nominal' => min($subtotal, $value),
                default => 0,
            };
            $total = max(0, round($subtotal - $discount, 2));
            $paid = round((float) $transaction->payments->sum(fn ($p) => (float) $p->amount), 2);
            $remaining = max(0, round($total - $paid, 2));
            $status = $total > 0 && $paid >= $total ? 'lunas' : ($paid > 0 ? 'sebagian' : 'belum_dibayar');
            $changed = abs((float) $transaction->subtotal - $subtotal) > 0.005
                || abs((float) $transaction->discount_amount - $discount) > 0.005
                || abs((float) $transaction->grand_total - $total) > 0.005
                || abs((float) $transaction->total_paid - $paid) > 0.005
                || abs((float) $transaction->remaining_amount - $remaining) > 0.005
                || $transaction->payment_status !== $status;
            if (! $changed) continue;
            $affected++;
            if ($affected <= 15) $this->line($transaction->internal_code.': '.number_format((float) $transaction->grand_total, 0, ',', '.').' → '.number_format($total, 0, ',', '.'));
            if ($this->option('apply')) {
                $calculator->recalculate($transaction);
                $fixed++;
            }
        }
    });
    $this->info('Transaksi tidak sinkron: '.$affected.' | Diperbaiki: '.$fixed);
    if (! $this->option('apply') && $affected > 0) $this->warn('Backup database lalu jalankan perintah ini dengan --apply untuk memperbaiki. Snapshot Invoice/Receipt tidak diubah otomatis.');
})->purpose('Cek dan perbaiki total transaksi tanpa mengubah snapshot dokumen');
