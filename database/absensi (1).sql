-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Sep 2024 pada 15.00
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `hari_libur`
--

CREATE TABLE `hari_libur` (
  `id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `nama_libur` varchar(255) NOT NULL,
  `jenis_libur` varchar(100) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hari_libur`
--

INSERT INTO `hari_libur` (`id`, `tanggal_mulai`, `tanggal_akhir`, `nama_libur`, `jenis_libur`, `semester`, `deskripsi`) VALUES
(3, '2024-09-05', '2024-09-06', 'Rapat', 'Libur Akademik', 'Genap', 'Libur Dua hari rapat guru'),
(4, '2024-09-05', '2024-09-05', 'Rapat', 'Libur Akademik', 'Genap', 'Hari Libur Guru Rapat'),
(5, '2024-09-27', '2024-09-30', 'Rapat', 'Libur Akademik', 'Ganjil', 'LIBUR');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `kelas` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `kelas`) VALUES
(11, '7 A'),
(12, '7 B'),
(13, '8 A'),
(14, '8 B'),
(16, '9 A'),
(17, '9 B');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ketidakhadiran`
--

CREATE TABLE `ketidakhadiran` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `keterangan` varchar(225) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` varchar(225) NOT NULL,
  `file` varchar(225) NOT NULL,
  `status_pengajuan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lokasi_presensi`
--

