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

$judul = "Detail Orang Tua";
include('../layout/header.php');
require_once('../../config.php');

$id = $_GET['id'];
$query = "
    SELECT orang_tua.*, siswa.nis, siswa.nama AS nama_siswa, siswa.kelas
    FROM orang_tua
    JOIN siswa ON orang_tua.id_siswa = siswa.id
    WHERE orang_tua.id = '$id'
";

$result = mysqli_query($connection, $query);

?>

<?php while($orang_tua = mysqli_fetch_array($result)) : ?>

<div class="page-body">
    <div class="container-xl">

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    
                    <table class="table">
                        <tr>
                            <td>Nama Orang Tua</td>
                            <td>: <?= htmlspecialchars($orang_tua['nama_orang_tua']) ?></td>
                        </tr>
                        <tr>
                            <td>Hubungan</td>
                            <td>: <?= htmlspecialchars($orang_tua['hubungan']) ?></td>
                        </tr>
                        <tr>
                            <td>Kontak</td>
                            <td>: <?= htmlspecialchars($orang_tua['kontak']) ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: <?= htmlspecialchars($orang_tua['alamat']) ?></td>
                        </tr>
                        <tr>
                            <td>Nama Siswa</td>
                            <td>: <?= htmlspecialchars($orang_tua['nama_siswa']) ?></td>
                        </tr>
                        <tr>
                            <td>NIS</td>
                            <td>: <?= htmlspecialchars($orang_tua['nis']) ?></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>: <?= htmlspecialchars($orang_tua['kelas']) ?></td>
                        </tr>
                    </table>
                    <a href="<?= base_url('admin/data_orangtua/orangtua.php')?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>

<?php endwhile ?>

<?php include('../layout/footer.php')?>
