@extends('layouts.app')
@php($editing=$customer->exists)
@section('title',$editing?'Edit Customer':'Tambah Customer')
@section('page_title',$editing?'Edit Customer':'Tambah Customer')
@section('eyebrow','Customer')
@section('content')
<div class="section-title"><div><h1>{{ $editing ? 'Perbarui data customer' : 'Customer baru' }}</h1><p>Minimal isi salah satu: nomor WhatsApp atau email.</p></div></div>
<form method="POST" action="{{ $editing ? route('customers.update',$customer) : route('customers.store') }}" data-submit-lock class="card card-pad stack-form">
@csrf @if($editing) @method('PUT') @endif
<div class="form-grid">
<label class="field"><span>Nama Customer / PIC *</span><input name="name" value="{{ old('name',$customer->name) }}" required></label>
<label class="field"><span>Nama Usaha / Perusahaan</span><input name="company_name" value="{{ old('company_name',$customer->company_name) }}" placeholder="Opsional"></label>
<label class="field"><span>WhatsApp</span><input name="whatsapp" value="{{ old('whatsapp',$customer->whatsapp) }}" placeholder="08xxxxxxxxxx"></label>
<label class="field"><span>Email</span><input type="email" name="email" value="{{ old('email',$customer->email) }}" placeholder="customer@email.com"></label>
<label class="field span-2"><span>Catatan</span><textarea name="note" placeholder="Catatan internal customer…">{{ old('note',$customer->note) }}</textarea></label>
</div>
<div class="button-row"><button class="btn btn-primary">{{ $editing ? 'Simpan Perubahan' : 'Tambah Customer' }}</button><a class="btn btn-ghost" href="{{ $editing ? route('customers.show',$customer) : route('customers.index') }}">Batal</a></div>
</form>
@endsection
