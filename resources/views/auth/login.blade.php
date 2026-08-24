@extends('layouts.auth')
@section('content')
<div class="auth-card">
    <div class="auth-logo-wrap"><img src="{{ asset('brand/awi-code-logo.jpg') }}" alt="Awi Code"></div>
    <div class="auth-heading">
        <span>Internal Workspace</span>
        <h1>Awi Code</h1>
        <p>Kelola transaksi, Invoice, dan Receipt dari satu tempat.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" data-submit-lock class="stack-form">
        @csrf
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="owner@awicode.com">
        </label>
        <label class="field">
            <span>Password</span>
            <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••••••">
        </label>
        <label class="check-row"><input type="checkbox" name="remember" value="1"> <span>Ingat saya di perangkat ini</span></label>
        <button class="btn btn-primary btn-block" type="submit">Masuk ke Dashboard</button>
    </form>
    <p class="auth-note">Tidak ada registrasi publik. Akun owner dibuat dari konfigurasi server.</p>
</div>
@endsection
