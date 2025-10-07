<?php
ob_start(); // Start output buffering to prevent any output before PDF generation

include 'koneksi.php';  // Include the database connection
require_once 'nav_siswa.php';

// Fetching the ranking results from the "spk.php" logic
$sqlNilai = "SELECT a.nama AS alternatif, k.nama_kriteria, n.nilai
             FROM tabel_nilai n
             INNER JOIN alternatif a ON n.alternatif_id = a.id_alt
             INNER JOIN kriteria k ON n.kriteria_id = k.id_kriteria";
$queryNilai = mysqli_query($dbcon, $sqlNilai);
$dataInput = array();

while ($row = mysqli_fetch_assoc($queryNilai)) {
    $alternatif = $row['alternatif'];
    $kriteria = $row['nama_kriteria'];
    $nilai = $row['nilai'];

    $dataInput[$alternatif][$kriteria] = $nilai;
}

// Fetching Min/Max values for each kriteria (as done in spk.php)
$minMaxValues = array();
$sqlKriteria = "SELECT * FROM kriteria";
$queryKriteria = mysqli_query($dbcon, $sqlKriteria);

while ($kriteria = mysqli_fetch_array($queryKriteria)) {
    $sqlMinMax = "SELECT MIN(nilai) as min, MAX(nilai) as max FROM tabel_nilai WHERE kriteria_id = " . $kriteria['id_kriteria'];
    $queryMinMax = mysqli_query($dbcon, $sqlMinMax);
    $minMax = mysqli_fetch_assoc($queryMinMax);
    $minMaxValues[$kriteria['id_kriteria']] = $minMax;
}

// Calculate utility and weighted score for each alternative (as done in spk.php)
$nilaiAkhir = array();
foreach ($dataInput as $alternatif => $nilaiKriteria) {
    $totalNilai = 0;

    foreach ($nilaiKriteria as $kriteria => $nilai) {
        $sqlKriteriaDetail = "SELECT id_kriteria, jenis_kriteria, bobot FROM kriteria WHERE nama_kriteria = '$kriteria'";
        $queryKriteriaDetail = mysqli_query($dbcon, $sqlKriteriaDetail);
        $row = mysqli_fetch_assoc($queryKriteriaDetail);
        $idKriteria = $row['id_kriteria'];
        $jenisKriteria = $row['jenis_kriteria'];
        $bobot = $row['bobot'];

        $min = $minMaxValues[$idKriteria]['min'];
        $max = $minMaxValues[$idKriteria]['max'];

        // Calculate utility based on the kriteria type (Benefit or Cost)
        if ($jenisKriteria === 'Benefit') {
            $utility = ($nilai - $min) / ($max - $min) * 1;
        } else {
            $utility = ($max - $nilai) / ($max - $min) * 1;
        }
        $utility = number_format($utility, 3);

        // Calculate the weighted score
        $bobot_nilai = $utility * $bobot;
        $bobot_nilai = number_format($bobot_nilai, 3);

        $totalNilai += $bobot_nilai;
    }

    $nilaiAkhir[$alternatif] = $totalNilai;
}

// Sort the alternatives by their final score (highest first)
arsort($nilaiAkhir);
$highestRanked = key($nilaiAkhir);
$highestScore = reset($nilaiAkhir);

// Fetch NIS based on the highest-ranked alternative (assuming the nis is stored in the alternatif table)
$sqlSiswa = "SELECT nis FROM alternatif WHERE nama = '$highestRanked'";
$querySiswa = mysqli_query($dbcon, $sqlSiswa);
$siswa = mysqli_fetch_assoc($querySiswa);


?>

<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="col-lg-12">
            <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600; margin-bottom: 40px;">Hasil Penghitungan SPK SMART</h1>
        </div>
        <div class="col-lg-12">
            <h5 class="page-header" style="margin-bottom: 20px;">Nilai Akhir dan Perankingan (10 Terbaik)</h5>

            <table class="table">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Alternatif</th>
                        <th>NIS</th> <!-- Add NIS column -->
                        <th>Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ranking = 1;
                    foreach ($nilaiAkhir as $alternatif => $totalNilai) {
                        if ($ranking > 10) {
                            break;
                        }

                        // Fetch NIS for each alternative
                        $sqlNIS = "SELECT nis FROM alternatif WHERE nama = '$alternatif'";
                        $queryNIS = mysqli_query($dbcon, $sqlNIS);
                        $rowNIS = mysqli_fetch_assoc($queryNIS);
                        $nis = $rowNIS['nis']; // Store the NIS value

                        echo '<tr>';
                        echo '<td>' . $ranking . '</td>';
                        echo '<td>' . $alternatif . '</td>';
                        echo '<td>' . $nis . '</td>'; // Display NIS value here
                        echo '<td>' . number_format($totalNilai, 3) . '</td>';
                        echo '</tr>';
                        $ranking++;
                    }
                    ?>
                </tbody>
            </table>
            <a href="spk_siswa.php" style="text-decoration: none;">Kembali ke SPK</a>
        </div>
    </div>
</div>

<?php
require_once 'foot.php';
?>
