<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow,noarchive">
<title>{{ $document->document_number }} — {{ data_get($snapshot,'branding.brand_name','Awi Code') }}</title>
<link rel="icon" href="{{ asset('brand/awi-code-logo.jpg') }}">
<link rel="stylesheet" href="{{ asset('assets/app.css') }}?v=1.0.3">
</head>
<body class="public-doc-body">
<div class="document-shell">
<div class="document-toolbar"><div><strong>{{ data_get($snapshot,'branding.brand_name','Awi Code') }}</strong><div class="small muted">Dokumen transaksi digital</div></div><div class="button-row"><button class="btn btn-ghost" onclick="window.print()">Print</button><a class="btn btn-primary" href="{{ route('public.documents.pdf',['token'=>$document->publicToken()]) }}">Download PDF</a></div></div>
@include('documents._paper')
</div>
</body>
</html>
