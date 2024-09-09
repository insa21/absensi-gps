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

$judul = "Tambah Data siswa";
include('../layout/header.php');
require_once('../../config.php');

if(isset($_POST['submit'])){
    $nis = htmlspecialchars($_POST['nis']);
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_handphone = htmlspecialchars($_POST['no_handphone']);
    $kelas = htmlspecialchars($_POST['kelas']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "siswa";
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);

    if(isset($_FILES['foto'])){
            $file = $_FILES['foto'];
            $nama_file = $file['name'];
            $file_tmp = $file['tmp_name'];
            $ukuran_file = $file['size'];
            $file_direktori = "../../assets/img/foto_siswa/" . $nama_file;
    
            // Validasi ekstensi file
            $ambil_ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
            $extensi_diizinkan = ["jpg", "png", "jpeg"];
            $max_ukuran_file = 10 * 1024 * 1024; // 10MB

            if (!in_array(strtolower($ambil_ekstensi), $extensi_diizinkan)) {
                $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Hanya file JPG, JPEG, PNG yang diperbolehkan";
            }
            if ($ukuran_file > $max_ukuran_file) {
                $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Ukuran file melebihi 10 MB";
            }

            if (empty($pesan_kesalahan)) {
                move_uploaded_file($file_tmp, $file_direktori);
            }
    } else {
        $nama_file = ''; // Jika tidak ada file yang diupload, set nama_file ke string kosong
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
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Kelas wajib diisi";
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

        if(!empty($pesan_kesalahan)){
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            // Simpan data ke database
            $siswa = mysqli_query($connection, "INSERT INTO siswa(nis, nama, jenis_kelamin, 
            alamat, no_handphone, kelas, lokasi_presensi, foto) VALUES ('$nis', '$nama', 
            '$jenis_kelamin', '$alamat', '$no_handphone', '$kelas', '$lokasi_presensi', '$nama_file')");  
    
                $id_siswa = mysqli_insert_id($connection);
                $user = mysqli_query($connection, "INSERT INTO users(id_siswa, username, password, status, role) 
                VALUES ('$id_siswa', '$username', '$password', '$status', '$role')");  
    
                $_SESSION['berhasil'] = 'Data berhasil disimpan';
                header('Location: siswa.php');
                exit;
        }
    }
}
?>

<div class="page-body">
    <div class="container-xl">
        <form action="<?= base_url('admin/data_siswa/tambah.php') ?>" method="POST" 
        enctype="multipart/form-data">
            <div class="row">
            <!-- Kolom untuk form tambah data siswa -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="">Nis</label>
                                <input type="text" class="form-control" name="nis" value="<?php if (isset($_POST['nis'])) echo $_POST['nis'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" name="nama" value="<?php if (isset($_POST['nama'])) echo $_POST['nama'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Jenis kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="">>---- Pilih jenis kelamin ----<</option>
                                    <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki-laki') echo 'selected'; ?> value="Laki-laki">Laki-Laki</option>
                                    <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?> value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="">Alamat</label>
                                <textarea class="form-control" name="alamat"><?php if (isset($_POST['alamat'])) echo $_POST['alamat']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="">No Handphone</label>
                                <input type="text" class="form-control" name="no_handphone" value="<?php if (isset($_POST['no_handphone'])) echo $_POST['no_handphone'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Kelas </label>
                                <select name="kelas" class="form-control">
                                    <option value="">>---- Pilih kelas ----<</option>
                                    <?php
                                    $ambil_kelas = mysqli_query($connection, "SELECT * FROM kelas ORDER BY kelas ASC");
                                    while ($kelas = mysqli_fetch_assoc($ambil_kelas)) {
                                        $nama_kelas = $kelas['kelas'];
                                        if (isset($_POST['kelas']) && $_POST['kelas'] == $nama_kelas) {
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
                                    <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Aktif') echo 'selected'; ?> value="Aktif">Aktif</option>
                                    <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Tidak aktif') echo 'selected'; ?> value="Tidak aktif">Tidak aktif</option>
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
                                <input type="text" class="form-control" name="username" value="<?php if (isset($_POST['username'])) echo $_POST['username'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="">Password</label>
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
                                    while ($lokasi = mysqli_fetch_assoc($ambil_lok_presensi)) {
                                        $nama_lokasi = $lokasi['nama_lokasi'];

                                        if (isset($_POST['lokasi_presensi']) && $_POST
                                        ['lokasi_presensi'] == $nama_lokasi) {
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
                                <input type="file" class="form-control" name="foto">
                            </div>
                            <a href="<?= base_url('admin/data_siswa/siswa.php')?>" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary" name="submit">Simpan</button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('../layout/footer.php')?>
