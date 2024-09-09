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
$id_siswa = $_POST['id'];
$tanggal_masuk = $_POST['tanggal_masuk'];
$jam_masuk = $_POST['jam_masuk'];

// Konversi jam masuk menjadi format waktu untuk perbandingan
$jam_batas = strtotime('09:00:00');
$jam_presensi = strtotime($jam_masuk);

// Cek apakah presensi dilakukan lebih dari jam 9 pagi
if ($jam_presensi > $jam_batas) {
    $keterangan = 'alfa';
} else {
    $keterangan = 'hadir';
}

// Proses menyimpan foto
$foto = $file_foto;
$foto = str_replace('data:image/jpeg;base64,', '', $foto);
$foto = str_replace(' ', '+', $foto); // Perbaiki penggantian spasi dengan +
$data = base64_decode($foto);
$nama_file = 'foto/' . 'masuk' . date('Y-m-d_H-i-s') . '.png';
$file = 'masuk' . date('Y-m-d_H-i-s') . '.png';
file_put_contents($nama_file, $data);

// Insert data ke database dengan keterangan
$result = mysqli_query($connection, "INSERT INTO presensi(id_siswa, tanggal_masuk, jam_masuk, foto_masuk, keterangan) 
VALUES ('$id_siswa', '$tanggal_masuk', '$jam_masuk', '$file', '$keterangan')");

if ($result) {
    $_SESSION['berhasil'] = "Presensi masuk berhasil";
} else {
    $_SESSION['gagal'] = "Presensi masuk gagal ";
}

header('Location: ../home/home.php');
exit();
