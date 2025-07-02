-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 12:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fresh_accounting_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_type` enum('Aset Lancar','Aset Tetap','Kewajiban','Aset Neto','Pendapatan','Biaya','Investasi') NOT NULL,
  `normal_balance` enum('Debit','Kredit') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `code`, `name`, `parent_id`, `account_type`, `normal_balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1-1', 'Aset Lancar', NULL, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(2, '1-11', 'Kas Setara Kas', 1, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(3, '1-110001', 'Kas Tangan', 2, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(4, '1-110002', 'Kas Bank', 2, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(5, '1-110002-1', 'Kas Bank Tabungan', 4, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(6, '1-12', 'Piutang', 1, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(7, '1-120001', 'Piutang Pembayaran Siswa', 6, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(8, '1-120001-1', 'Piutang PPDB', 7, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(9, '1-120001-2', 'Piutang DPP', 7, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(10, '1-120001-3', 'Piutang SPP', 7, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(11, '1-120001-4', 'Piutang UKS', 7, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(12, '1-120002', 'Piutang Internal', 6, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(13, '1-120003', 'Piutang Eksternal', 6, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(14, '1-13', 'Bangunan Dalam Proses', 1, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(15, '1-130001', 'Bangunan Dalam Proses', 14, 'Aset Lancar', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(16, '1-2', 'Aset Tetap', NULL, 'Aset Tetap', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(17, '1-21', 'Peralatan', 16, 'Aset Tetap', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(18, '1-210001', 'Peralatan Kantor', 17, 'Aset Tetap', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(19, '1-210002', 'Peralatan Penunjang Pembelajaran', 17, 'Aset Tetap', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(20, '1-210003', 'Peralatan Laboratorium', 17, 'Aset Tetap', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(21, '1-210004', 'Peralatan Ruang Serbaguna', 17, 'Aset Tetap', 'Debit', '2025-06-11 06:22:22', '2025-06-11 06:22:22', NULL),
