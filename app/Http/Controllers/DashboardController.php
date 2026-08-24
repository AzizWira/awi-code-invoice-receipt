<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $base = Transaction::query()->where('transaction_status', '!=', 'cancelled');
        $stats = [
            'transactions' => (clone $base)->count(),
            'unpaid' => (clone $base)->where('payment_status','belum_dibayar')->count(),
            'partial' => (clone $base)->where('payment_status','sebagian')->count(),
            'paid' => (clone $base)->where('payment_status','lunas')->count(),
            'value' => (float) (clone $base)->sum('grand_total'),
            'received' => (float) (clone $base)->sum('total_paid'),
            'remaining' => (float) (clone $base)->sum('remaining_amount'),
            'customers' => Customer::active()->count(),
            'documents' => Document::count(),
        ];

        $recent = Transaction::with('customer')->latest()->limit(7)->get();
        return view('dashboard', compact('stats','recent'));
    }
}
