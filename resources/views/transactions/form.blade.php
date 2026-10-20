@extends('layouts.app')
@php
$editing=$transaction->exists;
$oldItems=old('items');
if($oldItems===null){$oldItems=$items->map(fn($i)=>['name'=>$i->name,'description'=>$i->description,'quantity'=>$i->quantity ?: 1,'unit'=>$i->unit ?: 'project','unit_price'=>$i->unit_price ?: 0])->values()->all();}
$selectedCustomer=old('customer_id',$transaction->customer_id ?: ($selectedCustomerId ?? null));
$selectedWorkStatus=old('work_status',$transaction->work_status ?: 'menunggu');
$selectedDiscountType=old('discount_type',$transaction->discount_type);
@endphp
@section('title',$editing?'Edit Transaksi':'Buat Transaksi')
@section('page_title',$editing?'Edit Transaksi':'Buat Transaksi')
@section('eyebrow','Project')
@section('content')
<form method="POST" action="{{ $editing ? route('transactions.update',$transaction) : route('transactions.store') }}" data-submit-lock class="stack-form">
@csrf @if($editing) @method('PUT') @endif
<div class="page-grid">
<div class="stack-form">
<div class="card card-pad stack-form">
<div><span class="eyebrow">01 · Informasi Project</span><h2 style="margin:5px 0 0">Transaksi</h2></div>
<div class="form-grid">
<label class="field"><span>Customer *</span><select name="customer_id" required><option value="">Pilih customer</option>@foreach($customers as $c)<option value="{{ $c->id }}" @selected((string)$selectedCustomer===(string)$c->id)>{{ $c->displayName() }}</option>@endforeach</select><small>Belum ada? <a href="{{ route('customers.create') }}" style="color:var(--brand-primary);font-weight:800">Tambah customer</a></small></label>
<label class="field"><span>Tanggal *</span><input type="date" name="transaction_date" value="{{ old('transaction_date',optional($transaction->transaction_date)->format('Y-m-d') ?: today()->format('Y-m-d')) }}" required></label>
<label class="field span-2"><span>Nama Project *</span><input name="project_name" value="{{ old('project_name',$transaction->project_name) }}" placeholder="Contoh: Website Company Profile PT ABC" required></label>
<label class="field"><span>Jenis Jasa *</span><select name="service_type_id" data-service-select required><option value="">Pilih jenis jasa</option>@foreach($serviceTypes as $s)<option value="{{ $s->id }}" @selected((string)old('service_type_id',$transaction->service_type_id)===(string)$s->id)>{{ $s->name }}</option>@endforeach</select></label>
<div class="field" data-other-service hidden><span>Jenis Jasa Lainnya *</span><input name="custom_service_type" data-required-when-visible value="{{ old('custom_service_type',$transaction->custom_service_type) }}" placeholder="Contoh: Integrasi Virtual Try-On"><label class="check-row"><input type="checkbox" name="save_custom_service" value="1" @checked(old('save_custom_service'))> Simpan sebagai jenis jasa baru</label></div>
<label class="field span-2"><span>Deskripsi Pekerjaan</span><textarea name="description" placeholder="Ringkasan pekerjaan yang dilakukan…">{{ old('description',$transaction->description) }}</textarea></label>
</div>
</div>

<div class="card card-pad stack-form">
<div class="section-title"><div><span class="eyebrow">02 · Rincian Jasa</span><h2>Item Transaksi</h2></div><button type="button" class="btn btn-soft" data-add-item>+ Tambah Item</button></div>
<div class="item-builder" data-item-container>
@foreach($oldItems as $index=>$item)
<div class="item-row">
<div class="item-row-head"><strong data-item-number>Item {{ $index+1 }}</strong><button type="button" class="btn btn-danger" data-remove-item>Hapus</button></div>
<div class="item-fields">
<label class="field wide-mobile"><span>Nama Item *</span><input name="items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" required></label>
<label class="field"><span>Qty *</span><input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" data-item-qty required></label>
<label class="field"><span>Satuan *</span><input name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? 'project' }}" list="unit-list" required></label>
<label class="field"><span>Harga Satuan *</span><input type="number" min="0" step="1" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}" data-item-price required></label>
<div class="field"><span>Subtotal</span><div class="item-total" data-item-total>Rp0</div></div>
<label class="field description-field"><span>Deskripsi</span><input name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" placeholder="Opsional"></label>
</div></div>
@endforeach
</div>
<datalist id="unit-list"><option value="project"><option value="fitur"><option value="halaman"><option value="bulan"><option value="tahun"><option value="pcs"><option value="paket"></datalist>
<template id="item-template"><div class="item-row"><div class="item-row-head"><strong data-item-number>Item</strong><button type="button" class="btn btn-danger" data-remove-item>Hapus</button></div><div class="item-fields"><label class="field wide-mobile"><span>Nama Item *</span><input name="items[__INDEX__][name]" required></label><label class="field"><span>Qty *</span><input type="number" step="0.01" min="0.01" name="items[__INDEX__][quantity]" value="1" data-item-qty required></label><label class="field"><span>Satuan *</span><input name="items[__INDEX__][unit]" value="project" list="unit-list" required></label><label class="field"><span>Harga Satuan *</span><input type="number" min="0" step="1" name="items[__INDEX__][unit_price]" value="0" data-item-price required></label><div class="field"><span>Subtotal</span><div class="item-total" data-item-total>Rp0</div></div><label class="field description-field"><span>Deskripsi</span><input name="items[__INDEX__][description]" placeholder="Opsional"></label></div></div></template>
</div>

