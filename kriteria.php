<?php
include 'koneksi.php';
require_once 'nav.php';
?>

<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="letter-spacing: -2px; margin-bottom: 40px; font-weight: 600;">Data Kriteria</h1>

            </div>
            <div class="col-lg-8">
                <form role="form" action="" method="POST">
                    <div class="form-group">
                    <label style="margin-bottom: 5px;">Kriteria</label>
                        <input type="text" style=" margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;" required name="kriteria" class="form-control" placeholder="Nama Kriteria">
                    </div>
                    <div class="form-group">
                    <label style="margin-bottom: 5px;">Bobot</label>
                        <input type="text" style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;" required name="bobot" class="form-control" placeholder="Bobot">
                    </div>
                    <div class="form-group">
                    <label style="margin-bottom: 5px;">Jenis Kriteria</label>
                        <select style="padding: 15px; border: 1px solid #000;" class="form-select" required name="jenis_kriteria" aria-label="Default select example">
                            <option value="">Pilih</option>
                            <option value="Benefit">Benefit</option>
                            <option value="Cost">Cost</option>
                        </select>
                        <div class="d-flex justify-content-between align-items-center" style="margin-top: 20px;">
                        <button type="submit" name="submit" class="btn btn-primary" style="padding: 10px 30px;">
                            <i class="fa fa-save" aria-hidden="true" style="margin-right: 5px;"></i> Simpan
                        </button>
                        </div>

                    </div>

                </form>
                <?php
                include 'koneksi.php';
                require_once 'nav.php';

                if (isset($_POST['submit'])) {
                    $kriteria = $_POST['kriteria'];
                    $bobot = $_POST['bobot'];
                    $jenis_kriteria = $_POST['jenis_kriteria'];

                    $sql = "INSERT INTO kriteria (nama_kriteria, bobot, jenis_kriteria) VALUES ('$kriteria','$bobot','$jenis_kriteria')";
                    $query = mysqli_query($dbcon, $sql);

                    if ($query) {
                        echo "<script>alert('Berhasil memasukkan data Kriteria')</script>";
                    } else {
                        echo "<script>alert('Gagal Memasukkan data')</script>";
                    }
                } else {
                }
                ?>
            </div>
            <div class="col-lg-12" style="margin-top: 40px;">
                <div class="panel panel-default">
                    <h5 style="margin-bottom: 20px;">
                        Data Kriteria
                    </h5>
                    <div class="panel-body">
                        <div class="table-responsive">
                        <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
                                <thead>
                                <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                                        <th style="border: 1px solid #ddd; text-align: center;">Kode</th>
                                        <th style="border: 1px solid #ddd; text-align: center;">Nama Kriteria</th>
                                        <th style="border: 1px solid #ddd; text-align: center;">Bobot Kriteria</th>
                                        <th style="border: 1px solid #ddd; text-align: center;">Jenis Kriteria</th>
                                        <th style="border: 1px solid #ddd; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
    <?php
    $sqljumlah = "SELECT SUM(bobot) FROM kriteria";
    $queryjumlah = mysqli_query($dbcon, $sqljumlah);
    $jumlah0 = mysqli_fetch_array($queryjumlah);
    $jumlah = $jumlah0[0];

    $sql = "SELECT * FROM kriteria";
    $query = mysqli_query($dbcon, $sql);
    $n = 1;
    while ($barisbobot = mysqli_fetch_assoc($query)) {
    ?>
        <tr style="border: 1px solid #ddd;">
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= "C" . $n ?></td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;"><?= ucwords(strtolower($barisbobot['nama_kriteria'])) ?></td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= $barisbobot['bobot'] ?></td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                <?= $barisbobot['jenis_kriteria'] === 'Benefit' ? 'Benefit' : ($barisbobot['jenis_kriteria'] === 'Cost' ? 'Cost' : 'Unknown') ?>
            </td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                <div style="display: flex; justify-content: center; gap: 10px;">
                    <a href="editkriteria.php?id_kriteria=<?= $barisbobot['id_kriteria'] ?>" style="color: blue; text-decoration: none; padding: 5px 10px; border: 1px solid blue; border-radius: 4px;">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </a>
                    <a href="hapus_kriteria.php?id_kriteria=<?= $barisbobot['id_kriteria'] ?>" onclick="return confirm('Apakah yakin menghapus ?')" style="color: red; text-decoration: none; padding: 5px 10px; border: 1px solid red; border-radius: 4px;">
                        <i class="fa fa-trash-o" aria-hidden="true"></i> Hapus
                    </a>
                </div>
            </td>
        </tr>
    <?php
        $n++;
    }
    ?>
    <tr style="background-color: #f9f9f9; text-align: center;">
        <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">Total</td>
        <td style="border: 1px solid #ddd; padding: 8px;"></td>
        <td style="border: 1px solid #ddd; padding: 8px;"><?= $jumlah ?></td>
        <td style="border: 1px solid #ddd; padding: 8px;"></td>
        <td style="border: 1px solid #ddd; padding: 8px;"></td>
    </tr>
</tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php
require_once 'foot.php';
?>
