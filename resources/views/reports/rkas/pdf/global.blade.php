@php
    $rkasData = $rkasData ?? [];
    $school = $school ?? (object)['name' => 'NAMA SEKOLAH', 'address' => 'ALAMAT SEKOLAH', 'npsn' => '00000000', 'nss' => '00000000'];
    $activePeriod = $activePeriod ?? (object)['name' => 'TAHUN PELAJARAN YYYY/YYYY', 'start_date' => \Carbon\Carbon::now()];
    $totalIncome = $totalIncome ?? 0;
    $totalExpense = $totalExpense ?? 0;
    $balance = $balance ?? 0;

    $signerData = $signerData ?? [
        'ketuaMajelisName' => 'Nama Ketua Majelis', 'ketuaMajelisNip' => '1234567890',
        'kepalaSekolahName' => 'Nama Kepala Sekolah', 'kepalaSekolahNip' => '1234567890',
        'bendaharaName' => 'Nama Bendahara', 'bendaharaNip' => '1234567890',
        'city' => 'Mojokerto'
    ];

    $tanggalLaporan = optional($activePeriod->start_date)->isoFormat('D MMMM YYYY') ?? \Carbon\Carbon::now()->isoFormat('D MMMM YYYY');
    $maxItems = count($rkasData);
    $rowsToDisplay = max($maxItems, 6);
    $totalBelanjaAkhir = $totalIncome;

    function formatRupiah($amount) {
        return number_format($amount, 0, ',', '.');
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RKAS Global - Cetak</title>
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
        .text-xs { font-size: 0.75rem; }
    </style>
</head>
<body>

    <div class="page-container">
        <div class="no-print mb-6 text-center">
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150 shadow-md">
                Cetak ke PDF
            </button>
            <p class="text-sm text-gray-500 mt-2">Pastikan pengaturan cetak Anda menggunakan kertas A4.</p>
        </div>

        <header class="kop-surat">
            <div class="flex items-center">
                <!-- Placeholder Logo (Ganti dengan path/URL logo sebenarnya) -->
                <div style="width: 15%; margin-right: 15px;">
                    <!-- Gambar Logo Sekolah -->
                    <img src="https://placehold.co/100x100/1e40af/ffffff?text=LOGO" alt="Logo Sekolah" style="width: 80px; height: 80px; display: block; margin: 0 auto;">
                </div>

                <div style="width: 85%; text-align: center;">
                    <p class="text-xs font-bold">LAPORAN KEGIATAN DAN ANGGARAN SEKOLAH</p>
                    <p class="text-lg font-bold uppercase">{{ $school->name ?? 'NAMA SEKOLAH' }}</p>
                    <p class="text-sm">{{ $activePeriod->name ?? 'TAHUN PELAJARAN' }}</p>
                </div>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="table-print w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th colspan="3" style="width: 50%; background-color: #e5e5e5;">PENDAPATAN</th>
                        <th style="width: 0%; border-right: 2px solid #000;"></th>
                        <th colspan="3" style="width: 50%; background-color: #e5e5e5;">BELANJA</th>
                    </tr>
                    <tr class="bg-gray-50">
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Uraian</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 0%; border-right: 2px solid #000;"></th>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Uraian</th>
                        <th style="width: 15%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center font-bold">I</td>
                        <td class="font-bold">PENDAPATAN</td>
                        <td></td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td class="text-center font-bold">I</td>
                        <td class="font-bold">PENGELUARAN</td>
                        <td></td>
                    </tr>
                    @for ($i = 0; $i < $rowsToDisplay; $i++)
                        @php
                            $dataItem = $rkasData[$i] ?? [];
                            $nomor = '1.' . ($i + 1);
                        @endphp
                        <tr>
                            <td class="text-center">{{ isset($dataItem['name']) ? $nomor : '' }}</td>
                            <td>{{ $dataItem['name'] ?? '' }}</td>
                            <td class="text-right">{{ isset($dataItem['income']) ? formatRupiah($dataItem['income']) : '' }}</td>
                            <td style="border-right: 2px solid #000;"></td>
                            <td class="text-center">{{ isset($dataItem['name']) ? $nomor : '' }}</td>
                            <td>{{ $dataItem['name'] ?? '' }}</td>
                            <td class="text-right">{{ isset($dataItem['expense']) ? formatRupiah($dataItem['expense']) : '' }}</td>
                        </tr>
                    @endfor
                    <tr class="bg-gray-100">
                        <td colspan="2" class="font-bold text-center">Total Pendapatan</td>
                        <td class="text-right font-bold">{{ formatRupiah($totalIncome) }}</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td colspan="2" class="font-bold text-center">Total Pengeluaran</td>
                        <td class="text-right font-bold">{{ formatRupiah($totalExpense) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td colspan="2" class="font-bold text-center">Sisa Saldo</td>
                        <td class="text-right font-bold">{{ formatRupiah($balance) }}</td>
                    </tr>
                    <tr class="bg-gray-200">
                        <td colspan="2" class="font-bold text-center">JUMLAH</td>
                        <td class="text-right font-bold">{{ formatRupiah($totalIncome) }}</td>
                        <td style="border-right: 2px solid #000;"></td>
                        <td colspan="2" class="font-bold text-center">JUMLAH</td>
                        {{-- Total Belanja akhir = Total Pendapatan sesuai format RKAS --}}
                        <td class="text-right font-bold">{{ formatRupiah($totalBelanjaAkhir) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer class="mt-12 text-sm">
            <div class="flex justify-between w-full signature-row">
                <div class="w-1/3 text-center">
                    <p class="font-semibold">Menyetujui,</p>
                    <p>Ketua Majelis Dikdasmen Kota Mojokerto</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-base"></p>
                    <p class="text-xs"></p>
                </div>

                <div class="w-1/3 text-center">
                    <p class="mb-4">Mojokerto, {{ $tanggalLaporan }}</p>
                    <p>Kepala Sekolah</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-base"></p>
                    <p class="text-xs"></p>
                </div>
            </div>
        </footer>

    </div>

</body>
</html>
