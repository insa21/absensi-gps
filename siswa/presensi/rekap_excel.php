
<?php
session_start();
ob_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'siswa') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = "Rekap Presensi";
include_once("../../config.php");

require('../../assets/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id = $_SESSION['id_siswa'];
$tanggal_dari = $_POST['tanggal_dari'];
$tanggal_sampai = $_POST['tanggal_sampai'];
$result = mysqli_query($connection, "SELECT * FROM presensi WHERE id_siswa = '$id' AND tanggal_masuk BETWEEN '$tanggal_dari' 
    AND '$tanggal_sampai' ORDER BY tanggal_masuk DESC");

$lokasi_presensi = isset($_SESSION['lokasi_presensi']) ? $_SESSION['lokasi_presensi'] : '';

if (!empty($lokasi_presensi)) {
    $lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");
    if ($lokasi && mysqli_num_rows($lokasi) > 0) {
        $lokasi_result = mysqli_fetch_array($lokasi);
        $jam_masuk_kantor = strtotime($lokasi_result['jam_masuk']);
    } else {
        $jam_masuk_kantor = 0; // Set default value if no record found
    }
} else {
    $jam_masuk_kantor = 0; // Set default value if 'lokasi_presensi' is not set
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'REKAP PRESENSI');
$sheet->setCellValue('A2', 'Tanggal Awal');
$sheet->setCellValue('A3', 'Tanggal Akhir');
$sheet->setCellValue('C2', $tanggal_dari);
$sheet->setCellValue('C3', $tanggal_sampai);
$sheet->setCellValue('A5', 'NO');
$sheet->setCellValue('B5', 'TANGGAL MASUK');
$sheet->setCellValue('C5', 'JAM MASUK');
$sheet->setCellValue('D5', 'TANGGAL KELUAR');
$sheet->setCellValue('E5', 'JAM KELUAR');
$sheet->setCellValue('G5', 'TOTAL JAM TERLAMBAT');

$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:B2');
$sheet->mergeCells('A3:B3');

$no = 1;
$row = 6;

while ($data = mysqli_fetch_array($result)) {

    // Menghitung total jam terlambat 
    $timestamp_jam_masuk_real = strtotime($data['jam_masuk']);
    $terlambat = $timestamp_jam_masuk_real - $jam_masuk_kantor;

    if ($terlambat > 0) {
        $total_jam_terlambat = floor($terlambat / 3600);
        $selisih_menit_terlambat = floor(($terlambat % 3600) / 60);
    } else {
        $total_jam_terlambat = 0;
        $selisih_menit_terlambat = 0;
    }

    $sheet->setCellValue('A' . $row, $no);
    $sheet->setCellValue('B' . $row, $data['tanggal_masuk']);
    $sheet->setCellValue('C' . $row, $data['jam_masuk']);
    $sheet->setCellValue('D' . $row, $data['tanggal_keluar']);
    $sheet->setCellValue('E' . $row, $data['jam_keluar']);
    $sheet->setCellValue('G' . $row, $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit ');

    $no++;
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan presensi.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

?>


