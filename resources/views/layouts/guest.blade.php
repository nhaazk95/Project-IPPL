<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dapur Nusantara')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --coklat:    #2c1810;
            --emas:      #c9a227;
            --emas-light:#d4b040;
            --krem:      #faf5ee;
            --krem-dark: #f0e8d8;
            --teks-muted:#7a6552;
            --orange:    #c9a227;
            --brown:     #2c1810;
            --cream-dark:#f0e8d8;
            --bottombar: 68px;
        }
        html { font-size: 14px; -webkit-font-smoothing: antialiased; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #FAFAFA;
            color: var(--coklat);
            min-height: 100vh;
        }

        /* ── Topbar pelanggan (shared) ── */
        .topbar {
            background: var(--coklat);
            height: 56px;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 0 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(44,24,16,.25);
        }
        .topbar-back {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.1rem;
            text-decoration: none;
            flex-shrink: 0;
            transition: background .2s;
        }
        .topbar-back:hover { background: rgba(255,255,255,.22); }
        .topbar-icon {
            color: var(--emas);
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .topbar-title {
            color: var(--emas);
            font-weight: 700;
            font-size: .95rem;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Fix btn-ke-menu ── */
        .btn-ke-menu {
            background: var(--emas) !important;
            color: var(--coklat) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: .5rem 2rem !important;
            font-size: .9rem !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            font-family: inherit !important;
            text-decoration: none !important;
            display: inline-block !important;
            width: fit-content !important;
        }

        /* ── Toast ── */
        #toast-container {
            position: fixed;
            bottom: 12px;
            left: 50%; transform: translateX(-50%);
            display: flex; flex-direction: column; gap: 8px;
            z-index: 9999; width: 90%; max-width: 380px;
        }
        .toast {
            padding: 10px 16px; border-radius: 10px;
            font-size: .85rem; font-weight: 600;
            background: #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,.15);
            animation: slideUp .3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
    </style>
    @stack('styles')
</head>
<body>

@yield('content')

<div id="toast-container"></div>

<script>
window.showToast = function(msg, type='success') {
    const tc = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = 'toast';
    t.style.borderLeft = '4px solid ' + (type==='success'?'#27ae60':'#e74c3c');
    t.innerHTML = (type==='success'?'✅':'❌') + ' ' + msg;
    tc.appendChild(t);
    setTimeout(() => t.remove(), 3000);
};
</script>
@stack('scripts')
</body>
</html>
