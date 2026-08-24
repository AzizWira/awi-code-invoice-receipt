@extends('layouts.app')
@section('title','Detail Transaksi')
@section('page_title','Detail Transaksi')
@section('eyebrow','Project & Pembayaran')
@section('top_actions')<a class="btn btn-ghost" href="{{ route('transactions.edit',$transaction) }}">Edit</a>@endsection
@section('content')
@php
$invoice=$transaction->documents->firstWhere('type','invoice');
$receipt=$transaction->documents->firstWhere('type','receipt');
@endphp
<div class="page-grid">
<div class="stack-form">
<div class="card card-pad">
<div class="section-title"><div><span class="eyebrow">{{ $transaction->internal_code }}</span><h1>{{ $transaction->project_name }}</h1><p>{{ $transaction->customer->company_name ?: $transaction->customer->name }} · {{ $transaction->serviceLabel() }}</p></div><div class="button-row"><span class="badge {{ $transaction->transaction_status==='final'?'success':($transaction->transaction_status==='cancelled'?'danger':'warning') }}">{{ $transaction->transactionStatusLabel() }}</span><span class="badge {{ $transaction->payment_status==='lunas'?'success':($transaction->payment_status==='sebagian'?'warning':'') }}">{{ $transaction->paymentStatusLabel() }}</span></div></div>
<dl class="detail-list"><dt>Customer / PIC</dt><dd>{{ $transaction->customer->name }}</dd><dt>Perusahaan</dt><dd>{{ $transaction->customer->company_name ?: '—' }}</dd><dt>Tanggal</dt><dd>{{ $transaction->transaction_date?->format('d M Y') }}</dd><dt>Jenis Jasa</dt><dd>{{ $transaction->serviceLabel() }}</dd><dt>Deskripsi</dt><dd>{{ $transaction->description ?: '—' }}</dd><dt>Status Pengerjaan</dt><dd>{{ $transaction->workStatusLabel() }} · {{ $transaction->progress_percent }}%</dd><dt>Dibuat</dt><dd>{{ $transaction->created_at?->format('d M Y H:i') }}</dd><dt>Terakhir Diubah</dt><dd>{{ $transaction->updated_at?->format('d M Y H:i') }}</dd></dl>
<div style="margin-top:14px"><div class="progress-track"><div class="progress-fill" style="width:{{ $transaction->progress_percent }}%"></div></div></div>
</div>

<div class="card"><div class="card-header"><h2>Rincian Jasa</h2><span class="muted small">{{ $transaction->items->count() }} item</span></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Item</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>@foreach($transaction->items as $item)<tr><td><div class="table-title">{{ $item->name }}</div><div class="table-sub">{{ $item->description ?: '—' }}</div></td><td>{{ rtrim(rtrim(number_format((float)$item->quantity,2,'.',''),'0'),'.') }} {{ $item->unit }}</td><td class="money">{{ \App\Support\Money::rupiah($item->unit_price) }}</td><td class="money">{{ \App\Support\Money::rupiah($item->line_total) }}</td></tr>@endforeach</tbody></table></div><div class="card-pad"><div class="summary-box" style="margin-left:auto;max-width:420px"><div class="summary-line"><span>Subtotal</span><strong>{{ \App\Support\Money::rupiah($transaction->subtotal) }}</strong></div>@if((float)$transaction->discount_amount > 0)<div class="summary-line"><span>Diskon @if($transaction->discount_type==='percent')({{ (float)$transaction->discount_value }}%)@endif</span><strong>- {{ \App\Support\Money::rupiah($transaction->discount_amount) }}</strong></div>@endif<div class="summary-line total"><span>Total</span><strong>{{ \App\Support\Money::rupiah($transaction->grand_total) }}</strong></div><div class="summary-line"><span>Dibayar</span><strong>{{ \App\Support\Money::rupiah($transaction->total_paid) }}</strong></div><div class="summary-line remaining"><span>Sisa</span><strong>{{ \App\Support\Money::rupiah($transaction->remaining_amount) }}</strong></div></div></div></div>

<div class="card"><div class="card-header"><h2>Pembayaran Tercatat</h2><span class="badge {{ $transaction->payment_status==='lunas'?'success':($transaction->payment_status==='sebagian'?'warning':'') }}">{{ $transaction->paymentStatusLabel() }}</span></div>
@if($transaction->payments->isEmpty())<div class="empty-state"><strong>Belum ada pembayaran</strong><span>Catat DP atau pembayaran yang sudah Anda terima di luar sistem.</span></div>@else<div class="table-wrap"><table class="data-table"><thead><tr><th>Label</th><th>Metode</th><th>Nominal</th><th>Catatan</th><th></th></tr></thead><tbody>@foreach($transaction->payments as $payment)<tr><td>{{ $payment->label ?: 'Pembayaran' }}</td><td>{{ $payment->methodLabel() }}</td><td class="money">{{ \App\Support\Money::rupiah($payment->amount) }}</td><td>{{ $payment->note ?: '—' }}</td><td><div class="button-row"><a class="btn btn-ghost" href="{{ route('payments.edit',[$transaction,$payment]) }}">Edit</a><form method="POST" action="{{ route('payments.destroy',[$transaction,$payment]) }}" data-submit-lock>@csrf @method('DELETE')@if($receipt)<input type="hidden" name="confirm_finalized" value="1">@endif<button class="btn btn-danger" data-confirm="Hapus pembayaran ini? Status transaksi akan dihitung ulang.">Hapus</button></form></div></td></tr>@endforeach</tbody></table></div>@endif
</div>

