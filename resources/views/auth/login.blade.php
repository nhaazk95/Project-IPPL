@extends('layouts.guest')
@section('title', 'Login — Dapur Nusantara')

@section('content')

<div class="auth-badge">Staff Login</div>
<h1 class="auth-title">Selamat Datang</h1>
<p class="auth-subtitle">Masukkan username dan password untuk mengakses sistem</p>

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
@endif
@if(session('status'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    <div class="form-group">
        <label class="form-label">Username</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-user"></i></div>
            <input type="text" name="username" placeholder="Masukkan username"
                value="{{ old('username') }}" required autofocus autocomplete="username">
        </div>
    </div>

    <div class="form-group">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <label class="form-label" style="margin:0;">Password</label>
            <a href="{{ route('password.request') }}"
                style="font-size:12px;color:var(--gold-dark);font-weight:600;text-decoration:none;">
                Lupa Password?
            </a>
        </div>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
            <input type="password" name="password" id="passInput"
                placeholder="Masukkan password" required autocomplete="current-password">
            <button type="button" class="input-eye" onclick="togglePass('passInput','eyeIcon')">
                <i class="fa-solid fa-eye" id="eyeIcon"></i>
            </button>
        </div>
    </div>

    <div style="display:flex;align-items:center;gap:8px;margin-bottom:22px;">
        <input type="checkbox" name="remember" id="remember"
            style="width:16px;height:16px;accent-color:var(--gold);cursor:pointer;">
        <label for="remember" style="font-size:13px;color:var(--text-mid);cursor:pointer;">Ingat saya</label>
    </div>

    <button type="submit" class="btn-primary w-100" style="padding:12px;font-size:15px;" id="btnLogin">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
    </button>
</form>

<div style="display:flex;align-items:center;gap:12px;margin:20px 0 16px;">
    <div style="flex:1;height:1px;background:var(--cream-dark);"></div>
    <span style="font-size:12px;color:var(--text-light);">atau</span>
    <div style="flex:1;height:1px;background:var(--cream-dark);"></div>
</div>

<div class="auth-footer-link" style="margin-top:0;">
    Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
</div>

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnLogin');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
});
</script>
@endpush
@endsection