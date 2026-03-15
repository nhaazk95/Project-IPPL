<?php
    include "config/controller.php";
    session_start();
    $lg            = new resto();
    $table         = "tb_user";
    $autokode      = $lg->autokode($table, "kd_user", "US");
    $autokode2     = $lg->autokode("tb_pelanggan", "kd_pelanggan", "PG");
    $autokodeOrder = $lg->autokode("tb_order", "kd_order", "TR");
    $date          = date("Y-m-d");

    if ($lg->sessionCheck() == "true") {
        if (@$_SESSION['level'] == "Pelanggan") {
            header("location:pagePelanggan.php");
            exit();
        }
    }

    if (isset($_POST['btnLogin'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if ($username == "" || $password == "") {
            $response = ['response' => 'negative', 'alert' => 'Lengkapi Field !!!'];
        } else {
            $kd_pelanggan = $autokode2;
            $kd_user      = $autokode;
            $nama_user    = $username;
            $email        = "pelanggan@gmail.com";
            $username2    = strtolower($username);
            $level        = "Pelanggan";
            $status       = "belum_beli";
            $redirect     = "pagePelanggan.php";

            $select  = $lg->selectWhere2("tb_meja", "no_meja", $password, "status", "active");
            $select2 = $lg->selectWhere2("tb_meja", "no_meja", $password, "status", "non-active");

            if ($select == 1) {
                $response = ['response' => 'negative', 'alert' => 'No meja ini telah digunakan'];
            } elseif ($select2 == 1) {
                $reg = $lg->register_pelanggan($kd_user, $nama_user, $email, $username2, $password, $level, $redirect);
                if ($reg['response'] == 'negative') {
                    $response = $reg;
                } else {
                    $value        = "'$kd_pelanggan', '$username', '$password'";
                    $resPelanggan = $lg->insert("tb_pelanggan", $value, $redirect);
                    if ($resPelanggan['response'] == 'negative') {
                        $lg->delete("tb_user", "kd_user", $kd_user, "");
                        $response = ['response' => 'negative', 'alert' => 'Gagal menyimpan data pelanggan, coba lagi'];
                    } else {
                        $valueOrder = "'$autokodeOrder', '$password', null, '$nama_user', '$kd_user', '', '$status', '$date'";
                        $resOrder   = $lg->insert("tb_order", $valueOrder, $redirect);
                        if ($resOrder['response'] == 'negative') {
                            $lg->delete("tb_pelanggan", "kd_pelanggan", $kd_pelanggan, "");
                            $lg->delete("tb_user", "kd_user", $kd_user, "");
                            $response = ['response' => 'negative', 'alert' => 'Gagal membuat order, coba lagi'];
                        } else {
                            $valueMeja = "user_kd='$kd_user', status='active'";
                            $lg->update("tb_meja", $valueMeja, "no_meja", $password, $redirect);
                            $_SESSION['username'] = $username;
                            $_SESSION['level']    = $level;
                            $response = ['response' => 'positive', 'alert' => 'Login Berhasil', 'redirect' => $redirect];
                        }
                    }
                }
            } elseif ($select == 0 && $select2 == 0) {
                $response = ['response' => 'negative', 'alert' => 'No meja tidak terdaftar, silahkan cek no meja kembali'];
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Pelanggan - Dapur Nusantara</title>
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="css/sweet-alert.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            background: #F7F3EE;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Background subtle pattern */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: url('images/bg3.jpg') center/cover no-repeat;
            opacity: .08;
            z-index: 0;
        }

        /* ── CARD ── */
        .login-card {
            position: relative; z-index: 1;
            width: 100%; max-width: 420px;
            background: #fff;
            border-radius: 20px;
            padding: 40px 36px 36px;
            box-shadow: 0 8px 48px rgba(62,31,0,0.12);
        }

        /* Tag label */
        .login-tag {
            font-size: 11px; font-weight: 900;
            color: #A0522D; letter-spacing: 1.5px;
            text-transform: uppercase;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 16px;
        }
        .login-tag::before {
            content: '';
            display: inline-block;
            width: 20px; height: 2px;
            background: #A0522D; border-radius: 2px;
        }

        /* Title */
        .login-title {
            font-family: 'Lora', serif;
            font-size: 28px; font-weight: 700;
            color: #2C1A0E; line-height: 1.3;
            margin-bottom: 10px;
        }

        /* Subtitle */
        .login-subtitle {
            font-size: 14px; color: #A07850;
            line-height: 1.6; margin-bottom: 28px;
            font-weight: 600;
        }

        /* Field */
        .field-group { margin-bottom: 18px; }
        .field-label {
            display: block;
            font-size: 11px; font-weight: 900;
            color: #A07850; letter-spacing: 1px;
            text-transform: uppercase; margin-bottom: 8px;
        }
        .field-input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #EDE0CC;
            border-radius: 12px;
            font-family: 'Nunito', sans-serif;
            font-size: 15px; font-weight: 600;
            color: #2C1A0E; background: #FDFAF6;
            outline: none; transition: border .2s, background .2s;
        }
        .field-input:focus {
            border-color: #A0522D;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(160,82,45,0.08);
        }
        .field-input::placeholder { color: #C4A882; }

        /* Submit button */
        .btn-login {
            width: 100%; padding: 15px;
            border: none; border-radius: 12px;
            background: #2C1A0E;
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 15px; font-weight: 900;
            cursor: pointer; margin-top: 8px;
            transition: background .2s, transform .15s;
            letter-spacing: .3px;
        }
        .btn-login:hover {
            background: #3E1F00;
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }

        /* Staff link */
        .staff-link {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: #A07850; font-weight: 600;
        }
        .staff-link a {
            color: #A0522D; font-weight: 800; text-decoration: none;
        }
        .staff-link a:hover { text-decoration: underline; }

        /* Logo */
        .login-logo {
            text-align: center; margin-bottom: 28px;
        }
        .login-logo img { height: 44px; }
        .login-logo .logo-text {
            font-family: 'Lora', serif;
            font-size: 16px; font-weight: 700;
            color: #3E1F00; margin-top: 6px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Logo -->
        <div class="login-logo">
            <img src="images/icon/logo.png" alt="Logo"
                 onerror="this.style.display='none'">
            <span class="logo-text">Dapur Nusantara</span>
        </div>

        <!-- Tag -->
        <div class="login-tag">Login Pelanggan</div>

        <!-- Title -->
        <h1 class="login-title">Masuk untuk memesan<br>makanan Anda</h1>

        <!-- Subtitle -->
        <p class="login-subtitle">
            Gunakan nama dan nomor meja untuk mulai memesan makanan.
        </p>

        <!-- Form -->
        <form action="" method="post">
            <div class="field-group">
                <label class="field-label">Nama Lengkap</label>
                <input type="text" name="username" class="field-input"
                       placeholder="Nama"
                       value="<?= htmlspecialchars(@$_POST['username']) ?>"
                       autocomplete="off" required>
            </div>

            <div class="field-group">
                <label class="field-label">Nomor Meja</label>
                <input type="number" name="password" class="field-input"
                       placeholder="Nomor Meja" required>
            </div>

            <button type="submit" name="btnLogin" class="btn-login">
                Masuk Sekarang &nbsp;→
            </button>
        </form>

        <!-- Link ke login staff -->
        <div class="staff-link">
            Staff restoran? <a href="loginMulti.php">Login di sini</a>
        </div>
    </div>

    <script src="vendor/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script src="js/main.js"></script>
    <?php include "config/alert.php"; ?>
</body>
</html>