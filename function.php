<?php
session_start();

//Membuat koneksi ke database
$server = "localhost";
$username = "root";
$password = "";
$database_name="SistemInformasiMobil";

$koneksi = mysqli_connect($server,$username,$password,$database_name);
//Menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($koneksi,"insert into stock (namabarang, deskripsi, stock) values('$namabarang','$deskripsi','$stock')");
    if($addtotable){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
};

//menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];
    
    $cekstocksekarang = mysqli_query($koneksi, "SELECT * FROM stock WHERE idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stoksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stoksekarang + $qty;

    $addtomasuk = mysqli_query($koneksi, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$barangnya', '$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($koneksi, "UPDATE stock SET stock='$tambahkanstocksekarangdenganquantity' WHERE idbarang='$barangnya'");

    if($addtomasuk && $updatestockmasuk){
        header('Location: masuk.php');
    } else {
        echo 'Gagal';
        header('Location: masuk.php');
    }
}
?>



?>
