# School Accounting App

Requirements :

1. PHP ^8.1
2. Laravel ^10.0
3. Bootstrap 5.3

ALTER TABLE `schools` CHANGE `bendahara` `bendahara` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `schools` ADD `city` VARCHAR(20) NULL DEFAULT NULL AFTER `name`;

Revisi :

* Laporan
[x] Untuk Cetak PDF, dibuat agar preview sebelum dicetak
[v] Tanggal di bagian Signature dibuat otomatis dengan format Bulan Indonesia
[v] Nama Kepala Sekolah, Nama Ketua Majelis Dikdasmen, dan Bendahara di set masing masing sekolah

* Profile
[v] CRUD utk Kota, Nama Kepsek, Nama Ketua Majelis Dikdasmen, dan Bendahara masing masing sekolah

* Kwitansi
[x] Untuk Tampilan siswa UI diperbagus dengan datatables
[x] untuk filter tanggal masing masing siswa untuk pencetakan

* Data Master
[v] Menu Kelola siswa fitur hapus dihide
[x] Menu Kelola Dana (AdminMonitor)
[x] Menu Kelola Periode (AdminMonitor)
