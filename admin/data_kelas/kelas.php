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

$judul = "Data Kelas";
include('../layout/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT * FROM kelas ORDER BY id DESC");
?>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
          <a href ="<?= base_url('admin/data_kelas/tambah.php')?>" class="btn btn-primary"><span class="text">
            <i class="fa-solid fa-circle-plus"></i>Tambah Data</span></a>
            <div class="row row-deck row-cards mt-2">


            <table class="table table-bordered">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Aksi</th>
                </tr>
                <?php if(mysqli_num_rows($result) === 0 ) : ?>
                    <tr>
                        <td colspan="3">
                            Data Masih Kosong Silahkan Tambahkan Data Baru
                        </td>
                    </tr>
                    <?php else :  ?>
                    <?php $no=1;
                        while($kelas = mysqli_fetch_array($result)): ?>

                    <tr>
                        <td><?= $no++?></td>
                        <td><?= $kelas['kelas']?></td>
                        <td  class="text-center">
                        <a href="<?= base_url('admin/data_kelas/edit.php?id='.$kelas['id'])?>" class="badge bg-primary">Edit</a>
                        <a href="<?= base_url('admin/data_kelas/hapus.php?id='.$kelas['id'])?>" class="badge bg-danger tombol-hapus">Hapus</a>
                        </td>
                    </tr>

                    <?php endwhile; ?>

                <?php endif;?>

            </table>


            </div>
          </div>
        </div>


        <?php include('../layout/footer.php')?>