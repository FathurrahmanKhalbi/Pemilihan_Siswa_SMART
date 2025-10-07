<?php
ob_start(); // Start output buffering to prevent any output before PDF generation

include 'koneksi.php';  // Include the database connection
require_once 'nav.php';

// Fungsi untuk memformat tanggal dalam bahasa Indonesia
function formatTanggalIndonesia($date) {
    $bulanIndonesia = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($date);
    $tanggal = date('d', $timestamp);
    $bulan = $bulanIndonesia[(int)date('m', $timestamp) - 1];
    $tahun = date('Y', $timestamp);

    return "$tanggal $bulan $tahun";
}

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

                                // Ubah bobot menjadi desimal
                                $bobot_desimal = $bobot / 100;

                                $min = $minMaxValues[$idKriteria]['min'];
                                $max = $minMaxValues[$idKriteria]['max'];

                                if ($jenisKriteria === 'Benefit') {
                                    $utility = ($nilai - $min) / ($max - $min) * 1;
                                } else {
                                    $utility = ($max - $nilai) / ($max - $min) * 1;
                                }
                                $utility = number_format($utility, 3);

                                // Kalkulasi bobot nilai dengan bobot desimal
                                $bobot_nilai = $utility * $bobot_desimal;
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

// Check if the print button has been clicked
if (isset($_POST['print'])) {
    require('fpdf.php');  // Include the FPDF library

    // Create a PDF document
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->AddFont('Times', '', 'times.php');
    $pdf->SetFont('Times', '', 14);

    // Title section with a line under the logo and header
    $pdf->Image('logo.png', 5, 2, 30);
    $pdf->SetFont('Times', 'B', 14);  // Set font to bold and larger size
    $pdf->Cell(20);  // Move to the right
    $pdf->Cell(0, 10, 'HASIL PEMILIHAN 10 BESAR SISWA BERPRESTASI', 0, 1, 'C');
    $pdf->Cell(20);
    $pdf->Cell(0, 3, 'SMPN 18 KOTA TANGERANG SELATAN', 0, 1, 'C');
    $pdf->Ln(10);

    // Add a long line under the header
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(10);

    $pdf->SetFont('Times', '', 12);

    // Table header
    $colWidths = [15, 40, 95, 40];
    $header = ['No', 'NIS', 'Alternatif', 'Nilai Akhir'];

    $pdf->SetFont('Times', 'B', 12);
    $pdf->SetFillColor(220, 220, 220);
    foreach ($header as $i => $headerText) {
        $pdf->Cell($colWidths[$i], 10, $headerText, 1, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('Times', '', 12);
    $ranking = 1;
    foreach ($nilaiAkhir as $alternatif => $totalNilai) {
        if ($ranking > 10) {
            break;
        }

        $sqlNIS = "SELECT nis FROM alternatif WHERE nama = '$alternatif'";
        $queryNIS = mysqli_query($dbcon, $sqlNIS);
        $rowNIS = mysqli_fetch_assoc($queryNIS);
        $nis = $rowNIS['nis'];

        $pdf->Cell($colWidths[0], 10, $ranking, 1, 0, 'C');
        $pdf->Cell($colWidths[1], 10, $nis, 1, 0, 'C');
        $pdf->Cell($colWidths[2], 10, strtoupper($alternatif), 1, 0, 'L');
        $pdf->Cell($colWidths[3], 10, number_format($totalNilai, 3), 1, 0, 'C');
        $pdf->Ln();

        $ranking++;
    }

    $pdf->Ln(15);

    // Tanggal dalam format Indonesia
    $currentDate = formatTanggalIndonesia(date('Y-m-d'));
    $pdf->Cell(190, 15, 'Kota Tangerang Selatan, ' . $currentDate, 0, 1, 'R');
    $pdf->Cell(20);
    $pdf->Cell(148, 1, 'Kepala Sekolah', 0, 1, 'R');
    $pdf->SetX(120);
    $pdf->Cell(88, 60, '(' . str_repeat('.', 40) . ')', 0, 1, 'C');

    ob_end_clean();
    $pdf->Output();
    exit();
}
?>




<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="col-lg-12">
            <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600; margin-bottom: 40px;">Hasil Penghitungan SPK SMART</h1>
        </div>
        <div class="col-lg-12">
            <h5 class="page-header" style="margin-bottom: 20px;">Nilai Akhir dan Perankingan (10 Siswa Terbaik)</h5>

            <table class="table">
                <thead>
                    <tr>
                    <th style="text-align: justify;">Ranking</th>
                        <th style="text-align: justify;">Nama</th>
                        <th style="text-align: justify;">NIS</th> <!-- Add NIS column -->
                        <th style="text-align: justify;">Nilai Akhir</th>
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
                        echo '<td style="text-align: center;">' . $ranking . '</td>';
                        echo '<td style="text-align: justify;">' . $alternatif . '</td>';
                        echo '<td style="text-align: justify;">' . $nis . '</td>'; // Display NIS value here
                        echo '<td style="text-align: justify;">' . number_format($totalNilai, 3) . '</td>';
                        echo '</tr>';
                        $ranking++;
                    }
                    ?>
                </tbody>
            </table>
            <div class="d-flex align-items-center justify-content-between" style="width: fit-content; margin-top: 40px;">
                <!-- Add the Print Document Button -->
                <form method="post" action="">
                <button type="submit" name="print" class="btn btn-secondary" style="padding: 10px 30px; background-color: #6c757d; border-color: #6c757d;">
                    <i class="fa fa-print" aria-hidden="true" style="margin-right: 5px;"></i> Cetak
                </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'foot.php';
?>
