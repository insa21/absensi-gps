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

$judul = "Edit Data Orang Tua";
include('../layout/header.php');
require_once('../../config.php');

// Ambil ID orang tua dari URL
$id_orang_tua = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_orang_tua <= 0) {
    header('Location: orangtua.php');
    exit();
}

// Ambil data orang tua dan siswa terkait
$query_orangtua = "
    SELECT orang_tua.*, siswa.nis, siswa.nama AS nama_siswa, siswa.kelas, users.username
    FROM orang_tua
    JOIN siswa ON orang_tua.id_siswa = siswa.id
    JOIN users ON orang_tua.id_siswa = users.id_siswa
    WHERE orang_tua.id = '$id_orang_tua'
";

$orang_tua_result = mysqli_query($connection, $query_orangtua);

if (mysqli_num_rows($orang_tua_result) === 0) {
    header('Location: orangtua.php');
    exit();
}

$data_orangtua = mysqli_fetch_assoc($orang_tua_result);

if (isset($_POST['submit'])) {
    $nis = htmlspecialchars($_POST['nis']);
    $nama_orang_tua = htmlspecialchars($_POST['nama_orang_tua']);
    $hubungan = htmlspecialchars($_POST['hubungan']);
    $kontak = htmlspecialchars($_POST['kontak']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $ulangi_password = $_POST['ulangi_password'];
    
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
    if ($password && $password !== $ulangi_password) {
        $pesan_kesalahan[] = "Password tidak cocok";
    }

    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        // Update data orang tua
        $update_orangtua = mysqli_query($connection, "UPDATE orang_tua 
            SET nama_orang_tua='$nama_orang_tua', hubungan='$hubungan', kontak='$kontak', alamat='$alamat'
            WHERE id='$id_orang_tua'");

        // Update data akun orang tua
        $update_user = "UPDATE users SET username='$username'";
        if ($password) {
            // Hash password jika diubah
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_user .= ", password='$hashed_password'";
        }
        $update_user .= " WHERE id_siswa=(SELECT id_siswa FROM orang_tua WHERE id='$id_orang_tua')";

        $update_user_result = mysqli_query($connection, $update_user);

        if ($update_orangtua && $update_user_result) {
            $_SESSION['berhasil'] = 'Data orang tua berhasil diperbarui';
            header('Location: orangtua.php');
            exit;
        } else {
            $_SESSION['validasi'] = 'Gagal memperbarui data';
        }
    }
}
?>

<!-- Tampilan Form -->
<div class="page-body">
    <div class="container-xl">
        <form action="" method="POST">
            <div class="row">
                <!-- Kolom untuk form edit data orang tua -->
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
                                <input type="text" class="form-control" name="nis" value="<?= htmlspecialchars($data_orangtua['nis']) ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="nama_orang_tua">Nama Orang Tua</label>
                                <input type="text" class="form-control" name="nama_orang_tua" value="<?= htmlspecialchars($data_orangtua['nama_orang_tua']) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="hubungan">Hubungan</label>
                                <select name="hubungan" class="form-control">
                                    <option <?= $data_orangtua['hubungan'] == 'Ayah' ? 'selected' : '' ?> value="Ayah">Ayah</option>
                                    <option <?= $data_orangtua['hubungan'] == 'Ibu' ? 'selected' : '' ?> value="Ibu">Ibu</option>
                                    <option <?= $data_orangtua['hubungan'] == 'Wali' ? 'selected' : '' ?> value="Wali">Wali</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kontak">Kontak</label>
                                <input type="text" class="form-control" name="kontak" value="<?= htmlspecialchars($data_orangtua['kontak']) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" name="alamat"><?= htmlspecialchars($data_orangtua['alamat']) ?></textarea>
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
                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($data_orangtua['username']) ?>">
                            </div>

                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="mb-3">
                                <label for="ulangi_password">Ulangi Password</label>
                                <input type="password" class="form-control" name="ulangi_password">
                            </div>

                            <button type="submit" class="btn btn-primary" name="submit">Simpan Perubahan</button>
                            <a href="<?= base_url('admin/data_orangtua/orangtua.php')?>" class="btn btn-secondary">Kembali</a>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('../layout/footer.php')?>
