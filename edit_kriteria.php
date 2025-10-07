<?php
include 'koneksi.php'; // Database connection
require_once 'nav.php'; // Navigation

// Check if 'id_kriteria' is provided in the URL
if (isset($_GET['id_kriteria'])) {
    $id_kriteria = $_GET['id_kriteria'];

    // Fetch the data for the given ID
    $sql = "SELECT * FROM kriteria WHERE id_kriteria = '$id_kriteria'";
    $query = mysqli_query($dbcon, $sql);
    $data = mysqli_fetch_assoc($query);

    // Check if data was found
    if (!$data) {
        echo "<script>alert('Kriteria tidak ditemukan'); window.location.href='kriteria.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID Kriteria tidak ditemukan'); window.location.href='kriteria.php';</script>";
    exit;
}

if (isset($_POST['submit'])) {
    $kriteria = $_POST['kriteria'];
    $bobot = $_POST['bobot'];
    $jenis_kriteria = $_POST['jenis_kriteria'];

    // Update the Kriteria in the database
    $sql_update = "UPDATE kriteria SET nama_kriteria = '$kriteria', bobot = '$bobot', jenis_kriteria = '$jenis_kriteria' WHERE id_kriteria = '$id_kriteria'";
    $query_update = mysqli_query($dbcon, $sql_update);

    if ($query_update) {
        echo "<script>alert('Data Kriteria berhasil diubah'); window.location.href='kriteria.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data Kriteria');</script>";
    }
}
?>

<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600;">Edit Data Kriteria</h1>
            </div>

            <div class="col-lg-8">
                <form role="form" action="" method="POST">
                    <div class="form-group">
                    <h5 style="text-align: left; margin-top: 30px;">Nama Kriteria</h5>
                        <input type="text" style="margin-bottom: 10px; border: 1px solid #000; padding: 15px; width: 100%; margin-top: 20px;" required name="kriteria" class="form-control" placeholder="Nama Kriteria" value="<?= $data['nama_kriteria'] ?>">
                    </div>
                    <div class="form-group">
                    <h5 style="text-align: left; margin-top: 30px;">Bobot Kriteria</h5>
                        <input type="text" style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;" required name="bobot" class="form-control" placeholder="Bobot" value="<?= $data['bobot'] ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <h5 style="text-align: left; margin-top: 30px;">Jenis Kriteria</h5>
                        <select style="padding: 15px; border: 1px solid #000;" class="form-select" required name="jenis_kriteria" aria-label="Default select example">
                            <option value="Benefit" <?= $data['jenis_kriteria'] == 'Benefit' ? 'selected' : '' ?>>Benefit</option>
                            <option value="Cost" <?= $data['jenis_kriteria'] == 'Cost' ? 'selected' : '' ?>>Cost</option>
                        </select>
                    </div>

                    <div class="d-flex left-content-between align-items-left" style="margin-top: 20px;">
                    <button type="submit" name="submit" class="btn btn-primary" style="padding: 10px 30px;">
                        <i class="fa fa-save" aria-hidden="true" style="margin-right: 5px;"></i> Update
                    </button>
                        <a href="kriteria.php" class="btn btn-secondary" style="margin-left: 10px; padding: 10px 30px; border: none; float: left;">
                            <i class="fa fa-arrow-left" aria-hidden="true" style="margin-right: 5px;"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'foot.php'; ?>
