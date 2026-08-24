@extends('layouts.app')
@section('title','Transaksi')
@section('page_title','Transaksi')
@section('eyebrow','Project & Pembayaran')
@section('top_actions')<a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Transaksi</a>@endsection
@section('content')
<div class="section-title"><div><h1>Semua Transaksi</h1><p>Satu transaksi mewakili satu project dan menjadi sumber Invoice maupun Receipt.</p></div></div>
<form class="filters" method="GET">
<input type="search" name="q" value="{{ $q }}" placeholder="Project, customer, nomor dokumen…">
<select name="status"><option value="">Semua pembayaran</option><option value="belum_dibayar" @selected($status==='belum_dibayar')>Belum Dibayar</option><option value="sebagian" @selected($status==='sebagian')>Dibayar Sebagian</option><option value="lunas" @selected($status==='lunas')>Lunas</option></select>
<select name="work_status"><option value="">Semua status kerja</option>@foreach(['menunggu','dikerjakan','revisi','selesai','dibatalkan'] as $s)<option value="{{ $s }}" @selected($workStatus===$s)>{{ ucfirst($s) }}</option>@endforeach</select>
<select name="service_type"><option value="">Semua jenis jasa</option>@foreach($serviceTypes as $s)<option value="{{ $s->route_key }}" @selected((string)$serviceType===(string)$s->route_key)>{{ $s->name }}</option>@endforeach</select>
<select name="sort"><option value="latest" @selected($sort==='latest')>Terbaru</option><option value="oldest" @selected($sort==='oldest')>Terlama</option><option value="highest" @selected($sort==='highest')>Total tertinggi</option><option value="lowest" @selected($sort==='lowest')>Total terendah</option></select>
<button class="btn btn-ghost">Terapkan</button>@if(request()->query())<a href="{{ route('transactions.index') }}" class="btn btn-ghost">Reset</a>@endif
</form>
<div class="card">
@if($transactions->isEmpty())<div class="empty-state"><strong>Belum ada transaksi</strong><span>Buat transaksi pertama Awi Code.</span></div>@else
<div class="table-wrap"><table class="data-table"><thead><tr><th>Customer / Project</th><th>Total</th><th>Dibayar</th><th>Pembayaran</th><th>Dokumen</th><th>Tanggal</th><th></th></tr></thead><tbody>
@foreach($transactions as $t)
<tr>
<td><div class="table-title">{{ $t->customer->company_name ?: $t->customer->name }}</div><div class="table-sub">{{ $t->project_name }} · {{ $t->serviceLabel() }}</div></td>
<td class="money">{{ \App\Support\Money::rupiah($t->grand_total) }}</td><td class="money">{{ \App\Support\Money::rupiah($t->total_paid) }}</td>
<td><span class="badge {{ $t->payment_status==='lunas'?'success':($t->payment_status==='sebagian'?'warning':'') }}">{{ $t->paymentStatusLabel() }}</span></td>
<td>@if($t->documents->isEmpty())<span class="muted">—</span>@else @foreach($t->documents as $d)<span class="badge {{ $d->type==='receipt'?'success':'info' }}">{{ $d->type }}</span> @endforeach @endif</td>
<td>{{ $t->transaction_date?->format('d M Y') }}</td><td><a class="btn btn-ghost" href="{{ route('transactions.show',$t) }}">Detail</a></td>
</tr>@endforeach
</tbody></table></div><div class="card-pad">{{ $transactions->links('components.pagination') }}</div>@endif
</div>
@endsection
