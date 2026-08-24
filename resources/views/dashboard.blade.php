@extends('layouts.app')
@section('title','Dashboard')
@section('page_title','Dashboard')
@section('eyebrow','Ringkasan Internal')
@section('top_actions')
<a href="{{ route('transactions.create') }}" class="btn btn-primary"><span class="hide-mobile">Buat</span> Transaksi</a>
@endsection
@section('content')
<div class="stats-grid">
    <div class="stat-card"><span>Total Transaksi</span><strong>{{ number_format($stats['transactions']) }}</strong><em>{{ number_format($stats['customers']) }} customer aktif</em></div>
    <div class="stat-card"><span>Nilai Transaksi</span><strong>{{ \App\Support\Money::rupiah($stats['value']) }}</strong><em>Seluruh transaksi aktif</em></div>
    <div class="stat-card"><span>Belum Dibayar</span><strong>{{ number_format($stats['unpaid']) }}</strong><em>{{ number_format($stats['partial']) }} dibayar sebagian</em></div>
    <div class="stat-card"><span>Transaksi Lunas</span><strong>{{ number_format($stats['paid']) }}</strong><em>{{ number_format($stats['documents']) }} dokumen dibuat</em></div>
    <div class="stat-card"><span>Total Diterima</span><strong>{{ \App\Support\Money::rupiah($stats['received']) }}</strong><em>Pembayaran tercatat</em></div>
    <div class="stat-card"><span>Sisa Pembayaran</span><strong>{{ \App\Support\Money::rupiah($stats['remaining']) }}</strong><em>Dari transaksi aktif</em></div>
</div>

<div class="quick-actions">
    <a class="btn btn-primary" href="{{ route('transactions.create') }}">+ Buat Transaksi</a>
    <a class="btn btn-ghost" href="{{ route('customers.create') }}">+ Tambah Customer</a>
    <a class="btn btn-ghost" href="{{ route('documents.index') }}">Lihat Invoice / Receipt</a>
</div>

<div class="card">
    <div class="card-header"><h2>Transaksi Terbaru</h2><a href="{{ route('transactions.index') }}" class="btn btn-ghost">Lihat Semua</a></div>
    @if($recent->isEmpty())
        <div class="empty-state"><strong>Belum ada transaksi</strong><span>Buat transaksi pertama Awi Code untuk mulai menghasilkan Invoice atau Receipt.</span></div>
    @else
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Customer / Project</th><th>Total</th><th>Dibayar</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
            <tbody>
            @foreach($recent as $t)
                <tr>
                    <td><div class="table-title">{{ $t->customer->company_name ?: $t->customer->name }}</div><div class="table-sub">{{ $t->project_name }}</div></td>
                    <td class="money">{{ \App\Support\Money::rupiah($t->grand_total) }}</td>
                    <td class="money">{{ \App\Support\Money::rupiah($t->total_paid) }}</td>
                    <td>
                        @php($class=$t->payment_status==='lunas'?'success':($t->payment_status==='sebagian'?'warning':''))
                        <span class="badge {{ $class }}">{{ $t->paymentStatusLabel() }}</span>
                    </td>
                    <td>{{ $t->transaction_date?->format('d M Y') }}</td>
                    <td><a class="btn btn-ghost" href="{{ route('transactions.show',$t) }}">Detail</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
