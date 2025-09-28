<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RKAS Global - Tampilan Cetak Resmi</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menggunakan Times New Roman untuk tampilan formal, dengan fallback */
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7f6;
        }

        .page-container {
            max-width: 210mm; /* Lebar A4 */
            min-height: 297mm; /* Tinggi A4 */
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Gaya Khusus untuk Cetak (PDF) */
        @media print {
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 10pt; /* Ukuran font standar dokumen resmi */
            }
            .page-container {
                max-width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
            /* Memastikan semua border tercetak dengan warna hitam solid */
            .table-print td, .table-print th {
                border-color: #000 !important;
            }
            /* Hilangkan margin/padding yang tidak perlu di cetakan */
            .signature-row > div {
                padding: 0 !important;
                margin: 0 !important;
            }
        }
        
        /* Gaya KOP Surat */
        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        /* Gaya tabel cetak */
        .table-print {
            border-collapse: collapse;
            width: 100%;
        }

        .table-print th, .table-print td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 10pt;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
    </style>
</head>
<body>

    <div class="page-container">

        <!-- Tombol Cetak (Hanya Muncul di Layar) -->
        <div class="no-print mb-6 text-center">
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150 shadow-md">
                Cetak ke PDF
            </button>
            <p class="text-sm text-gray-500 mt-2">Pastikan pengaturan cetak Anda menggunakan kertas A4.</p>
        </div>

        <!-- 1. KOP SURAT DAN JUDUL -->
        <header class="kop-surat">
            <div class="flex items-center">
                <!-- Placeholder Logo (Ganti dengan path/URL logo sebenarnya) -->
                <div style="width: 15%; margin-right: 15px;">
                    <img src="https://placehold.co/100x100/1e40af/ffffff?text=LOGO" alt="Logo Sekolah" style="width: 80px; height: 80px; display: block; margin: 0 auto;">
                </div>
                
                <!-- Teks KOP SURAT -->
                <div style="width: 85%; text-align: center;">
                    <p class="text-xs">MAJELIS PENDIDIKAN DASAR DAN MENENGAH</p>
                    <p class="text-xs font-bold">PIMPINAN DAERAH MUHAMMADIYAH KOTA MOJOKERTO</p>
                    <p class="text-lg font-bold uppercase">SD PLUS MUHAMMADIYAH BRAWIJAYA KOTA MOJOKERTO</p>
                    <p class="text-sm">NPSN: 20534246 NSS: 10205600042</p>
                    <p class="text-xs">Jl. Brawijaya 40. Patihan, Kranggan, Kota Mojokerto. Telp/Fax. (0321) 321746 Kode Pos 61321</p>
                </div>
            </div>
        </header>

        <!-- JUDUL LAPORAN -->
        <div class="text-center mb-6">
            <h1 class="text-base font-bold underline uppercase">RENCANA KEGIATAN DAN ANGGARAN SEKOLAH (RKAS) GLOBAL</h1>
            <p class="text-sm font-semibold uppercase">TAHUN PELAJARAN 2022/2023</p>
        </div>

        <!-- 2. TABEL GABUNGAN PENDAPATAN DAN BELANJA -->
        <div class="overflow-x-auto">
            <table class="table-print w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th colspan="4" style="background-color: #e5e5e5;">PENDAPATAN</th>
                        <th colspan="4" style="background-color: #e5e5e5;">BELANJA</th>
                    </tr>
                    <tr class="bg-gray-50">
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Uraian</th>
                        <th style="width: 25%;">Jumlah</th>
                        <th style="width: 5%; border-right: 2px solid #000;"></th> <!-- Pemisah Vertikal -->
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Uraian</th>
                        <th style="width: 25%;">Jumlah</th>
                        <th style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Bagian Utama Pemasukan dan Pengeluaran -->
                    <tr>
                        <td class="text-center font-bold">I</td>
                        <td class="font-bold">PENDAPATAN</td>
                        <td></td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td class="text-center font-bold">I</td>
                        <td class="font-bold">PENGELUARAN</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">1.1</td>
                        <td>SPP</td>
                        <td class="text-right">804.600.000</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td class="text-center">1.1</td>
                        <td>SPP</td>
                        <td class="text-right">747.506.000</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">1.2</td>
                        <td>DPS</td>
                        <td class="text-right">169.125.000</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td class="text-center">1.2</td>
                        <td>DPS</td>
                        <td class="text-right">117.600.000</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">1.3</td>
                        <td>DAPEN</td>
                        <td class="text-right">251.700.000</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td class="text-center">1.3</td>
                        <td>DAPEN</td>
                        <td class="text-right">233.600.000</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">1.4</td>
                        <td>BOS</td>
                        <td class="text-right">188.000.000</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td class="text-center">1.4</td>
                        <td>BOS</td>
                        <td class="text-right">187.998.600</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">1.5</td>
                        <td>KOPERASI</td>
                        <td class="text-right">228.321.000</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td class="text-center">1.5</td>
                        <td>KOPERASI</td>
                        <td class="text-right">203.956.400</td>
                        <td></td>
                    </tr>
                    <!-- Baris Kosong -->
                    <tr>
                        <td></td><td></td><td></td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td></td><td></td><td></td>
                        <td></td>
                    </tr>
                    <!-- Total Pendapatan / Total Pengeluaran -->
                    <tr class="bg-gray-100">
                        <td colspan="2" class="font-bold text-center">Total Pendapatan</td>
                        <td class="text-right font-bold">1.641.746.000</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td colspan="2" class="font-bold text-center">Total Pengeluaran</td>
                        <td class="text-right font-bold">1.490.661.000</td>
                        <td></td>
                    </tr>
                    <!-- Sisa Saldo (Hanya di kolom Belanja) -->
                    <tr>
                        <td colspan="3"></td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td colspan="2" class="font-bold text-center">Sisa Saldo</td>
                        <td class="text-right font-bold">151.085.000</td>
                        <td></td>
                    </tr>
                    <!-- Total Akhir (Jumlah) -->
                    <tr class="bg-gray-200">
                        <td colspan="2" class="font-bold text-center">JUMLAH</td>
                        <td class="text-right font-bold">1.641.746.000</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td colspan="2" class="font-bold text-center">JUMLAH</td>
                        <td class="text-right font-bold">1.641.746.000</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 3. BLOK TANDA TANGAN (Sama Persis seperti di detail laporan) -->
        <footer class="mt-12 text-sm">
            <div class="flex justify-between w-full signature-row">
                <!-- Kiri: Mengetahui / Menyetujui -->
                <div class="w-1/3 text-center">
                    <p class="font-semibold">Mengetahui / Menyetujui,</p>
                    <p>Ketua Majelis Dikdasmen Kota Mojokerto</p>
                    <div class="h-16"></div> <!-- Jarak untuk Tanda Tangan -->
                    <p class="font-bold underline text-base">( Nama Lengkap Ketua Majelis )</p>
                    <p class="text-xs">NIP. ............................</p>
                </div>
                
                
                <!-- Kanan: Dibuat oleh (Bendahara) -->
                <div class="w-1/3 text-center">
                    <p class="mb-4">Mojokerto, 3 Agustus 2022</p>
                    <p>Kepala Sekolah</p>
                    <div class="h-16"></div> <!-- Jarak untuk Tanda Tangan -->
                    <p class="font-bold underline text-base">( Nama Lengkap Bendahara )</p>
                    <p class="text-xs">NIP. ............................</p>
                </div>
            </div>
        </footer>

    </div>

</body>
</html>
