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

$judul = "Edit Data Siswa";
include('../layout/header.php');
require_once('../../config.php');

if(isset($_POST['edit'])){

    $id = $_POST['id'];
    $nis = htmlspecialchars($_POST['nis']);
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_handphone = htmlspecialchars($_POST['no_handphone']);
    $kelas = htmlspecialchars($_POST['kelas']);
    $username = htmlspecialchars($_POST['username']);
    $role = "siswa";
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);

    if(empty($_POST['password'])){
        $password = $_POST['password_lama'];
    }else{
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if ($_FILES['foto_baru']['error'] === 4) {
        // Jika tidak ada file yang diupload, gunakan foto lama
        $nama_file = $_POST['foto_lama'];
    } else {
        // Jika ada file yang diupload
        if (isset($_FILES['foto_baru'])) {
            $file = $_FILES['foto_baru'];
            $nama_file = $file['name'];
            $file_tmp = $file['tmp_name'];
            $ukuran_file = $file['size'];
            $file_direktori = "../../assets/img/foto_siswa/" . $nama_file;
            
            // Validasi ekstensi file
            $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
            $extensi_diizinkan = ["jpg", "png", "jpeg"];
            $max_ukuran_file = 10 * 1024 * 1024; // 10MB
            move_uploaded_file($file_tmp, $file_direktori);
         }

    } 

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($nis)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> NIS wajib diisi";
        }
        if(empty($nama)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Nama wajib diisi";
        }
        if(empty($jenis_kelamin)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Jenis kelamin wajib diisi";
        }
        if(empty($alamat)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Alamat wajib diisi";
        }
        if(empty($no_handphone)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> No. Handphone wajib diisi";
        }
        if(empty($kelas)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> kelas wajib diisi";
        }
        if(empty($username)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Username wajib diisi";
        }
        if(empty($password)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Password wajib diisi";
        }
        if($_POST['password'] !== $_POST['ulangi_password']){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Password tidak cocok";
        }
        if(empty($status)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Status wajib diisi";
        }
        if(empty($lokasi_presensi)){
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Lokasi presensi wajib diisi";
        }
        if($_FILES['foto_baru']['error'] != 4 ){

            if(!in_array(strtolower($ambil_ekstensi), $extensi_diizinkan)){
                $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Hanya file JPG , JPEG , PNG yang diperbolehkan";
            }
            if($ukuran_file > $max_ukuran_file){
                $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Ukuran file melebihi 10 mb";
            }

        }


        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // Simpan data ke database
            $siswa = mysqli_query($connection, "UPDATE siswa SET
                nis='$nis',
                nama='$nama',
                jenis_kelamin='$jenis_kelamin',
                alamat='$alamat',
                no_handphone='$no_handphone',
                kelas='$kelas',
                lokasi_presensi='$lokasi_presensi',
                foto='$nama_file'

            WHERE id='$id'");  
    
                $user = mysqli_query($connection, "UPDATE users SET 
                    username = '$username',
                    password = '$password',
                    status = '$status',
                    role = '$role'

                    WHERE id='$id'");  
    
                $_SESSION['berhasil'] = 'Data berhasil update';
                header('Location: siswa.php');
                exit;
        }
    }

}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($connection, "SELECT users.id_siswa, users.username, users.password, users.status, users.role, 
siswa.* FROM users JOIN siswa ON users.id_siswa = siswa.id WHERE siswa.id = $id");

while($siswa = mysqli_fetch_array($result)){
    $nis = $siswa['nis'];
    $nama = $siswa['nama'];
    $jenis_kelamin = $siswa['jenis_kelamin'];
    $alamat = $siswa['alamat'];
    $no_handphone = $siswa['no_handphone'];
    $kelas = $siswa ['kelas'];
    $username = $siswa['username'];
    $password = $siswa['password'];
    $status = $siswa['status'];
    $lokasi_presensi = $siswa['lokasi_presensi'];
    $role = $siswa['role'];
    $foto = $siswa['foto'];
}
?>


<div class="page-body">
    <div class="container-xl">
        <form action="<?= base_url('admin/data_siswa/edit.php') ?>" method="POST" 
        enctype = "multipart/form-data">
            <div class="row">
            <!-- Kolom untuk form tambah data siswa -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="">Nis</label>
                                <input type="text" class="form-control" name="nis" value="<?= $nis ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" name="nama" value="<?= $nama ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Jenis kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="">>---- Pilih jenis kelamin ----<</option>
                                    <option <?php if ($jenis_kelamin == 'Laki-laki') echo 'selected'; ?> value="Laki-laki">Laki-Laki</option>
                                    <option <?php if ($jenis_kelamin == 'Perempuan') echo 'selected'; ?> value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="">Alamat</label>
                                <textarea class="form-control" name="alamat"><?= $alamat ?></textarea>
                            </div>


                            <div class="mb-3">
                                <label for="">No Handphone</label>
                                <input type="text" class="form-control" name="no_handphone" value="<?= $no_handphone ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Kelas </label>
                                <select name="kelas" class="form-control">
                                    <option value="">>---- Pilih kelas ----<</option>
                                    <?php
                                    $ambil_kelas = mysqli_query($connection, "SELECT * FROM kelas ORDER BY kelas ASC");
                                    while ($row = mysqli_fetch_assoc($ambil_kelas)) {
                                        $nama_kelas = $row['kelas'];
                                        if ($kelas == $nama_kelas) {
                                            echo '<option value="' . $nama_kelas . '" selected="selected">' . $nama_kelas . '</option>';
                                        } else {
                                            echo '<option value="' . $nama_kelas . '">' . $nama_kelas . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">>---- Pilih status ----<</option>
                                    <option <?php if ($status == 'Aktif') echo 'selected'; ?> value="Aktif">Aktif</option>
                                    <option <?php if ($status == 'Tidak aktif') echo 'selected'; ?> value="Tidak aktif">Tidak aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Kolom kosong atau konten lainnya -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="">Username</label>
                                <input type="text" class="form-control" name="username" value="<?= $username ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">password</label>
                                <input type="hidden" class="form-control" value="<?= $password; ?>" name="password_lama">
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="mb-3">
                                <label for="">Ulangi Password</label>
                                <input type="password" class="form-control" name="ulangi_password">
                            </div>
                            <div class="mb-3">
                                <label for="">Lokasi Presensi</label>
                                <select name="lokasi_presensi" class="form-control" readonly>
                                    <?php
                                    $ambil_lok_presensi = mysqli_query($connection, "SELECT * FROM 
                                    lokasi_presensi ORDER BY nama_lokasi ASC");
                                    while ($row = mysqli_fetch_assoc($ambil_lok_presensi)) {
                                        $nama_lokasi = $row['nama_lokasi'];

                                        if ($lokasi_presensi == $nama_lokasi) {
                                            echo '<option value="' . $nama_lokasi . '" 
                                            selected="selected">' . $nama_lokasi . '</option>';
                                        } else {
                                            echo '<option value="' . $nama_lokasi . '">' . 
                                            $nama_lokasi . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Foto</label>
                                <input type="hidden" value="<?= $foto?>" name="foto_lama">
                                <input type="file" class="form-control" name="foto_baru">
                            </div>

                            <input type="hidden" value="<?= $id?>" name="id">
                            <a href="<?= base_url('admin/data_siswa/siswa.php')?>" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary" name="edit">Update</button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('../layout/footer.php')?>