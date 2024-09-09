<?php 
ob_start();
session_start();

if(!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
}

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

$judul = "Ubah Password";
include('../layout/header.php');
require_once('../../config.php');


if(isset($_POST['update'])){
    $id = $_SESSION['id'];
    // Ambil password dari input
    $password_baru = $_POST['password_baru'];
    $ulangi_password_baru = $_POST['ulangi_password_baru'];
    $pesan_kesalahan = [];

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Validasi input
        if(empty($password_baru)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Password baru wajib diisi";
        }
        if(empty($ulangi_password_baru)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Ulangi Password baru wajib diisi";
        }
        if($password_baru !== $ulangi_password_baru){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Password tidak cocok";
        }

        // Jika ada kesalahan validasi
        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // Hash password baru
            $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

            // Simpan data ke database
            $siswa = mysqli_query($connection, "UPDATE users SET
                password ='$password_hashed'
            WHERE id_siswa=$id");

            if($siswa){
                $_SESSION['berhasil'] = 'Password berhasil diubah';
                header('Location: ../home/home.php');
                exit;
            } else {
                $_SESSION['validasi'] = 'Terjadi kesalahan saat mengubah password';
            }
        }
    }
}

?>


<div class="page-body">
    <div class="container-xl">
        <form action="" method="POST">

        <div class="card col-md-6">
            <div class="card-body">
            <div class="mb-3">
                <label for="">Password Baru</label>
                <input type="password" name="password_baru" class="form-control">
            </div>

            <div class="mb-3">
                <label for="">Ulangi Password Baru</label>
                <input type="password" name="ulangi_password_baru" class="form-control">
            </div>

            <input type="hidden" name="id" value="<?= $_SESSION['id'];?>">

            <button type="submit" class="btn btn-primary" name="update">Update</button>
            </div>
        </div>
        </form>
    </div>
</div>
<?php include('../layout/footer.php')?>