<?php
    include "config/controller.php";
    $rg       = new Resto();
    $table    = "tb_user";
    $autokode = $rg->autokode($table, "kd_user", "US");
    $getLevel = $rg->select("tb_level");

    if (isset($_POST['btnRegister'])) {
        $kd_user   = $_POST['kd_user'];
        $nama_user = $_POST['nama_user'];
        $email     = $_POST['email'];
        $username  = $_POST['username'];
        $password  = $_POST['password'];
        $confirm   = $_POST['confirm'];
        $level     = $_POST['level'];
        $redirect  = "loginMulti.php";

        if ($nama_user == "" || $email == "" || $username == "" || $password == "" || $confirm == "" || $level == "") {
            $response = ['response' => 'negative', 'alert' => 'Lengkapi Field !!!'];
        } else {
            $response = $rg->register($kd_user, $nama_user, $email, $username, $password, $confirm, $level, $redirect);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>Halaman Register</title>

<link href="css/font-face.css" rel="stylesheet">
<link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet">
<link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet">
<link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet">

<link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">

<link href="vendor/animsition/animsition.min.css" rel="stylesheet">
<link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
<link href="vendor/wow/animate.css" rel="stylesheet">
<link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet">
<link href="vendor/slick/slick.css" rel="stylesheet">
<link href="vendor/select2/select2.min.css" rel="stylesheet">
<link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet">

<link rel="stylesheet" href="css/sweet-alert.css">
<link href="css/theme.css" rel="stylesheet">

<style>

body{
    background:#F7F4EE;
}

.login-content{
    background:#ECE7E1;
    border-radius:18px;
    padding:40px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.login-logo img{
    width:300px;
}

.login-form label{
    font-size:12px;
    letter-spacing:1px;
    color:#7a746d;
    font-weight:600;
}

.au-input{
    border-radius:10px;
    height:46px;
}

.form-control{
    border-radius:10px;
    height:46px;
}

.au-btn--green{
    background:#1C1916;
    border:none;
    border-radius:12px;
}

.au-btn--green:hover{
    background:#2b2622;
}

.register-link a{
    color:#C0512B;
    font-weight:600;
}

</style>

</head>

<body class="animsition">

<div class="page-wrapper">

<div class="container">

<div class="login-wrap">

<div class="login-content">

<div class="login-logo">
<a href="#">
<img src="images/icon/logobaru.png" alt="logo">
</a>
</div>

<div class="login-form">

<form action="" method="post">

<div class="form-group">
<label>Kode User</label>
<input style="color:red;font-weight:bold;" class="au-input au-input--full" type="text" name="kd_user" readonly value="<?=$autokode;?>">
</div>

<div class="form-group">
<label>Nama</label>
<input class="au-input au-input--full" type="text" name="nama_user" value="<?=@$_POST['nama_user']?>" placeholder="Nama">
</div>

<div class="form-group">
<label>Email</label>
<input class="au-input au-input--full" type="email" name="email" placeholder="Email" value="<?=@$_POST['email']?>">
</div>

<div class="form-group">
<label>Username</label>
<input class="au-input au-input--full" type="text" name="username" placeholder="Username" value="<?=@$_POST['username']?>">
</div>

<div class="form-group">
<label>Password</label>
<input class="au-input au-input--full" type="password" name="password" placeholder="Password" value="<?=@$_POST['password']?>">
</div>

<div class="form-group">
<label>Confirm Password</label>
<input class="au-input au-input--full" type="password" name="confirm" placeholder="Confirm Password" value="<?=@$_POST['confirm']?>">
</div>

<div class="form-group">
<label for="level">Level</label>
<select name="level" class="form-control mb-1">
<option value="">Pilih Level</option>
<?php foreach ($getLevel as $level) { ?>
<option value="<?=$level['name']?>"><?=$level['name']?></option>
<?php } ?>
</select>
</div>

<br>

<button name="btnRegister" class="au-btn au-btn--block au-btn--green m-b-20" type="submit">
Register
</button>

</form>

<div class="register-link">
<p>
Already have account?
<a href="loginMulti.php">Sign In</a>
</p>
</div>

</div>

</div>

</div>

</div>

</div>


<script src="vendor/jquery-3.2.1.min.js"></script>
<script src="vendor/bootstrap-4.1/popper.min.js"></script>
<script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>

<script src="vendor/slick/slick.min.js"></script>
<script src="vendor/wow/wow.min.js"></script>
<script src="vendor/animsition/animsition.min.js"></script>
<script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
<script src="vendor/counter-up/jquery.waypoints.min.js"></script>
<script src="vendor/counter-up/jquery.counterup.min.js"></script>
<script src="vendor/circle-progress/circle-progress.min.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="vendor/chartjs/Chart.bundle.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>

<script src="js/sweetalert.min.js"></script>
<script src="js/main.js"></script>

<?php include "config/alert.php"; ?>

</body>
</html>