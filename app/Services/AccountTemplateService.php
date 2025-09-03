<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountTemplateService
{
    public static function createDefaultAccountsForSchool(int $schoolId): void
    {
        // Atur ID akun sementara secara incremental
        $idMap = []; // code → id (sementara)
        $nextId = 1;

        // Data akun dari accounts.sq
        $accounts = [
            // Aset Lancar
            ['code' => '1-1', 'name' => 'Aset Lancar', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => null],
            ['code' => '1-11', 'name' => 'Kas Setara Kas', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-1'],
            ['code' => '1-110001', 'name' => 'Kas Tangan', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-11'],
            ['code' => '1-110002', 'name' => 'Kas Bank', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-11'],
            ['code' => '1-110002-1', 'name' => 'Kas Bank Tabungan', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-110002'],
            ['code' => '1-12', 'name' => 'Piutang', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-1'],
            ['code' => '1-120001', 'name' => 'Piutang Pembayaran Siswa', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-12'],
            ['code' => '1-120001-1', 'name' => 'Piutang PPDB', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-120001'],
            ['code' => '1-120001-2', 'name' => 'Piutang DPP', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-120001'],
            ['code' => '1-120001-3', 'name' => 'Piutang SPP', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-120001'],
            ['code' => '1-120001-4', 'name' => 'Piutang UKS', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-120001'],
            ['code' => '1-120002', 'name' => 'Piutang Internal', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-12'],
            ['code' => '1-120003', 'name' => 'Piutang Eksternal', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-12'],
            ['code' => '1-13', 'name' => 'Bangunan Dalam Proses', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-1'],
            ['code' => '1-130001', 'name' => 'Bangunan Dalam Proses', 'type' => 'Aset Lancar', 'normal' => 'Debit', 'parent_code' => '1-13'],

            // Aset Tetap
            ['code' => '1-2', 'name' => 'Aset Tetap', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => null],
            ['code' => '1-21', 'name' => 'Peralatan', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-2'],
            ['code' => '1-210001', 'name' => 'Peralatan Kantor', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-21'],
            ['code' => '1-210002', 'name' => 'Peralatan Penunjang Pembelajaran', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-21'],
            ['code' => '1-210003', 'name' => 'Peralatan Laboratorium', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-21'],
            ['code' => '1-210004', 'name' => 'Peralatan Ruang Serbaguna', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-21'],
            ['code' => '1-210005', 'name' => 'Peralatan Kantin', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-21'],
            ['code' => '1-22', 'name' => 'Kendaraan', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-2'],
            ['code' => '1-220001', 'name' => 'Mobil', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-22'],
            ['code' => '1-220002', 'name' => 'Motor', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-22'],
            ['code' => '1-23', 'name' => 'Gedung', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-2'],
            ['code' => '1-230001', 'name' => 'Gedung Utama', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-23'],
            ['code' => '1-24', 'name' => 'Tanah', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-2'],
            ['code' => '1-240001', 'name' => 'Tanah', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-24'],
            ['code' => '1-25', 'name' => 'Akumulasi Penyusutan Peralatan', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-2'],
            ['code' => '1-250001', 'name' => 'Akumulasi Penyusutan Peralatan Kantor', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],
            ['code' => '1-250002', 'name' => 'Akumulasi Penyusutan Peralatan Penunjang Pembelajaran', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],
            ['code' => '1-250003', 'name' => 'Akumulasi Penyusutan Peralatan Laboratorium', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],
            ['code' => '1-250004', 'name' => 'Akumulasi Penyusutan Peralatan Ruang Serbaguna', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],
            ['code' => '1-250005', 'name' => 'Akumulasi Penyusutan Peralatan Kantin', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],
            ['code' => '1-250006', 'name' => 'Akumulasi Penyusutan Peralatan Mobil', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],
            ['code' => '1-250007', 'name' => 'Akumulasi Penyusutan Peralatan Motor', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],
            ['code' => '1-250008', 'name' => 'Akumulasi Penyusutan Peralatan Gedung', 'type' => 'Aset Tetap', 'normal' => 'Debit', 'parent_code' => '1-25'],

            // Kewajiban
            ['code' => '2-1', 'name' => 'Kewajiban', 'type' => 'Kewajiban', 'normal' => 'Kredit', 'parent_code' => null],
            ['code' => '2-11', 'name' => 'Kewajiban Jangka Pendek', 'type' => 'Kewajiban', 'normal' => 'Kredit', 'parent_code' => '2-1'],
            ['code' => '2-110001', 'name' => 'Kewajiban Internal', 'type' => 'Kewajiban', 'normal' => 'Kredit', 'parent_code' => '2-11'],
            ['code' => '2-110002', 'name' => 'Kewajiban Eksternal', 'type' => 'Kewajiban', 'normal' => 'Kredit', 'parent_code' => '2-11'],
            ['code' => '2-12', 'name' => 'Kewajiban Jangka Panjang', 'type' => 'Kewajiban', 'normal' => 'Kredit', 'parent_code' => '2-1'],
            ['code' => '2-120001', 'name' => 'Kewajiban Internal', 'type' => 'Kewajiban', 'normal' => 'Kredit', 'parent_code' => '2-12'],
            ['code' => '2-120002', 'name' => 'Kewajiban Eksternal', 'type' => 'Kewajiban', 'normal' => 'Kredit', 'parent_code' => '2-12'],

            // Aset Neto
            ['code' => '3-1', 'name' => 'Aset Neto', 'type' => 'Aset Neto', 'normal' => 'Kredit', 'parent_code' => null],
            ['code' => '3-11', 'name' => 'Aset Neto', 'type' => 'Aset Neto', 'normal' => 'Kredit', 'parent_code' => '3-1'],
            ['code' => '3-110001', 'name' => 'Aset Neto Tanpa Pembatas', 'type' => 'Aset Neto', 'normal' => 'Kredit', 'parent_code' => '3-11'],
            ['code' => '3-110002', 'name' => 'Aset Neto Dengan Pembatas', 'type' => 'Aset Neto', 'normal' => 'Kredit', 'parent_code' => '3-11'],

            // Pendapatan
            ['code' => '4-1', 'name' => 'Pendapatan', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => null],
            ['code' => '4-11', 'name' => 'Pendapatan Pembayaran Siswa', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-1'],
            ['code' => '4-110001', 'name' => 'Pendapatan PPDB', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-11'],
            ['code' => '4-110002', 'name' => 'Pendapatan DPP', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-11'],
            ['code' => '4-110003', 'name' => 'Pendapatan SPP', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-11'],
            ['code' => '4-110004', 'name' => 'Pendapatan UKS', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-11'],
            ['code' => '4-12', 'name' => 'Pendapatan Internal', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-1'],
            ['code' => '4-120001', 'name' => 'Pendapatan Sewa Kantin', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-12'],
            ['code' => '4-120002', 'name' => 'UIS (Uang Infaq Siswa)', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-12'],
            ['code' => '4-120003', 'name' => 'UIG (Uang Infaq Guru) dan UIK (Uang Infaq Karyawan)', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-12'],
            ['code' => '4-120004', 'name' => 'Kantin', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-12'],
            ['code' => '4-120005', 'name' => 'Koperasi Seragam', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-12'],
            ['code' => '4-120006', 'name' => 'Koperasi Buku', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-12'],
            ['code' => '4-13', 'name' => 'Pendapatan Eksternal', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-1'],
            ['code' => '4-130001', 'name' => 'Pendapatan Bantuan Pemerintah', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-13'],
            ['code' => '4-130001-1', 'name' => 'Bosnas', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-130001'],
            ['code' => '4-130001-2', 'name' => 'Bosko', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-130001'],
            ['code' => '4-130002', 'name' => 'Pendapatan Bantuan Swasta', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-13'],
            ['code' => '4-14', 'name' => 'Pendapatan Penghapusan Aset Tetap', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-1'],
            ['code' => '4-140001', 'name' => 'Pendapatan Penghapusan Aset Tetap', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-14'],
            ['code' => '4-15', 'name' => 'Pendapatan Lain-lain', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-1'],
            ['code' => '4-150001', 'name' => 'Pendapatan Acara Pameran/Kegiatan', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-15'],
            ['code' => '4-150002', 'name' => 'Pendapatan Rabat Penjualan', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-15'],
            ['code' => '4-150003', 'name' => 'Infaq Jumat', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-15'],
            ['code' => '4-150004', 'name' => 'Ekskul', 'type' => 'Pendapatan', 'normal' => 'Kredit', 'parent_code' => '4-15'],

            // Biaya
            ['code' => '6-1', 'name' => 'Biaya', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => null],
            ['code' => '6-11', 'name' => 'Biaya Standart Nasional Pendidikan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-1'],
            ['code' => '6-110001', 'name' => 'Biaya Standart Proses', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-11'],
            ['code' => '6-110001-1', 'name' => 'Administrasi Kelas', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110001'],
            ['code' => '6-110001-2', 'name' => 'Buku Penunjang Pembelajaran dan UKS', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110001'],
            ['code' => '6-110001-3', 'name' => 'Kelas Terapi', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110001'],
            ['code' => '6-110002', 'name' => 'Biaya Standart Kompetensi Kelulusan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-11'],
            ['code' => '6-110002-1', 'name' => 'Olimpiade dan Kompetisi', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110002-2', 'name' => 'Peringatan Hari Besar', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110002-3', 'name' => 'Aktivitas Luar Sekolah', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110002-4', 'name' => 'Kegiatan Ekstrakurikuler', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110002-5', 'name' => 'Kegiatan Intrakurikuler', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110002-6', 'name' => 'Pengembangan Karakter Siswa', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110002-7', 'name' => 'Acara Kemuhammadiyahan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110002-8', 'name' => 'Lain-Lain', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110002'],
            ['code' => '6-110003', 'name' => 'Biaya Standart Sarana dan Prasarana', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-11'],
            ['code' => '6-110003-1', 'name' => 'Operasional Sarana Kebersihan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110003'],
            ['code' => '6-110003-2', 'name' => 'Operasional Sarana Kelistrikan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110003'],
            ['code' => '6-110003-3', 'name' => 'Pembelian Alat Tulis Kantor', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110003'],
            ['code' => '6-110003-4', 'name' => 'Pakan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110003'],
            ['code' => '6-110003-5', 'name' => 'Operasional Servis dan Sewa', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110003'],
            ['code' => '6-110003-6', 'name' => 'Operasional Lain-lain', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110003'],
            ['code' => '6-110004', 'name' => 'Biaya Standart Pembiayaan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-11'],
            ['code' => '6-110004-1', 'name' => 'Biaya Cetak, Fotokopi, dan Scan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110004'],
            ['code' => '6-110004-2', 'name' => 'Biaya Maisyah Guru dan Karyawan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110004'],
            ['code' => '6-110004-3', 'name' => 'Biaya Konsumsi Pegawai dan Tamu', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110004'],
            ['code' => '6-110004-4', 'name' => 'Biaya Pelatihan Pegawai', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110004'],
            ['code' => '6-110004-5', 'name' => 'Biaya Listrik dan Komunikasi', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110004'],
            ['code' => '6-110005', 'name' => 'Biaya Standart Penilaian', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-11'],
            ['code' => '6-110005-1', 'name' => 'Biaya PTS', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110005'],
            ['code' => '6-110005-2', 'name' => 'Biaya PAS', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110005'],
            ['code' => '6-110005-3', 'name' => 'Biaya KDK', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110005'],
            ['code' => '6-110005-4 ', 'name' => 'Biaya Munaqosah', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110005'],
            ['code' => '6-110005-5', 'name' => 'Biaya AKM', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-110005'],
            ['code' => '6-12', 'name' => 'Biaya Penyusutan', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-1'],
            ['code' => '6-120001', 'name' => 'Biaya Penyusutan Peralatan Kantor', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-120002', 'name' => 'Biaya Penyusutan Peralatan Penunjang Pembelajaran', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-120003', 'name' => 'Biaya Penyusutan Peralatan Laboratorium', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-120004', 'name' => 'Biaya Penyusutan Peralatan Ruang Serbaguna', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-120005', 'name' => 'Biaya Penyusutan Peralatan Kantin', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-120006', 'name' => 'Biaya Penyusutan Mobil', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-120007', 'name' => 'Biaya Penyusutan Motor', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-120008', 'name' => 'Biaya Penyusutan Gedung', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-12'],
            ['code' => '6-13', 'name' => 'Biaya Penghapusan Aset Tetap', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-1'],
            ['code' => '6-130001', 'name' => 'Biaya Kerugian Penjualan Aset', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-13'],
            ['code' => '6-14', 'name' => 'Donasi', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-1'],
            ['code' => '6-140001', 'name' => 'Menjenguk Siswa', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-14'],
            ['code' => '6-140002', 'name' => 'Menjenguk Guru', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-14'],
            ['code' => '6-140003', 'name' => 'Rumah Tahfidz', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-14'],
            ['code' => '6-140004', 'name' => 'Donasi Lain-lain', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-14'],
            ['code' => '6-15', 'name' => 'Biaya Lain-lain', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-1'],
            ['code' => '6-150001', 'name' => 'Biaya Bank', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-15'],
            ['code' => '6-150001-1', 'name' => 'Administrasi Bank', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-150001'],
            ['code' => '6-150002', 'name' => 'Biaya Pajak', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-15'],
            ['code' => '6-150002-1', 'name' => 'Administrasi Pajak PPh', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-150002'],
            ['code' => '6-150002-2', 'name' => 'Administrasi Pajak PPN', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-150002'],
            ['code' => '6-150003', 'name' => 'Biaya Bos', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-15'],
            ['code' => '6-150003-1', 'name' => 'Penyusunan Laporan Bos', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-150003'],
            ['code' => '6-150004', 'name' => 'Pengembalian', 'type' => 'Biaya', 'normal' => 'Debit', 'parent_code' => '6-15'],

            // Investasi
            ['code' => '7-1', 'name' => 'Investasi', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => null],
            ['code' => '7-11', 'name' => 'Investasi', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-1'],
            ['code' => '7-110001', 'name' => 'Investasi Peralatan Kantor', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110002', 'name' => 'Investasi Peralatan Penunjang Pembelajaran', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110003', 'name' => 'Investasi Peralatan Laboratorium', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110004', 'name' => 'Investasi Peralatan Ruang Serbaguna', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110005', 'name' => 'Investasi Peralatan Kantin', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110006', 'name' => 'Investasi Mobil', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110007', 'name' => 'Investasi Motor', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110008', 'name' => 'Investasi Gedung Utama', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11'],
            ['code' => '7-110009', 'name' => 'Investasi Tanah', 'type' => 'Investasi', 'normal' => 'Debit', 'parent_code' => '7-11']
        ];

        DB::beginTransaction();
        try {
            $created = [];

            foreach ($accounts as $acc) {
                $parentId = null;
                if ($acc['parent_code'] && isset($created[$acc['parent_code']])) {
                    $parentId = $created[$acc['parent_code']];
                }

                $account = Account::create([
                    'code' => $acc['code'],
                    'name' => $acc['name'],
                    'account_type' => $acc['type'],
                    'normal_balance' => $acc['normal'],
                    'school_id' => $schoolId,
                    'parent_id' => $parentId,
                ]);

                $created[$acc['code']] = $account->id;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw new \RuntimeException('Gagal membuat akun default: ' . $e->getMessage());
        }
    }
}
