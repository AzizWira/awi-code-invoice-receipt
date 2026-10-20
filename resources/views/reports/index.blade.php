@extends('layouts.app')
@section('title', 'Rekap Bulanan / Tahunan')
@section('page_title', 'Rekap Keuangan')
@section('eyebrow', 'Laporan Internal')
@section('content')
<div class="report-page">
    <div class="section-title report-heading">
        <div>
            <span class="eyebrow">AWI CODE · REKAP TRANSAKSI</span>
            <h1>Rekap Bulanan & Tahunan</h1>
            <p>Ringkasan nilai project dan pembayaran yang sudah tercatat.</p>
        </div>
        <div class="button-row report-actions">
            <button type="button" class="btn btn-ghost" data-print-report>Cetak Rekap</button>
            <a href="{{ route('reports.pdf', ['mode' => $mode, 'year' => $year, 'month' => $month]) }}" class="btn btn-primary">Unduh PDF</a>
        </div>
    </div>

    <form method="GET" action="{{ route('reports.index') }}" class="card card-pad report-filter" data-report-filter>
        <div class="report-filter-title"><strong>Atur Periode Rekap</strong><span class="small muted">Pilih periode, lalu terapkan filter.</span></div>
        <div class="segmented-filter report-mode" role="radiogroup" aria-label="Jenis periode">
            <label><input type="radio" name="mode" value="month" @checked($mode === 'month') data-report-mode><span>Bulanan</span></label>
            <label><input type="radio" name="mode" value="year" @checked($mode === 'year') data-report-mode><span>Tahunan</span></label>
        </div>
        <label class="field report-year"><span>Tahun</span><select name="year">@foreach($years as $optionYear)<option value="{{ $optionYear }}" @selected($year === $optionYear)>{{ $optionYear }}</option>@endforeach</select></label>
        <label class="field report-month" data-report-month><span>Bulan</span><select name="month">@foreach($monthNames as $key => $label)<option value="{{ $key }}" @selected($month === $key)>{{ $label }}</option>@endforeach</select></label>
        <button type="submit" class="btn btn-ghost report-apply">Terapkan</button>
    </form>

    <div class="report-print-header">
        <div><strong>{{ $brand }}</strong><span>Rekap Transaksi & Pembayaran</span></div>
        <div><strong>{{ $mode === 'month' ? $monthNames[$month].' '.$year : 'Tahun '.$year }}</strong><span>Dicetak {{ $printedAt->format('d/m/Y H:i') }} WIB</span></div>
    </div>

    <div class="report-period">
        <div><span class="eyebrow">Periode Terpilih</span><h2>{{ $mode === 'month' ? $monthNames[$month].' '.$year : 'Tahun '.$year }}</h2></div>
        <span class="badge info">{{ $summary['count'] }} transaksi final</span>
    </div>
    <div class="stats-grid report-stats">
        <div class="stat-card"><span>Nilai Transaksi</span><strong>{{ \App\Support\Money::rupiah($summary['total']) }}</strong><em>Setelah diskon</em></div>
        <div class="stat-card"><span>Total Pembayaran</span><strong>{{ \App\Support\Money::rupiah($summary['paid']) }}</strong><em>Pembayaran yang tercatat</em></div>
        <div class="stat-card"><span>Sisa Pembayaran</span><strong>{{ \App\Support\Money::rupiah($summary['remaining']) }}</strong><em>Belum tertutup pembayaran</em></div>
        <div class="stat-card"><span>Transaksi Lunas</span><strong>{{ number_format($summary['paid_count']) }}</strong><em>{{ $summary['partial_count'] }} sebagian · {{ $summary['unpaid_count'] }} belum dibayar</em></div>
    </div>

    @if($mode === 'year')
        <div class="card report-panel">
            <div class="card-header"><div><h2>Perbandingan Per Bulan</h2><p class="small muted" style="margin:4px 0 0">Nilai transaksi final menurut bulan pada tahun {{ $year }}.</p></div></div>
            <div class="report-bars" aria-label="Grafik nilai transaksi bulanan">
                @foreach($monthly as $bucket)
                <div class="report-bar-item">
                    <div class="report-bar-track"><div class="report-bar-fill" style="height: {{ round($bucket['total'] / $maxMonth * 100, 1) }}%" title="{{ $bucket['label'] }}: {{ \App\Support\Money::rupiah($bucket['total']) }}"></div></div>
                    <small>{{ mb_substr($bucket['label'], 0, 3) }}</small>
                </div>
                @endforeach
            </div>
            <div class="table-wrap"><table class="data-table responsive-cards report-table"><thead><tr><th>Bulan</th><th>Transaksi</th><th>Nilai</th><th>Dibayar</th><th>Sisa</th></tr></thead><tbody>
                @foreach($monthly as $bucket)
                <tr><td data-label="Bulan"><strong>{{ $bucket['label'] }}</strong></td><td data-label="Transaksi">{{ $bucket['count'] }}</td><td data-label="Nilai" class="money">{{ \App\Support\Money::rupiah($bucket['total']) }}</td><td data-label="Dibayar" class="money">{{ \App\Support\Money::rupiah($bucket['paid']) }}</td><td data-label="Sisa" class="money">{{ \App\Support\Money::rupiah($bucket['remaining']) }}</td></tr>
                @endforeach
                </tbody></table></div>
        </div>
    @else
        <div class="card card-pad report-breakdown">
            <div class="report-breakdown-title"><strong>Rincian Nilai Periode</strong><span class="small muted">Total harga sebelum & sesudah diskon.</span></div>
            <div class="summary-box report-summary"><div class="summary-line"><span>Subtotal</span><strong>{{ \App\Support\Money::rupiah($summary['subtotal']) }}</strong></div>@if($summary['discount'] > 0)<div class="summary-line"><span>Diskon</span><strong>- {{ \App\Support\Money::rupiah($summary['discount']) }}</strong></div>@endif<div class="summary-line total"><span>Total Transaksi</span><strong>{{ \App\Support\Money::rupiah($summary['total']) }}</strong></div></div>
        </div>
    @endif

    <div class="card report-panel">
        <div class="card-header"><div><h2>Rincian Transaksi</h2><p class="small muted" style="margin:4px 0 0">{{ $summary['count'] }} project final pada periode terpilih</p></div></div>
        @if($transactions->isEmpty())
            <div class="empty-state"><strong>Belum ada transaksi final pada periode ini</strong><span>Coba pilih bulan atau tahun lain. Transaksi draft dan yang dibatalkan tidak dimasukkan.</span></div>
        @else
            <div class="table-wrap"><table class="data-table responsive-cards report-table"><thead><tr><th>Tanggal</th><th>Customer / Project</th><th>Nilai</th><th>Dibayar</th><th>Sisa</th><th>Status</th></tr></thead><tbody>
                @foreach($transactions as $t)
                    <tr><td data-label="Tanggal">{{ $t->transaction_date->format('d/m/Y') }}</td>
                        <td data-label="Customer / Project"><div class="table-title">{{ $t->customer?->company_name ?: $t->customer?->name }}</div><div class="table-sub">{{ $t->project_name }}</div></td>
                        <td data-label="Nilai" class="money">{{ \App\Support\Money::rupiah($t->grand_total) }}</td>
                        <td data-label="Dibayar" class="money">{{ \App\Support\Money::rupiah($t->total_paid) }}</td>
                        <td data-label="Sisa" class="money">{{ \App\Support\Money::rupiah($t->remaining_amount) }}</td>
                        <td data-label="Status"><span class="badge {{ $t->payment_status === 'lunas' ? 'success' : ($t->payment_status === 'sebagian' ? 'warning' : '') }}">{{ $t->paymentStatusLabel() }}</span></td></tr>
                @endforeach
                </tbody></table></div>
        @endif
    </div>
    <p class="report-footnote"><strong>Catatan:</strong> Rekap dikelompokkan menurut <strong>tanggal transaksi</strong>, bukan tanggal dana masuk. Pembayaran DP dan pelunasan yang tercatat akan dikaitkan ke periode tanggal project. Data hanya mencakup transaksi <strong>Final</strong>; transaksi Draft/Dibatalkan dikecualikan. Ini merupakan rekap internal, bukan laporan arus kas berbasis tanggal pembayaran.</p>
</div>
@endsection
