<?php
session_start();
ob_start();
if(!isset($_SESSION["login"])){
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
} else if($_SESSION["role"] != 'admin'){
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

include_once("../../config.php");
require_once '../../assets/vendor/autoload.php'; // Pastikan PHPExcel terinstal

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$tanggal_dari = $_POST['tanggal_dari'];
$tanggal_sampai = $_POST['tanggal_sampai'];
$kelas = $_POST['kelas'];

$query = "SELECT presensi.*, siswa.nama, siswa.lokasi_presensi, siswa.kelas FROM presensi 
JOIN siswa ON presensi.id_siswa = siswa.id 
WHERE tanggal_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";

// Tambahkan filter kelas jika dipilih
if (!empty($kelas)) {
    $query .= " AND siswa.kelas = '$kelas'";
}

$query .= " ORDER BY tanggal_masuk DESC";
$result = mysqli_query($connection, $query);

// Membuat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Rekap Presensi Siswa');

// Menambahkan header kolom
$sheet->setCellValue('A1', 'No')
      ->setCellValue('B1', 'Nama Siswa')
      ->setCellValue('C1', 'Kelas')
      ->setCellValue('D1', 'Tanggal')
      ->setCellValue('E1', 'Jam Masuk')
      ->setCellValue('F1', 'Jam Pulang')
      ->setCellValue('G1', 'Total Jam')
      ->setCellValue('H1', 'Total Terlambat');

// Mengisi data
$rowNumber = 2;
$no = 1;
while($rekap = mysqli_fetch_array($result)) {
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

    $sheet->setCellValue('A' . $rowNumber, $no++)
          ->setCellValue('B' . $rowNumber, $rekap['nama'])
          ->setCellValue('C' . $rowNumber, $rekap['kelas'])
          ->setCellValue('D' . $rowNumber, date('d F Y', strtotime($rekap['tanggal_masuk'])))
          ->setCellValue('E' . $rowNumber, $rekap['jam_masuk'])
          ->setCellValue('F' . $rowNumber, $rekap['jam_keluar'])
          ->setCellValue('G' . $rowNumber, $rekap['tanggal_keluar'] == '0000-00-00' ? '0 jam 0 menit' : $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit')
          ->setCellValue('H' . $rowNumber, $total_jam_terlambat < 0 ? 'On Time' : $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit');

    $rowNumber++;
}

// Menyimpan file Excel
$writer = new Xlsx($spreadsheet);
$file_name = 'Rekap_Presensi_harian_' . date('Ymd') . '.xlsx';
$writer->save($file_name);

// Mengunduh file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Cache-Control: max-age=0');
readfile($file_name);
unlink($file_name); // Hapus file setelah diunduh

exit();
?>
