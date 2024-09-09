<?php 
session_start();

if(!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
}

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

$judul = "Jam masuk dan pulang";
include('../layout/header.php');
require_once('../../config.php');

// Query untuk mendapatkan data lokasi presensi
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY id DESC");
?>

<div class="page-body">
    <div class="container-xl">

        <?php if(mysqli_num_rows($result) === 0) { ?>
            <p class="text-center">Data kosong, silahkan tambahkan data baru</p>
        <?php } else { ?>
            <?php while($lokasi = mysqli_fetch_array($result)) : ?>
                <div class="mb-4 p-3 border rounded">
                    <h4><strong>Nama Sekolah : </strong><?= $lokasi['nama_lokasi'] ?></h4>
                    <p><strong>Alamat Sekolah : </strong> <?= $lokasi['alamat_lokasi'] ?></p>
                    <p><strong>Jam Masuk : </strong> <?= $lokasi['jam_masuk'] ?></p>
                    <p><strong>Jam Pulang : </strong> <?= $lokasi['jam_pulang'] ?></p>
                    <div class="mt-2">
                        <!-- Tombol Edit Jam Presensi -->
                        <a href="<?= base_url('admin/data_jam/edit.php?id='.$lokasi['id']) ?>" 
                        class="btn btn-sm btn-primary">Edit Jam</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php } ?>
    </div>
</div>

<?php include('../layout/footer.php'); ?>
