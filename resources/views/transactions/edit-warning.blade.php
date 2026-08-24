@extends('layouts.app')
@section('title','Konfirmasi Edit')
@section('page_title','Konfirmasi Edit')
@section('eyebrow','Perlindungan Dokumen')
@section('content')
<div class="card card-pad danger-zone" style="max-width:760px;margin:24px auto">
<span class="eyebrow">Perlu Konfirmasi</span><h1 style="margin:8px 0">Transaksi ini sudah sensitif untuk diedit</h1>
<p class="muted">Transaksi <strong>{{ $transaction->project_name }}</strong> sudah lunas dan/atau memiliki dokumen yang mungkin telah dibagikan kepada customer. Perubahan transaksi tidak otomatis mengubah snapshot Invoice/Receipt lama.</p>
<div class="alert alert-warning">Jika Anda melanjutkan, setelah menyimpan perubahan Anda dapat memilih <strong>Perbarui Snapshot</strong> pada dokumen tertentu bila memang ingin dokumen mengikuti data terbaru.</div>
<div class="button-row"><a class="btn btn-danger" href="{{ route('transactions.edit',[$transaction,'ack'=>1]) }}">Saya Paham, Lanjut Edit</a><a class="btn btn-ghost" href="{{ route('transactions.show',$transaction) }}">Batal</a></div>
</div>
@endsection
