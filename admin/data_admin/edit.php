<?php
ob_start();
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

$judul = "Edit Jam masuk dan pulang";
include('../layout/header.php');
require_once('../../config.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validasi id
if ($id !== 13) {
    echo "ID tidak valid.";
    exit();
}

// Query untuk mendapatkan data lokasi berdasarkan id
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE id=$id");
$lokasi = mysqli_fetch_array($result);

if (isset($_POST['submit'])) {
    $jam_masuk = $_POST['jam_masuk'];
    $jam_pulang = $_POST['jam_pulang'];

    // Update data jam masuk dan jam pulang
    $query = "UPDATE lokasi_presensi SET jam_masuk='$jam_masuk', jam_pulang='$jam_pulang' WHERE id=$id";
    if (mysqli_query($connection, $query)) {
        $_SESSION['berhasil'] = "Data berhasil diperbarui.";
        header("Location: " . base_url('admin/data_jam/jam.php'));
        exit();
    } else {
        $_SESSION['gagal'] = "Terjadi kesalahan: " . mysqli_error($connection);
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <h4>Edit Jam Presensi Lokasi</h4>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="jam_masuk" class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" value="<?= htmlspecialchars($lokasi['jam_masuk']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="jam_pulang" class="form-label">Jam Pulang</label>
                                <input type="time" class="form-control" id="jam_pulang" name="jam_pulang" value="<?= htmlspecialchars($lokasi['jam_pulang']) ?>" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="jam.php" class="btn btn-secondary tombol-kembali">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>