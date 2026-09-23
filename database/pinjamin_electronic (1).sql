-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 02:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pinjamin_electronic`
--

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id_alat` int(11) NOT NULL,
  `kode_alat` varchar(20) NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `kondisi` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `status` enum('tersedia','dipinjam','tidak_aktif') NOT NULL DEFAULT 'tersedia',
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id_alat`, `kode_alat`, `nama_alat`, `id_kategori`, `merk`, `jumlah`, `kondisi`, `status`, `deskripsi`) VALUES
(1, 'ELC-001', 'Laptop', 1, 'ASUS', 5, 'baik', 'tersedia', 'Laptop untuk kegiatan belajar dan tugas siswa'),
(2, 'ELC-002', 'Laptop', 1, 'Acer', 3, 'baik', 'tersedia', 'Laptop untuk kegiatan pembelajaran'),
(3, 'ELC-003', 'Proyektor', 2, 'Epson', 4, 'baik', 'tersedia', 'Proyektor untuk presentasi di kelas'),
(4, 'ELC-004', 'Proyektor', 2, 'BenQ', 2, 'baik', 'tersedia', 'Proyektor untuk presentasi dan kegiatan sekolah'),
(5, 'ELC-005', 'Kamera Digital', 3, 'Canon', 2, 'baik', 'tersedia', 'Kamera untuk dokumentasi kegiatan sekolah'),
(6, 'ELC-006', 'Kamera DSLR', 3, 'Nikon', 1, 'baik', 'tersedia', 'Kamera untuk dokumentasi dan produksi multimedia'),
(7, 'ELC-007', 'Speaker Portable', 4, 'JBL', 4, 'baik', 'tersedia', 'Speaker untuk kegiatan sekolah'),
(8, 'ELC-008', 'Microphone Wireless', 4, 'Sennheiser', 3, 'baik', 'tersedia', 'Microphone wireless untuk presentasi dan acara'),
(9, 'ELC-009', 'Headset', 5, 'Logitech', 8, 'baik', 'tersedia', 'Headset untuk kebutuhan pembelajaran multimedia'),
(10, 'ELC-010', 'Mouse', 5, 'Logitech', 10, 'baik', 'tersedia', 'Mouse USB untuk laptop atau komputer'),
(11, 'ELC-011', 'Keyboard', 5, 'Fantech', 8, 'baik', 'tersedia', 'Keyboard USB untuk komputer'),
(12, 'ELC-012', 'Kabel HDMI', 5, 'Vention', 10, 'baik', 'tersedia', 'Kabel HDMI untuk menghubungkan laptop dengan proyektor'),
(13, 'ELC-013', 'Charger Laptop', 5, 'Universal', 6, 'rusak_ringan', 'tersedia', 'Charger laptop cadangan'),
(14, 'ELC-014', 'Tripod Kamera', 5, 'Takara', 3, 'baik', 'tersedia', 'Tripod untuk kamera saat dokumentasi'),
(15, 'ELC-015', 'Webcam', 5, 'Logitech', 4, 'baik', 'tidak_aktif', 'Webcam sedang tidak digunakan');

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id_detail` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `id_alat` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `kondisi_keluar` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id_detail`, `id_peminjaman`, `id_alat`, `jumlah`, `kondisi_keluar`) VALUES
(1, 1, 3, 1, 'baik'),
(2, 1, 12, 1, 'baik'),
(3, 2, 5, 1, 'baik'),
(4, 3, 4, 1, 'baik'),
(5, 4, 2, 1, 'baik');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(5, 'Aksesoris Elektronik'),
(4, 'Audio'),
(3, 'Kamera'),
(1, 'Laptop'),
(2, 'Proyektor');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `waktu` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu`) VALUES
(1, 1, 'login', '2026-08-24 08:00:00'),
(2, 3, 'login', '2026-08-24 08:05:00'),
(3, 2, 'mengajukan peminjaman', '2026-08-01 09:00:00'),
(4, 3, 'menyetujui peminjaman', '2026-08-01 10:00:00'),
(5, 4, 'login', '2026-08-05 07:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `keperluan` text DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak','selesai') NOT NULL DEFAULT 'menunggu',
  `disetujui_oleh` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `tanggal_pengajuan`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `keperluan`, `status`, `disetujui_oleh`, `keterangan`) VALUES
