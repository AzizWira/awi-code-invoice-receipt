@extends('layouts.app')
@section('title',$document->document_number)
@section('page_title',strtoupper($document->type).' '.$document->document_number)
@section('eyebrow','Dokumen')
@section('content')
<div class="document-toolbar">
<div><span class="badge {{ $document->isActive()?'success':'danger' }}">Link {{ $document->isActive()?'Aktif':'Dicabut' }}</span><p class="small muted" style="margin:6px 0 0">Snapshot dibuat {{ $document->generated_at?->format('d M Y H:i') }}. Token berakhir ••••{{ $document->public_token_last4 }}</p></div>
<div class="button-row">
@if($document->isActive() && $publicLink)<button type="button" class="btn btn-ghost" data-copy="{{ $publicLink }}">Copy Link</button><a class="btn btn-ghost" href="{{ $publicLink }}" target="_blank" rel="noopener">Buka Publik</a><a class="btn btn-primary" href="{{ route('documents.whatsapp',$document) }}">WhatsApp</a>@endif
<a class="btn btn-ghost" href="{{ route('documents.pdf',$document) }}">Download PDF</a>
</div></div>
@include('documents._paper')
<div class="card card-pad" style="margin-top:18px"><div class="section-title"><div><h2>Kontrol Dokumen</h2><p>Dokumen memakai snapshot agar data yang sudah dibagikan tidak berubah diam-diam.</p></div></div><div class="button-row">
<form method="POST" action="{{ route('documents.snapshot',$document) }}" data-submit-lock>@csrf<button class="btn btn-ghost" data-confirm="Perbarui snapshot dokumen menggunakan data transaksi terbaru? Dokumen yang dibagikan akan berubah setelah proses ini.">Perbarui Snapshot</button></form>
<form method="POST" action="{{ route('documents.token',$document) }}" data-submit-lock>@csrf<button class="btn btn-soft" data-confirm="Buat link publik baru? Link lama langsung tidak berlaku.">Regenerate Link</button></form>
@if($document->isActive())<form method="POST" action="{{ route('documents.revoke',$document) }}" data-submit-lock>@csrf<button class="btn btn-danger" data-confirm="Cabut akses publik dokumen ini?">Cabut Link Publik</button></form>@endif
<a class="btn btn-ghost" href="{{ route('transactions.show',$document->transaction) }}">Kembali ke Transaksi</a>
</div></div>
@endsection
