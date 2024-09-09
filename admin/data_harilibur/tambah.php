<?php 
session_start();
ob_start();

if(!isset($_SESSION['login'])) {
    header("Location: ../../auth/login.php?pesan=belum_login");
    exit();
}

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php?pesan=tolak_akses");
    exit();
}

$judul = "Tambah Hari Libur";
include('../layout/header.php');
require_once('../../config.php');

if(isset($_POST['submit'])){
    $tanggal_mulai = htmlspecialchars($_POST['tanggal_mulai']);
    $tanggal_akhir = htmlspecialchars($_POST['tanggal_akhir']);
    $nama_libur = htmlspecialchars($_POST['nama_libur']);
    $jenis_libur = htmlspecialchars($_POST['jenis_libur']);
    $semester = htmlspecialchars($_POST['semester']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($tanggal_mulai)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Tanggal mulai wajib diisi";
        }
        if(empty($tanggal_akhir)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Tanggal akhir wajib diisi";
        }
        if(empty($nama_libur)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Nama libur wajib diisi";
        }
        if(empty($jenis_libur)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Jenis libur wajib diisi";
        }
        if(empty($semester)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Semester wajib diisi";
        }

        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = implode("<br>",$pesan_kesalahan);
        }else{
            $result = mysqli_query($connection, "INSERT INTO hari_libur(tanggal_mulai, tanggal_akhir, 
            nama_libur, jenis_libur, semester, deskripsi) 
            VALUES ('$tanggal_mulai','$tanggal_akhir','$nama_libur','$jenis_libur','$semester','$deskripsi')");
        
            $_SESSION['berhasil'] = 'Data berhasil disimpan';
            header('Location: harilibur.php');
            exit;
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?= base_url('admin/data_harilibur/tambah.php') ?>" method="POST">
                    <div class="mb-3">
                        <label for="">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?php 
                        if(isset($_POST['tanggal_mulai'])) echo $_POST['tanggal_mulai']?>">
                    </div>
                    <div class="mb-3">
                        <label for="">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_akhir" value="<?php 
                        if(isset($_POST['tanggal_akhir'])) echo $_POST['tanggal_akhir']?>">
                    </div>
                    <div class="mb-3">
                        <label for="">Nama Libur</label>
                        <input type="text" class="form-control" name="nama_libur" value="<?php 
                        if(isset($_POST['nama_libur'])) echo $_POST['nama_libur']?>">
                    </div>
                    <div class="mb-3">
                        <label for="">Jenis Libur</label>
                        <select name="jenis_libur" class="form-control">
                            <option value="">>---- Pilih jenis libur ----<</option>
                            <option <?php if(isset($_POST['jenis_libur']) && $_POST['jenis_libur'] == 'Libur Nasional'){
                                echo 'selected';
                            }?> value="Libur Nasional">Libur Nasional</option>
                            <option <?php if(isset($_POST['jenis_libur']) && $_POST['jenis_libur'] == 'Libur Akademik'){
                                echo 'selected';
                            }?> value="Libur Akademik">Libur Akademik</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Semester</label>
                        <select name="semester" class="form-control">
                            <option value="">>---- Pilih semester ----<</option>
                            <option <?php if(isset($_POST['semester']) && $_POST['semester'] == 'Ganjil'){
                                echo 'selected';
                            }?> value="Ganjil">Ganjil</option>
                            <option <?php if(isset($_POST['semester']) && $_POST['semester'] == 'Genap'){
                                echo 'selected';
                            }?> value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi"><?php 
                        if(isset($_POST['deskripsi'])) echo $_POST['deskripsi']?></textarea>
                    </div>
                    <a href="harilibur.php" class="btn btn-secondary tombol-kembali">Kembali</a>
                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php')?>