(1, 2, '2026-08-01', '2026-08-02', '2026-08-05', 'Presentasi tugas RPL', 'selesai', 3, 'Sudah dikembalikan dengan baik'),
(2, 4, '2026-08-05', '2026-08-06', '2026-08-08', 'Dokumentasi kegiatan sekolah', 'selesai', 3, 'Barang dikembalikan tepat waktu'),
(3, 5, '2026-08-10', '2026-08-11', '2026-08-13', 'Presentasi tugas kelompok', 'disetujui', 3, NULL),
(4, 2, '2026-08-18', '2026-08-19', '2026-08-21', 'Lomba presentasi', 'disetujui', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_detail` int(11) NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `kondisi_kembali` varchar(50) DEFAULT NULL,
  `denda` decimal(10,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_detail`, `tanggal_kembali`, `kondisi_kembali`, `denda`, `keterangan`) VALUES
(1, 1, '2026-08-05', 'baik', 0.00, 'Proyektor dikembalikan dengan kondisi baik'),
(2, 2, '2026-08-05', 'baik', 0.00, 'Kabel HDMI lengkap'),
(3, 3, '2026-08-08', 'baik', 0.00, 'Kamera dikembalikan dengan baik');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','peminjam') NOT NULL DEFAULT 'peminjam',
  `no_telp` varchar(20) DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama`, `username`, `password`, `role`, `no_telp`, `status_aktif`, `created_at`) VALUES
(1, 'Administrator', 'admin', '123', 'admin', '0800000000', 1, '2026-08-31 02:02:43'),
(2, 'Andi Pratama', 'andipratama', '123', 'peminjam', '081234567891', 1, '2026-08-31 02:02:43'),
(3, 'Budi Santoso', 'budisantoso', '123', 'petugas', '081234567892', 1, '2026-08-31 02:02:43'),
(4, 'Sinta Dewi', 'sintadewi', '123', 'peminjam', '081234567893', 1, '2026-08-31 02:02:43'),
(5, 'Rudi Hartono', 'rudihartono', '123', 'peminjam', '081234567894', 1, '2026-08-31 02:02:43'),
(7, 'ssoyu', 'adit', '$2y$10$NLn/dZcnKhDHscnuwbUwTO.CzevAFvQ03.SExYZ5o8YPgJnIVd3AC', 'admin', '0987653123456', 1, '2026-09-14 02:20:48'),
(9, 'aditya', 'cudit', '$2y$10$0itZC5lBn9gzcd0DWcQJD.0ApGmXT/CkaFcG1EioxE1I7Vw7vMGLa', 'admin', '0975356712', 1, '2026-09-21 08:29:51'),
(10, 'cudit', 'cudit12', '$2y$10$yQPpoRbz5KAl0seVZvW3ceLpYYniiOPb9PXDQD2Niim9j1vH4Yvyq', 'admin', '098686527', 1, '2026-09-21 08:41:25'),
(11, 'aidit', 'adt12', 'adt123', 'peminjam', NULL, 1, '2026-09-22 12:14:00'),
(12, 'galang', 'galangg', 'galang123', 'admin', NULL, 1, '2026-09-22 12:15:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id_alat`),
  ADD UNIQUE KEY `kode_alat` (`kode_alat`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_alat` (`id_alat`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `disetujui_oleh` (`disetujui_oleh`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `id_detail` (`id_detail`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id_alat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alat`
--
ALTER TABLE `alat`
  ADD CONSTRAINT `alat_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`id_alat`) REFERENCES `alat` (`id_alat`);

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`disetujui_oleh`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_detail`) REFERENCES `detail_peminjaman` (`id_detail`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