<div class="card"><div class="card-header"><h2>Riwayat Aktivitas</h2><span class="muted small">15 aktivitas terakhir</span></div><div class="card-pad timeline">@forelse($logs as $log)<div class="timeline-item"><strong>{{ $log->actionLabel() }}</strong><span>{{ $log->created_at?->format('d M Y H:i') }}</span></div>@empty<div class="muted small">Belum ada aktivitas.</div>@endforelse</div></div>
</div>

<aside class="stack-form">
<div class="card card-pad stack-form"><div><span class="eyebrow">Dokumen</span><h3 style="margin:5px 0">Invoice / Receipt</h3></div>
@if(!$transaction->isFinal())<div class="alert alert-warning">Finalkan transaksi terlebih dahulu untuk membuat dokumen.</div>@endif
@if($invoice)<a class="btn btn-ghost btn-block" href="{{ route('documents.show',$invoice) }}">Lihat Invoice</a>@else<form method="POST" action="{{ route('documents.generate',[$transaction,'invoice']) }}" data-submit-lock>@csrf<button class="btn btn-primary btn-block" @disabled(!$transaction->isFinal())>Generate Invoice</button></form>@endif
@if($receipt)<a class="btn btn-success btn-block" href="{{ route('documents.show',$receipt) }}">Lihat Receipt</a>@else<form method="POST" action="{{ route('documents.generate',[$transaction,'receipt']) }}" data-submit-lock>@csrf<button class="btn btn-success btn-block" @disabled(!$transaction->isFinal() || !$transaction->isPaid())>Generate Receipt</button></form>@if(!$transaction->isPaid())<p class="small muted" style="margin:0">Receipt aktif setelah total pembayaran mencapai {{ \App\Support\Money::rupiah($transaction->grand_total) }}.</p>@endif @endif
</div>

<div class="card card-pad stack-form"><div><span class="eyebrow">Pembayaran</span><h3 style="margin:5px 0">Catat Pembayaran</h3></div><form method="POST" action="{{ route('payments.store',$transaction) }}" data-submit-lock class="stack-form">@csrf
<label class="field"><span>Label</span><input name="label" placeholder="DP / Pelunasan / Pembayaran"></label>
<label class="field"><span>Metode *</span><select name="method" data-payment-method required>@foreach(['DANA','GoPay','OVO','Bank Jago','BRI','QRIS','ShopeePay','Allo Bank','Lainnya'] as $m)<option>{{ $m }}</option>@endforeach</select></label>
<label class="field" data-other-payment hidden><span>Metode Lainnya *</span><input name="custom_method" data-required-when-visible placeholder="Nama metode"></label>
<label class="field"><span>DP / Pembayaran dalam persen</span><input type="number" min="0" max="100" step="1" data-payment-percent data-total="{{ (float)$transaction->grand_total }}" placeholder="Contoh: 50"><small>Opsional. Mengisi ini akan menghitung nominal di bawah.</small></label>
<label class="field"><span>Nominal *</span><input type="number" min="1" step="1" name="amount" data-payment-amount required placeholder="0"></label>
<label class="field"><span>Catatan</span><textarea name="note" placeholder="Opsional"></textarea></label>
@if($receipt)<label class="check-row"><input type="checkbox" name="confirm_finalized" value="1"> Saya paham Receipt sudah pernah dibuat.</label>@endif
<label class="check-row"><input type="checkbox" name="confirm_overpayment" value="1"> Izinkan nominal melebihi sisa pembayaran.</label>
<button class="btn btn-primary">Catat Pembayaran</button></form></div>

@if($transaction->internal_note)<div class="card card-pad"><span class="eyebrow">Internal</span><h3>Catatan</h3><p class="muted" style="white-space:pre-wrap">{{ $transaction->internal_note }}</p></div>@endif
<div class="card card-pad danger-zone"><h3 style="margin-top:0">Aksi Sensitif</h3><div class="stack-form"><a class="btn btn-ghost btn-block" href="{{ route('transactions.edit',$transaction) }}">Edit Transaksi</a>@if($transaction->transaction_status!=='cancelled')<form method="POST" action="{{ route('transactions.cancel',$transaction) }}" data-submit-lock>@csrf<button class="btn btn-danger btn-block" data-confirm="Batalkan transaksi ini? Dokumen yang sudah dibuat tidak otomatis dihapus.">Batalkan Transaksi</button></form>@endif
@php
$deleteHasRelated = $transaction->payments->isNotEmpty() || $transaction->documents->isNotEmpty();
$deleteWarning = $deleteHasRelated
    ? 'PERINGATAN: Hapus permanen transaksi ini? Seluruh rincian jasa, '.$transaction->payments->count().' pembayaran, '.$transaction->documents->count().' dokumen Invoice/Receipt, dan riwayat aktivitas terkait akan ikut dihapus. Tindakan ini tidak dapat dibatalkan.'
    : 'Hapus permanen transaksi ini beserta seluruh rincian jasanya? Tindakan ini tidak dapat dibatalkan.';
@endphp
<form method="POST" action="{{ route('transactions.destroy',$transaction) }}" data-submit-lock>@csrf @method('DELETE')<input type="hidden" name="confirm_delete" value="1"><button class="btn btn-danger btn-block" data-confirm="{{ $deleteWarning }}">Hapus Transaksi Permanen</button></form>
</div></div>
</aside>
</div>
@endsection
