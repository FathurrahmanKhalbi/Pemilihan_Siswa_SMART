<?php
include 'koneksi.php';
require_once 'nav.php';
?>

<div id="page-wrapper" style="padding: 30px 70px;">
    <div class="container-fluid">
        <div class="col-lg-12" style="text-align: center;">
            <p style="text-align: center; line-height: 1.5;">Sistem Pendukung Keputusan Siswa Berprestasi Metode SMART<br>SMPN 18 KOTA TANGERANG SELATAN</p>
            <img src="logo.png" alt="" style="margin-bottom: 20px; text-align: center;" width="120" height="120">
            <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600;">Selamat Datang <?= $_SESSION['username'] ?>!</h1>
        </div>
        <div>
            <p class="alert" style=" line-height: 1.8; padding-inline: 0; text-align: center;">
                Ini adalah aplikasi sistem pendukung keputusan untuk pemilihan siswa berprestasi dengan metode SMART (Simple Multi Attribute Rating Technique). Mulailah dengan <a href="alternatif.php" class="alert-link">memasukkan data alternatif</a>
            </p>
        </div>

    </div>
</div>

<?php
require_once 'foot.php';
?>
