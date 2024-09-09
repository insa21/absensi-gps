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

$judul = "Data Siswa";
include('../layout/header.php');
require_once('../../config.php');

// Filter kelas
$selected_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';

// Query data siswa berdasarkan filter kelas
$query = "
    SELECT users.id_siswa, users.username, users.password, users.status, users.role, siswa.* 
    FROM users 
    JOIN siswa ON users.id_siswa = siswa.id 
    WHERE users.role = 'siswa'"; // Filter hanya siswa

if ($selected_kelas && $selected_kelas !== 'Semua') {
    // Filter berdasarkan kelas
    $query .= " AND siswa.kelas = '$selected_kelas'";
}

$result = mysqli_query($connection, $query);

// Ambil daftar kelas dari tabel kelas untuk dropdown
$kelas_result = mysqli_query($connection, "SELECT DISTINCT kelas FROM kelas");
?>

<div class="page-body">
    <div class="container-xl">

    <a href ="<?= base_url('admin/data_siswa/tambah.php')?>" class="btn btn-primary mb-3">
        <span class="text"><i class="fa-solid fa-circle-plus"></i> Tambah Data <br></span>
    </a>

        <!-- Filter Kelas -->
        <form action="" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="kelas" class="form-control" onchange="this.form.submit()">
                    <option value="Semua" <?= $selected_kelas == 'Semua' ? 'selected' : '' ?>>Semua Kelas</option>
                    <?php while ($kelas = mysqli_fetch_array($kelas_result)) : ?>
                        <option value="<?= $kelas['kelas'] ?>" <?= $selected_kelas == $kelas['kelas'] ? 'selected' : '' ?>>
                            <?= $kelas['kelas'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </form>

    <table class="table table-bordered mt-3">
        <tr class="text-center">
            <th>No</th>
            <th>Nis</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Username</th>
            <th>Kelas</th>
            <th>Aksi</th>
        </tr>

        <?php if(mysqli_num_rows($result) === 0) { ?>
            <tr>
                <td colspan="8" class="text-center">Data kosong, silahkan tambahkan data baru</td>
            </tr>
        <?php } else { ?>
            <?php $no = 1; ?>
            <?php while($siswa = mysqli_fetch_array($result)) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $siswa['nis'] ?></td>
                    <td><?= $siswa['nama'] ?></td>
                    <td><?= $siswa['alamat'] ?></td>
                    <td><?= $siswa['username'] ?></td>
                    <td><?= $siswa['kelas'] ?></td>
                    <td class="text-center">
                        <a href="<?= base_url('admin/data_siswa/detail.php?id='.$siswa['id'])?>" 
                        class="badge badge-pill bg-primary">Detail</a>
                        <a href="<?= base_url('admin/data_siswa/edit.php?id='.$siswa['id'])?>" 
                        class="badge badge-pill bg-primary">Edit</a>
                        <a href="<?= base_url('admin/data_siswa/hapus.php?id='.$siswa['id'])?>" 
                        class="badge badge-pill bg-danger tombol-hapus">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php } ?>
    </table>
    </div>
</div>
<?php include('../layout/footer.php')?>
