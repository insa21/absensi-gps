<?php 
session_start();
ob_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

$judul = "Detail Hari Libur";
include('../layout/header.php');
require_once('../../config.php');

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah ID valid
if (!$id) {
    header('Location: libur.php');
    exit();
}

// Ambil data hari libur berdasarkan ID
$query = "SELECT * FROM hari_libur WHERE id = $id";
$result = mysqli_query($connection, $query);
$libur = mysqli_fetch_assoc($result);

// Cek apakah data ada
if (!$libur) {
    header('Location: libur.php');
    exit();
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Tanggal Mulai</th>
                        <td><?= htmlspecialchars($libur['tanggal_mulai']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Akhir</th>
                        <td><?= htmlspecialchars($libur['tanggal_akhir']) ?></td>
                    </tr>
                    <tr>
                        <th>Nama Libur</th>
                        <td><?= htmlspecialchars($libur['nama_libur']) ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Libur</th>
                        <td><?= htmlspecialchars($libur['jenis_libur']) ?></td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td><?= htmlspecialchars($libur['semester']) ?></td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td><?= htmlspecialchars($libur['deskripsi']) ?></td>
                    </tr>
                </table>
                <a href="harilibur.php" class="btn btn-secondary tombol-kembali">Kembali</a>
                <a href="edit.php?id=<?= $id ?>" class="btn btn-primary">Edit</a>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php')?>
