<?php
include 'koneksi.php';
require('fpdf.php'); // Include the FPDF library

// Start output buffering to capture any unwanted output
ob_start();

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

// Fungsi untuk menghitung utility dengan penanganan division by zero
function calculateUtility($nilai, $min, $max, $jenisKriteria) {
    // Jika min = max, maka semua nilai sama, berikan utility 1
    if ($max - $min == 0) {
        return 1;
    }
    
    if ($jenisKriteria === 'Benefit') {
        $utility = ($nilai - $min) / ($max - $min) * 1;
    } else {
        $utility = ($max - $nilai) / ($max - $min) * 1;
    }
    
    return $utility;
}

// SQL query dan pengolahan data
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

$sqlKriteria = "SELECT * FROM kriteria";
$queryKriteria = mysqli_query($dbcon, $sqlKriteria);
$minMaxValues = array();

while ($kriteria = mysqli_fetch_array($queryKriteria)) {
    $sqlMinMax = "SELECT MIN(nilai) as min, MAX(nilai) as max FROM tabel_nilai WHERE kriteria_id = " . $kriteria['id_kriteria'];
    $queryMinMax = mysqli_query($dbcon, $sqlMinMax);
    $minMax = mysqli_fetch_assoc($queryMinMax);
    $minMaxValues[$kriteria['id_kriteria']] = $minMax;
}

