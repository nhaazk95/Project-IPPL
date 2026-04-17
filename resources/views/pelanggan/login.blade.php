<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Login Pelanggan — Dapur Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --coklat: #2c1810;
            --emas: #c9a227;
            --krem: #faf5ee;
        }
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh;
            background: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1400&q=80') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
        }
        .login-card {
            background: #fff;
            border-radius: 24px;
            padding: 2rem 2rem 2.5rem;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }
        .logo-wrap {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-wrap img {
            width: 90px; height: 90px;
            object-fit: contain;
        }
        .logo-placeholder {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: var(--krem);
            border: 3px solid var(--emas);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto;
            font-size: 2rem;
            color: var(--coklat);
        }
        .label-section {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: var(--emas);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-bottom: .5rem;
        }
        .label-section::before {
            content: '';
            width: 24px; height: 2px;
            background: var(--emas);
            border-radius: 2px;
        }
        h2 { color: var(--coklat); font-weight: 800; font-size: 1.6rem; line-height: 1.3; margin-bottom: .4rem; }
        .subtitle { color: #8a7060; font-size: .875rem; margin-bottom: 1.75rem; }

        .input-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--emas);
            margin-bottom: .4rem;
        }
        .input-group-custom {
            background: var(--krem);
            border-radius: 12px;
            border: 1.5px solid #e8ddd0;
            display: flex;
            align-items: center;
            padding: 0 .9rem;
            transition: border-color .2s;
            margin-bottom: 1.25rem;
        }
        .input-group-custom:focus-within {
            border-color: var(--emas);
        }
        .input-group-custom i {
            color: #b0998a;
            font-size: 1rem;
            margin-right: .6rem;
            flex-shrink: 0;
        }
        .input-group-custom input {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            padding: .8rem 0;
            font-size: .95rem;
            color: var(--coklat);
            font-family: inherit;
        }
        .input-group-custom input::placeholder { color: #b0998a; }

        .btn-masuk {
            background: var(--coklat);
            color: #fff;
            border: none;
            border-radius: 14px;
            width: 100%;
            padding: .9rem;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .02em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            cursor: pointer;
            transition: background .2s, transform .1s;
            margin-top: .5rem;
        }
        .btn-masuk:hover { background: #1a0e08; }
        .btn-masuk:active { transform: scale(.98); }
        .btn-masuk:disabled { background: #ccc; cursor: not-allowed; }

        /* Modal sukses / error */
        .modal-icon-wrap {
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .modal-icon-wrap.success { border: 3px solid #27ae60; }
        .modal-icon-wrap.error   { border: 3px solid #e74c3c; }
    </style>
</head>
<body>

<div class="login-card">
    {{-- Logo --}}
    <div class="logo-wrap">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        @else
            <div class="logo-placeholder">
                <i class="bi bi-shop"></i>
            </div>
        @endif
    </div>

    <div class="label-section">Login Pelanggan</div>
    <h2>Masuk untuk memesan<br>makanan Anda</h2>
    <p class="subtitle">Gunakan nama dan nomor meja untuk mulai memesan makanan</p>

    <form id="formLogin" action="{{ route('pelanggan.masuk') }}" method="POST">
        @csrf

        <div class="input-label">Nama Lengkap</div>
        <div class="input-group-custom">
            <i class="bi bi-person-fill"></i>
            <input type="text" name="name_pelanggan" placeholder="nama"
                value="{{ old('name_pelanggan') }}" required autocomplete="off">
        </div>

        <div class="input-label">Nomor Meja</div>
        <div class="input-group-custom">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <input type="number" name="no_meja" placeholder="no meja"
                value="{{ old('no_meja') }}" required min="1">
        </div>

        <button type="submit" class="btn-masuk" id="btnMasuk">
            Masuk Sekarang <i class="bi bi-arrow-right"></i>
        </button>
    </form>
</div>

{{-- Modal Berhasil --}}
@if(session('login_success'))
<div class="modal fade" id="modalBerhasil" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:340px;margin:auto;">
        <div class="modal-content border-0" style="border-radius:20px;text-align:center;padding:2rem;">
            <div class="modal-icon-wrap success">
                <i class="bi bi-check-lg" style="font-size:2.5rem;color:#27ae60;"></i>
            </div>
            <h5 class="fw-bold mb-1">Berhasil</h5>
            <p class="text-muted">Mengalihkan...</p>
        </div>
    </div>
</div>
@endif

{{-- Modal Error --}}
@if($errors->any() || session('error'))
<div class="modal fade" id="modalError" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:340px;margin:auto;">
        <div class="modal-content border-0" style="border-radius:20px;text-align:center;padding:2rem;">
            <div class="modal-icon-wrap error">
                <i class="bi bi-x-lg" style="font-size:2.5rem;color:#e74c3c;"></i>
            </div>
            <h5 class="fw-bold mb-1">Error</h5>
            <p class="text-muted mb-3">
                @if(session('error'))
                    {{ session('error') }}
                @else
                    {{ $errors->first() }}
                @endif
            </p>
            <button class="btn-masuk" style="background:#c9a227;color:#2c1810;border-radius:10px;padding:.5rem;"
                data-bs-dismiss="modal">OK</button>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Show modal berhasil
    @if(session('login_success'))
        new bootstrap.Modal(document.getElementById('modalBerhasil')).show();
        setTimeout(() => window.location.href = '{{ route("pelanggan.beranda") }}', 1800);
    @endif

    // Show modal error
    @if($errors->any() || session('error'))
        new bootstrap.Modal(document.getElementById('modalError')).show();
    @endif

    // Loading state on submit
    document.getElementById('formLogin').addEventListener('submit', function() {
        const btn = document.getElementById('btnMasuk');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    });
</script>
</body>
</html>