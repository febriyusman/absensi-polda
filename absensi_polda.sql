-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2025 at 05:33 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_polda`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int NOT NULL,
  `anggota_id` int NOT NULL,
  `apel` enum('Hadir','Tidak Hadir') NOT NULL,
  `keterangan` text,
  `waktu` datetime NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_shift` enum('Pagi','Sore') DEFAULT NULL,
  `sprint_pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `anggota_id`, `apel`, `keterangan`, `waktu`, `tanggal`, `waktu_shift`, `sprint_pdf_path`, `created_at`) VALUES
(1, 1, 'Hadir', 'Hadir pada pagi hari', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(2, 1, 'Hadir', 'Hadir pada sore hari', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(3, 2, 'Tidak Hadir', 'Alasan: Sakit', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(4, 2, 'Hadir', 'Hadir pada sore hari', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(5, 3, 'Hadir', 'Hadir pada pagi hari', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(6, 3, 'Tidak Hadir', 'Alasan: Libur', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(7, 4, 'Hadir', 'Hadir pada pagi hari', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(8, 4, 'Hadir', 'Hadir pada sore hari', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(9, 5, 'Tidak Hadir', 'Alasan: Keperluan Dinas', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(10, 5, 'Hadir', 'Hadir pada sore hari', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(11, 6, 'Hadir', 'Hadir pada pagi hari', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(12, 6, 'Hadir', 'Hadir pada sore hari', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(13, 7, 'Tidak Hadir', 'Alasan: Sakit', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(14, 7, 'Tidak Hadir', 'Alasan: Sakit', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(15, 8, 'Hadir', 'Hadir pada pagi hari', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(16, 8, 'Hadir', 'Hadir pada sore hari', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(17, 9, 'Hadir', 'Hadir pada pagi hari', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(18, 9, 'Tidak Hadir', 'Alasan: Libur', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(19, 10, 'Tidak Hadir', 'Alasan: Libur', '2025-06-21 07:00:00', '2025-06-21', 'Pagi', NULL, '2025-06-22 05:03:52'),
(20, 10, 'Tidak Hadir', 'Alasan: Libur', '2025-06-21 15:00:00', '2025-06-21', 'Sore', NULL, '2025-06-22 05:03:52'),
(21, 1, 'Hadir', 'Hadir pada pagi hari', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(22, 1, 'Hadir', 'Hadir pada sore hari', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(23, 2, 'Tidak Hadir', 'Alasan: Sakit', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(24, 2, 'Hadir', 'Hadir pada sore hari', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(25, 3, 'Hadir', 'Hadir pada pagi hari', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(26, 3, 'Tidak Hadir', 'Alasan: Libur', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(27, 4, 'Hadir', 'Hadir pada pagi hari', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(28, 4, 'Hadir', 'Hadir pada sore hari', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(29, 5, 'Tidak Hadir', 'Alasan: Keperluan Dinas', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(30, 5, 'Hadir', 'Hadir pada sore hari', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(31, 6, 'Hadir', 'Hadir pada pagi hari', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(32, 6, 'Hadir', 'Hadir pada sore hari', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(33, 7, 'Tidak Hadir', 'Alasan: Sakit', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(34, 7, 'Tidak Hadir', 'Alasan: Sakit', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(35, 8, 'Hadir', 'Hadir pada pagi hari', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(36, 8, 'Hadir', 'Hadir pada sore hari', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(37, 9, 'Hadir', 'Hadir pada pagi hari', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(38, 9, 'Tidak Hadir', 'Alasan: Libur', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(39, 10, 'Tidak Hadir', 'Alasan: Libur', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:03:52'),
(40, 10, 'Tidak Hadir', 'Alasan: Libur', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 05:03:52'),
(41, 11, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(42, 12, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(43, 13, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(44, 14, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(45, 15, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(46, 16, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(47, 17, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(48, 18, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(49, 19, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(50, 20, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(51, 21, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(52, 22, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(53, 23, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(54, 24, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(55, 25, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(56, 26, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(57, 27, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(58, 28, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(59, 29, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(60, 30, 'Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 05:05:55'),
(61, 11, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(62, 12, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(63, 13, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(64, 14, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(65, 15, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(66, 16, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(67, 17, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(68, 18, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(69, 19, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(70, 20, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(71, 21, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(72, 22, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(73, 23, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(74, 24, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(75, 25, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(76, 26, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(77, 27, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(78, 28, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(79, 29, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59'),
(80, 30, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', '', '2025-06-22 05:18:59');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `pangkat_id` int DEFAULT NULL,
  `jabatan_id` int DEFAULT NULL,
  `subsatker_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `nama`, `pangkat_id`, `jabatan_id`, `subsatker_id`) VALUES
