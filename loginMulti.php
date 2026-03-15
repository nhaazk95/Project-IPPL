<?php
    include "config/controller.php";
    session_start();
    $lg = new Resto();

    if ($lg->sessionCheck() == "true") {
        if (@$_SESSION['level'] == "Admin")  header("location:pageAdmin.php");
        if (@$_SESSION['level'] == "Kasir")  header("location:pageKasir.php");
        if (@$_SESSION['level'] == "Owner")  header("location:pageOwner.php");
        if (@$_SESSION['level'] == "Koki")   header("location:pageKoki.php");
    }

    if (isset($_POST['btnLogin'])) {
        $username = strtolower($_POST['username']);
        $password = $_POST['password'];

        if ($response = $lg->login($username, $password)) {
            if ($response['response'] == "positive") {
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['level']    = $response['level'];

                if ($response['level'] == "Admin")       $response = $lg->redirect("pageAdmin.php");
                else if ($response['level'] == "Kasir")  $response = $lg->redirect("pageKasir.php");
                else if ($response['level'] == "Owner")  $response = $lg->redirect("pageOwner.php");
                else if ($response['level'] == "Koki")   $response = $lg->redirect("pageKoki.php");
                else if ($response['level'] == "Waiter") $response = ['response' => 'negative', 'alert' => 'Level Waiter tidak tersedia'];
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Staff — Restoran</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sweet-alert.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream:   #F7F4EE;
            --ink:     #1C1916;
            --muted:   #8A8580;
            --border:  #E2DDD6;
            --accent:  #C0512B;
            --accent2: #E8724A;
            --surface: #FFFFFF;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            background: var(--cream);
            display: flex;
            align-items: stretch;
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            flex: 0 0 44%;
            background: var(--ink);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 52px 56px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle texture rings */
        .panel-left::before {
            content: '';
            position: absolute;
            width: 480px; height: 480px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.04);
            top: -120px; right: -160px;
            pointer-events: none;
        }
        .panel-left::after {
            content: '';
            position: absolute;
            width: 320px; height: 320px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.04);
            bottom: -80px; left: -80px;
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative; z-index: 1;
        }
        .brand-dot {
            width: 28px; height: 28px;
            background: var(--accent);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-dot svg { width: 14px; height: 14px; fill: #fff; }
        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 18px;
            color: #fff;
            letter-spacing: .3px;
        }

        .panel-headline {
            position: relative; z-index: 1;
        }
        .panel-headline h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 48px;
            line-height: 1.15;
            color: #fff;
            font-weight: 400;
            margin-bottom: 20px;
        }
        .panel-headline h1 em {
            font-style: italic;
            color: var(--accent2);
        }
        .panel-headline p {
            font-size: 14px;
            line-height: 1.7;
            color: rgba(255,255,255,0.45);
            max-width: 280px;
            font-weight: 300;
        }

        .role-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative; z-index: 1;
        }
        .role-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.06);
            background: rgba(255,255,255,0.03);
            transition: background .2s;
        }
        .role-item:hover { background: rgba(255,255,255,0.06); }
        .role-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
        }
        .role-icon.r1 { background: rgba(192,81,43,0.18); }
        .role-icon.r2 { background: rgba(89,163,120,0.18); }
        .role-icon.r3 { background: rgba(91,140,199,0.18); }
        .role-icon.r4 { background: rgba(169,120,62,0.18); }
        .role-text strong {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
        }
        .role-text span {
            font-size: 11px;
            color: rgba(255,255,255,0.35);
            font-weight: 300;
        }

        /* ── RIGHT PANEL ── */
        .panel-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
            animation: fadeUp .5s cubic-bezier(.22,.68,0,1.15) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-header {
            margin-bottom: 36px;
        }
        .form-header .tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 14px;
        }
        .form-header .tag::before {
            content: '';
            width: 16px; height: 1px;
            background: var(--accent);
            display: block;
        }
        .form-header h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 32px;
            color: var(--ink);
            font-weight: 400;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        .form-header p {
            font-size: 13px;
            color: var(--muted);
            font-weight: 400;
        }

        /* Fields */
        .field {
            margin-bottom: 20px;
        }
        .field label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .field-inner {
            position: relative;
        }
        .field-inner svg.field-icon {
            position: absolute;
            left: 14px;
            top: 50%; transform: translateY(-50%);
            width: 16px; height: 16px;
            fill: var(--border);
            transition: fill .2s;
            pointer-events: none;
        }
        .field-inner input {
            width: 100%;
            padding: 13px 44px 13px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: var(--ink);
            background: var(--surface);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .field-inner input::placeholder { color: #C8C4BE; }
        .field-inner input:focus {
            border-color: var(--ink);
            box-shadow: 0 0 0 4px rgba(28,25,22,0.06);
        }
        .field-inner input:focus ~ svg.field-icon { fill: var(--ink); }
        .toggle-pass {
            position: absolute;
            right: 13px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            display: flex; align-items: center;
            padding: 4px;
        }
        .toggle-pass svg { width: 16px; height: 16px; fill: #C8C4BE; transition: fill .2s; }
        .toggle-pass:hover svg { fill: var(--ink); }

        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .remember {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer;
            font-size: 13px; color: var(--muted);
            user-select: none;
        }
        .remember input[type=checkbox] {
            width: 15px; height: 15px;
            accent-color: var(--ink);
            cursor: pointer;
        }
        .forgot {
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            transition: color .2s;
        }
        .forgot:hover { color: var(--ink); }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: var(--ink);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .5px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: background .2s, transform .15s;
            position: relative;
            overflow: hidden;
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background .2s;
        }
        .btn-submit:hover {
            background: var(--accent);
        }
        .btn-submit:active { transform: scale(0.98); }
        .btn-submit svg { width: 16px; height: 16px; fill: rgba(255,255,255,0.6); transition: transform .2s; }
        .btn-submit:hover svg { transform: translateX(3px); }

        .form-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
        }
        .form-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }
        .form-footer a:hover { text-decoration: underline; }

        /* Divider */
        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 24px 0;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--border);
        }
        .divider span {
            font-size: 11px; color: var(--muted);
            font-weight: 500; letter-spacing: .5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .panel-left {
                flex: 0 0 auto;
                padding: 32px 28px;
            }
            .panel-headline h1 { font-size: 32px; }
            .role-list { display: none; }
            .panel-right { padding: 36px 24px; }
        }
    </style>
