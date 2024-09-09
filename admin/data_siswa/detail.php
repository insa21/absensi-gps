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

$judul = "Detail Siswa";
include('../layout/header.php');
require_once('../../config.php');

$id =  $_GET['id'];
$result = mysqli_query($connection, "SELECT users.id_siswa, users.username, users.password, users.status, users.role, 
siswa.* FROM users JOIN siswa ON users.id_siswa = siswa.id WHERE siswa.id=$id");

?>

<?php while($siswa= mysqli_fetch_array($result)) : ?>

<div class="page-body">
    <div class="container-xl">

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    
                    <table class="table">
                    <tr>
                            <td>Nis</td>
                            <td>: <?= $siswa['nis']?></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>: <?= $siswa['nama']?></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>: <?= $siswa['jenis_kelamin']?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: <?= $siswa['alamat']?></td>
                        </tr>
                        <tr>
                            <td>No Handphone</td>
                            <td>: <?= $siswa['no_handphone']?></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>: <?= $siswa['kelas']?></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>: <?= $siswa['username']?></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>: <?= $siswa['jenis_kelamin']?></td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td>: <?= $siswa['role']?></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: <?= $siswa['status']?></td>
                        </tr>
                    </table>
                    <a href="<?= base_url('admin/data_siswa/siswa.php')?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <img style="width: 350px; border-radius: 15px"src="<?= base_url('assets/img/foto_siswa/'.$siswa['foto'])?>" alt="">
        </div>
    </div>

    </div>
</div>

<?php endwhile ?>

<?php include('../layout/footer.php')?>