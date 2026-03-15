<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login Pelanggan</title>

<link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/sweet-alert.css">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>

body{
background:#EDE9E3;
font-family:'Inter',sans-serif;
height:100vh;
display:flex;
align-items:center;
justify-content:center;
}

.login-container{
width:420px;
background:#F7F5F2;
padding:40px;
border-radius:14px;
box-shadow:0 6px 30px rgba(0,0,0,0.08);
}

.login-small{
color:#d35f2e;
font-size:12px;
letter-spacing:2px;
font-weight:600;
margin-bottom:10px;
}

.login-title{
font-size:25px;
font-weight:600;
margin-bottom:5px;
color:#2d2a26;
}

.login-desc{
font-size:14px;
color:#7a756f;
margin-bottom:25px;
}

label{
font-size:12px;
font-weight:600;
letter-spacing:1px;
color:#7a756f;
margin-bottom:6px;
}

.form-control{
height:48px;
border-radius:10px;
border:1px solid #e2ddd7;
background:#faf9f7;
}

.form-control:focus{
box-shadow:none;
border-color:#d35f2e;
}

.btn-login{
background:#1e1a17;
color:white;
height:48px;
border-radius:10px;
font-weight:500;
transition:0.2s;
}

.btn-login:hover{
background:#000;
}

.extra{
text-align:center;
margin-top:20px;
font-size:13px;
color:#777;
}

.extra a{
color:#d35f2e;
font-weight:500;
text-decoration:none;
}

</style>

</head>

<body>

<div class="login-container">

<div class="login-small">
— LOGIN PELANGGAN
</div>

<div class="login-title">
Masuk untuk memesan makanan Anda
</div>

<div class="login-desc">
Gunakan nama dan nomor meja untuk mulai memesan makanan.
</div>

<form method="POST">

<div class="form-group">
<label>NAMA LENGKAP</label>
<input 
type="text"
name="username"
class="form-control"
placeholder="Nama"
value="<?=@$_POST['username']?>"
required>
</div>

<div class="form-group">
<label>NOMOR MEJA</label>
<input 
type="number"
name="password"
class="form-control"
placeholder="Nomor Meja"
required>
</div>

<br>

<button name="btnLogin" class="btn btn-login btn-block">
Masuk Sekarang →
</button>

</div>

<script src="vendor/jquery-3.2.1.min.js"></script>
<script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
<script src="js/sweetalert.min.js"></script>

<?php include "config/alert.php"; ?>

</body>
</html>