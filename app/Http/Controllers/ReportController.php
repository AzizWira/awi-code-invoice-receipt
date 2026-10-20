<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Services\DocumentAssetService;
use Carbon\CarbonImmutable;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index', $this->reportData($request));
    }

    public function pdf(Request $request, DocumentAssetService $assets)
    {
        $data = $this->reportData($request);
        $data['logoDataUri'] = $assets->dataUri(Setting::value('logo_path', 'brand/awi-code-logo.jpg'));
        $html = view('reports.pdf', $data)->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $pdf = new Dompdf($options);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $filename = 'Awi-Code-Rekap-'.($data['mode'] === 'month' ? sprintf('%04d-%02d', $data['year'], $data['month']) : $data['year']).'.pdf';
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function reportData(Request $request): array
    {
        $filters = $request->validate([
            'mode' => ['nullable', 'in:month,year'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $mode = $filters['mode'] ?? 'month';
        $year = (int) ($filters['year'] ?? now()->year);
        $month = (int) ($filters['month'] ?? now()->month);
        $start = CarbonImmutable::create($year, $mode === 'month' ? $month : 1, 1)->startOfDay();
        $end = $mode === 'month' ? $start->endOfMonth() : $start->endOfYear();

        // Pengukuran periode berdasarkan tanggal transaksi (bukan tanggal pembayaran).
        // Draft dan dibatalkan tidak dihitung sebagai nilai usaha yang sudah disepakati.
        $transactions = Transaction::query()
            ->with(['customer', 'serviceType'])
            ->where('transaction_status', 'final')
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $sum = fn ($items, $column) => round((float) $items->sum(fn ($t) => (float) $t->{$column}), 2);
        $summary = [
            'count' => $transactions->count(),
            'subtotal' => $sum($transactions, 'subtotal'),
            'discount' => $sum($transactions, 'discount_amount'),
            'total' => $sum($transactions, 'grand_total'),
            'paid' => $sum($transactions, 'total_paid'),
            'remaining' => $sum($transactions, 'remaining_amount'),
            'paid_count' => $transactions->where('payment_status', 'lunas')->count(),
            'partial_count' => $transactions->where('payment_status', 'sebagian')->count(),
            'unpaid_count' => $transactions->where('payment_status', 'belum_dibayar')->count(),
        ];

        $monthNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $monthly = collect(range(1, 12))->map(function ($m) use ($transactions, $sum, $monthNames) {
            $subset = $transactions->filter(fn ($t) => (int) $t->transaction_date->month === $m);
            return [
                'month' => $m,
                'label' => $monthNames[$m],
                'count' => $subset->count(),
                'total' => $sum($subset, 'grand_total'),
                'paid' => $sum($subset, 'total_paid'),
                'remaining' => $sum($subset, 'remaining_amount'),
            ];
        });

        $maxMonth = max(1, (float) $monthly->max('total'));
        $minYear = Transaction::min('transaction_date');
        $maxYear = Transaction::max('transaction_date');
        $firstYear = $minYear ? (int) substr((string)$minYear, 0, 4) : now()->year;
        $lastYear = $maxYear ? (int) substr((string)$maxYear, 0, 4) : now()->year;
        $years = range(min($firstYear, $year, now()->year), max($lastYear, $year, now()->year));
        rsort($years);

        return compact('mode', 'year', 'month', 'start', 'end', 'transactions', 'summary', 'monthNames', 'monthly', 'maxMonth', 'years')
            + ['brand' => Setting::value('brand_name', 'Awi Code'), 'printedAt' => now()];
    }
}
