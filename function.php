<?php
session_start();

//Membuat koneksi ke database
$server = "localhost";
$username = "root";
$password = "";
$database_name="SistemInformasiMobil";

$koneksi = mysqli_connect($server,$username,$password,$database_name);




?>