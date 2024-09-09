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

$judul = "Data Orang Tua";
include('../layout/header.php');
require_once('../../config.php');

// Filter kelas
$selected_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';

// Query untuk mengambil data orang tua dan siswa terkait
$query = "
    SELECT DISTINCT orang_tua.id, orang_tua.nama_orang_tua, orang_tua.hubungan, orang_tua.kontak, orang_tua.alamat, 
           siswa.nis, siswa.nama AS nama_siswa, siswa.kelas
    FROM orang_tua
    JOIN siswa ON orang_tua.id_siswa = siswa.id
    JOIN users ON orang_tua.id_siswa = users.id_siswa
    WHERE users.role = 'orangtua'"; // Filter hanya orang tua

if ($selected_kelas && $selected_kelas !== 'Semua') {
    $query .= " AND siswa.kelas = '$selected_kelas'";
}

$result = mysqli_query($connection, $query);

// Ambil daftar kelas dari tabel siswa untuk dropdown
$kelas_result = mysqli_query($connection, "SELECT DISTINCT kelas FROM siswa");

?>

<div class="page-body">
    <div class="container-xl">

        <a href="<?= base_url('admin/data_orangtua/tambah.php')?>" class="btn btn-primary mb-3">
            <span class="text"><i class="fa-solid fa-circle-plus"></i> Tambah Data Orang Tua</span>
        </a>

        <!-- Filter Kelas -->
        <form action="" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="kelas" class="form-control" onchange="this.form.submit()">
                    <option value="Semua" <?= $selected_kelas == 'Semua' ? 'selected' : '' ?>>Semua Kelas</option>
                    <?php while ($kelas = mysqli_fetch_array($kelas_result)) : ?>
                        <option value="<?= htmlspecialchars($kelas['kelas']) ?>" <?= $selected_kelas == $kelas['kelas'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kelas['kelas']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </form>

        <table class="table table-bordered mt-3">
            <tr class="text-center">
                <th>No</th>
                <th>Nama Orang Tua</th>
                <th>Hubungan</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>

            <?php if(mysqli_num_rows($result) === 0) { ?>
                <tr>
                    <td colspan="9" class="text-center">Data orang tua kosong</td>
                </tr>
            <?php } else { ?>
                <?php $no = 1; ?>
                <?php while($orang_tua = mysqli_fetch_array($result)) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($orang_tua['nama_orang_tua']) ?></td>
                        <td><?= htmlspecialchars($orang_tua['hubungan']) ?></td>
                        <td><?= htmlspecialchars($orang_tua['kontak']) ?></td>
                        <td><?= htmlspecialchars($orang_tua['alamat']) ?></td>
                        <td><?= htmlspecialchars($orang_tua['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($orang_tua['nis']) ?></td>
                        <td><?= htmlspecialchars($orang_tua['kelas']) ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('admin/data_orangtua/detail.php?id='.$orang_tua['id'])?>" 
                            class="badge badge-pill bg-primary">Detail</a>
                            <a href="<?= base_url('admin/data_orangtua/edit.php?id='.$orang_tua['id'])?>" 
                            class="badge badge-pill bg-warning">Edit</a>
                            <a href="<?= base_url('admin/data_orangtua/hapus.php?id='.$orang_tua['id'])?>" 
                            class="badge badge-pill bg-danger tombol-hapus">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php } ?>
        </table>
    </div>
</div>
<?php include('../layout/footer.php')?>
