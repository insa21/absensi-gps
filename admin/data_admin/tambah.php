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

$judul = "Tambah Data Admin";
include('../layout/header.php');
require_once('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status = htmlspecialchars($_POST['status']);
    $role = "admin";

    // Query untuk menambahkan data admin
    $query = "INSERT INTO users (username, password, status, role, id_siswa) VALUES ('$username', '$password', '$status', 'admin', NULL)";
    
    if (mysqli_query($connection, $query)) {
        $_SESSION['berhasil'] = 'Data berhasil disimpan';
        header('Location: admin.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <form action="" method="POST">
            <div class="mb-3">
                <label for="">Username</label>
                <input type="text" class="form-control" name="username" value="<?php if (isset($_POST['username'])) echo htmlspecialchars($_POST['username']); ?>">
            </div>

            <div class="mb-3">
                <label for="">Password</label>
                <input type="password" class="form-control" name="password">
            </div>

            <div class="mb-3">
                <label for="">Ulangi Password</label>
                <input type="password" class="form-control" name="ulangi_password">
            </div>

            <div class="mb-3">
                <label for="">Status</label>
                <select name="status" class="form-control">
                    <option value="">>---- Pilih status ----<</option>
                    <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Aktif') echo 'selected'; ?> value="Aktif">Aktif</option>
                    <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Tidak aktif') echo 'selected'; ?> value="Tidak aktif">Tidak aktif</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Tambah</button>
            <a href="<?= base_url('admin/data_admin/admin.php')?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<?php include('../layout/footer.php'); ?>
<?php ob_end_flush(); ?>
