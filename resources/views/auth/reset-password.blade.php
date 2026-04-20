@extends('layouts.auth')
@section('title', 'Reset Password — Dapur Nusantara')

@section('content')

<div style="text-align:center;margin-bottom:18px;">
    <div style="width:60px;height:60px;border-radius:50%;background:var(--brown);
        display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:26px;color:var(--gold);">
        <i class="fa-solid fa-key"></i>
    </div>
    <div class="auth-badge" style="justify-content:center;">Buat Password Baru</div>
</div>

<div class="step-indicator">
    <div class="step inactive" style="background:var(--gold);color:var(--brown);">✓</div>
    <div class="step-line" style="background:var(--gold);"></div>
    <div class="step active">2</div>
</div>

@if($errors->any())
    <div class="alert alert-danger" style="margin-top:14px;">
        <i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}" style="margin-top:16px;">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-envelope"></i></div>
            <input type="email" name="email" placeholder="Email terdaftar"
                value="{{ old('email') }}" required autofocus>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Password Baru</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
            <input type="password" name="password" id="pass1"
                placeholder="Min. 8 karakter" required>
            <button type="button" class="input-eye" onclick="togglePass('pass1','eye1')">
                <i class="fa-solid fa-eye-slash" id="eye1"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Konfirmasi Password</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-lock-open"></i></div>
            <input type="password" name="password_confirmation" id="pass2"
                placeholder="Ulangi password baru" required>
            <button type="button" class="input-eye" onclick="togglePass('pass2','eye2')">
                <i class="fa-solid fa-eye-slash" id="eye2"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-primary w-100" style="padding:12px;font-size:15px;">
        <i class="fa-solid fa-check"></i> Simpan Password Baru
    </button>
</form>

<div class="auth-footer-link">
    <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left" style="font-size:11px;margin-right:4px;"></i> Kembali ke Login</a>
</div>

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text'; icon.className = 'fa-solid fa-eye';
    } else {
        input.type = 'password'; icon.className = 'fa-solid fa-eye-slash';
    }
}
</script>
@endpush
@endsection