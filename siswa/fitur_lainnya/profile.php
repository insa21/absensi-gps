<?php 
session_start();

if(!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
}

if($_SESSION['role'] !== 'siswa') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

$judul = "";
include('../layout/header.php');
require_once('../../config.php');

$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT users.id_siswa, users.username, users.status, users.role, 
siswa.* FROM users JOIN siswa ON users.id_siswa = siswa.id WHERE siswa.id=$id");
?>
<?php while($siswa= mysqli_fetch_array($result)): ?>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                    <center>
                    <img style="border-radius: 100%; width: 50%;" src="<?= base_url('assets/img/foto_siswa/' . $siswa['foto'] )?>" alt="">
                    </center>


                        <table class="table mt-3">
                            <tr>
                                <td>Nama :</td>
                                <td><?= $siswa['nama']?></td>
                            </tr>
                            <tr>
                                <td>Jenis kelamin :</td>
                                <td><?= $siswa['jenis_kelamin']?></td>
                            </tr>
                            <tr>
                                <td>Alamat :</td>
                                <td><?= $siswa['alamat']?></td>
                            </tr>
                            <tr>
                                <td>No Handphone :</td>
                                <td><?= $siswa['no_handphone']?></td>
                            </tr>
                            <tr>
                                <td>Jabatan :</td>
                                <td><?= $siswa['jabatan']?></td>
                            </tr>
                            <tr>
                                <td>Username :</td>
                                <td><?= $siswa['username']?></td>
                            </tr>
                            <tr>
                                <td>Role :</td>
                                <td><?= $siswa['role']?></td>
                            </tr>
                            <tr>
                                <td>Lokasi Presensi :</td>
                                <td><?= $siswa['lokasi_presensi']?></td>
                            </tr>
                            <tr>
                                <td>Status :</td>
                                <td><?= $siswa['status']?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</div>
<?php endwhile; ?>
<?php include('../layout/footer.php')?>