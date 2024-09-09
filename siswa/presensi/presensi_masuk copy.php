<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"
  integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw=="
  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
  crossorigin="" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
  crossorigin=""></script>

<style>
  #map {
    height: 300px;
  }
</style>

<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'siswa') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = "Presensi Masuk";
include('../layout/header.php');
include_once("../../config.php");

if (isset($_POST['tombol_masuk'])) {
  $latitude_siswa = floatval($_POST['latitude_siswa']);
  $longitude_siswa = floatval($_POST['longitude_siswa']);
  $latitude_sekolah = floatval($_POST['latitude_sekolah']);
  $longitude_sekolah = floatval($_POST['longitude_sekolah']);
  $radius = floatval($_POST['radius']);
  $zona_waktu = $_POST['zona_waktu'];
  $tanggal_masuk = $_POST['tanggal_masuk'];
  $jam_masuk = $_POST['jam_masuk'];
}

if (empty($latitude_siswa) || empty($longitude_siswa)) {
  $_SESSION['gagal'] = "Presensi gagal, Lokasi anda belum aktif";
  header('Location: ../home/home.php');
  exit();
}

if (empty($latitude_sekolah) || empty($longitude_sekolah)) {
  $_SESSION['gagal'] = "Presensi gagal, koordinat Sekolah belum di setting";
  header('Location: ../home/home.php');
  exit();
}

$perbedaan_koordinat = $longitude_siswa - $longitude_sekolah;

$jarak = sin(deg2rad($latitude_siswa)) * sin(deg2rad($latitude_sekolah)) +
  cos(deg2rad($latitude_siswa)) * cos(deg2rad($latitude_sekolah)) *
  cos(deg2rad($perbedaan_koordinat));

$jarak = acos($jarak);
$jarak = rad2deg($jarak);
$mil = $jarak * 60 * 1.1515;
$jarak_km = $mil * 1.609344;
$jarak_meter = $jarak_km * 1000;


?>

<?php if ($jarak_meter > $radius) { ?>
  <?= $_SESSION['gagal'] = "Anda berada di luar area kantor";
  header('Location: ../home/home.php');
  exit(); ?>
<?php } else { ?>
  <div class="page-body">
    <div class="container-xl">
      <div class="row">

        <div class="col-md-6">
          <div class="card">
            <div class="card body">
              <div id="map">

              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card text-center">
            <div class="card-body" style="margin: auto;">
              <input type="text" id="id" value="<?= $_SESSION['id'] ?>">
              <input type="text" id="tanggal_masuk" value="<?= $tanggal_masuk ?>">
              <input type="text" id="jam_masuk" value="<?= $jam_masuk ?>">
              <div id="my_camera" style="width:320px; height:240px;"></div>
              <div id="my_result"></div>
              <div>
                <?= date('d F Y', strtotime($tanggal_masuk)) . ' - ' . $jam_masuk ?>
              </div>
              <button class="btn btn-primary mt-2" id="ambil-foto">Masuk</button>
            </div>
          </div>
        </div>



      </div>
    </div>
  </div>

  <script language="JavaScript">
    Webcam.set({
      width: 320,
      height: 240,
      dest_width: 320,
      dest_height: 240,
      image_format: 'jpeg',
      jpeg_quality: 90,
      force_flash: false
    });
    Webcam.attach('#my_camera');

    document.getElementById('ambil-foto').addEventListener('click', function() {
      let id = document.getElementById('id').value;
      let tanggal_masuk = document.getElementById('tanggal_masuk').value;
      let jam_masuk = document.getElementById('jam_masuk').value;

      Webcam.snap(function(data_uri) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (xhttp.readyState == 4 && xhttp.status == 200) {
            Swal.fire({
              icon: 'success',
              title: 'Presensi Berhasil',
              text: 'Presensi masuk berhasil',
              confirmButtonText: 'OK'
            }).then(() => {
              window.location.href = '../home/home.php';
            });
          }
        };
        xhttp.open("POST", "presensi_masuk_aksi.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(
          'photo=' + encodeURIComponent(data_uri) +
          '&id=' + id +
          '&tanggal_masuk=' + tanggal_masuk +
          '&jam_masuk=' + jam_masuk
        );
      });
    });


    // maps leaflet js
    let latitude_ktr = <?= $latitude_sekolah ?>;
    let longitude_ktr = <?= $longitude_sekolah ?>;

    let latitude_peg = <?= $latitude_siswa ?>;
    let longitude_peg = <?= $longitude_siswa ?>;

    let map = L.map('map').setView([latitude_ktr, longitude_ktr], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker = L.marker([latitude_ktr, longitude_ktr]).addTo(map);
    var circle = L.circle([latitude_peg, longitude_peg], {
      color: 'red',
      fillColor: '#f03',
      fillOpacity: 0.5,
      radius: 500
    }).addTo(map).bindPopup("Lokasi anda saat ini").openPopup();
  </script>

<?php } ?>


<?php include('../layout/footer.php') ?>