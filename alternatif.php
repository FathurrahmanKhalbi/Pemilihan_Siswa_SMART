<?php
include 'koneksi.php';
require_once 'nav.php';
?>

<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="letter-spacing: -2px; font-weight: 600;">Data Siswa</h1>
            </div>
            <div class="">
                <form role="form" action="" method="POST">
                    <div class="form-group" style="padding: 20px 0;">
                        <label style="margin-bottom: 5px;">Nama Siswa</label>
                        <input style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;" type="text" required name="nama" class="form-control" placeholder="Nama Siswa">
                        <label style="margin-bottom: 5px;">NIS</label>
                        <input style="margin-bottom: 20px; border: 1px solid #000; padding: 15px; width: 100%;" type="text" name="nis" class="form-control" placeholder="NIS">
                        <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary" style="margin-right: 10px; padding: 10px 30px; border: none;">
                            <i class="fa fa-save" aria-hidden="true" style="margin-right: 5px;"></i> Tambah Siswa
                        </button>
                     </div>
                </form>
                <?php
                include 'koneksi.php';
                require_once 'nav.php';

                if (isset($_POST['submit'])) {
                    $nama = $_POST['nama'];
                    $nis = $_POST['nis'];
                    $sql = "INSERT INTO alternatif (nama, nis) VALUES ('$nama', '$nis')";
                    $query = mysqli_query($dbcon, $sql);

                    if ($query) {
                        echo "<script>alert('Berhasil memasukkan data Alternatif')</script>";
                    } else {
                        echo "<script>alert('Gagal Memasukkan data')</script>";
                    }
                }
                ?>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-default" style="margin-top: 40px;">
                    <h5>
                        Data Siswa 
                    </h5>
                    <div class="panel-body">
                        <div class="table-responsive">
                        <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
                            <thead>
                                <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                                    <th style="border: 1px solid #ddd; text-align: justify;">No</th>
                                    <th style="border: 1px solid #ddd; text-align: center;">Nama Siswa</th>
                                    <th style="border: 1px solid #ddd; text-align: center;">NIS</th>
                                    <th style="border: 1px solid #ddd; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php
    $sql = "SELECT * FROM alternatif";
    $query = mysqli_query($dbcon, $sql);
    $n = 1;
    while ($alternatif = mysqli_fetch_array($query)) {
    ?>
        <tr style="border: 1px solid #ddd;">
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= $n ?></td>
            <!-- Menggunakan ucwords untuk merapikan nama siswa -->
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;"><?= ucwords(strtolower($alternatif['nama'])) ?></td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= $alternatif['nis'] ?></td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                <div style="display: flex; justify-content: center; gap: 10px;">
                    <a href="editalternatif.php?id_alt=<?= $alternatif['id_alt']; ?>" style="color: blue; text-decoration: none; padding: 5px 10px; border: 1px solid blue; border-radius: 4px;">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </a>
                    <a href="hapus_alt.php?id_alt=<?= $alternatif['id_alt']; ?>" onclick="return confirm('Apakah yakin menghapus ?')" style="color: red; text-decoration: none; padding: 5px 10px; border: 1px solid red; border-radius: 4px;">
                        <i class="fa fa-trash-o" aria-hidden="true"></i> Hapus
                    </a>
                </div>
            </td>
        </tr>
    <?php
        $n++;
    }
    ?>
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
