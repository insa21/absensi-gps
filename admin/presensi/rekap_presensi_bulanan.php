<?php 
session_start();
ob_start();
if(!isset($_SESSION["login"])){
  header("Location: ../../auth/login.php?pesan=belum_login");
}else if($_SESSION["role"] != 'admin'){
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = "Rekap Presensi";
include('../layout/header.php');
include_once("../../config.php");

// Ambil daftar kelas dari tabel kelas
$kelas_result = mysqli_query($connection, "SELECT * FROM kelas ORDER BY kelas ASC");

// Ambil filter dari GET request
$filter_bulan = isset($_GET['filter_bulan']) ? $_GET['filter_bulan'] : '';
$filter_tahun = isset($_GET['filter_tahun']) ? $_GET['filter_tahun'] : '';
$filter_kelas = isset($_GET['filter_kelas']) ? $_GET['filter_kelas'] : '';

// Query untuk rekap presensi
$bulan_sekarang = empty($filter_bulan) ? date('Y-m') : $filter_tahun . '-' . $filter_bulan;
$query = "SELECT presensi.*, siswa.nama, siswa.lokasi_presensi, siswa.kelas FROM presensi 
          JOIN siswa ON presensi.id_siswa = siswa.id 
          WHERE DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$bulan_sekarang'";

if (!empty($filter_kelas)) {
    $query .= " AND siswa.kelas = '$filter_kelas'";
}

$query .= " ORDER BY tanggal_masuk DESC";
$result = mysqli_query($connection, $query);
?>

<div class="page-body">
    <div class="container-xl">

    <div class="row mb-3">
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Export Excel
            </button>
        </div>
        <div class="col-md-10">
            <form method="GET">
                <div class="input-group">
                    <select name="filter_bulan" class="form-control">
                        <option value="">>-----Pilih Bulan-----<</option>
                        <?php
                            $bulan_array = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                            foreach ($bulan_array as $key => $value) {
                                $selected = ($filter_bulan == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$value</option>";
                            }
                        ?>
                    </select>
                    <select name="filter_tahun" class="form-control">
                        <option value="">>-----Pilih Tahun-----<</option>
                        <?php
                            $tahun_sekarang = date('Y');
                            $tahun_max = $tahun_sekarang + 5; // Mengatur batas tahun hingga 5 tahun ke depan
                            
                            for($tahun = $tahun_sekarang; $tahun <= $tahun_max; $tahun++) {
                                $selected = ($filter_tahun == $tahun) ? 'selected' : '';
                                echo "<option value='$tahun' $selected>$tahun</option>";
                            }
                        ?>
                    </select>
                    <select name="filter_kelas" class="form-control">
                        <option value="">Semua Kelas</option>
                        <?php while($row = mysqli_fetch_assoc($kelas_result)): ?>
                            <option value="<?= $row['kelas'] ?>" <?= ($filter_kelas == $row['kelas']) ? 'selected' : '' ?>><?= $row['kelas'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <span>Rekap Presensi Bulan: <?= date('F', strtotime($bulan_sekarang)) ?> 
    Tahun: <?= date('Y', strtotime($bulan_sekarang)) ?> <?= !empty($filter_kelas) ? 'Kelas: ' . $filter_kelas : '' ?> </span>
    <table class="table table-bordered mt-2">
        <tr class="text-center">
            <th>No</th>
            <th>Nama</th>
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
            <?php 
            $no = 1;
            while($rekap = mysqli_fetch_array($result)) :
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

                // Menghitung total jam terlambat
                $lokasi_presensi = $rekap['lokasi_presensi'];
                $lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

                while($lokasi_result = mysqli_fetch_array($lokasi)) :
                    $jam_masuk_sekolah = strtotime($lokasi_result['jam_masuk']);
                endwhile;

                $jam_masuk = strtotime($rekap['jam_masuk']);
                $terlambat = $jam_masuk - $jam_masuk_sekolah;
                
                if ($terlambat > 0) {
                    $total_jam_terlambat = floor($terlambat / 3600);
                    $selisih_menit_terlambat = floor(($terlambat % 3600) / 60);
                } else {
                    $total_jam_terlambat = 0;
                    $selisih_menit_terlambat = 0;
                }
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
                        <span>0 Jam 0 Menit</span>
                    <?php else : ?>
                        <?= $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit' ?>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($total_jam_terlambat == 0 && $selisih_menit_terlambat == 0) : ?>    
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
        <h5 class="modal-title">Export Excel Rekap Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
    <form method="POST" action="<?= base_url('admin/presensi/rekap_bulanan_excel.php') ?>">
      <div class="modal-body">
        <div class="mb-3">
            <label for="">Tanggal Awal</label>
            <input type="date" class="form-control" name="tanggal_dari">
        </div>

        <div class="mb-3">
            <label for="">Tanggal Akhir</label>
            <input type="date" class="form-control" name="tanggal_sampai">
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Export</button>
      </div>
    </form>
    </div>
  </div>
</div>
<?php include('../layout/footer.php')?>
