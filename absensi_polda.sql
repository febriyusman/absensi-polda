-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2025 at 04:18 AM
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
(6, 2, 'Tidak Hadir', '', '2025-06-22 07:00:00', '2025-06-22', 'Pagi', NULL, '2025-06-22 03:59:52'),
(12, 2, 'Hadir', '', '2025-06-22 15:00:00', '2025-06-22', 'Sore', NULL, '2025-06-22 04:13:22');

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
(2, 'Budi', 2, 2, 2);

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
  ADD UNIQUE KEY `unique_absensi_combined` (`anggota_id`,`tanggal`,`waktu_shift`),
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
