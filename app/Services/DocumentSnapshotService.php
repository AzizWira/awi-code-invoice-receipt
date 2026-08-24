<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Transaction;

class DocumentSnapshotService
{
    public function make(Transaction $transaction): array
    {
        $transaction->load(['customer','serviceType','items','payments']);
        $settings = Setting::map();

        return [
            'transaction' => [
                'internal_code' => $transaction->internal_code,
                'project_name' => $transaction->project_name,
                'service_type' => $transaction->serviceLabel(),
                'description' => $transaction->description,
                'transaction_date' => $transaction->transaction_date?->format('Y-m-d'),
                'subtotal' => (float) $transaction->subtotal,
                'discount_type' => $transaction->discount_type,
                'discount_value' => (float) $transaction->discount_value,
                'discount_amount' => (float) $transaction->discount_amount,
                'grand_total' => (float) $transaction->grand_total,
                'total_paid' => (float) $transaction->total_paid,
                'remaining_amount' => (float) $transaction->remaining_amount,
                'payment_status' => $transaction->payment_status,
            ],
            'customer' => [
                'name' => $transaction->customer->name,
                'company_name' => $transaction->customer->company_name,
                'whatsapp' => $transaction->customer->whatsapp,
                'email' => $transaction->customer->email,
            ],
            'items' => $transaction->items->map(fn ($item) => [
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit' => $item->unit,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
            ])->values()->all(),
            'payments' => $transaction->payments->map(fn ($payment) => [
                'label' => $payment->label,
                'method' => $payment->methodLabel(),
                'amount' => (float) $payment->amount,
                'note' => $payment->note,
            ])->values()->all(),
            'branding' => [
                'brand_name' => $settings['brand_name'] ?? 'Awi Code',
                'logo_path' => $settings['logo_path'] ?? 'brand/awi-code-logo.jpg',
                'signer_name' => $settings['signer_name'] ?? 'Awi Code',
                'signature_path' => $settings['signature_path'] ?? null,
                'business_whatsapp' => $settings['business_whatsapp'] ?? null,
                'business_email' => $settings['business_email'] ?? null,
                'business_address' => $settings['business_address'] ?? null,
                'invoice_footer' => $settings['invoice_footer'] ?? 'Terima kasih telah mempercayakan kebutuhan digital Anda kepada Awi Code.',
                'receipt_footer' => $settings['receipt_footer'] ?? 'Terima kasih telah mempercayakan kebutuhan digital Anda kepada Awi Code.',
            ],
        ];
    }
}