(1, 'Andi', 1, 1, 1),
(2, 'Budi', 2, 2, 2),
(3, 'Citra', 3, 3, 3),
(4, 'Dedi', 1, 2, 4),
(5, 'Elina', 2, 1, 2),
(6, 'Fajar', 3, 2, 1),
(7, 'Gina', 1, 3, 3),
(8, 'Hadi', 4, 1, 4),
(9, 'Indra', 1, 2, 2),
(10, 'Joko', 2, 3, 1),
(11, 'Kiki', 3, 1, 4),
(12, 'Lina', 1, 2, 2),
(13, 'Mira', 4, 1, 3),
(14, 'Nina', 2, 3, 1),
(15, 'Omar', 3, 2, 4),
(16, 'Putu', 1, 1, 3),
(17, 'Qori', 4, 3, 2),
(18, 'Rini', 2, 1, 4),
(19, 'Sari', 3, 2, 2),
(20, 'Toni', 1, 3, 3),
(21, 'Uli', 2, 1, 1),
(22, 'Vina', 3, 2, 4),
(23, 'Wawan', 1, 3, 2),
(24, 'Xena', 4, 1, 1),
(25, 'Yani', 2, 3, 4),
(26, 'Zain', 3, 1, 3),
(27, 'Agus', 1, 2, 4),
(28, 'Beni', 2, 3, 1),
(29, 'Cahyo', 3, 1, 2),
(30, 'Dewi', 4, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` int NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `nama_jabatan`) VALUES
(1, 'Komandan Regu'),
(2, 'Anggota'),
(3, 'Intel'),
(7, 'Kapolda 1');

-- --------------------------------------------------------

--
-- Table structure for table `pangkat`
--

CREATE TABLE `pangkat` (
  `id` int NOT NULL,
  `nama_pangkat` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pangkat`
--

INSERT INTO `pangkat` (`id`, `nama_pangkat`) VALUES
(1, 'Letda'),
(2, 'Sertu'),
(3, 'Aiptu'),
(4, 'Serda');

-- --------------------------------------------------------

--
-- Table structure for table `subsatker`
--

CREATE TABLE `subsatker` (
  `id` int NOT NULL,
  `nama_subsatker` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subsatker`
--

INSERT INTO `subsatker` (`id`, `nama_subsatker`) VALUES
(1, 'Subsatker A'),
(2, 'Subsatker B'),
(3, 'Subsatker C'),
(4, 'Subtaker D');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_absensi` (`anggota_id`,`tanggal`,`waktu_shift`),
  ADD KEY `anggota_id` (`anggota_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pangkat_id` (`pangkat_id`),
  ADD KEY `jabatan_id` (`jabatan_id`),
  ADD KEY `subsatker_id` (`subsatker_id`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pangkat`
--
ALTER TABLE `pangkat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subsatker`
--
ALTER TABLE `subsatker`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pangkat`
--
ALTER TABLE `pangkat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subsatker`
--
ALTER TABLE `subsatker`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`);

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`pangkat_id`) REFERENCES `pangkat` (`id`),
  ADD CONSTRAINT `anggota_ibfk_2` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan` (`id`),
  ADD CONSTRAINT `anggota_ibfk_3` FOREIGN KEY (`subsatker_id`) REFERENCES `subsatker` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
