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

$judul = "Data Hari Libur";
include('../layout/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT * FROM hari_libur ORDER BY tanggal_mulai DESC");
?>

<div class="page-body">
    <div class="container-xl">

    <a href="<?= base_url('admin/data_harilibur/tambah.php')?>" class="btn btn-primary">
        <span class="text"><i class="fa-solid fa-circle-plus"></i> Tambah Data</span>
    </a>
    <table class="table table-bordered mt-3">
        <tr class="text-center">
                <th>No</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Akhir</th>
                <th>Nama Libur</th>
                <th>Jenis Libur</th>
                <th>Semester</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
        </tr>
        <?php if(mysqli_num_rows($result) === 0) { ?>
            <tr>
                <td colspan="8" class="text-center">Data kosong, silahkan tambahkan data baru</td>
            </tr>
        <?php } else { ?>
            <?php $no = 1; ?>
            <?php while($libur = mysqli_fetch_array($result)) : ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $libur['tanggal_mulai'] ?></td>
                    <td><?= $libur['tanggal_akhir'] ?></td>
                    <td><?= $libur['nama_libur'] ?></td>
                    <td><?= $libur['jenis_libur'] ?></td>
                    <td><?= $libur['semester'] ?: '-' ?></td>
                    <td><?= $libur['deskripsi'] ?: '-' ?></td>
                    <td class="text-center">
                        <a href="<?= base_url('admin/data_harilibur/detail.php?id='.$libur['id'])?>" 
                        class="badge badge-pill bg-primary">Detail</a>
                        <a href="<?= base_url('admin/data_harilibur/edit.php?id='.$libur['id'])?>" 
                        class="badge badge-pill bg-primary">Edit</a>
                        <a href="<?= base_url('admin/data_harilibur/hapus.php?id='.$libur['id'])?>" 
                        class="badge badge-pill bg-danger tombol-hapus">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php } ?>
    </table>
    </div>
</div>

<?php include('../layout/footer.php')?>
