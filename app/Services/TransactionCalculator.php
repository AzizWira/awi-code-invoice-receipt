<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionCalculator
{
    public function recalculate(Transaction $transaction): Transaction
    {
        $transaction->loadMissing('items', 'payments');
        $subtotal = round((float) $transaction->items->sum(fn ($item) => (float) $item->line_total), 2);
        $discountValue = max(0, (float) $transaction->discount_value);
        $discountAmount = 0;

        if ($transaction->discount_type === 'percent') {
            $discountValue = min(100, $discountValue);
            $discountAmount = round($subtotal * ($discountValue / 100), 2);
        } elseif ($transaction->discount_type === 'nominal') {
            $discountAmount = min($subtotal, $discountValue);
        }

        $grandTotal = max(0, round($subtotal - $discountAmount, 2));
        $totalPaid = round((float) $transaction->payments->sum(fn ($payment) => (float) $payment->amount), 2);
        $remaining = max(0, round($grandTotal - $totalPaid, 2));

        $paymentStatus = 'belum_dibayar';
        if ($grandTotal > 0 && $totalPaid >= $grandTotal) $paymentStatus = 'lunas';
        elseif ($totalPaid > 0) $paymentStatus = 'sebagian';

        $transaction->forceFill([
            'subtotal' => $subtotal,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
            'total_paid' => $totalPaid,
            'remaining_amount' => $remaining,
            'payment_status' => $paymentStatus,
        ])->save();

        return $transaction->fresh(['customer','serviceType','items','payments','documents']);
    }
}
