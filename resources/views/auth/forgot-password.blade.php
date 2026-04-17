@extends('layouts.guest')
@section('title', 'Lupa Password — Dapur Nusantara')

@section('content')

<div style="text-align:center;margin-bottom:18px;">
    <div style="width:60px;height:60px;border-radius:50%;background:var(--brown);
        display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:26px;color:var(--gold);">
        <i class="fa-solid fa-lock"></i>
    </div>
    <div class="auth-badge" style="justify-content:center;">Reset Password</div>
    <p style="font-size:13px;color:var(--text-light);margin-top:8px;line-height:1.6;">
        Masukkan email akun kamu.<br>Kami akan kirim link reset password.
    </p>
</div>

<div class="step-indicator">
    <div class="step active">1</div>
    <div class="step-line"></div>
    <div class="step inactive">2</div>
</div>

@if(session('status'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label class="form-label">Alamat Email</label>
        <div class="input-group-auth">
            <div class="input-icon"><i class="fa-solid fa-envelope"></i></div>
            <input type="email" name="email" placeholder="Email terdaftar"
                value="{{ old('email') }}" required autofocus>
        </div>
        <p style="font-size:12px;color:var(--text-light);margin-top:6px;">
            Gunakan email yang terdaftar saat mendaftar. Jika lupa, hubungi administrator.
        </p>
    </div>

    <button type="submit" class="btn-primary w-100" style="padding:12px;font-size:15px;">
        <i class="fa-solid fa-paper-plane"></i> Kirim Link Reset
    </button>
</form>

<div style="display:flex;align-items:center;gap:12px;margin:20px 0 14px;">
    <div style="flex:1;height:1px;background:var(--cream-dark);"></div>
    <span style="font-size:12px;color:var(--text-light);">atau</span>
    <div style="flex:1;height:1px;background:var(--cream-dark);"></div>
</div>

<div class="auth-footer-link" style="margin-top:0;">
    Ingat password? <a href="{{ route('login') }}">Masuk Sekarang</a>
</div>

@endsection