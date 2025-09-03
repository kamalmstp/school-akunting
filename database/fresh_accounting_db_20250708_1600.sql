/*
 Navicat Premium Data Transfer

 Source Server         : localhost - mysql
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : fresh_accounting_db

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 08/07/2025 16:08:28
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for accounts
-- ----------------------------
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED NULL DEFAULT NULL,
  `account_type` enum('Aset Lancar','Aset Tetap','Kewajiban','Aset Neto','Pendapatan','Biaya','Investasi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `normal_balance` enum('Debit','Kredit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `accounts_code_unique`(`code` ASC) USING BTREE,
  INDEX `accounts_parent_id_foreign`(`parent_id` ASC) USING BTREE,
  INDEX `accounts_code_index`(`code` ASC) USING BTREE,
  CONSTRAINT `accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 145 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of accounts
-- ----------------------------
INSERT INTO `accounts` VALUES (1, '1-1', 'Aset Lancar', NULL, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (2, '1-11', 'Kas Setara Kas', 1, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (3, '1-110001', 'Kas Tangan', 2, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (4, '1-110002', 'Kas Bank', 2, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (5, '1-110002-1', 'Kas Bank Tabungan', 4, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (6, '1-12', 'Piutang', 1, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (7, '1-120001', 'Piutang Pembayaran Siswa', 6, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (8, '1-120001-1', 'Piutang PPDB', 7, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (9, '1-120001-2', 'Piutang DPP', 7, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (10, '1-120001-3', 'Piutang SPP', 7, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (11, '1-120001-4', 'Piutang UKS', 7, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (12, '1-120002', 'Piutang Internal', 6, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (13, '1-120003', 'Piutang Eksternal', 6, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (14, '1-13', 'Bangunan Dalam Proses', 1, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (15, '1-130001', 'Bangunan Dalam Proses', 14, 'Aset Lancar', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (16, '1-2', 'Aset Tetap', NULL, 'Aset Tetap', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (17, '1-21', 'Peralatan', 16, 'Aset Tetap', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (18, '1-210001', 'Peralatan Kantor', 17, 'Aset Tetap', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (19, '1-210002', 'Peralatan Penunjang Pembelajaran', 17, 'Aset Tetap', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (20, '1-210003', 'Peralatan Laboratorium', 17, 'Aset Tetap', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (21, '1-210004', 'Peralatan Ruang Serbaguna', 17, 'Aset Tetap', 'Debit', '2025-06-11 13:22:22', '2025-06-11 13:22:22', NULL);
INSERT INTO `accounts` VALUES (22, '1-210005', 'Peralatan Kantin', 17, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (23, '1-22', 'Kendaraan', 16, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (24, '1-220001', 'Mobil', 23, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (25, '1-220002', 'Motor', 23, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (26, '1-23', 'Gedung', 16, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (27, '1-230001', 'Gedung Utama', 26, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (28, '1-24', 'Tanah', 16, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (29, '1-240001', 'Tanah', 28, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (30, '1-25', 'Akumulasi Penyusutan Peralatan', 16, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (31, '1-250001', 'Akumulasi Penyusutan Peralatan Kantor', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (32, '1-250002', 'Akumulasi Penyusutan Peralatan Penunjang Pembelajaran', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (33, '1-250003', 'Akumulasi Penyusutan Peralatan Laboratorium', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (34, '1-250004', 'Akumulasi Penyusutan Peralatan Ruang Serbaguna', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (35, '1-250005', 'Akumulasi Penyusutan Peralatan Kantin', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (36, '1-250006', 'Akumulasi Penyusutan Peralatan Mobil', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (37, '1-250007', 'Akumulasi Penyusutan Peralatan Motor', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (38, '1-250008', 'Akumulasi Penyusutan Peralatan Gedung', 30, 'Aset Tetap', 'Debit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (39, '2-1', 'Kewajiban', NULL, 'Kewajiban', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (40, '2-11', 'Kewajiban Jangka Pendek', 39, 'Kewajiban', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (41, '2-110001', 'Kewajiban Internal', 40, 'Kewajiban', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (42, '2-110002', 'Kewajiban Eksternal', 40, 'Kewajiban', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (43, '2-12', 'Kewajiban Jangka Panjang', 39, 'Kewajiban', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (44, '2-120001', 'Kewajiban Internal', 43, 'Kewajiban', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (45, '2-120002', 'Kewajiban Eksternal', 43, 'Kewajiban', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (46, '3-1', 'Aset Neto', NULL, 'Aset Neto', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (47, '3-11', 'Aset Neto', 46, 'Aset Neto', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (48, '3-110001', 'Aset Neto Tanpa Pembatas', 47, 'Aset Neto', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (49, '3-110002', 'Aset Neto Dengan Pembatas', 47, 'Aset Neto', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (50, '4-1', 'Pendapatan', NULL, 'Pendapatan', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (51, '4-11', 'Pendapatan Pembayaran Siswa', 50, 'Pendapatan', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (52, '4-110001', 'Pendapatan PPDB', 51, 'Pendapatan', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (53, '4-110002', 'Pendapatan DPP', 51, 'Pendapatan', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (54, '4-110003', 'Pendapatan SPP', 51, 'Pendapatan', 'Kredit', '2025-06-11 13:22:23', '2025-06-11 13:22:23', NULL);
INSERT INTO `accounts` VALUES (55, '4-110004', 'Pendapatan UKS', 51, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (56, '4-12', 'Pendapatan Internal', 50, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (57, '4-120001', 'Pendapatan Sewa Kantin', 56, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (58, '4-120002', 'UIS (Uang Infaq Siswa)', 56, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (59, '4-120003', 'UIG (Uang Infaq Guru) dan UIK (Uang Infaq Karyawan)', 56, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (60, '4-120004', 'Kantin', 56, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (61, '4-120005', 'Koperasi Seragam', 56, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (62, '4-120006', 'Koperasi Buku', 56, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (63, '4-13', 'Pendapatan Eksternal', 50, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (64, '4-130001', 'Pendapatan Bantuan Pemerintah', 63, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (65, '4-130001-1', 'Bosnas', 64, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (66, '4-130001-2', 'Bosko', 64, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (67, '4-130002', 'Pendapatan Bantuan Swasta', 63, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (68, '4-14', 'Pendapatan Penghapusan Aset Tetap', 50, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (69, '4-140001', 'Pendapatan Penghapusan Aset Tetap', 68, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (70, '4-15', 'Pendapatan Lain-lain', 50, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (71, '4-150001', 'Pendapatan Acara Pameran/Kegiatan', 70, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (72, '4-150002', 'Pendapatan Rabat Penjualan', 70, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (73, '4-150003', 'Infaq Jumat', 70, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (74, '4-150004', 'Ekskul', 70, 'Pendapatan', 'Kredit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (75, '6-1', 'Biaya', NULL, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (76, '6-11', 'Biaya Standart Nasional Pendidikan', 75, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (77, '6-110001', 'Biaya Standart Proses', 76, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (78, '6-110001-1', 'Administrasi Kelas', 77, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (79, '6-110001-2', 'Buku Penunjang Pembelajaran dan UKS', 77, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (80, '6-110001-3', 'Kelas Terapi', 77, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (81, '6-110002', 'Biaya Standart Kompetensi Kelulusan', 76, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (82, '6-110002-1', 'Olimpiade dan Kompetisi', 81, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (83, '6-110002-2', 'Peringatan Hari Besar', 81, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (84, '6-110002-3', 'Aktivitas Luar Sekolah', 81, 'Biaya', 'Debit', '2025-06-11 13:22:24', '2025-06-11 13:22:24', NULL);
INSERT INTO `accounts` VALUES (85, '6-110002-4', 'Kegiatan Ekstrakurikuler', 81, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (86, '6-110002-5', 'Kegiatan Intrakurikuler', 81, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (87, '6-110002-6', 'Pengembangan Karakter Siswa', 81, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (88, '6-110002-7', 'Acara Kemuhammadiyahan', 81, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (89, '6-110002-8', 'Lain-Lain', 81, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (90, '6-110003', 'Biaya Standart Sarana dan Prasarana', 76, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (91, '6-110003-1', 'Operasional Sarana Kebersihan', 90, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (92, '6-110003-2', 'Operasional Sarana Kelistrikan', 90, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (93, '6-110003-3', 'Pembelian Alat Tulis Kantor', 90, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (94, '6-110003-4', 'Pakan', 90, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (95, '6-110003-5', 'Operasional Servis dan Sewa', 90, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (96, '6-110003-6', 'Operasional Lain-lain', 90, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (97, '6-110004', 'Biaya Standart Pembiayaan', 76, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (98, '6-110004-1', 'Biaya Cetak, Fotokopi, dan Scan', 97, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (99, '6-110004-2', 'Biaya Maisyah Guru dan Karyawan', 97, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (100, '6-110004-3', 'Biaya Konsumsi Pegawai dan Tamu', 97, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (101, '6-110004-4', 'Biaya Pelatihan Pegawai', 97, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (102, '6-110004-5', 'Biaya Listrik dan Komunikasi', 97, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (103, '6-110005', 'Biaya Standart Penilaian', 76, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (104, '6-110005-1', 'Biaya PTS', 103, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (105, '6-110005-2', 'Biaya PAS', 103, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (106, '6-110005-3', 'Biaya KDK', 103, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (107, '6-110005-4 ', 'Biaya Munaqosah', 103, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (108, '6-110005-5', 'Biaya AKM', 103, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (109, '6-12', 'Biaya Penyusutan', 75, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (110, '6-120001', 'Biaya Penyusutan Peralatan Kantor', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (111, '6-120002', 'Biaya Penyusutan Peralatan Penunjang Pembelajaran', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (112, '6-120003', 'Biaya Penyusutan Peralatan Laboratorium', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (113, '6-120004', 'Biaya Penyusutan Peralatan Ruang Serbaguna', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (114, '6-120005', 'Biaya Penyusutan Peralatan Kantin', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (115, '6-120006', 'Biaya Penyusutan Mobil', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (116, '6-120007', 'Biaya Penyusutan Motor', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (117, '6-120008', 'Biaya Penyusutan Gedung', 109, 'Biaya', 'Debit', '2025-06-11 13:22:25', '2025-06-11 13:22:25', NULL);
INSERT INTO `accounts` VALUES (118, '6-13', 'Biaya Penghapusan Aset Tetap', 75, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (119, '6-130001', 'Biaya Kerugian Penjualan Aset', 118, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (120, '6-14', 'Donasi', 75, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (121, '6-140001', 'Menjenguk Siswa', 120, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (122, '6-140002', 'Menjenguk Guru', 120, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (123, '6-140003', 'Rumah Tahfidz', 120, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (124, '6-140004', 'Donasi Lain-lain', 120, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (125, '6-15', 'Biaya Lain-lain', 75, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (126, '6-150001', 'Biaya Bank', 125, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (127, '6-150001-1', 'Administrasi Bank', 126, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (128, '6-150002', 'Biaya Pajak', 125, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (129, '6-150002-1', 'Administrasi Pajak PPh', 128, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (130, '6-150002-2', 'Administrasi Pajak PPN', 128, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (131, '6-150003', 'Biaya Bos', 125, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (132, '6-150003-1', 'Penyusunan Laporan Bos', 131, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (133, '6-150004', 'Pengembalian', 125, 'Biaya', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (134, '7-1', 'Investasi', NULL, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (135, '7-11', 'Investasi', 134, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (136, '7-110001', 'Investasi Peralatan Kantor', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (137, '7-110002', 'Investasi Peralatan Penunjang Pembelajaran', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (138, '7-110003', 'Investasi Peralatan Laboratorium', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (139, '7-110004', 'Investasi Peralatan Ruang Serbaguna', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (140, '7-110005', 'Investasi Peralatan Kantin', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (141, '7-110006', 'Investasi Mobil', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (142, '7-110007', 'Investasi Motor', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (143, '7-110008', 'Investasi Gedung Utama', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);
INSERT INTO `accounts` VALUES (144, '7-110009', 'Investasi Tanah', 135, 'Investasi', 'Debit', '2025-06-11 13:22:26', '2025-06-11 13:22:26', NULL);

-- ----------------------------
-- Table structure for beginning_balances
-- ----------------------------
DROP TABLE IF EXISTS `beginning_balances`;
CREATE TABLE `beginning_balances`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `balance` decimal(15, 2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `beginning_balances_unique`(`school_id` ASC, `account_id` ASC, `month` ASC, `year` ASC) USING BTREE,
  INDEX `beginning_balances_account_id_foreign`(`account_id` ASC) USING BTREE,
  CONSTRAINT `beginning_balances_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `beginning_balances_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of beginning_balances
-- ----------------------------

-- ----------------------------
-- Table structure for depreciations
-- ----------------------------
DROP TABLE IF EXISTS `depreciations`;
CREATE TABLE `depreciations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fix_asset_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `depreciations_account_id_foreign`(`account_id` ASC) USING BTREE,
  INDEX `depreciations_fix_asset_id_account_id_index`(`fix_asset_id` ASC, `account_id` ASC) USING BTREE,
  CONSTRAINT `depreciations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `depreciations_fix_asset_id_foreign` FOREIGN KEY (`fix_asset_id`) REFERENCES `fix_assets` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of depreciations
-- ----------------------------

-- ----------------------------
-- Table structure for employee_receivable_details
-- ----------------------------
DROP TABLE IF EXISTS `employee_receivable_details`;
CREATE TABLE `employee_receivable_details`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_receivable_id` bigint UNSIGNED NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `period` date NULL DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `employee_receivable_details_employee_receivable_id_index`(`employee_receivable_id` ASC) USING BTREE,
  CONSTRAINT `employee_receivable_details_employee_receivable_id_foreign` FOREIGN KEY (`employee_receivable_id`) REFERENCES `employee_receivables` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of employee_receivable_details
-- ----------------------------

-- ----------------------------
-- Table structure for employee_receivables
-- ----------------------------
DROP TABLE IF EXISTS `employee_receivables`;
CREATE TABLE `employee_receivables`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` bigint UNSIGNED NOT NULL,
  `school_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `paid_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `due_date` date NULL DEFAULT NULL,
  `status` enum('Unpaid','Partial','Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unpaid',
  `deleted_at` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `employee_receivables_account_id_foreign`(`account_id` ASC) USING BTREE,
  INDEX `employee_receivables_employee_id_foreign`(`employee_id` ASC) USING BTREE,
  INDEX `employee_receivables_school_id_account_id_employee_id_index`(`school_id` ASC, `account_id` ASC, `employee_id` ASC) USING BTREE,
  CONSTRAINT `employee_receivables_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `employee_receivables_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `employee_receivables_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of employee_receivables
-- ----------------------------

-- ----------------------------
-- Table structure for employees
-- ----------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `employees_employee_id_number_unique`(`employee_id_number` ASC) USING BTREE,
  UNIQUE INDEX `employees_email_unique`(`email` ASC) USING BTREE,
  INDEX `employees_school_id_employee_id_number_index`(`school_id` ASC, `employee_id_number` ASC) USING BTREE,
  CONSTRAINT `employees_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of employees
-- ----------------------------

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for fix_assets
-- ----------------------------
DROP TABLE IF EXISTS `fix_assets`;
CREATE TABLE `fix_assets`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `acquisition_date` date NOT NULL,
  `acquisition_cost` decimal(15, 2) NOT NULL,
  `useful_life` int NOT NULL,
  `accumulated_depriciation` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `depreciation_percentage` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fix_assets_account_id_foreign`(`account_id` ASC) USING BTREE,
  INDEX `fix_assets_school_id_account_id_index`(`school_id` ASC, `account_id` ASC) USING BTREE,
  CONSTRAINT `fix_assets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fix_assets_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fix_assets
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2025_07_08_071354_create_student_receivable_discounts_table', 1);

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_permissions_model_id_model_type_index`(`model_id` ASC, `model_type` ASC) USING BTREE,
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles`  (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_roles_model_id_model_type_index`(`model_id` ASC, `model_type` ASC) USING BTREE,
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of model_has_roles
-- ----------------------------

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `permissions_name_guard_name_unique`(`name` ASC, `guard_name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 'view_reports', 'web', '2025-06-10 11:50:59', '2025-06-10 11:50:59');
INSERT INTO `permissions` VALUES (2, 'edit_transactions', 'web', '2025-06-10 11:50:59', '2025-06-10 11:50:59');
INSERT INTO `permissions` VALUES (3, 'manage_schools', 'web', '2025-06-10 11:50:59', '2025-06-10 11:50:59');

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token` ASC) USING BTREE,
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type` ASC, `tokenable_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`) USING BTREE,
  INDEX `role_has_permissions_role_id_foreign`(`role_id` ASC) USING BTREE,
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------
INSERT INTO `role_has_permissions` VALUES (1, 1);
INSERT INTO `role_has_permissions` VALUES (3, 1);
INSERT INTO `role_has_permissions` VALUES (1, 2);
INSERT INTO `role_has_permissions` VALUES (3, 2);
INSERT INTO `role_has_permissions` VALUES (1, 3);
INSERT INTO `role_has_permissions` VALUES (2, 3);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `roles_name_guard_name_unique`(`name` ASC, `guard_name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'SuperAdmin', 'web', '2025-06-10 11:50:58', '2025-06-10 11:50:58');
INSERT INTO `roles` VALUES (2, 'AdminMonitor', 'web', '2025-06-10 11:50:58', '2025-06-10 11:50:58');
INSERT INTO `roles` VALUES (3, 'SchoolAdmin', 'web', '2025-06-10 11:50:58', '2025-06-10 11:50:58');

-- ----------------------------
-- Table structure for schedules
-- ----------------------------
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `income_account_id` bigint UNSIGNED NOT NULL,
  `user_type` enum('Siswa','Guru','Karyawan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `amount` decimal(15, 2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `due_date` date NULL DEFAULT NULL,
  `schedule_type` enum('Bulanan','Non Bulanan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bulanan',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `schedules_account_id_foreign`(`account_id` ASC) USING BTREE,
  INDEX `schedules_income_account_id_foreign`(`income_account_id` ASC) USING BTREE,
  INDEX `schedules_school_id_account_id_income_account_id_index`(`school_id` ASC, `account_id` ASC, `income_account_id` ASC) USING BTREE,
  CONSTRAINT `schedules_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `schedules_income_account_id_foreign` FOREIGN KEY (`income_account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `schedules_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of schedules
-- ----------------------------

-- ----------------------------
-- Table structure for school_majors
-- ----------------------------
DROP TABLE IF EXISTS `school_majors`;
CREATE TABLE `school_majors`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `school_majors_school_id_index`(`school_id` ASC) USING BTREE,
  CONSTRAINT `school_majors_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of school_majors
-- ----------------------------

-- ----------------------------
-- Table structure for schools
-- ----------------------------
DROP TABLE IF EXISTS `schools`;
CREATE TABLE `schools`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `schools_email_unique`(`email` ASC) USING BTREE,
  INDEX `schools_name_index`(`name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of schools
-- ----------------------------
INSERT INTO `schools` VALUES (1, 'Sekolah A', 'jagakarsa', '2025-07-08 02:42:36', '2025-07-08 02:42:36', 'admin@sekolaha.sch.id', '021832432424', 1, NULL);

-- ----------------------------
-- Table structure for student_receivable_details
-- ----------------------------
DROP TABLE IF EXISTS `student_receivable_details`;
CREATE TABLE `student_receivable_details`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_receivable_id` bigint UNSIGNED NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `period` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `student_receivable_details_student_receivable_id_index`(`student_receivable_id` ASC) USING BTREE,
  CONSTRAINT `student_receivable_details_student_receivable_id_foreign` FOREIGN KEY (`student_receivable_id`) REFERENCES `student_receivables` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of student_receivable_details
-- ----------------------------
INSERT INTO `student_receivable_details` VALUES (10, 28, 'bayar', 500000.00, '2025-07-08', NULL, '2025-07-08 08:57:56', '2025-07-08 08:57:56');
INSERT INTO `student_receivable_details` VALUES (12, 28, 'lunasin', 9000000.00, '2025-07-08', NULL, '2025-07-08 09:01:58', '2025-07-08 09:01:58');

-- ----------------------------
-- Table structure for student_receivable_discounts
-- ----------------------------
DROP TABLE IF EXISTS `student_receivable_discounts`;
CREATE TABLE `student_receivable_discounts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_receivable_id` bigint UNSIGNED NOT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent` tinyint UNSIGNED NOT NULL,
  `nominal` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `student_receivable_discounts_student_receivable_id_foreign`(`student_receivable_id` ASC) USING BTREE,
  CONSTRAINT `student_receivable_discounts_student_receivable_id_foreign` FOREIGN KEY (`student_receivable_id`) REFERENCES `student_receivables` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of student_receivable_discounts
-- ----------------------------
INSERT INTO `student_receivable_discounts` VALUES (12, 28, 'Prestasi', 5, 500000, '2025-07-08 08:56:21', '2025-07-08 08:56:21');

-- ----------------------------
-- Table structure for student_receivables
-- ----------------------------
DROP TABLE IF EXISTS `student_receivables`;
CREATE TABLE `student_receivables`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` bigint UNSIGNED NOT NULL,
  `school_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `paid_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_discount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_payable` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `due_date` date NULL DEFAULT NULL,
  `status` enum('Unpaid','Partial','Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `period` date NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `student_receivables_account_id_foreign`(`account_id` ASC) USING BTREE,
  INDEX `student_receivables_school_id_account_id_index`(`school_id` ASC, `account_id` ASC) USING BTREE,
  INDEX `student_receivables_student_id_index`(`student_id` ASC) USING BTREE,
  CONSTRAINT `student_receivables_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `student_receivables_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `student_receivables_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of student_receivables
-- ----------------------------
INSERT INTO `student_receivables` VALUES (28, 7, 1, 10000000.00, 9500000.00, 500000.00, 9500000.00, '2025-07-24', 'Paid', '2025-07-08 08:51:28', '2025-07-08 09:01:58', 1, NULL, NULL);

-- ----------------------------
-- Table structure for students
-- ----------------------------
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `students_student_id_number_unique`(`student_id_number` ASC) USING BTREE,
  INDEX `students_school_id_student_id_number_index`(`school_id` ASC, `student_id_number` ASC) USING BTREE,
  CONSTRAINT `students_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of students
-- ----------------------------
INSERT INTO `students` VALUES (1, 1, 'alfonso', '38178238', 'I', '2025-07-08 02:43:10', '2025-07-08 02:43:10', 1, '081234902394', 'jakarta selatan', NULL);

-- ----------------------------
-- Table structure for teacher_receivable_details
-- ----------------------------
DROP TABLE IF EXISTS `teacher_receivable_details`;
CREATE TABLE `teacher_receivable_details`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_receivable_id` bigint UNSIGNED NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `period` date NULL DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `teacher_receivable_details_teacher_receivable_id_index`(`teacher_receivable_id` ASC) USING BTREE,
  CONSTRAINT `teacher_receivable_details_teacher_receivable_id_foreign` FOREIGN KEY (`teacher_receivable_id`) REFERENCES `teacher_receivables` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of teacher_receivable_details
-- ----------------------------

-- ----------------------------
-- Table structure for teacher_receivables
-- ----------------------------
DROP TABLE IF EXISTS `teacher_receivables`;
CREATE TABLE `teacher_receivables`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` bigint UNSIGNED NOT NULL,
  `school_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15, 2) NOT NULL,
  `paid_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `due_date` date NULL DEFAULT NULL,
  `status` enum('Unpaid','Partial','Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unpaid',
  `deleted_at` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `teacher_receivables_account_id_foreign`(`account_id` ASC) USING BTREE,
  INDEX `teacher_receivables_teacher_id_foreign`(`teacher_id` ASC) USING BTREE,
  INDEX `teacher_receivables_school_id_account_id_teacher_id_index`(`school_id` ASC, `account_id` ASC, `teacher_id` ASC) USING BTREE,
  CONSTRAINT `teacher_receivables_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `teacher_receivables_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `teacher_receivables_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of teacher_receivables
-- ----------------------------

-- ----------------------------
-- Table structure for teachers
-- ----------------------------
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `teacher_id_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `teachers_teacher_id_number_unique`(`teacher_id_number` ASC) USING BTREE,
  UNIQUE INDEX `teachers_email_unique`(`email` ASC) USING BTREE,
  INDEX `teachers_school_id_teacher_id_number_index`(`school_id` ASC, `teacher_id_number` ASC) USING BTREE,
  CONSTRAINT `teachers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of teachers
-- ----------------------------

-- ----------------------------
-- Table structure for transactions
-- ----------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `debit` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `reference_id` bigint NULL DEFAULT NULL,
  `reference_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  `type` enum('general','adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `transactions_account_id_foreign`(`account_id` ASC) USING BTREE,
  INDEX `transactions_school_id_account_id_date_index`(`school_id` ASC, `account_id` ASC, `date` ASC) USING BTREE,
  CONSTRAINT `transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `transactions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transactions
-- ----------------------------
INSERT INTO `transactions` VALUES (5, 1, 8, '2025-07-08', 'Piutang PPDB siswa: alfonso', 9000000.00, 0.00, 26, 'App\\Models\\StudentReceivables', '2025-07-08 08:11:57', '2025-07-08 08:51:01', NULL, '2025-07-08 08:51:01', 'general');
INSERT INTO `transactions` VALUES (6, 1, 50, '2025-07-08', 'Pendapatan siswa: alfonso', 0.00, 9000000.00, 26, 'App\\Models\\StudentReceivables', '2025-07-08 08:11:57', '2025-07-08 08:51:01', NULL, '2025-07-08 08:51:01', 'general');
INSERT INTO `transactions` VALUES (7, 1, 8, '2025-07-08', 'Piutang PPDB siswa: alfonso', 5000000.00, 0.00, 27, 'App\\Models\\StudentReceivables', '2025-07-08 08:46:42', '2025-07-08 08:50:57', NULL, '2025-07-08 08:50:57', 'general');
INSERT INTO `transactions` VALUES (8, 1, 50, '2025-07-08', 'Piutang PPDB siswa: alfonso', 0.00, 5000000.00, 27, 'App\\Models\\StudentReceivables', '2025-07-08 08:46:42', '2025-07-08 08:50:57', NULL, '2025-07-08 08:50:57', 'general');
INSERT INTO `transactions` VALUES (9, 1, 7, '2025-07-08', 'Piutang Pembayaran Siswa siswa: alfonso', 9500000.00, 0.00, 28, 'App\\Models\\StudentReceivables', '2025-07-08 08:51:28', '2025-07-08 09:01:58', NULL, NULL, 'general');
INSERT INTO `transactions` VALUES (10, 1, 50, '2025-07-08', 'Pendapatan siswa: alfonso', 0.00, 9500000.00, 28, 'App\\Models\\StudentReceivables', '2025-07-08 08:51:29', '2025-07-08 08:56:21', NULL, NULL, 'general');
INSERT INTO `transactions` VALUES (11, 1, 3, '2025-07-08', 'Pembayaran piutang: alfonso', 9500000.00, 0.00, 28, 'App\\Models\\StudentReceivables', '2025-07-08 08:57:56', '2025-07-08 09:01:58', NULL, NULL, 'general');
INSERT INTO `transactions` VALUES (12, 1, 7, '2025-07-08', 'Pembayaran piutang: alfonso', 0.00, 9500000.00, 28, 'App\\Models\\StudentReceivables', '2025-07-08 08:57:56', '2025-07-08 09:01:58', NULL, NULL, 'general');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SchoolAdmin',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `school_id` bigint UNSIGNED NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE,
  INDEX `users_email_index`(`email` ASC) USING BTREE,
  INDEX `users_school_id_index`(`school_id` ASC) USING BTREE,
  CONSTRAINT `users_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Super Admin', 'superadmin@example.com', NULL, '$2y$10$.5/6sT1bQ0KlA.1k6GqLruxNo0mGFwL0s75qylFYs3qctTzSapJa.', 'SuperAdmin', NULL, '2025-06-10 11:50:07', '2025-06-20 17:49:06', NULL, '089912341234', 1);
INSERT INTO `users` VALUES (2, 'Admin Monitor', 'admin@example.com', NULL, '$2y$10$.5/6sT1bQ0KlA.1k6GqLruxNo0mGFwL0s75qylFYs3qctTzSapJa.', 'AdminMonitor', NULL, '2025-06-10 11:50:07', '2025-06-14 06:37:33', NULL, '082118681625', 1);

SET FOREIGN_KEY_CHECKS = 1;
