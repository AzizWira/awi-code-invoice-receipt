@extends('layouts.app')
@section('title','Detail Customer')
@section('page_title','Detail Customer')
@section('eyebrow','Customer')
@section('top_actions')<a class="btn btn-primary" href="{{ route('transactions.create',['customer'=>$customer->route_key]) }}">+ Transaksi</a>@endsection
@section('content')
<div class="page-grid">
<div>
<div class="card card-pad">
<div class="section-title"><div><h1>{{ $customer->company_name ?: $customer->name }}</h1><p>{{ $customer->company_name ? 'PIC: '.$customer->name : 'Customer perorangan' }}</p></div><a class="btn btn-ghost" href="{{ route('customers.edit',$customer) }}">Edit</a></div>
<dl class="detail-list"><dt>WhatsApp</dt><dd>{{ $customer->whatsapp ?: '—' }}</dd><dt>Email</dt><dd>{{ $customer->email ?: '—' }}</dd><dt>Catatan</dt><dd>{{ $customer->note ?: '—' }}</dd><dt>Dibuat</dt><dd>{{ $customer->created_at?->format('d M Y H:i') }}</dd></dl>
<div class="stats-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));margin:18px 0 0"><div class="stat-card"><span>Transaksi</span><strong>{{ number_format($customerStats['count']) }}</strong></div><div class="stat-card"><span>Total Nilai</span><strong style="font-size:18px">{{ \App\Support\Money::rupiah($customerStats['value']) }}</strong></div><div class="stat-card"><span>Sudah Diterima</span><strong style="font-size:18px">{{ \App\Support\Money::rupiah($customerStats['paid']) }}</strong></div></div>
</div>
<div class="card" style="margin-top:18px"><div class="card-header"><h2>Riwayat Transaksi</h2></div>
@if($transactions->isEmpty())<div class="empty-state"><strong>Belum ada transaksi</strong><span>Customer ini belum mempunyai riwayat transaksi.</span></div>@else
<div class="table-wrap"><table class="data-table responsive-cards"><thead><tr><th>Project</th><th>Total</th><th>Status</th><th>Tanggal</th><th></th></tr></thead><tbody>
@foreach($transactions as $t)<tr><td data-label="Project"><div class="table-title">{{ $t->project_name }}</div><div class="table-sub">{{ $t->internal_code }}</div></td><td data-label="Total" class="money">{{ \App\Support\Money::rupiah($t->grand_total) }}</td><td data-label="Status"><span class="badge {{ $t->payment_status==='lunas'?'success':($t->payment_status==='sebagian'?'warning':'') }}">{{ $t->paymentStatusLabel() }}</span></td><td data-label="Tanggal">{{ $t->transaction_date?->format('d M Y') }}</td><td data-label="Aksi"><a class="btn btn-ghost" href="{{ route('transactions.show',$t) }}">Detail</a></td></tr>@endforeach
</tbody></table></div><div class="card-pad">{{ $transactions->links('components.pagination') }}</div>@endif
</div>
</div>
<aside class="stack-form">
<div class="card card-pad"><h3 style="margin-top:0">Aksi Customer</h3><div class="stack-form"><a class="btn btn-primary btn-block" href="{{ route('transactions.create',['customer'=>$customer->route_key]) }}">Buat Transaksi</a>@if(!$customer->archived_at)<form method="POST" action="{{ route('customers.archive',$customer) }}" data-submit-lock>@csrf<button class="btn btn-soft btn-block" data-confirm="Arsipkan customer ini? Customer tidak akan muncul di daftar aktif, tetapi seluruh riwayat transaksi tetap aman." data-confirm-title="Arsipkan Customer" data-confirm-text="Ya, Arsipkan">Arsipkan Customer</button></form>@else<form method="POST" action="{{ route('customers.restore',$customer) }}" data-submit-lock>@csrf<button class="btn btn-soft btn-block">Pulihkan Customer</button></form>@endif</div></div>

@php
$customerDeleteWarning = $customerStats['count'] > 0
    ? 'PERINGATAN: Customer ini memiliki '.$customerStats['count'].' transaksi. Menghapus customer secara permanen juga akan menghapus seluruh transaksi, item jasa, pembayaran, Invoice/Receipt, serta riwayat aktivitas terkait. Tindakan ini tidak dapat dibatalkan.'
    : 'Hapus customer ini secara permanen? Tindakan ini tidak dapat dibatalkan.';
@endphp
<div class="card card-pad danger-zone">
    <span class="eyebrow">Aksi Sensitif</span>
    <h3 style="margin:5px 0 8px">Hapus Customer</h3>
    <p class="small muted" style="margin:0 0 14px">Arsip tetap menjadi pilihan utama. Gunakan hapus permanen hanya jika data memang tidak ingin dipertahankan.</p>
    <form method="POST" action="{{ route('customers.destroy',$customer) }}" data-submit-lock>
        @csrf
        @method('DELETE')
        <input type="hidden" name="confirm_delete" value="1">
        <button class="btn btn-danger btn-block"
            data-confirm="{{ $customerDeleteWarning }}"
            data-confirm-title="Hapus Customer Permanen"
            data-confirm-text="Ya, Hapus Permanen"
            data-confirm-tone="danger">Hapus Customer Permanen</button>
    </form>
</div>
</aside>
</div>
@endsection
