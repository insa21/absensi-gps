<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
  exit();
} else if ($_SESSION["role"] != 'siswa') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
  exit();
}

include_once("../../config.php");

$file_foto = $_POST['photo'];
$id_presensi = $_POST['id'];
$tanggal_keluar = $_POST['tanggal_keluar'];
$jam_keluar = $_POST['jam_keluar'];

// Konversi jam keluar menjadi format waktu untuk perbandingan
$jam_batas_keluar = strtotime('14:00:00');
$jam_presensi_keluar = strtotime($jam_keluar);

// Cek apakah siswa keluar sebelum jam 2 siang
if ($jam_presensi_keluar < $jam_batas_keluar) {
  $status_keterangan = 'bolos';
} else {
  $status_keterangan = 'hadir';
}

// Ambil data keterangan yang sudah ada sebelumnya
$query = mysqli_query($connection, "SELECT keterangan FROM presensi WHERE id=$id_presensi");
$row = mysqli_fetch_assoc($query);
$keterangan_sebelumnya = $row['keterangan'];

// Jika sudah ada keterangan sebelumnya, tambahkan strip sebelum keterangan baru
if (!empty($keterangan_sebelumnya)) {
  $keterangan = $keterangan_sebelumnya . ' - ' . $status_keterangan;
} else {
  $keterangan = $status_keterangan;
}

// Proses menyimpan foto keluar
$foto = $file_foto;
$foto = str_replace('data:image/jpeg;base64,', '', $foto);
$foto = str_replace(' ', '+', $foto); // Perbaiki penggantian spasi dengan +
$data = base64_decode($foto);
$nama_file = 'foto/' . 'keluar' . date('Y-m-d_H-i-s') . '.png';
$file = 'keluar' . date('Y-m-d_H-i-s') . '.png';
file_put_contents($nama_file, $data);

// Update data ke database dengan keterangan yang telah dimodifikasi
$result = mysqli_query($connection, "UPDATE presensi SET tanggal_keluar='$tanggal_keluar', 
jam_keluar='$jam_keluar', foto_keluar='$file', keterangan='$keterangan' WHERE id=$id_presensi");

if ($result) {
  $_SESSION['berhasil'] = "Presensi keluar berhasil";
} else {
  $_SESSION['gagal'] = "Presensi keluar gagal";
}

header('Location: ../home/home.php');
exit();
