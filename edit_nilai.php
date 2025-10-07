<?php
include 'koneksi.php';
require_once 'nav.php';

// Get current alternatif ID from any nilai being edited
if (isset($_GET['edit'])) {
    $editID = $_GET['edit'];

    // Fetch nilai record to get alternatif_id
    $sqlEdit = "SELECT * FROM tabel_nilai WHERE id_nilai = ?";
    $stmtEdit = mysqli_prepare($dbcon, $sqlEdit);
    mysqli_stmt_bind_param($stmtEdit, "i", $editID);
    mysqli_stmt_execute($stmtEdit);
    $result = mysqli_stmt_get_result($stmtEdit);

    if (mysqli_num_rows($result) == 0) {
        echo "<script>alert('Data tidak ditemukan'); window.location.href='nilai.php';</script>";
        exit;
    }

    $editData = mysqli_fetch_assoc($result);
    $currentAlternatif = $editData['alternatif_id'];
    mysqli_stmt_close($stmtEdit);
} else {
    echo "<script>alert('Parameter edit tidak valid'); window.location.href='nilai.php';</script>";
    exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
    $nilaiArray = $_POST['nilai'];

    foreach ($nilaiArray as $kriteriaID => $nilai) {
        $nilai = trim($nilai);
        $nilai = ($nilai === '') ? null : $nilai;

        // Validate: allow null or numeric only
        if ($nilai !== null && !is_numeric($nilai)) {
            echo "<script>alert('Nilai untuk kriteria ID $kriteriaID harus angka atau kosong.');</script>";
            continue;
        }

        // Check if record exists
        $checkSql = "SELECT * FROM tabel_nilai WHERE alternatif_id = ? AND kriteria_id = ?";
        $checkStmt = mysqli_prepare($dbcon, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "ii", $currentAlternatif, $kriteriaID);
        mysqli_stmt_execute($checkStmt);
        $result = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($result) > 0) {
            // Update existing
            $updateSql = "UPDATE tabel_nilai SET nilai = ? WHERE alternatif_id = ? AND kriteria_id = ?";
            $stmt = mysqli_prepare($dbcon, $updateSql);
            mysqli_stmt_bind_param($stmt, "sii", $nilai, $currentAlternatif, $kriteriaID);
        } else {
            // Insert new
            $insertSql = "INSERT INTO tabel_nilai (nilai, alternatif_id, kriteria_id) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($dbcon, $insertSql);
            mysqli_stmt_bind_param($stmt, "sii", $nilai, $currentAlternatif, $kriteriaID);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_stmt_close($checkStmt);
    }

    echo "<script>alert('Data berhasil diperbarui'); window.location.href='nilai.php';</script>";
    exit;
}
?>

<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600; margin-bottom: 40px;">Edit Nilai Alternatif</h1>
            </div>
        </div>

        <div class="col-lg-8">
            <form role="form" method="POST" action="">
                <?php
                // Ambil semua kriteria
                $sqlKriteria = "SELECT * FROM kriteria";
                $queryKriteria = mysqli_query($dbcon, $sqlKriteria);

                while ($kriteria = mysqli_fetch_array($queryKriteria)) {
                    $idKriteria = $kriteria['id_kriteria'];
                    $namaKriteria = $kriteria['nama_kriteria'];

                    // Ambil nilai saat ini jika ada
                    $sqlNilai = "SELECT nilai FROM tabel_nilai WHERE alternatif_id = '$currentAlternatif' AND kriteria_id = '$idKriteria'";
                    $resultNilai = mysqli_query($dbcon, $sqlNilai);
                    $rowNilai = mysqli_fetch_assoc($resultNilai);
                    $currentNilai = isset($rowNilai['nilai']) ? $rowNilai['nilai'] : '';

                    echo '<div class="form-group">';
                    echo '<label for="nilai_' . $idKriteria . '">' . $namaKriteria . ':</label>';
                    echo '<input type="text" name="nilai[' . $idKriteria . ']" class="form-control" value="' . htmlspecialchars($currentNilai) . '" placeholder="Masukkan nilai untuk ' . $namaKriteria . '" style="margin-top: 10px; margin-bottom: 20px; border: 1px solid #000; padding: 15px;">';
                    echo '</div>';
                }
                ?>

                <div class="form-group">
                    <button type="submit" name="submit" class="btn btn-primary" style="margin-right: 10px; padding: 10px 30px; border: none;">
                        <i class="fa fa-save" aria-hidden="true" style="margin-right: 5px;"></i> Simpan
                    </button>
                    <a href="nilai.php" class="btn btn-secondary" style="margin-right: 10px; padding: 10px 30px; border: none;">
                        <i class="fa fa-arrow-left" aria-hidden="true" style="margin-right: 5px;"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'foot.php'; ?>
