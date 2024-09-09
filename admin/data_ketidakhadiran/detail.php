<?php
ob_start(); // Memulai buffering output
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
} elseif ($_SESSION["role"] != 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

$judul = "Detail Ketidakhadiran";
include('../layout/header.php');
include_once("../../config.php");

// Proses update jika form disubmit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $status_pengajuan = $_POST['status_pengajuan'];

    // Sanitasi ID dan status_pengajuan untuk mencegah SQL Injection
    $id = mysqli_real_escape_string($connection, $id);
    $status_pengajuan = mysqli_real_escape_string($connection, $status_pengajuan);

    $query = "UPDATE ketidakhadiran SET status_pengajuan = '$status_pengajuan' WHERE id = '$id'";
    if (mysqli_query($connection, $query)) {
        $_SESSION['berhasil'] = 'Status Pengajuan berhasil diupdate';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui status pengajuan: ' . mysqli_error($connection);
    }
    header('Location: ketidakhadiran.php');
    exit;
}

// Mengambil data untuk ditampilkan di form
$id = $_GET['id'] ?? '';
$id = mysqli_real_escape_string($connection, $id); // Sanitasi ID

$query = "SELECT * FROM ketidakhadiran WHERE id = '$id'";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $tanggal = $data['tanggal'] ?? '';
    $keterangan = $data['keterangan'] ?? '';
    $status_pengajuan = $data['status_pengajuan'] ?? '';
    $file = $data['file'] ?? '';
} else {
    $_SESSION['error'] = 'Data tidak ditemukan';
    header('Location: ketidakhadiran.php');
    exit;
}
?>

<div class="page-body">
    <div class="container-xl">

        <div class="card col-md-6">
            <div class="card-body">

                <form action="" method="POST">
                    <div class="md-3">
                        <label for="">Tanggal</label>
                        <input name="tanggal" type="date" class="form-control" value="<?= htmlspecialchars($tanggal); ?>" readonly>
                    </div>

                    <div class="md-3">
                        <label for="">Keterangan</label>
                        <input name="keterangan" type="text" class="form-control" value="<?= htmlspecialchars($keterangan); ?>" readonly>
                    </div>

                    <div class="md-3">
                        <label for="">Status Pengajuan</label>
                        <select name="status_pengajuan" class="form-control">
                            <option value="">>---- Pilih Status ----<< /option>
                            <option <?= $status_pengajuan == 'PENDING' ? 'selected' : '' ?> value="PENDING">PENDING</option>
                            <option <?= $status_pengajuan == 'REJECTED' ? 'selected' : '' ?> value="REJECTED">REJECTED</option>
                            <option <?= $status_pengajuan == 'APPROVED' ? 'selected' : '' ?> value="APPROVED">APPROVED</option>
                        </select>
                    </div>

                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                </form>

            </div>
        </div>

    </div>
</div>

<?php
include('../layout/footer.php');
ob_end_flush(); // Mengakhiri buffering output
?>