</head>
<body>

    <!-- LEFT PANEL -->
    <div class="panel-left">
        <div class="brand">
            <div class="brand-dot">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
            </div>
            <span class="brand-name">Food</span>
        </div>

        <div class="panel-headline">
            <h1>Portal<br>Staff <em>Restoran</em></h1>
            <p>Sistem manajemen operasional terpadu untuk tim Anda. Masuk untuk memulai sesi kerja.</p>
        </div>

        <div class="role-list">
            <div class="role-item">
                <div class="role-icon r1">🛡️</div>
                <div class="role-text">
                    <strong>Admin</strong>
                    <span>Akses penuh sistem</span>
                </div>
            </div>
            <div class="role-item">
                <div class="role-icon r2">💳</div>
                <div class="role-text">
                    <strong>Kasir</strong>
                    <span>Transaksi & pembayaran</span>
                </div>
            </div>
            <div class="role-item">
                <div class="role-icon r3">👔</div>
                <div class="role-text">
                    <strong>Owner</strong>
                    <span>Laporan & analitik</span>
                </div>
            </div>
            <div class="role-item">
                <div class="role-icon r4">🍳</div>
                <div class="role-text">
                    <strong>Koki</strong>
                    <span>Antrian dapur</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="panel-right">
        <div class="form-box">
            <div class="form-header">
                <div class="tag">Staff Login</div>
                <h2>Masuk ke Akun Anda</h2>
                <p>Gunakan kredensial yang diberikan oleh administrator.</p>
            </div>

            <form action="" method="post" autocomplete="off">

                <!-- Username -->
                <div class="field">
                    <label for="usernameInput">Username</label>
                    <div class="field-inner">
                        <input type="text" id="usernameInput" name="username"
                               placeholder="Masukkan username"
                               value="<?= htmlspecialchars(@$_POST['username']) ?>"
                               required>
                        <svg class="field-icon" viewBox="0 0 24 24">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                </div>

                <!-- Password -->
                <div class="field">
                    <label for="passInput">Password</label>
                    <div class="field-inner">
                        <input type="password" id="passInput" name="password"
                               placeholder="Masukkan password"
                               required>
                        <svg class="field-icon" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.8-2.2-5-5-5S7 3.2 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.7 1.4-3.1 3.1-3.1 1.7 0 3.1 1.4 3.1 3.1v2z"/>
                        </svg>
                        <button type="button" class="toggle-pass" id="togglePass" title="Tampilkan password">
                            <svg id="eyeIcon" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-extras">
                    <label class="remember">
                        <input type="checkbox" name="remember">
                        Ingat Saya
                    </label>
                    <a href="#" class="forgot">Lupa Password?</a>
                </div>

                <button type="submit" name="btnLogin" class="btn-submit">
                    Masuk Sekarang
                    <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>
            </form>

            <div class="divider"><span>atau</span></div>

            <div class="form-footer">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
    </div>

    <script src="vendor/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.getElementById('togglePass').addEventListener('click', function () {
            var inp  = document.getElementById('passInput');
            var icon = document.getElementById('eyeIcon');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46A11.804 11.804 0 001 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
            } else {
                inp.type = 'password';
                icon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
            }
        });
    </script>
    <?php include "config/alert.php"; ?>
</body>
</html>