@extends('layouts.app')
@section('title','Invoice / Receipt')
@section('page_title','Invoice / Receipt')
@section('eyebrow','Document Library')
@section('content')
<div class="section-title"><div><h1>Dokumen</h1><p>Semua Invoice dan Receipt yang pernah dibuat dari transaksi Awi Code.</p></div></div>
<form class="filters" method="GET">
<input type="search" name="q" value="{{ $q }}" placeholder="Nomor, project, customer…">
<div class="segmented-filter" role="radiogroup" aria-label="Tipe dokumen">
<label><input type="radio" name="type" value="" @checked(!$type)><span>Semua</span></label>
<label><input type="radio" name="type" value="invoice" @checked($type==='invoice')><span>Invoice</span></label>
<label><input type="radio" name="type" value="receipt" @checked($type==='receipt')><span>Receipt</span></label>
</div>
<select name="status"><option value="">Semua akses</option><option value="active" @selected($status==='active')>Aktif</option><option value="revoked" @selected($status==='revoked')>Dicabut</option></select>
<button class="btn btn-ghost">Terapkan</button>@if(request()->query())<a class="btn btn-ghost" href="{{ route('documents.index') }}">Reset</a>@endif
</form>
<div class="card">
@if($documents->isEmpty())<div class="empty-state"><strong>Belum ada dokumen</strong><span>Generate Invoice atau Receipt dari detail transaksi.</span></div>@else
<div class="table-wrap"><table class="data-table"><thead><tr><th>Dokumen</th><th>Customer / Project</th><th>Total</th><th>Status</th><th>Dibuat / Diubah</th><th></th></tr></thead><tbody>
@foreach($documents as $document)
<tr><td><div class="table-title">{{ $document->document_number }}</div><div class="table-sub">{{ strtoupper($document->type) }}</div></td><td><div class="table-title">{{ $document->transaction->customer->company_name ?: $document->transaction->customer->name }}</div><div class="table-sub">{{ $document->transaction->project_name }}</div></td><td class="money">{{ \App\Support\Money::rupiah(data_get($document->snapshot_json,'transaction.grand_total',0)) }}</td><td><span class="badge {{ $document->isActive()?'success':'danger' }}">{{ $document->isActive()?'Aktif':'Dicabut' }}</span></td><td>{{ $document->generated_at?->format('d M Y H:i') }}<div class="table-sub">Update {{ $document->updated_at?->format('d M Y H:i') }}</div></td><td><a class="btn btn-ghost" href="{{ route('documents.show',$document) }}">Lihat</a></td></tr>
@endforeach
</tbody></table></div><div class="card-pad">{{ $documents->links('components.pagination') }}</div>@endif
</div>
@endsection
