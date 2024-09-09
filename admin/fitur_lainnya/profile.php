<?php 
session_start();

if(!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$id = $_SESSION['id'];
$judul = "";
include('../layout/header.php');
require_once('../../config.php');

// Query untuk mendapatkan data pengguna berdasarkan id
$result = mysqli_query($connection, "SELECT * FROM users WHERE id=$id");
?>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                    <?php if ($role === 'admin'): ?>
                        <table class="table mt-3">
                            <tr>
                                <td>Username :</td>
                                <td><?= $username?></td>
                            </tr>
                            <tr>
                                <td>Role :</td>
                                <td><?= $role?></td>
                            </tr>
                        </table>
                        <a href="<?= base_url('admin/home/home.php')?>" class="btn btn-secondary">Kembali</a>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</div>
<?php include('../layout/footer.php')?>
