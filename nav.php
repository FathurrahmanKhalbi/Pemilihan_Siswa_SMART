<?php
include 'cek.php';
$current_page = basename($_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="css/metisMenu.min.css" rel="stylesheet">
    <!-- Timeline CSS -->
    <link href="css/timeline.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/startmin.css" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="css/morris.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <style>
        body {
            font-family: "Poppins", serif;
            padding-top: 60px; 
        }

        .navbar-fixed-top {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .sidebar-nav {
            padding-top: 50px;
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

    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="padding: 20px 40px;">
            <div class="navbar-header">
                <a class="navbar-brand" href="">SMART</a>
            </div>

            <!--<ul class="nav navbar-nav navbar-left navbar-top-links">
                    <li><a href="#"><i class="fa fa-home fa-fw"></i> Website</a></li>
                </ul>-->


            
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse" style="width: 84%;">
                    <img src="logo.png" alt="" style="margin-top: 450px; margin-bottom: 20px;" width="120" height="120">
                    <ul class="nav" id="side-menu">
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'index.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #888' ?>" ; href="index.php" class=""><i class="fa fa-dashboard fa-fw" style="margin-left: 10px"></i> Dashboard</a>
                        </li>
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'kriteria.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #aaa' ?>" ; href="kriteria.php" class=""><i class="fa fa-file-o fa-fw" style="margin-left: 10px;"></i> Data Kriteria</a>
                        </li>
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'alternatif.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #aaa' ?>" ; href="alternatif.php" class=""><i class="fa fa-address-book-o fa-fw" style="margin-left: 10px"></i> Data Siswa</a>
                        </li>
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'nilai.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #aaa' ?>" ; href="nilai.php" class=""><i class="fa fa-edit fa-fw" style="margin-left: 10px"></i> Nilai Alternatif</a>
                        </li>
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'spk.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #aaa' ?>" ; href="spk.php" class=""><i class="fa fa-cogs fa-fw" style="margin-left: 10px"></i> Perhitungan</a>
                        </li>
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'hasil.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #aaa' ?>" ; href="hasil.php" class=""><i class="fa fa-thumbs-o-up fa-fw" style="margin-left: 10px"></i> Hasil</a>
                        </li>
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'pengguna.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #aaa' ?>" ; href="pengguna.php" class=""><i class="fa fa-user fa-fw" style="margin-left: 10px"></i> Pengguna</a>
                        </li>
                        <li style="width: 100%; padding: 6px 0; margin-bottom: 10px;">
                            <a style="text-decoration: none; letter-spacing: -0.3px; <?= ($current_page == 'logout.php') ? 'color: #000; border-left: 3px solid #000; font-weight: 600;' : 'color: #aaa' ?>" ; href="logout.php" class=""><i class="fa fa-sign-out fa-fw" style="margin-left: 10px"></i> Keluar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>
