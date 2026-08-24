<!doctype html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Awi Code</title>
    <script>(()=>{const t=localStorage.getItem('awi-theme')||'light';document.documentElement.dataset.theme=t==='dark'?'dark':'light';})();</script>
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}?v=1.0.3">
</head>
<body class="auth-body">
    <button type="button" class="auth-theme theme-toggle" data-theme-toggle><span class="theme-toggle-dot"></span><span data-theme-label>Light</span></button>
    @yield('content')
    <script src="{{ asset('assets/app.js') }}?v=1.0.3" defer></script>
</body>
</html>
