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

$judul = "Edit Data Hari Libur";
include('../layout/header.php');
require_once('../../config.php');

// Cek apakah ada parameter id di URL atau POST
$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);

// Jika ID tidak ada, redirect ke daftar hari libur
if (!$id) {
    header('Location: libur.php');
    exit();
}

// Ambil data dari database berdasarkan ID
$query = "SELECT * FROM hari_libur WHERE id = $id";
$result = mysqli_query($connection, $query);
$libur = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan, redirect ke daftar hari libur
if (!$libur) {
    header('Location: libur.php');
    exit();
}

// Proses form jika disubmit
if (isset($_POST['update'])) {
    $tanggal_mulai = htmlspecialchars($_POST['tanggal_mulai']);
    $tanggal_akhir = htmlspecialchars($_POST['tanggal_akhir']);
    $nama_libur = htmlspecialchars($_POST['nama_libur']);
    $jenis_libur = htmlspecialchars($_POST['jenis_libur']);
    $semester = htmlspecialchars($_POST['semester']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    $pesan_kesalahan = [];

    if (empty($tanggal_mulai)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Tanggal mulai wajib diisi";
    }
    if (empty($tanggal_akhir)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Tanggal akhir wajib diisi";
    }
    if (empty($nama_libur)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Nama libur wajib diisi";
    }
    if (empty($jenis_libur)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Jenis libur wajib diisi";
    }
    if (empty($semester)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Semester wajib diisi";
    }

    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        $query = "UPDATE hari_libur SET 
            tanggal_mulai = '$tanggal_mulai',
            tanggal_akhir = '$tanggal_akhir',
            nama_libur = '$nama_libur',
            jenis_libur = '$jenis_libur',
            semester = '$semester',
            deskripsi = '$deskripsi'
            WHERE id = $id";

        mysqli_query($connection, $query);

        $_SESSION['berhasil'] = 'Data berhasil diperbarui';
        header('Location: libur.php');
        exit;
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-8">
            <div class="card-body">
                <form action="<?= base_url('admin/data_libur/edit.php') ?>" method="POST">
                    <div class="mb-3">
                        <label for="">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?= htmlspecialchars($libur['tanggal_mulai']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_akhir" value="<?= htmlspecialchars($libur['tanggal_akhir']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="">Nama Libur</label>
                        <input type="text" class="form-control" name="nama_libur" value="<?= htmlspecialchars($libur['nama_libur']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="">Jenis Libur</label>
                        <select name="jenis_libur" class="form-control">
                            <option value="">>---- Pilih jenis libur ----<</option>
                            <option <?= $libur['jenis_libur'] == 'Libur Nasional' ? 'selected' : '' ?> value="Libur Nasional">Libur Nasional</option>
                            <option <?= $libur['jenis_libur'] == 'Libur Akademik' ? 'selected' : '' ?> value="Libur Akademik">Libur Akademik</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Semester</label>
                        <select name="semester" class="form-control">
                            <option value="">>---- Pilih semester ----<</option>
                            <option <?= $libur['semester'] == 'Ganjil' ? 'selected' : '' ?> value="Ganjil">Ganjil</option>
                            <option <?= $libur['semester'] == 'Genap' ? 'selected' : '' ?> value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi"><?= htmlspecialchars($libur['deskripsi']) ?></textarea>
                    </div>
                    <input type="hidden" value="<?= htmlspecialchars($libur['id']) ?>" name="id">
                    <a href="harilibur.php" class="btn btn-secondary tombol-kembali">Kembali</a>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php')?>
