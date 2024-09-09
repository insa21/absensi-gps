<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require '../../vendor/autoload.php'; // Pastikan path ini sesuai dengan lokasi autoload.php

session_start();
ob_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
} else if ($_SESSION["role"] != 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

include_once("../../config.php");

// Ambil parameter dari form
$tanggal_dari = isset($_POST['tanggal_dari']) ? $_POST['tanggal_dari'] : '';
$tanggal_sampai = isset($_POST['tanggal_sampai']) ? $_POST['tanggal_sampai'] : '';

if (empty($tanggal_dari) || empty($tanggal_sampai)) {
    die('Tanggal awal dan akhir harus diisi.');
}

// Query untuk data presensi
$query = "SELECT presensi.*, siswa.nama, siswa.lokasi_presensi, siswa.kelas FROM presensi
          JOIN siswa ON presensi.id_siswa = siswa.id
          WHERE tanggal_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai'
          ORDER BY tanggal_masuk DESC";

$result = mysqli_query($connection, $query);

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Rekap Presensi');

// Set header
$sheet->setCellValue('A1', 'No')
      ->setCellValue('B1', 'Nama')
      ->setCellValue('C1', 'Tanggal')
      ->setCellValue('D1', 'Jam Masuk')
      ->setCellValue('E1', 'Jam Pulang')
      ->setCellValue('F1', 'Total Jam')
      ->setCellValue('G1', 'Total Terlambat');

// Isi data
$row = 2;
$no = 1;
while ($rekap = mysqli_fetch_assoc($result)) {
    $jam_tanggal_masuk = strtotime($rekap['tanggal_masuk'] . ' ' . $rekap['jam_masuk']);
    $jam_tanggal_keluar = strtotime($rekap['tanggal_keluar'] . ' ' . $rekap['jam_keluar']);

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
    $jam_masuk_sekolah = 0;

    while ($lokasi_result = mysqli_fetch_array($lokasi)) {
        $jam_masuk_sekolah = strtotime($lokasi_result['jam_masuk']);
    }

    $jam_masuk = strtotime($rekap['jam_masuk']);
    $terlambat = $jam_masuk - $jam_masuk_sekolah;

    if ($terlambat > 0) {
        $total_jam_terlambat = floor($terlambat / 3600);
        $selisih_menit_terlambat = floor(($terlambat % 3600) / 60);
    } else {
        $total_jam_terlambat = 0;
        $selisih_menit_terlambat = 0;
    }

    $sheet->setCellValue('A' . $row, $no++)
          ->setCellValue('B' . $row, $rekap['nama'])
          ->setCellValue('C' . $row, date('d F Y', strtotime($rekap['tanggal_masuk'])))
          ->setCellValue('D' . $row, $rekap['jam_masuk'])
          ->setCellValue('E' . $row, $rekap['jam_keluar'])
          ->setCellValue('F' . $row, ($rekap['tanggal_keluar'] == '0000-00-00' ? '0 Jam 0 Menit' : $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit'))
          ->setCellValue('G' . $row, ($total_jam_terlambat == 0 && $selisih_menit_terlambat == 0 ? 'On Time' : $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit'));

    $row++;
}

// Simpan ke file Excel
$writer = new Xlsx($spreadsheet);
$filename = 'rekap_presensi_' . date('YmdHis') . '.xlsx';
$filepath = '../../exports/' . $filename;
$writer->save($filepath);

// Redirect atau download file
header("Location: ../../exports/$filename");
exit();
