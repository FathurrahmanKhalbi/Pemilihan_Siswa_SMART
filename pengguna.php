<?php
include 'koneksi.php'; // Koneksi ke database
require_once 'nav.php'; // Navbar

// Ambil data admin dari database
$query = "SELECT * FROM admin";
$result = mysqli_query($dbcon, $query);
?>

<div id="page-wrapper" style="padding: 70px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="letter-spacing: -2px; margin-bottom: 40px; font-weight: 600;">Admin</h1>
                <h5 class="page-header" style="margin-bottom: 20px;">Silahkan Ubah Data Pengguna  </h5>
                
                <!-- Tabel Pengguna -->
                <div class="table-responsive">
    <table class="table table-hover" style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
        <thead>
            <tr style="background-color: #f2f2f2; border: 1px solid #ddd;">
                <th style="border: 1px solid #ddd; text-align: center;">No</th>
                <th style="border: 1px solid #ddd; text-align: center;">Nama Pengguna</th>
                <th style="border: 1px solid #ddd; text-align: center;">Username</th>
                <th style="border: 1px solid #ddd; text-align: center;">Password</th>
                <th style="border: 1px solid #ddd; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr style="border: 1px solid #ddd;">
                    <td style="border: 1px solid #ddd; text-align: center;"><?= $no++; ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= htmlspecialchars($row['nama']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= htmlspecialchars($row['username']); ?></td>
                    <td style="border: 1px solid #ddd; text-align: center;"><?= htmlspecialchars($row['password']); ?></td>
                    <td style="border: 1px solid #ddd; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 10px;">
                            <!-- Tombol Edit -->
                            <a href="edit_pengguna.php?id=<?= $row['id_admin']; ?>" style="color: blue; text-decoration: none; padding: 5px 10px; border: 1px solid blue; border-radius: 4px;">
                                <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                            </a>
                        </div>
                    </td>
                </tr>
            <?php 
        } 
        ?>
        </tbody>
    </table>
</div>

                <!-- Akhir Tabel Pengguna -->

            </div>
        </div>
    </div>
</div>



<?php
require_once 'foot.php'; // Footer
?>