CREATE TABLE `lokasi_presensi` (
  `id` int(11) NOT NULL,
  `nama_lokasi` varchar(225) NOT NULL,
  `alamat_lokasi` varchar(225) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `radius` int(11) NOT NULL,
  `zona_waktu` varchar(4) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lokasi_presensi`
--

INSERT INTO `lokasi_presensi` (`id`, `nama_lokasi`, `alamat_lokasi`, `latitude`, `longitude`, `radius`, `zona_waktu`, `jam_masuk`, `jam_pulang`) VALUES
(13, 'SMP BUSTANUL ULUM BANDUNG', 'kp. abc', '-6.902436161549575', '107.61850121750805', 10000000, '', '08:00:00', '14:00:00'),
(14, 'komplek ujung berung', 'kp. abc', '-6.910855721907237', '107.70603171165993', 10000000, '', '00:00:00', '00:00:00'),
(15, 'Kantor pusat', 'kp. abc', '-6.902436161549575', '107.61850121750805', 10000000, '', '11:05:00', '08:10:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orang_tua`
--

CREATE TABLE `orang_tua` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `nama_orang_tua` varchar(100) NOT NULL,
  `hubungan` enum('Ayah','Ibu','Wali') NOT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orang_tua`
--

INSERT INTO `orang_tua` (`id`, `id_siswa`, `nama_orang_tua`, `hubungan`, `kontak`, `alamat`) VALUES
(9, 19, 'Xiumin', 'Ayah', '0099889900', 'Seoul'),
(10, 20, 'Gofar', 'Ayah', '88776690', 'kp'),
(11, 14, 'siti', 'Ibu', '12345', 'pli'),
(12, 22, 'jokowidodo', 'Ayah', '83939392', 'solo');

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi`
--

CREATE TABLE `presensi` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `foto_masuk` varchar(255) NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `jam_keluar` time NOT NULL,
  `foto_keluar` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `presensi`
--

INSERT INTO `presensi` (`id`, `id_siswa`, `tanggal_masuk`, `jam_masuk`, `foto_masuk`, `tanggal_keluar`, `jam_keluar`, `foto_keluar`, `keterangan`) VALUES
(9, 52, '2024-09-08', '16:55:15', 'masuk2024-09-08_16-55-59.png', '2024-09-08', '16:56:03', 'keluar2024-09-08_16-56-27.png', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `nama` varchar(225) NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `alamat` varchar(225) NOT NULL,
  `no_handphone` varchar(20) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `lokasi_presensi` varchar(50) NOT NULL,
  `foto` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nama`, `jenis_kelamin`, `alamat`, `no_handphone`, `kelas`, `lokasi_presensi`, `foto`) VALUES
(14, 'NIS004', 'Januar', 'Laki-laki', 'Seoul incheon', '7788990011', '7 b', 'Kantor pusat', 'IMG_4385.JPG'),
(18, 'NIS001', 'Suho', 'Laki-laki', 'Seoul', '654567890998', '7 A', 'Kantor pusat', 'IMG_20181120_111924.jpg'),
(19, 'NIS002', 'elrumi', 'Laki-laki', 'durian', '0099887766899', '7 B', 'Kantor pusat', 'IMG_20181120_111924.jpg'),
(20, 'NIS003', 'Andi', 'Laki-laki', 'kp', '0009', '8 B', 'Kantor pusat', 'IMG20230406175558.jpg'),
(21, 'NIS004', 'zaqia', 'Perempuan', 'pli', '087899', '8 A', 'SMP BUSTANUL ULUM BANDUNG', 'IMG20221231203450.jpg'),
(22, '123', ' dono', 'Laki-laki', 'jawa', '90222', '7 A', 'SMP BUSTANUL ULUM BANDUNG', 'R (3).jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `status` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `id_siswa`, `username`, `password`, `status`, `role`) VALUES
(32, 18, 'suho', '$2y$10$Ry/CtJA6nRl3Fa2f3SfcX.S4LRcN/tLZdRttnk4iz75hHRinDoeo.', 'Aktif', 'siswa'),
(43, NULL, 'exo', '$2y$10$Qu5hXSYtfyn1cX773HDkPuQJCvBFjMrIatq07WMBiPHICHMxE52ra', 'Aktif', 'admin'),
(44, NULL, 'pcy', '$2y$10$cGsj43t.ZHQ3niHfXqLtYuijm1p/6OCX14mF3RHP9UjDX4As.ZSyu', 'Aktif', 'admin'),
(45, NULL, 'exol', '$2y$10$UVS6bt7r3Wh.RMCg8vnrv.mKBuXAMdx0GV3m0j5pk5r26SonLxAUK', 'Aktif', 'admin'),
(46, 19, 'elrumi', '$2y$10$0q6JBaQyt.AbalqVV1eLUO68ExTeXtLxxMUvOSj4/kcRpoeIqu8cy', 'Aktif', 'siswa'),
(47, 19, 'xiumin', '$2y$10$aIkZwYKN356epl1VskBSJOov/yPDX50gePbZ2hx49188D3AgNB1F2', 'Aktif', 'orangtua'),
(48, 20, 'andi', '$2y$10$4LDCKehlPK6mXcBBorUhW.w0xkycbn7L08h1ePwsJ5Q5oQv9WRO8C', 'Aktif', 'siswa'),
(49, 20, 'gofar', '$2y$10$j2BdzCqqnxg8DZOh.o30pueSCCYBp8oaV.mi3zM3zzkVrlHOyTHHm', 'Aktif', 'orangtua'),
(50, 21, 'kia', '$2y$10$2n/YkVWQmlPfVznDzBNvwuLEdjkcMWl89VbIdGOqhiGYb4t5In2p.', 'Aktif', 'siswa'),
(51, 14, 'siti', '$2y$10$Ei2njm7Ri5/Z3egdjx7p/.IGxTAjaIU0GDDmLvVUBXYMk4M.iPSKK', 'Aktif', 'orangtua'),
(52, 22, 'siswa', '$2y$10$Yk89Yo1S/KPMCeUaKB/0ouLCOnOe1RV4UL7f84epVQ6roVgZFDooi', 'Aktif', 'siswa'),
(53, 22, 'ortu', '$2y$10$zGbFk.0/2uWwfq.Lm4huAejONkTlMwrdi3j7g4AiA1kbndi622lg2', 'Aktif', 'orangtua'),
(54, NULL, 'admin', '$2y$10$NPbtNz9af0LrWU9PkcKuv.fEhNR6q2H3pPidBBAD3tj4OOTi7dx.W', 'Aktif', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `hari_libur`
--
ALTER TABLE `hari_libur`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ketidakhadiran`
--
ALTER TABLE `ketidakhadiran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa_index` (`id_siswa`);

--
-- Indeks untuk tabel `lokasi_presensi`
--
ALTER TABLE `lokasi_presensi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indeks untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pegawai` (`id_siswa`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `hari_libur`
--
ALTER TABLE `hari_libur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `ketidakhadiran`
--
ALTER TABLE `ketidakhadiran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `lokasi_presensi`
--
ALTER TABLE `lokasi_presensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `orang_tua`
--
ALTER TABLE `orang_tua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `ketidakhadiran`
--
ALTER TABLE `ketidakhadiran`
  ADD CONSTRAINT `ketidakhadiran_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD CONSTRAINT `orang_tua_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
