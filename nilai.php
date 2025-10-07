<?php
include 'koneksi.php';
require_once 'nav.php';

// Fetch existing nilai data
$sqlNilai = "SELECT a.nama AS alternatif, k.nama_kriteria, n.nilai, n.id_nilai, n.alternatif_id
             FROM tabel_nilai n
             INNER JOIN alternatif a ON n.alternatif_id = a.id_alt
             INNER JOIN kriteria k ON n.kriteria_id = k.id_kriteria";
$queryNilai = mysqli_query($dbcon, $sqlNilai);
$dataInput = array();

while ($row = mysqli_fetch_assoc($queryNilai)) {
    $alternatif = $row['alternatif'];
    $kriteria = $row['nama_kriteria'];
    $nilai = $row['nilai'];
    $id_nilai = $row['id_nilai'];
    $alternatif_id = $row['alternatif_id'];

    $dataInput[$alternatif][$kriteria] = ['nilai' => $nilai, 'id_nilai' => $id_nilai, 'alternatif_id' => $alternatif_id];
}

// Handle delete request
if (isset($_GET['delete'])) {
    $idAlternatif = $_GET['delete']; // Use alternatif_id to delete entire row

    // Delete all rows for the given alternatif_id
    $deleteSql = "DELETE FROM tabel_nilai WHERE alternatif_id = ?";
    $stmt = mysqli_prepare($dbcon, $deleteSql);
    mysqli_stmt_bind_param($stmt, "i", $idAlternatif);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data berhasil dihapus'); window.location.href='nilai.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data'); window.location.href='nilai.php';</script>";
    }
    mysqli_stmt_close($stmt);
}

// Handle form submission to add new data
if (isset($_POST['submit'])) {
    $alternatifId = $_POST['alternatif'];
    $kriteriaValues = $_POST['kriteria'];

    foreach ($kriteriaValues as $kriteriaId => $nilai) {
        // Insert into tabel_nilai for each kriteria
        $insertSql = "INSERT INTO tabel_nilai (alternatif_id, kriteria_id, nilai) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($dbcon, $insertSql);
        mysqli_stmt_bind_param($stmt, "iis", $alternatifId, $kriteriaId, $nilai);  // assuming 'nilai' is a string or number
        if (mysqli_stmt_execute($stmt)) {
            // Optionally, you can give a success message
            echo "<script>alert('Data berhasil ditambahkan'); window.location.href='nilai.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data'); window.location.href='nilai.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600;">Input Nilai Siswa</h1>
            </div>
        </div>

        <form role="form" method="POST" action="">
            <div class="form-group" style="margin-block: 30px;">
                <label for="alternatif">Siswa</label>
                <select required name="alternatif" class="form-select" style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; margin-top: 10px;">
                    <option value="">Pilih Siswa</option>
                    <?php
                    $sqlAlternatif = "SELECT * FROM alternatif";
                    $queryAlternatif = mysqli_query($dbcon, $sqlAlternatif);

                    while ($alternatif = mysqli_fetch_array($queryAlternatif)) {
                        echo '<option value="' . $alternatif['id_alt'] . '">' . $alternatif['nama'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <?php
            $sqlKriteria = "SELECT * FROM kriteria";
            $queryKriteria = mysqli_query($dbcon, $sqlKriteria);

            while ($kriteria = mysqli_fetch_array($queryKriteria)) {
                echo '<div class="form-group" style="margin-block: 30px;">';
                echo '<label for="kriteria_' . $kriteria['id_kriteria'] . '">' . $kriteria['nama_kriteria'] . '</label>';
                echo '<input required type="text" name="kriteria[' . $kriteria['id_kriteria'] . ']" class="form-control" placeholder="Masukan Nilai" style="margin-top: 10px; margin-bottom: 20px; border: 1px solid #000; padding: 15px;">';
                echo '</div>';
            }
            ?>

                <button type="submit" name="submit" class="btn btn-primary" style="margin-right: 10px; padding: 10px 30px; border: none;">
                    <i class="fa fa-save" aria-hidden="true" style="margin-right: 5px;"></i> Simpan
                </button>

        </form>

        <div class="col-lg-12">
    <h5 class="page-header" style="margin-block: 40px 20px;">Data yang telah diinput</h5>
    <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
<thead>
    <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama Siswa</th>
        <?php
        // Save kriteria into an array
        $kriteriaList = [];
        $sqlKriteria = "SELECT * FROM kriteria";
        $queryKriteria = mysqli_query($dbcon, $sqlKriteria);
        while ($kriteria = mysqli_fetch_array($queryKriteria)) {
            $kriteriaList[] = $kriteria['nama_kriteria'];
            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $kriteria['nama_kriteria'] . '</th>';
        }
        ?>
        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Aksi</th>
    </tr>
</thead>
<tbody>
    <?php
    $n = 1;
    foreach ($dataInput as $alternatif => $nilaiKriteria) {
        echo '<tr style="border: 1px solid #ddd;">';
        echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $n . '</td>';
        echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: left;">' . ucwords(strtolower($alternatif)) . '</td>';

        foreach ($kriteriaList as $kriteriaName) {
            if (isset($nilaiKriteria[$kriteriaName])) {
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $nilaiKriteria[$kriteriaName]['nilai'] . '</td>';
                $lastIdNilai = $nilaiKriteria[$kriteriaName]['id_nilai'];
                $lastAltId = $nilaiKriteria[$kriteriaName]['alternatif_id'];
            } else {
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">-</td>';
            }
        }

        echo '<td style="text-align: center;">';
        echo '<div style="display: flex; justify-content: center; gap: 10px;">';
        echo '<a href="edit_nilai.php?edit=' . $lastIdNilai . '" style="color: blue; text-decoration: none; padding: 5px 10px; border: 1px solid blue; border-radius: 4px;">';
        echo '<i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
        echo '<a href="?delete=' . $lastAltId . '" onclick="return confirm(\'Apakah yakin menghapus ?\')" style="color: red; text-decoration: none; padding: 5px 10px; border: 1px solid red; border-radius: 4px;">';
        echo '<i class="fa fa-trash-o" aria-hidden="true"></i> Hapus</a>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
        $n++;
    }
    ?>
</tbody>

    </table>
</div>


    </div>
</div>

<?php
require_once 'foot.php';
?>
