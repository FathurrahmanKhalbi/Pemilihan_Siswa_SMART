<?php
include 'koneksi.php'; // Koneksi ke database

// Ambil data pengguna berdasarkan ID
if (isset($_GET['id'])) {
    $id_admin = $_GET['id'];
    $query = "SELECT * FROM admin WHERE id_admin = '$id_admin'";
    $result = mysqli_query($dbcon, $query);
    $user = mysqli_fetch_assoc($result);
} else {
    echo "<script>alert('ID tidak ditemukan!');</script>";
    exit;
}

// Proses update data pengguna
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query_update = "UPDATE admin SET nama = '$nama', username = '$username', password = '$password' WHERE id_admin = '$id_admin'";
    if (mysqli_query($dbcon, $query_update)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href = 'pengguna.php';</script>";
    } else {
        echo "<script>alert('Data gagal diperbarui!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div id="page-wrapper" style="padding: 70px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600;">Edit Pengguna</h1>
                </div>
                <div class="col-lg-8">
                    <form action="" method="POST">
                        <div class="form-group" style="padding: 20px 0;">
                            <label style="margin-bottom: 5px;">Nama Pengguna</label>
                            <input
                                style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;"
                                type="text"
                                required
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama pengguna..."
                                value="<?= htmlspecialchars($user['nama']); ?>">
                            <label style="margin-bottom: 5px;">Username</label>
                            <input
                                style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;"
                                type="text"
                                required
                                name="username"
                                class="form-control"
                                placeholder="Masukkan username..."
                                value="<?= htmlspecialchars($user['username']); ?>">
                            <label style="margin-bottom: 5px;">Password</label>
                            <input
                                style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;"
                                type="text"
                                required
                                name="password"
                                class="form-control"
                                placeholder="Masukkan password..."
                                value="<?= htmlspecialchars($user['password']); ?>">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-primary" style="margin-right: 10px; padding: 10px 30px; border: none;">
                                <i class="fa fa-save" aria-hidden="true" style="margin-right: 5px;"></i> Simpan
                            </button>
                            <a href="pengguna.php" class="btn btn-secondary" style="margin-right: 10px; padding: 10px 30px; border: none;">
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
