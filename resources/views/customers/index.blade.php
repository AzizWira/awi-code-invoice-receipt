@extends('layouts.app')
@section('title','Customer')
@section('page_title','Customer')
@section('eyebrow','Relasi & Riwayat')
@section('top_actions')<a href="{{ route('customers.create') }}" class="btn btn-primary">+ Customer</a>@endsection
@section('content')
<div class="section-title"><div><h1>Daftar Customer</h1><p>Simpan PIC, perusahaan, kontak, dan seluruh riwayat transaksi.</p></div></div>
<form class="filters" method="GET">
    <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama, perusahaan, WhatsApp, email…">
    <select name="archived"><option value="0">Customer aktif</option><option value="1" @selected(request('archived')==='1')>Diarsipkan</option></select>
    <button class="btn btn-ghost">Terapkan</button>
    @if(request()->query())<a class="btn btn-ghost" href="{{ route('customers.index') }}">Reset</a>@endif
</form>
<div class="card">
@if($customers->isEmpty())
<div class="empty-state"><strong>Belum ada customer</strong><span>Tambahkan customer pertama untuk mulai membuat transaksi.</span></div>
@else
<div class="table-wrap"><table class="data-table responsive-cards">
<thead><tr><th>Customer</th><th>Kontak</th><th>Transaksi</th><th>Status</th><th></th></tr></thead>
<tbody>@foreach($customers as $customer)<tr>
<td data-label="Customer"><div class="table-title">{{ $customer->company_name ?: $customer->name }}</div><div class="table-sub">{{ $customer->company_name ? 'PIC: '.$customer->name : 'Perorangan' }}</div></td>
<td data-label="Kontak"><div>{{ $customer->whatsapp ?: '—' }}</div><div class="table-sub">{{ $customer->email ?: '—' }}</div></td>
<td data-label="Transaksi">{{ number_format($customer->transactions_count) }}</td>
<td data-label="Status"><span class="badge {{ $customer->archived_at ? 'danger' : 'success' }}">{{ $customer->archived_at ? 'Arsip' : 'Aktif' }}</span></td>
<td data-label="Aksi"><div class="button-row"><a class="btn btn-ghost" href="{{ route('customers.show',$customer) }}">Detail</a>@if($customer->archived_at)<form method="POST" action="{{ route('customers.restore',$customer) }}" data-submit-lock>@csrf<button class="btn btn-soft">Pulihkan</button></form>@endif</div></td>
</tr>@endforeach</tbody></table></div>
<div class="card-pad">{{ $customers->links('components.pagination') }}</div>
@endif
</div>
@endsection
