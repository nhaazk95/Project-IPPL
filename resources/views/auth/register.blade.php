@extends('layouts.guest')
@section('title', 'Register — Dapur Nusantara')

@section('content')

<div class="auth-badge">Buat Akun Baru</div>
<h1 class="auth-title" style="font-size:22px;">Daftar ke Sistem</h1>
<p class="auth-subtitle">Isi data di bawah untuk mendaftar sebagai staf</p>

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-user"></i></div>
            <input type="text" name="name" placeholder="Nama lengkap" value="{{ old('name') }}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-envelope"></i></div>
            <input type="email" name="email" placeholder="Alamat email" value="{{ old('email') }}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Username</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-at"></i></div>
            <input type="text" name="username" placeholder="Username unik" value="{{ old('username') }}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-lock"></i></div>
            <input type="password" name="password" id="pass1" placeholder="Min. 8 karakter" required>
            <button type="button" class="input-eye" onclick="togglePass('pass1','eye1')">
                <i class="fa-solid fa-eye-slash" id="eye1"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Konfirmasi Password</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-lock-open"></i></div>
            <input type="password" name="password_confirmation" id="pass2" placeholder="Ulangi password" required>
            <button type="button" class="input-eye" onclick="togglePass('pass2','eye2')">
                <i class="fa-solid fa-eye-slash" id="eye2"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Level / Role</label>
        <select name="level_id" class="form-control" required>
            <option value="">-- Pilih Level --</option>
            @foreach($levels as $level)
                <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                    {{ $level->nama_level }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn-primary w-100" style="padding:12px;font-size:15px;">
        <i class="fa-solid fa-user-plus"></i> Buat Akun
    </button>
</form>

<div class="auth-footer-link">
    Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a>
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