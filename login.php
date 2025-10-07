<?php
session_start();
include 'koneksi.php';

if (isset($_GET['id'])) {
    if ($_GET['id'] == 'false') {
        echo "<script>alert('username / password salah. Gagal masuk.')</script>";
        header("location:login.php");
    } else if ($_GET['id'] == 'out') {
        echo "<script>alert('Anda belum masuk, silahkan masuk.')</script>";
        header("location:login.php");
    } else {
        echo "<script>alert('Logout berhasil.')</script>";
        header("location:login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sistem Pengambilan Keputusan SMART</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/startmin.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <style>
        body {
            font-family: "Poppins", serif;
        }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

    <div class="container" style="width: 40%; margin-top: 7%;">
            <div class="panel-heading">
                <h1 class="panel-title text-center" style="letter-spacing: -2px; font-weight: 600;">Log In Admin</h1>
                <h5 style="margin-bottom: 40px; text-align: center;">Sistem Pendukung Keputusan Metode SMART</h5>
            </div>
            <div class="panel-body">
                <form role="form" action="" method="POST">
                    <div class="form-group">
                    <label style="margin-bottom: 5px;">Username</label>
                        <input style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%; box-shadow: none;" class="form-control" placeholder="Username" name="username" type="text" autofocus>
                    </div>
                    <div class="form-group">
                    <label style="margin-bottom: 5px;">Password</label>
                        <input style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%; box-shadow: none;" class="form-control" placeholder="Password" name="password" type="password" value="">
                    </div>
                    <!-- Change this to a button or input when using this as a form -->
                    <input type="submit" name="submit" class="btn btn-lg btn-primary btn-block" style="padding: 10px 30px; border: none; width: 100%; margin-bottom: 20px;" value="Masuk">
                </form>
                <?php
                if (isset($_POST['submit'])) {
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    $sqllogin = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
                    $querylogin = mysqli_query($dbcon, $sqllogin);

                    if (mysqli_num_rows($querylogin) > 0) {
                        $_SESSION['username'] = $username;
                        $_SESSION['stat'] = 'masuk';
                        echo "<script>alert('Berhasil masuk. Selamat datang, $username.')</script>";
                        echo ($_SESSION['stat']);
                        header("location:index.php");
                    } else {
                        echo "<script>alert('Username/password salah.')</script>";
                    }
                }
                ?>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>