<div class="card card-pad stack-form">
<div><span class="eyebrow">03 · Internal</span><h2 style="margin:5px 0 0">Status Pengerjaan</h2></div>
<div class="form-grid">
<div class="field"><span>Status</span><div class="status-stepper" data-status-stepper><button type="button" class="status-stepper-btn" data-status-prev aria-label="Status sebelumnya">←</button><div class="status-stepper-value"><small>Status pengerjaan</small><strong data-status-label>{{ match($selectedWorkStatus){'menunggu'=>'Menunggu','dikerjakan'=>'Dikerjakan','revisi'=>'Revisi','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan',default=>'Menunggu'} }}</strong></div><button type="button" class="status-stepper-btn" data-status-next aria-label="Status berikutnya">→</button><input type="hidden" name="work_status" value="{{ $selectedWorkStatus }}" data-status-input></div></div>
<div class="field"><span>Progress</span><div class="slider-row"><input type="range" min="0" max="100" step="1" name="progress_percent" value="{{ old('progress_percent',$transaction->progress_percent ?? 0) }}" data-slider="#progress-output"><output class="slider-value" id="progress-output">0%</output></div><small>Saat transaksi difinalkan, sistem otomatis menjadi Selesai / 100%.</small></div>
<label class="field span-2"><span>Catatan Internal</span><textarea name="internal_note" placeholder="Tidak akan tampil pada Invoice / Receipt">{{ old('internal_note',$transaction->internal_note) }}</textarea></label>
</div></div>
</div>

<aside class="stack-form">
<div class="card card-pad stack-form">
<div><span class="eyebrow">Ringkasan</span><h3 style="margin:5px 0 0">Harga & Diskon</h3></div>
<label class="field"><span>Tipe Diskon</span><select name="discount_type" data-discount-type><option value="">Tanpa diskon</option><option value="nominal" @selected($selectedDiscountType==='nominal')>Nominal Rupiah</option><option value="percent" @selected($selectedDiscountType==='percent')>Persentase</option></select></label>
<label class="field" data-discount-value-wrap @if(!$selectedDiscountType) hidden @endif><span>Nilai Diskon</span><input type="number" min="0" step="1" name="discount_value" value="{{ old('discount_value',$transaction->discount_value ?? 0) }}" data-discount-value></label>
<div class="summary-box"><div class="summary-line"><span>Subtotal</span><strong data-summary-subtotal>Rp0</strong></div><div class="summary-line" data-summary-discount-row @if(!$selectedDiscountType) hidden @endif><span>Diskon</span><strong data-summary-discount>- Rp0</strong></div><div class="summary-line total"><span>Total</span><strong data-summary-total>Rp0</strong></div></div>
</div>
<div class="card card-pad stack-form"><h3 style="margin:0">Simpan Transaksi</h3><p class="small muted" style="margin:0">Draft belum dapat menghasilkan dokumen. Finalkan saat project sudah benar-benar final.</p><button type="submit" name="intent" value="draft" class="btn btn-ghost">Simpan Draft</button><button type="submit" name="intent" value="final" class="btn btn-primary">Simpan & Finalkan</button>@if($editing)<a class="btn btn-ghost" href="{{ route('transactions.show',$transaction) }}">Batal</a>@else<a class="btn btn-ghost" href="{{ route('transactions.index') }}">Batal</a>@endif</div>
@if($editing && (float)$transaction->total_paid > 0)<div class="alert alert-info">Pembayaran yang sudah tercatat tetap tersimpan. Jika harga berubah, subtotal, total, dan sisa pembayaran akan dihitung ulang saat Anda menyimpan.</div><div class="card card-pad"><label class="check-row"><input type="checkbox" name="confirm_overpayment" value="1" @checked(old('confirm_overpayment'))> Izinkan total baru lebih kecil dari pembayaran yang telah tercatat bila perubahannya menyebabkan overpayment.</label></div>@endif
@if($editing && $transaction->documents()->exists())<div class="alert alert-warning">Transaksi ini sudah memiliki dokumen. Edit tidak otomatis mengubah snapshot dokumen lama.</div>@endif
</aside>
</div>
</form>
@endsection
