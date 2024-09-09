<?php 
session_start();

require_once('../config.php');

if (isset($_POST["login"])) {
  $username = isset($_POST["username"]) ? $_POST["username"] : '';
  $password = isset($_POST["password"]) ? $_POST["password"] : '';

  // Query untuk mendapatkan data pengguna
  $result = mysqli_query($connection, "SELECT * FROM users WHERE username = '$username'");

  if (mysqli_num_rows($result) === 1) {
      $row = mysqli_fetch_assoc($result);

      // Verifikasi password
      if (password_verify($password, $row["password"])) {
          if ($row['status'] == 'Aktif') {
              // Set session berdasarkan role user
              $_SESSION['login'] = true;
              $_SESSION['id'] = $row['id'];
              $_SESSION['role'] = $row['role'];
              $_SESSION['username'] = $row['username'];

              // Jika role siswa, tambahkan informasi siswa
              if ($row['role'] === 'siswa') {
                  $siswa_result = mysqli_query($connection, "SELECT * FROM siswa WHERE id = {$row['id_siswa']}");
                  if (mysqli_num_rows($siswa_result) === 1) {
                      $siswa = mysqli_fetch_assoc($siswa_result);
                      $_SESSION['id_siswa'] = $siswa['id']; // ID siswa
                      $_SESSION['nama'] = $siswa['nama'];
                      $_SESSION['nis'] = $siswa['nis'];
                      $_SESSION['kelas'] = $siswa['kelas'];
                      $_SESSION['lokasi_presensi'] = $siswa['lokasi_presensi'];
                  }
              }

              if ($row['role'] === 'orangtua') {
                // Ambil informasi orang tua dari database
                $orangtua_result = mysqli_query($connection, "SELECT * FROM orang_tua WHERE id = {$row['id']}");
                if (mysqli_num_rows($orangtua_result) === 1) {
                    $orangtua = mysqli_fetch_assoc($orangtua_result);
                    $_SESSION['id'] = $orangtua['id'];
                    $_SESSION['nama_orangtua'] = $orangtua['nama_orang_tua'];
                    $_SESSION['hubungan'] = $orangtua['hubungan'];
                    $_SESSION['kontak'] = $orangtua['kontak'];
                    $_SESSION['alamat'] = $orangtua['alamat'];
                    $_SESSION['id_siswa'] = $orangtua['id_siswa']; // ID siswa dari tabel orangtua
            
                    // Ambil data anak (siswa) berdasarkan id_siswa
                    $siswa_result = mysqli_query($connection, "SELECT * FROM siswa WHERE id = {$orangtua['id_siswa']}");
                    if (mysqli_num_rows($siswa_result) === 1) {
                        $siswa = mysqli_fetch_assoc($siswa_result);
                        $_SESSION['nis_anak'] = $siswa['nis']; // NIS anak
                        $_SESSION['nama_anak'] = $siswa['nama']; // Nama anak jika diperlukan
                    }
                }
            
                header("Location: ../orangtua/home/home.php");
                exit();
            }

              // Redirect berdasarkan role
              if ($row['role'] === 'admin') {
                  header("Location: ../admin/home/home.php");
                  exit();
              } elseif ($row['role'] === 'siswa') {
                  header("Location: ../siswa/home/home.php");
                  exit();
              } elseif ($row['role'] === 'orangtua') {
                  header("Location: ../orangtua/home/home.php");
                  exit();
              }
          } else {
              $_SESSION["gagal"] = "Akun anda belum aktif";
          }
      } else {
          $_SESSION["gagal"] = "Password salah, silahkan coba lagi";
      }
  } else {
      $_SESSION["gagal"] = "Username salah, silahkan coba lagi";
  }
}
?>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Sign in with illustration - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <!-- CSS files -->
    <link href="<?= base_url('assets/css/tabler.min.css?1692870487')?>" rel="stylesheet"/>
    <link href="<?= base_url('assets/css/tabler-vendors.min.css?1692870487')?>" rel="stylesheet"/>
    <link href="<?= base_url('assets/css/demo.min.css?1692870487')?>" rel="stylesheet"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
  </head>
  <body  class=" d-flex flex-column">
    <script src="./dist/js/demo-theme.min.js?1692870487"></script>
    <div class="page page-center">
      <div class="container container-normal py-4">
        <div class="row align-items-center g-4">
          <div class="col-lg">
            <div class="container-tight">
              <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark"><img src="<?= base_url('assets/img/logobu.png')?>" height="36" alt=""></a>
              </div>

              <?php 
              
              if(isset($_GET['pesan'])){
                if($_GET['pesan'] == "belum_login"){
                  $_SESSION['gagal'] = 'Anda belum login';
                }else if($_GET['pesan'] == "tolak_akses"){
                  $_SESSION['gagal'] = 'Akses ke halaman ditolak';
                }
              }
              
              ?>

              <div class="card card-md">
                <div class="card-body">
                  <h2 class="h2 text-center mb-4">Login to your account</h2>
                  <form action="" method="POST" autocomplete="off" novalidate>
                    <div class="mb-3">
                      <label class="form-label">Username</label>
                      <input type="text" class="form-control" autofocus name="username" placeholder="Username" autocomplete="off">
                    </div>
                    <div class="mb-2">
                      <label class="form-label">
                        Password
                      </label>
                      <div class="input-group input-group-flat">
                        <input type="password" class="form-control"  name="password" placeholder="Password"  autocomplete="off">
                        <span class="input-group-text">
                          <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                          </a>
                        </span>
                      </div>
                    </div>
                    <div class="form-footer">
                      <button type="submit" name="login" class="btn btn-primary w-100">Sign in</button>
                    </div>
                  </form>
                </div>
              </div>

            </div>
          </div>
          <div class="col-lg d-none d-lg-block">
            <img src="<?= base_url('assets/img/login.svg')?>" height="300" class="d-block mx-auto" alt="">
          </div>
        </div>
      </div>
    </div>
     <!-- Libs JS -->
    <script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js?1692870487')?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487')?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world.js?1692870487')?>" defer></script>
    <script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world-merc.js?1692870487')?>" defer></script>
    <!-- Tabler Core -->
    <script src="<?= base_url('assets/js/tabler.min.js?1692870487')?>" defer></script>
    <script src="<?= base_url('assets/js/demo.min.js?1692870487')?>" defer></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if($_SESSION['gagal']) {?>
    <script>
        Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "<?= $_SESSION['gagal']?>",
        });
    </script>

    <?php unset($_SESSION['gagal']); ?>

    <?php }?>

  </body>
</html>