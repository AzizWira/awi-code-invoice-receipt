@php
$t=$snapshot['transaction'];$c=$snapshot['customer'];$b=$snapshot['branding'];$items=$snapshot['items']??[];$payments=$snapshot['payments']??[];
$isReceipt=$document->type==='receipt';
@endphp
<article class="paper">
<header class="paper-header">
<div class="paper-brand">@if($logoDataUri)<img src="{{ $logoDataUri }}" class="paper-logo" alt="Logo">@endif<div><h1>{{ $b['brand_name'] ?? 'Awi Code' }}</h1><p>@if(!empty($b['business_email'])){{ $b['business_email'] }}@endif @if(!empty($b['business_whatsapp'])) · {{ $b['business_whatsapp'] }}@endif</p>@if(!empty($b['business_address']))<p>{{ $b['business_address'] }}</p>@endif</div></div>
<div class="paper-type"><strong>{{ $isReceipt ? 'RECEIPT' : 'INVOICE' }}</strong><span>{{ $document->document_number }}</span></div>
</header>
<section class="doc-info-grid">
<div class="doc-info-block"><span>Ditujukan Kepada</span><strong>{{ $c['company_name'] ?: $c['name'] }}</strong>@if($c['company_name'])<p>PIC: {{ $c['name'] }}</p>@endif @if(!empty($c['email']))<p>{{ $c['email'] }}</p>@endif</div>
<div class="doc-info-block"><span>Detail Transaksi</span><strong>{{ $t['project_name'] }}</strong><p>{{ $t['service_type'] }}</p><p>Tanggal: {{ \Illuminate\Support\Carbon::parse($t['transaction_date'])->translatedFormat('d F Y') }}</p></div>
</section>
@if(!empty($t['description']))<div class="terbilang-box" style="border-left-color:#934d2b"><span>Deskripsi Pekerjaan</span>{{ $t['description'] }}</div>@endif
<div class="paper-table-wrap"><table class="paper-table"><thead><tr><th>Item</th><th class="center">Qty</th><th>Satuan</th><th class="right">Harga</th><th class="right">Subtotal</th></tr></thead><tbody>
@foreach($items as $item)<tr><td><strong>{{ $item['name'] }}</strong>@if(!empty($item['description']))<div style="color:#7c6c64;margin-top:2px">{{ $item['description'] }}</div>@endif</td><td class="center">{{ rtrim(rtrim(number_format($item['quantity'],2,'.',''),'0'),'.') }}</td><td>{{ $item['unit'] }}</td><td class="right">{{ \App\Support\Money::rupiah($item['unit_price']) }}</td><td class="right"><strong>{{ \App\Support\Money::rupiah($item['line_total']) }}</strong></td></tr>@endforeach
</tbody></table></div>
<div class="paper-summary">
<div class="summary-line"><span>Subtotal</span><strong>{{ \App\Support\Money::rupiah($t['subtotal']) }}</strong></div>
@if(($t['discount_amount']??0)>0)<div class="summary-line"><span>Diskon @if(($t['discount_type']??null)==='percent')({{ $t['discount_value'] }}%)@endif</span><strong>- {{ \App\Support\Money::rupiah($t['discount_amount']) }}</strong></div>@endif
<div class="summary-line total"><span>Total</span><strong>{{ \App\Support\Money::rupiah($t['grand_total']) }}</strong></div>
@if(!$isReceipt)<div class="summary-line"><span>Sudah Dibayar</span><strong>{{ \App\Support\Money::rupiah($t['total_paid']) }}</strong></div><div class="summary-line" style="color:#ef3012;font-weight:800"><span>Sisa Pembayaran</span><strong>{{ \App\Support\Money::rupiah($t['remaining_amount']) }}</strong></div>@endif
@if(!$isReceipt)<div class="summary-line"><span>Status Pembayaran</span><strong>{{ $t['payment_status']==='sebagian' ? 'Dibayar Sebagian' : ucwords(str_replace('_',' ',$t['payment_status'])) }}</strong></div>@endif
</div>
<div class="terbilang-box"><span>Terbilang</span>{{ $terbilang }}</div>
@if(count($payments))<div class="payment-list"><h3>{{ $isReceipt ? 'Metode Pembayaran' : 'Pembayaran yang Telah Diterima' }}</h3>@foreach($payments as $payment)<span class="payment-chip"><strong>{{ $payment['label'] ?: 'Pembayaran' }}</strong> · {{ $payment['method'] }} · {{ \App\Support\Money::rupiah($payment['amount']) }}</span>@endforeach</div>@endif
@if($isReceipt)<div class="paid-stamp">LUNAS</div>@endif
<footer class="paper-footer"><div class="footer-note">{{ $isReceipt ? ($b['receipt_footer'] ?? '') : ($b['invoice_footer'] ?? '') }}</div><div class="signature-box"><div style="font-size:10px;color:#7c6c64">Hormat kami,</div>@if($signatureDataUri)<img src="{{ $signatureDataUri }}" alt="Tanda tangan">@else<div style="height:58px"></div>@endif<div class="signature-line">{{ $b['signer_name'] ?: ($b['brand_name'] ?? 'Awi Code') }}</div></div></footer>
</article>
