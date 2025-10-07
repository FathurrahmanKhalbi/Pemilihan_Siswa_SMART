<?php
include 'koneksi.php';  // Make sure to include the database connection

// Check if the id_alt is set
if (isset($_GET['id_alt'])) {
    $id_alt = $_GET['id_alt'];

    // Fetch the existing data for this alternatif
    $sql = "SELECT * FROM alternatif WHERE id_alt = '$id_alt'";
    $query = mysqli_query($dbcon, $sql);
    $alternatif = mysqli_fetch_array($query);

    if (!$alternatif) {
        echo "<script>alert('Alternatif tidak ditemukan!');</script>";
        exit;
    }
} else {
    echo "<script>alert('ID alternatif tidak ditemukan!');</script>";
    exit;
}

if (isset($_POST['submit'])) {
    // If the form is submitted, update the alternative data
    $nama = $_POST['nama'];
    $nis = $_POST['nis'];

    // Update query
    $update_sql = "UPDATE alternatif SET nama = '$nama', nis = '$nis' WHERE id_alt = '$id_alt'";
    $update_query = mysqli_query($dbcon, $update_sql);

    if ($update_query) {
        echo "<script>alert('Data alternatif berhasil diubah!'); window.location.href='alternatif.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data alternatif.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Alternatif</title>
    <link rel="stylesheet" href="path_to_your_stylesheet.css">
</head>

<body>
    <?php include 'nav.php'; ?>

    <div id="page-wrapper" style="padding: 70px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600;">Edit Siswa</h1>
                </div>
                <div class="col-lg-8">
                    <form role="form" action="" method="POST">
                        <div class="form-group" style="padding: 20px 0;">
                            <label style="margin-bottom: 5px;">Nama Siswa</label>
                            <input
                                style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;"
                                type="text"
                                required
                                name="nama"
                                class="form-control"
                                placeholder="Coba masukkan alternatif..."
                                value="<?= $alternatif['nama']; ?>">
                            <label style="margin-bottom: 5px;">NIS</label>
                            <input
                                style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;"
                                type="text"
                                required
                                name="nis"
                                class="form-control"
                                placeholder="Coba masukkan alternatif..."
                                value="<?= $alternatif['nis']; ?>">
                        </div>
                        <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary" style="margin-right: 10px; padding: 10px 30px; border: none;">
                            <i class="fa fa-save" aria-hidden="true" style="margin-right: 5px;"></i> Simpan
                        </button>
                            <a href="alternatif.php" class="btn btn-secondary"style="margin-right: 10px; padding: 10px 30px; border: none;">
                                <i class="fa fa-arrow-left" aria-hidden="true" style="margin-right: 5px;"></i> Kembali
                            </a>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'foot.php'; ?>
</body>

</html>