(22, '1-210005', 'Peralatan Kantin', 17, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(23, '1-22', 'Kendaraan', 16, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(24, '1-220001', 'Mobil', 23, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(25, '1-220002', 'Motor', 23, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(26, '1-23', 'Gedung', 16, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(27, '1-230001', 'Gedung Utama', 26, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(28, '1-24', 'Tanah', 16, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(29, '1-240001', 'Tanah', 28, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(30, '1-25', 'Akumulasi Penyusutan Peralatan', 16, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(31, '1-250001', 'Akumulasi Penyusutan Peralatan Kantor', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(32, '1-250002', 'Akumulasi Penyusutan Peralatan Penunjang Pembelajaran', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(33, '1-250003', 'Akumulasi Penyusutan Peralatan Laboratorium', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(34, '1-250004', 'Akumulasi Penyusutan Peralatan Ruang Serbaguna', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(35, '1-250005', 'Akumulasi Penyusutan Peralatan Kantin', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(36, '1-250006', 'Akumulasi Penyusutan Peralatan Mobil', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(37, '1-250007', 'Akumulasi Penyusutan Peralatan Motor', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(38, '1-250008', 'Akumulasi Penyusutan Peralatan Gedung', 30, 'Aset Tetap', 'Debit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(39, '2-1', 'Kewajiban', NULL, 'Kewajiban', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(40, '2-11', 'Kewajiban Jangka Pendek', 39, 'Kewajiban', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(41, '2-110001', 'Kewajiban Internal', 40, 'Kewajiban', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(42, '2-110002', 'Kewajiban Eksternal', 40, 'Kewajiban', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(43, '2-12', 'Kewajiban Jangka Panjang', 39, 'Kewajiban', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(44, '2-120001', 'Kewajiban Internal', 43, 'Kewajiban', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(45, '2-120002', 'Kewajiban Eksternal', 43, 'Kewajiban', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(46, '3-1', 'Aset Neto', NULL, 'Aset Neto', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(47, '3-11', 'Aset Neto', 46, 'Aset Neto', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(48, '3-110001', 'Aset Neto Tanpa Pembatas', 47, 'Aset Neto', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(49, '3-110002', 'Aset Neto Dengan Pembatas', 47, 'Aset Neto', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(50, '4-1', 'Pendapatan', NULL, 'Pendapatan', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(51, '4-11', 'Pendapatan Pembayaran Siswa', 50, 'Pendapatan', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(52, '4-110001', 'Pendapatan PPDB', 51, 'Pendapatan', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(53, '4-110002', 'Pendapatan DPP', 51, 'Pendapatan', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(54, '4-110003', 'Pendapatan SPP', 51, 'Pendapatan', 'Kredit', '2025-06-11 06:22:23', '2025-06-11 06:22:23', NULL),
(55, '4-110004', 'Pendapatan UKS', 51, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(56, '4-12', 'Pendapatan Internal', 50, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(57, '4-120001', 'Pendapatan Sewa Kantin', 56, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(58, '4-120002', 'UIS (Uang Infaq Siswa)', 56, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(59, '4-120003', 'UIG (Uang Infaq Guru) dan UIK (Uang Infaq Karyawan)', 56, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(60, '4-120004', 'Kantin', 56, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(61, '4-120005', 'Koperasi Seragam', 56, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(62, '4-120006', 'Koperasi Buku', 56, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(63, '4-13', 'Pendapatan Eksternal', 50, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(64, '4-130001', 'Pendapatan Bantuan Pemerintah', 63, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(65, '4-130001-1', 'Bosnas', 64, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(66, '4-130001-2', 'Bosko', 64, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(67, '4-130002', 'Pendapatan Bantuan Swasta', 63, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(68, '4-14', 'Pendapatan Penghapusan Aset Tetap', 50, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(69, '4-140001', 'Pendapatan Penghapusan Aset Tetap', 68, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(70, '4-15', 'Pendapatan Lain-lain', 50, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(71, '4-150001', 'Pendapatan Acara Pameran/Kegiatan', 70, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(72, '4-150002', 'Pendapatan Rabat Penjualan', 70, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(73, '4-150003', 'Infaq Jumat', 70, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(74, '4-150004', 'Ekskul', 70, 'Pendapatan', 'Kredit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(75, '6-1', 'Biaya', NULL, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(76, '6-11', 'Biaya Standart Nasional Pendidikan', 75, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(77, '6-110001', 'Biaya Standart Proses', 76, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(78, '6-110001-1', 'Administrasi Kelas', 77, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(79, '6-110001-2', 'Buku Penunjang Pembelajaran dan UKS', 77, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(80, '6-110001-3', 'Kelas Terapi', 77, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(81, '6-110002', 'Biaya Standart Kompetensi Kelulusan', 76, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(82, '6-110002-1', 'Olimpiade dan Kompetisi', 81, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(83, '6-110002-2', 'Peringatan Hari Besar', 81, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(84, '6-110002-3', 'Aktivitas Luar Sekolah', 81, 'Biaya', 'Debit', '2025-06-11 06:22:24', '2025-06-11 06:22:24', NULL),
(85, '6-110002-4', 'Kegiatan Ekstrakurikuler', 81, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(86, '6-110002-5', 'Kegiatan Intrakurikuler', 81, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(87, '6-110002-6', 'Pengembangan Karakter Siswa', 81, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(88, '6-110002-7', 'Acara Kemuhammadiyahan', 81, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(89, '6-110002-8', 'Lain-Lain', 81, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(90, '6-110003', 'Biaya Standart Sarana dan Prasarana', 76, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(91, '6-110003-1', 'Operasional Sarana Kebersihan', 90, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(92, '6-110003-2', 'Operasional Sarana Kelistrikan', 90, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(93, '6-110003-3', 'Pembelian Alat Tulis Kantor', 90, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(94, '6-110003-4', 'Pakan', 90, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(95, '6-110003-5', 'Operasional Servis dan Sewa', 90, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(96, '6-110003-6', 'Operasional Lain-lain', 90, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(97, '6-110004', 'Biaya Standart Pembiayaan', 76, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(98, '6-110004-1', 'Biaya Cetak, Fotokopi, dan Scan', 97, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(99, '6-110004-2', 'Biaya Maisyah Guru dan Karyawan', 97, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(100, '6-110004-3', 'Biaya Konsumsi Pegawai dan Tamu', 97, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(101, '6-110004-4', 'Biaya Pelatihan Pegawai', 97, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(102, '6-110004-5', 'Biaya Listrik dan Komunikasi', 97, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(103, '6-110005', 'Biaya Standart Penilaian', 76, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(104, '6-110005-1', 'Biaya PTS', 103, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(105, '6-110005-2', 'Biaya PAS', 103, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(106, '6-110005-3', 'Biaya KDK', 103, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(107, '6-110005-4 ', 'Biaya Munaqosah', 103, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(108, '6-110005-5', 'Biaya AKM', 103, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(109, '6-12', 'Biaya Penyusutan', 75, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(110, '6-120001', 'Biaya Penyusutan Peralatan Kantor', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(111, '6-120002', 'Biaya Penyusutan Peralatan Penunjang Pembelajaran', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(112, '6-120003', 'Biaya Penyusutan Peralatan Laboratorium', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(113, '6-120004', 'Biaya Penyusutan Peralatan Ruang Serbaguna', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(114, '6-120005', 'Biaya Penyusutan Peralatan Kantin', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(115, '6-120006', 'Biaya Penyusutan Mobil', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(116, '6-120007', 'Biaya Penyusutan Motor', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(117, '6-120008', 'Biaya Penyusutan Gedung', 109, 'Biaya', 'Debit', '2025-06-11 06:22:25', '2025-06-11 06:22:25', NULL),
(118, '6-13', 'Biaya Penghapusan Aset Tetap', 75, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(119, '6-130001', 'Biaya Kerugian Penjualan Aset', 118, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(120, '6-14', 'Donasi', 75, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(121, '6-140001', 'Menjenguk Siswa', 120, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(122, '6-140002', 'Menjenguk Guru', 120, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(123, '6-140003', 'Rumah Tahfidz', 120, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(124, '6-140004', 'Donasi Lain-lain', 120, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(125, '6-15', 'Biaya Lain-lain', 75, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(126, '6-150001', 'Biaya Bank', 125, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(127, '6-150001-1', 'Administrasi Bank', 126, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(128, '6-150002', 'Biaya Pajak', 125, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(129, '6-150002-1', 'Administrasi Pajak PPh', 128, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(130, '6-150002-2', 'Administrasi Pajak PPN', 128, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(131, '6-150003', 'Biaya Bos', 125, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(132, '6-150003-1', 'Penyusunan Laporan Bos', 131, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(133, '6-150004', 'Pengembalian', 125, 'Biaya', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(134, '7-1', 'Investasi', NULL, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(135, '7-11', 'Investasi', 134, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(136, '7-110001', 'Investasi Peralatan Kantor', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(137, '7-110002', 'Investasi Peralatan Penunjang Pembelajaran', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(138, '7-110003', 'Investasi Peralatan Laboratorium', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(139, '7-110004', 'Investasi Peralatan Ruang Serbaguna', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(140, '7-110005', 'Investasi Peralatan Kantin', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(141, '7-110006', 'Investasi Mobil', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(142, '7-110007', 'Investasi Motor', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(143, '7-110008', 'Investasi Gedung Utama', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL),
(144, '7-110009', 'Investasi Tanah', 135, 'Investasi', 'Debit', '2025-06-11 06:22:26', '2025-06-11 06:22:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `beginning_balances`
--

CREATE TABLE `beginning_balances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `month` tinyint(3) UNSIGNED NOT NULL,
  `year` smallint(5) UNSIGNED NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `depreciations`
--

CREATE TABLE `depreciations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fix_asset_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `employee_id_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_receivables`
--

CREATE TABLE `employee_receivables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('Unpaid','Partial','Paid') NOT NULL DEFAULT 'Unpaid',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_receivable_details`
--

CREATE TABLE `employee_receivable_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_receivable_id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `period` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fix_assets`
--

CREATE TABLE `fix_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `acquisition_date` date NOT NULL,
  `acquisition_cost` decimal(15,2) NOT NULL,
  `useful_life` int(11) NOT NULL,
  `accumulated_depriciation` decimal(15,2) NOT NULL DEFAULT 0.00,
  `depreciation_percentage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view_reports', 'web', '2025-06-10 04:50:59', '2025-06-10 04:50:59'),
(2, 'edit_transactions', 'web', '2025-06-10 04:50:59', '2025-06-10 04:50:59'),
(3, 'manage_schools', 'web', '2025-06-10 04:50:59', '2025-06-10 04:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', 'web', '2025-06-10 04:50:58', '2025-06-10 04:50:58'),
(2, 'AdminMonitor', 'web', '2025-06-10 04:50:58', '2025-06-10 04:50:58'),
(3, 'SchoolAdmin', 'web', '2025-06-10 04:50:58', '2025-06-10 04:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 3),
(3, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `income_account_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('Siswa','Guru','Karyawan') NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `schedule_type` enum('Bulanan','Non Bulanan') NOT NULL DEFAULT 'Bulanan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_majors`
--

CREATE TABLE `school_majors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `student_id_number` varchar(255) NOT NULL,
  `class` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_receivables`
--

CREATE TABLE `student_receivables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('Unpaid','Partial','Paid') NOT NULL DEFAULT 'Unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `period` date DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_receivable_details`
--

CREATE TABLE `student_receivable_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_receivable_id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `period` date NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `teacher_id_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_receivables`
--

CREATE TABLE `teacher_receivables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('Unpaid','Partial','Paid') NOT NULL DEFAULT 'Unpaid',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_receivable_details`
--

CREATE TABLE `teacher_receivable_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_receivable_id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `period` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reference_id` bigint(20) DEFAULT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `type` enum('general','adjustment') NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'SchoolAdmin',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `school_id`, `phone`, `status`) VALUES
(1, 'Super Admin', 'superadmin@example.com', NULL, '$2y$10$H/j/VIC6tbU8JG/t374GyuS3CzkkSQNf46wfqdiOYAGCvo/7KD6da', 'SuperAdmin', NULL, '2025-06-10 04:50:07', '2025-06-20 10:49:06', NULL, '089912341234', 1),
(2, 'Admin Monitor', 'admin@example.com', NULL, '$2y$10$9ZByvCVgilCzzV5.4e7pg.xt2gIeAFVdzChGMhmYt/gwUs0rGQWVi', 'AdminMonitor', NULL, '2025-06-10 04:50:07', '2025-06-13 23:37:33', NULL, '082118681625', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounts_code_unique` (`code`),
  ADD KEY `accounts_parent_id_foreign` (`parent_id`),
  ADD KEY `accounts_code_index` (`code`);

--
-- Indexes for table `beginning_balances`
--
ALTER TABLE `beginning_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `beginning_balances_unique` (`school_id`,`account_id`,`month`,`year`),
  ADD KEY `beginning_balances_account_id_foreign` (`account_id`);

--
-- Indexes for table `depreciations`
--
ALTER TABLE `depreciations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `depreciations_account_id_foreign` (`account_id`),
  ADD KEY `depreciations_fix_asset_id_account_id_index` (`fix_asset_id`,`account_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_id_number_unique` (`employee_id_number`),
  ADD UNIQUE KEY `employees_email_unique` (`email`),
  ADD KEY `employees_school_id_employee_id_number_index` (`school_id`,`employee_id_number`);

--
-- Indexes for table `employee_receivables`
--
ALTER TABLE `employee_receivables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_receivables_account_id_foreign` (`account_id`),
  ADD KEY `employee_receivables_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_receivables_school_id_account_id_employee_id_index` (`school_id`,`account_id`,`employee_id`);

--
-- Indexes for table `employee_receivable_details`
--
ALTER TABLE `employee_receivable_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_receivable_details_employee_receivable_id_index` (`employee_receivable_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fix_assets`
--
ALTER TABLE `fix_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fix_assets_account_id_foreign` (`account_id`),
  ADD KEY `fix_assets_school_id_account_id_index` (`school_id`,`account_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_account_id_foreign` (`account_id`),
  ADD KEY `schedules_income_account_id_foreign` (`income_account_id`),
  ADD KEY `schedules_school_id_account_id_income_account_id_index` (`school_id`,`account_id`,`income_account_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schools_email_unique` (`email`),
  ADD KEY `schools_name_index` (`name`);

--
-- Indexes for table `school_majors`
--
ALTER TABLE `school_majors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_majors_school_id_index` (`school_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_id_number_unique` (`student_id_number`),
  ADD KEY `students_school_id_student_id_number_index` (`school_id`,`student_id_number`);

--
-- Indexes for table `student_receivables`
--
ALTER TABLE `student_receivables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_receivables_account_id_foreign` (`account_id`),
  ADD KEY `student_receivables_school_id_account_id_index` (`school_id`,`account_id`),
  ADD KEY `student_receivables_student_id_index` (`student_id`);

--
-- Indexes for table `student_receivable_details`
--
ALTER TABLE `student_receivable_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_receivable_details_student_receivable_id_index` (`student_receivable_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_teacher_id_number_unique` (`teacher_id_number`),
  ADD UNIQUE KEY `teachers_email_unique` (`email`),
  ADD KEY `teachers_school_id_teacher_id_number_index` (`school_id`,`teacher_id_number`);

--
-- Indexes for table `teacher_receivables`
--
ALTER TABLE `teacher_receivables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_receivables_account_id_foreign` (`account_id`),
  ADD KEY `teacher_receivables_teacher_id_foreign` (`teacher_id`),
  ADD KEY `teacher_receivables_school_id_account_id_teacher_id_index` (`school_id`,`account_id`,`teacher_id`);

--
-- Indexes for table `teacher_receivable_details`
--
ALTER TABLE `teacher_receivable_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_receivable_details_teacher_receivable_id_index` (`teacher_receivable_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_account_id_foreign` (`account_id`),
  ADD KEY `transactions_school_id_account_id_date_index` (`school_id`,`account_id`,`date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_email_index` (`email`),
  ADD KEY `users_school_id_index` (`school_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `beginning_balances`
--
ALTER TABLE `beginning_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `depreciations`
--
ALTER TABLE `depreciations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_receivables`
--
ALTER TABLE `employee_receivables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_receivable_details`
--
ALTER TABLE `employee_receivable_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fix_assets`
--
ALTER TABLE `fix_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school_majors`
--
ALTER TABLE `school_majors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_receivables`
--
ALTER TABLE `student_receivables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_receivable_details`
--
ALTER TABLE `student_receivable_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_receivables`
--
ALTER TABLE `teacher_receivables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_receivable_details`
--
ALTER TABLE `teacher_receivable_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `beginning_balances`
--
ALTER TABLE `beginning_balances`
  ADD CONSTRAINT `beginning_balances_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `beginning_balances_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `depreciations`
--
ALTER TABLE `depreciations`
  ADD CONSTRAINT `depreciations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `depreciations_fix_asset_id_foreign` FOREIGN KEY (`fix_asset_id`) REFERENCES `fix_assets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_receivables`
--
ALTER TABLE `employee_receivables`
  ADD CONSTRAINT `employee_receivables_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_receivables_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_receivables_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_receivable_details`
--
ALTER TABLE `employee_receivable_details`
  ADD CONSTRAINT `employee_receivable_details_employee_receivable_id_foreign` FOREIGN KEY (`employee_receivable_id`) REFERENCES `employee_receivables` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fix_assets`
--
ALTER TABLE `fix_assets`
  ADD CONSTRAINT `fix_assets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fix_assets_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_income_account_id_foreign` FOREIGN KEY (`income_account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `school_majors`
--
ALTER TABLE `school_majors`
  ADD CONSTRAINT `school_majors_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_receivables`
--
ALTER TABLE `student_receivables`
  ADD CONSTRAINT `student_receivables_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_receivables_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_receivables_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_receivable_details`
--
ALTER TABLE `student_receivable_details`
  ADD CONSTRAINT `student_receivable_details_student_receivable_id_foreign` FOREIGN KEY (`student_receivable_id`) REFERENCES `student_receivables` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_receivables`
--
ALTER TABLE `teacher_receivables`
  ADD CONSTRAINT `teacher_receivables_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_receivables_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_receivables_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_receivable_details`
--
ALTER TABLE `teacher_receivable_details`
  ADD CONSTRAINT `teacher_receivable_details_teacher_receivable_id_foreign` FOREIGN KEY (`teacher_receivable_id`) REFERENCES `teacher_receivables` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
