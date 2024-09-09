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

$judul = "Data Admin";
include('../layout/header.php');
require_once('../../config.php');

// Filter berdasarkan role admin
$query = "
    SELECT users.id, users.username, users.password, users.status, users.role
    FROM users
    WHERE users.role = 'admin'"; // Filter hanya admin

$result = mysqli_query($connection, $query);
?>

<div class="page-body">
    <div class="container-xl">

        <a href="<?= base_url('admin/data_admin/tambah.php')?>" class="btn btn-primary mb-3">Tambah Data admin
        </a>

        <table class="table table-bordered mt-3">
            <tr class="text-center">
                <th>No</th>
                <th>Username</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php if (mysqli_num_rows($result) === 0) { ?>
                <tr>
                    <td colspan="4" class="text-center">Data admin kosong</td>
                </tr>
            <?php } else { ?>
                <?php $no = 1; ?>
                <?php while ($admin = mysqli_fetch_array($result)) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td><?= htmlspecialchars($admin['status']) ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('admin/data_admin/edit.php?id=' . $admin['id'])?>" 
                            class="badge badge-pill bg-warning">Edit</a>
                            <a href="<?= base_url('admin/data_admin/hapus.php?id=' . $admin['id'])?>" 
                            class="badge badge-pill bg-danger tombol-hapus">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php } ?>
        </table>
    </div>
</div>
<?php include('../layout/footer.php')?>
