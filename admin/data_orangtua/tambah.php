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

$judul = "Tambah Data Orang Tua";
include('../layout/header.php');
require_once('../../config.php');

if (isset($_POST['submit'])) {
    $nis = htmlspecialchars($_POST['nis']);
    $nama_orang_tua = htmlspecialchars($_POST['nama_orang_tua']);
    $hubungan = htmlspecialchars($_POST['hubungan']);
    $kontak = htmlspecialchars($_POST['kontak']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $ulangi_password = $_POST['ulangi_password'];
    $role = "orangtua"; // Set the role to 'orangtua'
    $status = "Aktif"; // Set status default aktif

    $pesan_kesalahan = [];

    // Validasi
    if (empty($nis)) {
        $pesan_kesalahan[] = "NIS wajib diisi";
    }
    if (empty($nama_orang_tua)) {
        $pesan_kesalahan[] = "Nama Orang Tua wajib diisi";
    }
    if (empty($hubungan)) {
        $pesan_kesalahan[] = "Hubungan wajib diisi";
    }
    if (empty($kontak)) {
        $pesan_kesalahan[] = "Kontak wajib diisi";
    }
    if (empty($alamat)) {
        $pesan_kesalahan[] = "Alamat wajib diisi";
    }
    if (empty($username)) {
        $pesan_kesalahan[] = "Username wajib diisi";
    }
    if (empty($password)) {
        $pesan_kesalahan[] = "Password wajib diisi";
    }
    if ($password !== $ulangi_password) {
        $pesan_kesalahan[] = "Password tidak cocok";
    }

    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        // Dapatkan id_siswa berdasarkan nis
        $siswa_result = mysqli_query($connection, "SELECT id FROM siswa WHERE nis='$nis'");
        if (mysqli_num_rows($siswa_result) > 0) {
            $id_siswa = mysqli_fetch_assoc($siswa_result)['id'];
            
            // Simpan data orang tua ke database
            $orang_tua = mysqli_query($connection, "INSERT INTO orang_tua (id_siswa, nama_orang_tua, hubungan, kontak, alamat) 
            VALUES ('$id_siswa', '$nama_orang_tua', '$hubungan', '$kontak', '$alamat')");

            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan akun orang tua
            $user = mysqli_query($connection, "INSERT INTO users (id_siswa, username, password, status, role) 
            VALUES ('$id_siswa', '$username', '$hashed_password', '$status', '$role')");

            if ($orang_tua && $user) {
                $_SESSION['berhasil'] = 'Data orang tua berhasil disimpan';
                header('Location: orangtua.php');
                exit;
            } else {
                $_SESSION['validasi'] = 'Gagal menyimpan data';
            }
        } else {
            $_SESSION['validasi'] = 'NIS tidak ditemukan';
        }
    }
}
?>

<!-- Tampilan Form -->
<div class="page-body">
    <div class="container-xl">
        <form action="" method="POST">
            <div class="row">
                <!-- Kolom untuk form tambah data orang tua -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <?php if (isset($_SESSION['validasi'])): ?>
                                <div class="alert alert-danger">
                                    <?= $_SESSION['validasi'] ?>
                                </div>
                                <?php unset($_SESSION['validasi']); ?>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="nis">NIS Siswa</label>
                                <input type="text" class="form-control" name="nis" value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="nama_orang_tua">Nama Orang Tua</label>
                                <input type="text" class="form-control" name="nama_orang_tua" value="<?= htmlspecialchars($_POST['nama_orang_tua'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="hubungan">Hubungan</label>
                                <select name="hubungan" class="form-control">
                                    <option value="">>---- Pilih Hubungan ----<</option>
                                    <option <?= (isset($_POST['hubungan']) && $_POST['hubungan'] == 'Ayah') ? 'selected' : '' ?> value="Ayah">Ayah</option>
                                    <option <?= (isset($_POST['hubungan']) && $_POST['hubungan'] == 'Ibu') ? 'selected' : '' ?> value="Ibu">Ibu</option>
                                    <option <?= (isset($_POST['hubungan']) && $_POST['hubungan'] == 'Wali') ? 'selected' : '' ?> value="Wali">Wali</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kontak">Kontak</label>
                                <input type="text" class="form-control" name="kontak" value="<?= htmlspecialchars($_POST['kontak'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" name="alamat"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom untuk akun orang tua -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="mb-3">
                                <label for="ulangi_password">Ulangi Password</label>
                                <input type="password" class="form-control" name="ulangi_password">
                            </div>

                            <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                            <a href="<?= base_url('admin/data_orangtua/orangtua.php')?>" class="btn btn-secondary">Kembali</a>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('../layout/footer.php')?>
