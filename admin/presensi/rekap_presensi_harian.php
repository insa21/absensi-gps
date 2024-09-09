<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = "Rekap Presensi Siswa";
include('../layout/header.php');
include_once("../../config.php");

// Ambil daftar kelas dari tabel kelas
$kelas_result = mysqli_query($connection, "SELECT * FROM kelas ORDER BY kelas ASC"); // Sesuaikan nama kolom

$tanggal_dari = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : date('Y-m-d');
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-d');
$kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';

$query = "SELECT presensi.*, siswa.nama, siswa.lokasi_presensi, siswa.kelas FROM presensi 
JOIN siswa ON presensi.id_siswa = siswa.id 
WHERE tanggal_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";

// Tambahkan filter kelas jika dipilih
if (!empty($kelas)) {
    $query .= " AND siswa.kelas = '$kelas'";
}

$query .= " ORDER BY tanggal_masuk DESC";
$result = mysqli_query($connection, $query);

?>

<div class="page-body">
    <div class="container-xl">
    
    <div class="row">
        <div class="col-md-2">
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Export Excel 
            </button>
        </div>
        <div class="col-md-10">
            <form method="GET">
                <div class="input-group">
                    <input type="date" class="form-control" name="tanggal_dari" value="<?= $tanggal_dari ?>">
                    <input type="date" class="form-control" name="tanggal_sampai" value="<?= $tanggal_sampai ?>">
                    
                    <!-- Dropdown untuk memilih kelas -->
                    <select class="form-control" name="kelas">
                        <option value="">Semua Kelas</option>
                        <?php while($row = mysqli_fetch_assoc($kelas_result)): ?>
                            <option value="<?= $row['id'] ?>" <?= ($kelas == $row['id']) ? 'selected' : '' ?>><?= $row['kelas'] ?></option> <!-- Sesuaikan nama kolom -->
                        <?php endwhile; ?>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Menampilkan Tanggal Filter -->
    <span>Rekap Presensi Tanggal: <?= date('d F Y', strtotime($tanggal_dari)) . ' sampai ' . date('d F Y', strtotime($tanggal_sampai)) ?> </span>
    <?php if (!empty($kelas)) : ?>
        <span> - Kelas: <?= $kelas ?></span>
    <?php endif; ?>
    
    <!-- Tabel Rekap Presensi -->
    <table class="table table-bordered mt-2">
        <tr class="text-center">
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Total Jam</th>
            <th>Total Terlambat</th>
        </tr>
        <?php if(mysqli_num_rows($result) === 0) { ?>
            <tr>
                <td colspan="8" class="text-center">
                    Data Rekap Presensi Masih Kosong
                </td>
            </tr>
        <?php } else { ?>
            <?php $no = 1; 
            while($rekap = mysqli_fetch_array($result)) : 
                // menghitung total jam kerja 
                $jam_tanggal_masuk = strtotime($rekap['tanggal_masuk'].' '.$rekap['jam_masuk']);
                $jam_tanggal_keluar = strtotime($rekap['tanggal_keluar'].' '.$rekap['jam_keluar']);
                
                if ($jam_tanggal_keluar && $jam_tanggal_masuk) {
                    $selisih = $jam_tanggal_keluar - $jam_tanggal_masuk;
                    $total_jam_kerja = floor($selisih / 3600); 
                    $selisih -= $total_jam_kerja * 3600; 
                    $selisih_menit_kerja = floor($selisih / 60); 
                } else {
                    $total_jam_kerja = 0;
                    $selisih_menit_kerja = 0;
                }
                
                // menghitung total jam terlambat 
                $lokasi_presensi = $rekap['lokasi_presensi'];
                $lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");
                
                while($lokasi_result = mysqli_fetch_array($lokasi)) :
                    $jam_masuk_sekolah = date('H:i:s', strtotime($lokasi_result['jam_masuk']));
                endwhile;

                $jam_masuk = date('H:i:s', strtotime($rekap['jam_masuk']));
                $timestamp_jam_masuk_real = strtotime($jam_masuk);
                $timestamp_jam_masuk_sekolah = strtotime($jam_masuk_sekolah);

                $terlambat = $timestamp_jam_masuk_real - $timestamp_jam_masuk_sekolah;
                $total_jam_terlambat = floor($terlambat / 3600);
                $terlambat -= $total_jam_terlambat * 3600;
                $selisih_menit_terlambat = floor($terlambat / 60);
            ?>
            
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $rekap['nama'] ?></td>
                <td><?= $rekap['kelas'] ?></td>
                <td><?= date('d F Y', strtotime($rekap['tanggal_masuk'])) ?></td>
                <td class="text-center"><?= $rekap['jam_masuk'] ?></td>
                <td class="text-center"><?= $rekap['jam_keluar'] ?></td>
                <td class="text-center">
                    <?php if($rekap['tanggal_keluar'] == '0000-00-00') : ?>
                        <span>0 jam 0 menit</span>
                    <?php else : ?>
                        <?= $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit' ?>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($total_jam_terlambat < 0 ) : ?>    
                        <span class="badge bg-success">On Time</span>
                    <?php else : ?>
                        <?= $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit' ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php } ?>
    </table>

    </div>
</div>

<!-- Modal Export Excel -->
<div class="modal" id="exampleModal" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export Excel Rekap Presensi Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
    <form method="POST" action="<?= base_url('admin/presensi/rekap_harian_excel.php') ?>">
      <div class="modal-body">
        <div class="md-3">
            <label for="">Tanggal Awal</label>
            <input type="date" class="form-control" name="tanggal_dari" value="<?= $tanggal_dari ?>">
        </div>

        <div class="md-3">
            <label for="">Tanggal Akhir</label>
            <input type="date" class="form-control" name="tanggal_sampai" value="<?= $tanggal_sampai ?>">
        </div>

        <!-- Dropdown untuk memilih kelas -->
        <div class="md-3">
            <label for="">Kelas</label>
            <select class="form-control" name="kelas">
                <option value="">Semua Kelas</option>
                <?php 
                mysqli_data_seek($kelas_result, 0); // Reset pointer
                while($row = mysqli_fetch_assoc($kelas_result)): ?>
                    <option value="<?= $row['id'] ?>" <?= ($kelas == $row['id']) ? 'selected' : '' ?>><?= $row['kelas'] ?></option> <!-- Sesuaikan nama kolom -->
                <?php endwhile; ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Export</button>
      </div>
    </form>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>
