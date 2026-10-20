<!doctype html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\Setting::value('brand_name','Awi Code') }}</title>
    <link rel="icon" href="{{ asset(\App\Models\Setting::value('logo_path','brand/awi-code-logo.jpg')) }}">
    <script>
        (() => {
            const saved = localStorage.getItem('awi-theme') || 'light';
            document.documentElement.dataset.theme = saved === 'dark' ? 'dark' : 'light';
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}?v=1.0.4">
    @stack('head')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand-block">
            <img src="{{ asset(\App\Models\Setting::value('logo_path','brand/awi-code-logo.jpg')) }}" alt="Awi Code" class="brand-logo">
            <div>
                <strong>{{ \App\Models\Setting::value('brand_name','Awi Code') }}</strong>
                <span>Invoice & Receipt</span>
            </div>
        </div>

        <button type="button" class="sidebar-close" data-sidebar-close-button aria-label="Tutup navigasi">✕</button>
        <nav class="side-nav" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') || request()->routeIs('payments.*') ? 'active' : '' }}">Transaksi</a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Rekap Bulanan / Tahunan</a>
            <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.*') ? 'active' : '' }}">Invoice / Receipt</a>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customer</a>
            <a href="{{ route('service-types.index') }}" class="{{ request()->routeIs('service-types.*') ? 'active' : '' }}">Jenis Jasa</a>
            <a href="{{ route('settings.edit') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">Settings</a>
        </nav>

        <div class="sidebar-bottom">
            <div class="theme-row">
                <span>Tema</span>
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema">
                    <span class="theme-toggle-dot"></span>
                    <span data-theme-label>Light</span>
                </button>
            </div>
            <form method="POST" action="{{ route('logout') }}" data-submit-lock>
                @csrf
                <button type="submit" class="btn btn-ghost btn-block">Logout</button>
            </form>
        </div>
    </aside>

    <div class="sidebar-backdrop" data-sidebar-close></div>

    <main class="main-area">
        <header class="topbar">
            <button type="button" class="menu-button" data-sidebar-open aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">☰</button>
            <div class="topbar-title">
                <span>@yield('eyebrow','Awi Code')</span>
                <strong>@yield('page_title','Dashboard')</strong>
            </div>
            <div class="topbar-actions">@yield('top_actions')</div>
        </header>

        <section class="content-wrap">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('info')) <div class="alert alert-info">{{ session('info') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Ada data yang perlu diperbaiki.</strong>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </section>
    </main>
</div>
<script src="{{ asset('assets/app.js') }}?v=1.0.4" defer></script>
@stack('scripts')
</body>
</html>
