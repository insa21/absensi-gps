<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
  exit();
} elseif ($_SESSION["role"] != 'siswa') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
  exit();
}

$judul = "Home";
include('../layout/header.php');
include_once("../../config.php");

$lokasi_presensi = $_SESSION['lokasi_presensi'];
$lokasi_presensi = mysqli_real_escape_string($connection, $lokasi_presensi);
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

if ($lokasi = mysqli_fetch_assoc($result)) {
  $latitude_sekolah = $lokasi['latitude'];
  $longitude_sekolah = $lokasi['longitude'];
  $radius = $lokasi['radius'];
  $jam_pulang = $lokasi['jam_pulang'];
} else {
  // Handle the case where location is not found
  die("Lokasi presensi tidak ditemukan.");
}

date_default_timezone_set('Asia/Jakarta');

?>

<style>
  .parent_date {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    font-size: 18px;
    text-align: center;
    justify-content: center;
  }

  .parent_clock {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    font-size: 28px;
    text-align: center;
    font-weight: bold;
    justify-content: center;
  }
</style>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-4">
        <div class="card text-center h-100">
          <div class="card-header">Presensi Masuk</div>
          <div class="card-body">
            <?php
            $id_siswa = $_SESSION['id'];
            $tanggal_hari_ini = date('Y-m-d');

            $cek_presensi_masuk = mysqli_query($connection, "SELECT * FROM presensi WHERE id_siswa = '$id_siswa' AND tanggal_masuk = '$tanggal_hari_ini'");
            ?>

            <?php if (mysqli_num_rows($cek_presensi_masuk) === 0) { ?>

              <div class="parent_date">
                <div id="tanggal_masuk"></div>
                <div class="ms-2"></div>
                <div id="bulan_masuk"></div>
                <div class="ms-2"></div>
                <div id="tahun_masuk"></div>
                <div class="ms-2"></div>
              </div>
              <div class="parent_clock">
                <div id="jam_masuk"></div>
                <div>:</div>
                <div id="menit_masuk"></div>
                <div>:</div>
                <div id="detik_masuk"></div>
              </div>
              <form method="POST" action="<?= base_url('siswa/presensi/presensi_masuk.php') ?>">
                <input type="hidden" name="latitude_siswa" id="latitude_siswa">
                <input type="hidden" name="longitude_siswa" id="longitude_siswa">
                <input type="hidden" value="<?= $latitude_sekolah ?>" name="latitude_sekolah">
                <input type="hidden" value="<?= $longitude_sekolah ?>" name="longitude_sekolah">
                <input type="hidden" value="<?= $radius ?>" name="radius">
                <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_masuk">
                <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_masuk">
                <button type="submit" class="btn btn-primary mt-3" name="tombol_masuk">Masuk</button>
              </form>

            <?php } else { ?>
              <i class="fa-solid fa-circle-check fa-4x text-success"></i>
              <h4 class="my-3">Anda telah melakukan <br> presensi masuk</h4>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card text-center h-100">
          <div class="card-header">Presensi Keluar</div>
          <div class="card-body">
            <?php
            $ambil_data_presensi = mysqli_query($connection, "SELECT * FROM presensi WHERE id_siswa='$id_siswa' AND tanggal_masuk = '$tanggal_hari_ini'");
            ?>
            <?php $waktu_sekarang = date('H:i:s');

            if (strtotime($waktu_sekarang) < strtotime($jam_pulang)) { ?>
              <i class="fa-solid fa-circle-xmark fa-4x text-danger"></i>
              <h4 class="my-3">Belum waktunya pulang</h4>

            <?php } elseif (
              strtotime($waktu_sekarang) > strtotime($jam_pulang) &&
              mysqli_num_rows($ambil_data_presensi) == 0
            ) { ?>
              <i class="fa-solid fa-circle-xmark fa-4x text-danger"></i>
              <h4 class="my-3">Silahkan lakukan absensi <br> masuk terlebih dahulu</h4>
            <?php } else { ?>

              <?php while ($cek_presensi_keluar = mysqli_fetch_array($ambil_data_presensi)) { ?>

                <?php if ($cek_presensi_keluar['tanggal_keluar'] == '0000-00-00') { ?>

                  <div class="parent_date">
                    <div id="tanggal_keluar"></div>
                    <div class="ms-2"></div>
                    <div id="bulan_keluar"></div>
                    <div class="ms-2"></div>
                    <div id="tahun_keluar"></div>
                    <div class="ms-2"></div>
                  </div>
                  <div class="parent_clock">
                    <div id="jam_keluar"></div>
                    <div>:</div>
                    <div id="menit_keluar"></div>
                    <div>:</div>
                    <div id="detik_keluar"></div>
                  </div>
                  <form method="POST" action="<?= base_url('siswa/presensi/presensi_keluar.php') ?>">
                    <input type="hidden" name="id" value="<?= $cek_presensi_keluar['id'] ?>">
                    <input type="hidden" name="latitude_siswa" id="latitude_siswa">
                    <input type="hidden" name="longitude_siswa" id="longitude_siswa">
                    <input type="hidden" value="<?= $latitude_sekolah ?>" name="latitude_sekolah">
                    <input type="hidden" value="<?= $longitude_sekolah ?>" name="longitude_sekolah">
                    <input type="hidden" value="<?= $radius ?>" name="radius">
                    <input type="hidden" value="<?= date('Y-m-d') ?>" name="tanggal_keluar">
                    <input type="hidden" value="<?= date('H:i:s') ?>" name="jam_keluar">
                    <button type="submit" name="tombol_keluar" class="btn btn-danger mt-3">Keluar</button>
                  </form>

                <?php } else { ?>
                  <i class="fa-solid fa-circle-check fa-4x text-success"></i>
                  <h4 class="my-3">Anda telah melakukan <br> presensi keluar</h4>
                <?php } ?>

              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="col-md-2"></div>
    </div>
  </div>
</div>

<script>
  window.onload = function() {
    getLocation();
    setInterval(waktuMasuk, 1000);
    setInterval(waktuKeluar, 1000);
  };

  const namaBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

  function waktuMasuk() {
    const waktu = new Date();
    document.getElementById("tanggal_masuk").innerHTML = waktu.getDate();
    document.getElementById("bulan_masuk").innerHTML = namaBulan[waktu.getMonth()];
    document.getElementById("tahun_masuk").innerHTML = waktu.getFullYear();
    document.getElementById("jam_masuk").innerHTML = String(waktu.getHours()).padStart(2, '0');
    document.getElementById("menit_masuk").innerHTML = String(waktu.getMinutes()).padStart(2, '0');
    document.getElementById("detik_masuk").innerHTML = String(waktu.getSeconds()).padStart(2, '0');
  }

  function waktuKeluar() {
    const waktu = new Date();
    document.getElementById("tanggal_keluar").innerHTML = waktu.getDate();
    document.getElementById("bulan_keluar").innerHTML = namaBulan[waktu.getMonth()];
    document.getElementById("tahun_keluar").innerHTML = waktu.getFullYear();
    document.getElementById("jam_keluar").innerHTML = String(waktu.getHours()).padStart(2, '0');
    document.getElementById("menit_keluar").innerHTML = String(waktu.getMinutes()).padStart(2, '0');
    document.getElementById("detik_keluar").innerHTML = String(waktu.getSeconds()).padStart(2, '0');
  }

  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    }
  }

  function showPosition(position) {
    document.getElementById('latitude_siswa').value = position.coords.latitude;
    document.getElementById('longitude_siswa').value = position.coords.longitude;
  }
</script>

<?php include('../layout/footer.php'); ?>