// Fungsi untuk membuat PDF
function generatePDF($dbcon, $minMaxValues, $dataInput)
{
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->AddFont('Times', '', 'times.php');
    $pdf->SetFont('Times', '', 14);

    // Header with Logo
    $pdf->Image('logo.png', 5, 2, 30);
    $pdf->SetFont('Times', 'B', 14);
    $pdf->Cell(20);
    $pdf->Cell(0, 10, 'HASIL PEMILIHAN SISWA BERPRESTASI', 0, 1, 'C');
    $pdf->Cell(20);
    $pdf->Cell(0, 3, 'SMPN 18 KOTA TANGERANG SELATAN', 0, 1, 'C');
    $pdf->Ln(5);

    // Add a long line under the header
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(10);

    $pdf->SetFont('Times', 'B', 12);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->SetTextColor(0, 0, 0);

    // Header tabel
    $pdf->Cell(15, 10, 'No', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'NIS', 1, 0, 'C', true);
    $pdf->Cell(95, 10, 'Siswa', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Nilai Akhir', 1, 0, 'C', true);
    $pdf->Ln();

    $pdf->SetFont('Times', '', 12);
    $pdf->SetTextColor(0, 0, 0);

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

            // Menggunakan fungsi calculateUtility untuk menghindari division by zero
            $utility = calculateUtility($nilai, $min, $max, $jenisKriteria);
            $utility = number_format($utility, 3);

            // Kalkulasi bobot nilai dengan bobot desimal
            $bobot_nilai = $utility * $bobot_desimal;
            $bobot_nilai = number_format($bobot_nilai, 3);

            $totalNilai += $bobot_nilai;
        }

        $nilaiAkhir[$alternatif] = $totalNilai;
    }

    arsort($nilaiAkhir);
    $ranking = 1;

    foreach ($nilaiAkhir as $alternatif => $totalNilai) {
        $sqlNIS = "SELECT nis FROM alternatif WHERE nama = '$alternatif'";
        $queryNIS = mysqli_query($dbcon, $sqlNIS);
        $rowNIS = mysqli_fetch_assoc($queryNIS);
        $nis = $rowNIS['nis'];

        $pdf->Cell(15, 10, $ranking, 1, 0, 'C');
        $pdf->Cell(40, 10, $nis, 1, 0, 'C');
        $pdf->Cell(95, 10, ' ' . strtoupper($alternatif), 1);
        $pdf->Cell(40, 10, number_format($totalNilai, 3), 1, 0, 'C');
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


if (isset($_POST['generate_pdf'])) {
    generatePDF($dbcon, $minMaxValues, $dataInput);
    exit;
}

ob_end_clean();
require_once 'nav.php';
?>


<div id="page-wrapper" style="padding: 50px;">
    <div class="container-fluid">
        <div class="row" style="gap: 50px;">
            <div class="col-lg-12">
                <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600;">PERHITUNGAN SPK SMART</h1>
            </div>
            <div class="col-lg-12">
                <h5 class="page-header" style="margin-bottom: 20px;">Nilai Alternatif Pada Setiap Kriteria </h5>
                <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
                    <thead>
                        <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Nama Siswa</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NIS</th>
                            <?php
                            $sqlKriteria = "SELECT * FROM kriteria";
                            $queryKriteria = mysqli_query($dbcon, $sqlKriteria);
                            while ($kriteria = mysqli_fetch_array($queryKriteria)) {
                                echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $kriteria['nama_kriteria'] . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $n = 1;
                        foreach ($dataInput as $alternatif => $nilaiKriteria) {
                            // Fetch the NIS for the current alternatif (student)
                            $sqlNIS = "SELECT nis FROM alternatif WHERE nama = '$alternatif'";
                            $queryNIS = mysqli_query($dbcon, $sqlNIS);
                            $rowNIS = mysqli_fetch_assoc($queryNIS);
                            $nis = $rowNIS['nis']; // Store the NIS value

                            echo '<tr style="border: 1px solid #ddd;">';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $n . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: justify;">' . $alternatif . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $nis . '</td>'; // Display NIS value here
                            foreach ($nilaiKriteria as $nilai) {
                                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $nilai . '</td>';
                            }
                            echo '</tr>';
                            $n++;
                        }
                        ?>
                    </tbody>
                </table>

            </div>
            <div class="col-lg-12">
                <h5 class="page-header" style="margin-bottom: 20px;">Penentuan Nilai MIN/MAX</h5>
                <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
                    <thead>
                    <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">MIN/MAX</th>
                            <?php
                            $sqlKriteria = "SELECT * FROM kriteria";
                            $queryKriteria = mysqli_query($dbcon, $sqlKriteria);
                            $minMaxValues = array();

                            while ($kriteria = mysqli_fetch_array($queryKriteria)) {
                                echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $kriteria['nama_kriteria'] . '</th>';

                                $sqlMinMax = "SELECT MIN(nilai) as min, MAX(nilai) as max FROM tabel_nilai WHERE kriteria_id = " . $kriteria['id_kriteria'];
                                $queryMinMax = mysqli_query($dbcon, $sqlMinMax);
                                $minMax = mysqli_fetch_assoc($queryMinMax);
                                $minMaxValues[$kriteria['id_kriteria']] = $minMax;
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <th style="text-align: center; font-weight: normal;">MIN</th>
                            <?php
                            foreach ($minMaxValues as $minMax) {
                                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $minMax['min'] . '</td>';
                            }
                            ?>
                        </tr>
                        <tr>
                        <th style="text-align: center; font-weight: normal;">MAX</th>
                            <?php
                            foreach ($minMaxValues as $minMax) {
                                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $minMax['max'] . '</td>';
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
                <h5 class="page-header" style="margin-bottom: 20px;">Nilai utility</h5>
                <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
                    <thead>
                        <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Nama Siswa</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NIS</th>
                            <?php
                            $sqlKriteria = "SELECT * FROM kriteria";
                            $queryKriteria = mysqli_query($dbcon, $sqlKriteria);

                            while ($kriteria = mysqli_fetch_array($queryKriteria)) {
                                echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $kriteria['nama_kriteria'] . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $n = 1;
                        foreach ($dataInput as $alternatif => $nilaiKriteria) {
                            // Fetch the NIS for the current alternatif (student)
                            $sqlNIS = "SELECT nis FROM alternatif WHERE nama = '$alternatif'";
                            $queryNIS = mysqli_query($dbcon, $sqlNIS);
                            $rowNIS = mysqli_fetch_assoc($queryNIS);
                            $nis = $rowNIS['nis']; // Store the NIS value

                            echo '<tr style="border: 1px solid #ddd;">';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $n . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: justify;">' . $alternatif . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $nis . '</td>'; // Display NIS value here

                            foreach ($nilaiKriteria as $kriteria => $nilai) {
                                $sqlKriteriaId = "SELECT id_kriteria, jenis_kriteria FROM kriteria WHERE nama_kriteria = '$kriteria'";
                                $queryKriteriaId = mysqli_query($dbcon, $sqlKriteriaId);
                                $row = mysqli_fetch_assoc($queryKriteriaId);
                                $idKriteria = $row['id_kriteria'];
                                $jenisKriteria = $row['jenis_kriteria'];

                                $min = $minMaxValues[$idKriteria]['min'];
                                $max = $minMaxValues[$idKriteria]['max'];

                                // Menggunakan fungsi calculateUtility untuk menghindari division by zero
                                $utility = calculateUtility($nilai, $min, $max, $jenisKriteria);
                                $utility = number_format($utility, 3);
                                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $utility . '</td>';
                            }
                            echo '</tr>';
                        $n++; 
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
    <h5 class="page-header" style="margin-bottom: 20px;">Pembobotan</h5>
    <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
        <thead>
            <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Nama Siswa</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NIS</th>
                <?php
                $sqlKriteria = "SELECT * FROM kriteria";
                $queryKriteria = mysqli_query($dbcon, $sqlKriteria);

                while ($kriteria = mysqli_fetch_array($queryKriteria)) {
                    echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $kriteria['nama_kriteria'] . '</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $n = 1;
            foreach ($dataInput as $alternatif => $nilaiKriteria) {
                $sqlNIS = "SELECT nis FROM alternatif WHERE nama = '$alternatif'";
                $queryNIS = mysqli_query($dbcon, $sqlNIS);
                $rowNIS = mysqli_fetch_assoc($queryNIS);
                $nis = $rowNIS['nis']; // Store the NIS value

                echo '<tr style="border: 1px solid #ddd;">';
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $n . '</td>';
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: justify;">' . $alternatif . '</td>';
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $nis . '</td>'; // Display NIS value here

                foreach ($nilaiKriteria as $kriteria => $nilai) {
                    $sqlKriteriaDetail = "SELECT id_kriteria, jenis_kriteria, bobot FROM kriteria WHERE nama_kriteria = '$kriteria'";
                    $queryKriteriaDetail = mysqli_query($dbcon, $sqlKriteriaDetail);
                    $row = mysqli_fetch_assoc($queryKriteriaDetail);
                    $idKriteria = $row['id_kriteria'];
                    $jenisKriteria = $row['jenis_kriteria'];
                    $bobot = $row['bobot'] / 100; // Convert bobot to decimal

                    $min = $minMaxValues[$idKriteria]['min'];
                    $max = $minMaxValues[$idKriteria]['max'];

                    // Menggunakan fungsi calculateUtility untuk menghindari division by zero
                    $utility = calculateUtility($nilai, $min, $max, $jenisKriteria);
                    $utility = number_format($utility, 3);
                    $bobot_nilai = $utility * $bobot; // Use decimal bobot here
                    $bobot_nilai = number_format($bobot_nilai, 3);

                    echo '<td style="text-align: center;">' . $bobot_nilai . '</td>';
                }
                echo '</tr>';
                $n++;
            }
            ?>
        </tbody>
    </table>
</div>

            <div class="col-lg-12">
                <h5 class="page-header" style="margin-bottom: 20px;">Nilai Akhir dan Perankingan</h5>
                <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
                    <thead>
                        <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Rangking</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Nama Siswa</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NIS</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
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

                                // Menggunakan fungsi calculateUtility untuk menghindari division by zero
                                $utility = calculateUtility($nilai, $min, $max, $jenisKriteria);
                                $utility = number_format($utility, 3);

                                // Kalkulasi bobot nilai dengan bobot desimal
                                $bobot_nilai = $utility * $bobot_desimal;
                                $bobot_nilai = number_format($bobot_nilai, 3);

                                $totalNilai += $bobot_nilai;
                            }

                            $nilaiAkhir[$alternatif] = $totalNilai;
                        }

                        arsort($nilaiAkhir);
                        $ranking = 1;
                        foreach ($nilaiAkhir as $alternatif => $totalNilai) {
                            // Fetch the NIS for the current alternatif (student)
                            $sqlNIS = "SELECT nis FROM alternatif WHERE nama = '$alternatif'";
                            $queryNIS = mysqli_query($dbcon, $sqlNIS);
                            $rowNIS = mysqli_fetch_assoc($queryNIS);
                            $nis = $rowNIS['nis']; // Store the NIS value

                            echo '<tr>';
                            echo '<tr style="border: 1px solid #ddd;">';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $ranking . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: justify;">' . $alternatif . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $nis . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . number_format($totalNilai, 3) . '</td>';
                            echo '</tr>';
                            $ranking++;
                        }
                        ?>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
                <form method="POST">
                <button type="submit" name="generate_pdf" class="btn btn-secondary" style="padding: 10px 30px;">
                    <i class="fa fa-print" aria-hidden="true" style="margin-right: 5px;"></i> Cetak
                </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'foot.php'; ?>
