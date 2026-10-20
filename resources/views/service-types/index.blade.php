@extends('layouts.app')
@section('title','Jenis Jasa')
@section('page_title','Jenis Jasa')
@section('eyebrow','Master Data')
@section('content')
<div class="page-grid">
<div class="card">
<div class="card-header"><h2>Master Jenis Jasa</h2><span class="muted small">Dipakai saat membuat transaksi</span></div>
<div class="table-wrap"><table class="data-table responsive-cards"><thead><tr><th>Nama</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($serviceTypes as $service)
<tr><td data-label="Nama"><form method="POST" action="{{ route('service-types.update',$service) }}" class="button-row" data-submit-lock>@csrf @method('PUT')<input name="name" value="{{ $service->name }}" style="max-width:260px"><label class="check-row"><input type="checkbox" name="is_active" value="1" @checked($service->is_active)> Aktif</label><button class="btn btn-ghost">Simpan</button></form></td><td data-label="Status"><span class="badge {{ $service->is_active?'success':'danger' }}">{{ $service->is_active?'Aktif':'Nonaktif' }}</span></td><td data-label="Aksi">@if(strtolower($service->name)!=='lainnya')<form method="POST" action="{{ route('service-types.destroy',$service) }}" data-submit-lock>@csrf @method('DELETE')<button class="btn btn-danger" data-confirm="Nonaktifkan jenis jasa ini?">Nonaktifkan</button></form>@endif</td></tr>
@endforeach
</tbody></table></div>
</div>
<aside><div class="card card-pad"><h3 style="margin-top:0">Tambah Jenis Jasa</h3><form method="POST" action="{{ route('service-types.store') }}" data-submit-lock class="stack-form">@csrf<label class="field"><span>Nama jasa</span><input name="name" placeholder="Contoh: Integrasi API" required></label><button class="btn btn-primary">Tambah</button></form><p class="small muted" style="margin-bottom:0">Opsi <strong>Lainnya</strong> tetap tersedia untuk input custom saat transaksi dibuat.</p></div></aside>
</div>
@endsection
