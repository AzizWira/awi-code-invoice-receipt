@extends('layouts.app')
@section('title','Edit Pembayaran')
@section('page_title','Edit Pembayaran')
@section('eyebrow','Transaksi')
@section('content')
<div class="card card-pad" style="max-width:720px;margin:0 auto"><div class="section-title"><div><h1>Edit Pembayaran</h1><p>{{ $transaction->project_name }}</p></div></div>
@if($transaction->documents()->where('type','receipt')->exists())<div class="alert alert-warning">Receipt sudah pernah dibuat. Mengubah pembayaran tidak otomatis mengubah Receipt lama.</div>@endif
<form method="POST" action="{{ route('payments.update',[$transaction,$payment]) }}" data-submit-lock class="stack-form">@csrf @method('PUT')
<label class="field"><span>Label</span><input name="label" value="{{ old('label',$payment->label) }}"></label>
<label class="field"><span>Metode *</span><select name="method" data-payment-method>@foreach($methods as $m)<option value="{{ $m }}" @selected(old('method',$payment->method)===$m)>{{ $m }}</option>@endforeach</select></label>
<label class="field" data-other-payment hidden><span>Metode Lainnya</span><input name="custom_method" data-required-when-visible value="{{ old('custom_method',$payment->custom_method) }}"></label>
<label class="field"><span>Nominal *</span><input type="number" name="amount" min="1" step="1" value="{{ old('amount',(float)$payment->amount) }}" required></label>
<label class="field"><span>Catatan</span><textarea name="note">{{ old('note',$payment->note) }}</textarea></label>
@if($transaction->documents()->where('type','receipt')->exists())<label class="check-row"><input type="checkbox" name="confirm_finalized" value="1" required> Saya memahami Receipt lama tetap memakai snapshot sebelumnya.</label>@endif
<label class="check-row"><input type="checkbox" name="confirm_overpayment" value="1"> Izinkan nominal menyebabkan overpayment.</label>
<div class="button-row"><button class="btn btn-primary">Simpan Pembayaran</button><a class="btn btn-ghost" href="{{ route('transactions.show',$transaction) }}">Batal</a></div></form></div>
@endsection
