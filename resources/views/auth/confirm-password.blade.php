@extends('layouts.auth')
@section('title', 'Konfirmasi Password — Dapur Nusantara')

@section('content')

<div style="text-align:center;margin-bottom:20px;">
    <div style="width:60px;height:60px;border-radius:50%;background:var(--brown);
        display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:26px;color:var(--gold);">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
    <div class="auth-badge" style="justify-content:center;">Konfirmasi Keamanan</div>
    <h1 class="auth-title" style="font-size:21px;margin-top:6px;">Verifikasi Password</h1>
    <p class="auth-subtitle">Halaman ini memerlukan konfirmasi password untuk melanjutkan.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div class="form-group">
        <label class="form-label">Password Saat Ini</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
            <input type="password" name="password" id="passConfirm"
                placeholder="Masukkan password Anda" required autofocus>
            <button type="button" class="input-eye" onclick="togglePass('passConfirm','eyeConfirm')">
                <i class="fa-solid fa-eye" id="eyeConfirm"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-primary w-100" style="padding:12px;font-size:15px;">
        <i class="fa-solid fa-shield-check"></i> Konfirmasi
    </button>
</form>

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text'; icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password'; icon.className = 'fa-solid fa-eye';
    }
}
</script>
@endpush
@endsection