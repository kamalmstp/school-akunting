@php
$rkasData = $rkasData ?? [
// Data yang diambil dari PDF Anda
['name' => 'SPP', 'income' => 47867500, 'expense' => 0],
['name' => 'DPS', 'income' => 0, 'expense' => 0],
['name' => 'DAPEN', 'income' => 0, 'expense' => 0],
['name' => 'BOSNAS', 'income' => 0, 'expense' => 0],
['name' => 'BOSDA', 'income' => 0, 'expense' => 0],
['name' => 'KOPERASI', 'income' => 0, 'expense' => 0],
];

$school = $school ?? (object)['name' => 'SD PLUS MUHAMMADIYAH BRAWIJAYA', 'address' => 'ALAMAT SEKOLAH', 'npsn' => '00000000', 'nss' => '00000000', 'logo' => 'path/ke/logo/sekolah.png']; 
$activePeriod = $activePeriod ?? (object)['name' => 'Tahun Ajaran 2025/2026', 'start_date' => \Carbon\Carbon::createFromDate(2025, 7, 1)];

$totalIncome = 47867500;
$totalExpense = 0;
$balance = 47867500;

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

// Fungsi format Rupiah dengan simbol 'Rp. '
function formatRupiah($amount, $withSymbol = true) {
    $formatted = number_format($amount, 0, ',', '.');
    return $withSymbol ? 'Rp. ' . $formatted : $formatted;
}

// VARIABEL LOGO: Menggunakan logika public_path() sesuai solusi Anda.
$logoPath = public_path($school->logo);

@endphp

<!DOCTYPE html>

<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RKAS Global - Cetak PDF</title>
<style>
/* PDF/Print focused styles */
body {
font-family: 'Times New Roman', Times, serif;
margin: 0;
padding: 30px;
background-color: white;
font-size: 10pt;
}

/* Enforce print-specific rules */
@media print {
body {
margin: 0;
padding: 0;
font-size: 10pt;
-webkit-print-color-adjust: exact !important;
print-color-adjust: exact !important;
}
.no-print {
display: none !important;
}
}

/* Gaya KOP Surat (Lebih Rapat & Rata Tengah Vertikal) /
.kop-surat {
border-bottom: 3px solid #000;
padding-bottom: 5px; / Dibuat lebih rapat /
margin-bottom: 10px; / Dibuat lebih rapat */
}

.kop-surat .flex {
align-items: center; /* Memastikan logo dan teks rata tengah vertikal */
}

.kop-surat p {
margin: 0;
line-height: 1.1; /* Dibuat lebih rapat */
}

/* Gaya tabel cetak */
.table-print {
border-collapse: collapse;
width: 100%;
}

.table-print th, .table-print td {
border: 1px solid #000;
padding: 6px 8px; /* Padding lebih besar untuk kerapian */
font-size: 10pt;
vertical-align: top;
}

/* Utility classes */
.text-right { text-align: right; }
.text-center { text-align: center; }

.font-bold { font-weight: bold; }
.text-xs { font-size: 0.75rem; }
.text-lg { font-size: 1.125rem; }
.text-base { font-size: 1rem; }
.uppercase { text-transform: uppercase; }
.flex { display: flex; }
.items-center { align-items: center; }
.w-full { width: 100%; }
/* Utility class untuk 1/3 lebar (untuk 3 penandatangan) /
.w-1/3-print { width: 33.3333%; }
/ MENGURANGI JARAK DARI TABEL KE FOOTER: dari 3rem menjadi 1.5rem (mt-6) /
.mt-6 { margin-top: 1.5rem; }
.justify-between { justify-content: space-between; }
/ Tinggi tanda tangan disesuaikan, dan margin bawah dikurangi agar lebih rapat /
.h-16 { height: 4rem; }
.underline { text-decoration: underline; }
.mb-2 { margin-bottom: 0.5rem; } / Mengurangi margin bawah di footer */
.mt-4 { margin-top: 1rem; }

/* Custom colors for table headers */
.bg-header { background-color: #e5e5e5; }
.bg-total { background-color: #f3f3f3; }

/* Specific style for the separation column */
.separator-col {
width: 0%;
border-right: 2px solid #000;
padding: 0;
}

</style>

</head>
<body>

<header class="kop-surat">
<div class="flex items-center">
{{-- MENGURANGI JARAK HORIZONTAL DENGAN PENGURANGAN margin-right dari 15px menjadi 10px --}}
<div style="width: 15%; margin-right: 10px;">
{{-- LOGO DINAMIS --}}
<img src="{{ $logoPath }}"
alt="Logo Sekolah"
style="width: 80px; height: 80px; display: block; margin: 0 auto;">
</div>
<div style="width: 85%; text-align: center;">
<p class="text-xs font-bold">LAPORAN KEGIATAN DAN ANGGARAN SEKOLAH</p>
<p class="text-lg font-bold uppercase">{{ $school->name ?? 'NAMA SEKOLAH' }}</p>
<p class="text-sm">{{ $activePeriod->name ?? 'TAHUN PELAJARAN' }}</p>
</div>
</div>
</header>

<div>
<table class="table-print w-full">
<thead>
<tr class="bg-header">
<th colspan="3" style="width: 50%;">PENDAPATAN</th>
<th class="separator-col"></th>
<th colspan="3" style="width: 50%;">BELANJA</th>
</tr>
<tr class="bg-total">
<th style="width: 5%;">No</th>
<th style="width: 30%;">Uraian</th>
<th style="width: 15%;">Jumlah</th>
<th class="separator-col"></th>
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
<td class="separator-col"></td>
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
<td class="separator-col"></td>
<td class="text-center">{{ isset($dataItem['name']) ? $nomor : '' }}</td>
<td>{{ $dataItem['name'] ?? '' }}</td>
<td class="text-right">{{ isset($dataItem['expense']) ? formatRupiah($dataItem['expense']) : '' }}</td>
</tr>
@endfor
<tr class="bg-total">
<td colspan="2" class="font-bold text-center">Total Pendapatan</td>
<td class="text-right font-bold">{{ formatRupiah($totalIncome) }}</td>
<td class="separator-col"></td>
<td colspan="2" class="font-bold text-center">Total Pengeluaran</td>
<td class="text-right font-bold">{{ formatRupiah($totalExpense) }}</td>
</tr>
<tr>
<td colspan="3"></td>
<td class="separator-col"></td>
<td colspan="2" class="font-bold text-center">Sisa Saldo</td>
<td class="text-right font-bold">{{ formatRupiah($balance) }}</td>
</tr>
<tr class="bg-header">
<td colspan="2" class="font-bold text-center">JUMLAH</td>
<td class="text-right font-bold">{{ formatRupiah($totalIncome) }}</td>
<td class="separator-col"></td>
<td colspan="2" class="font-bold text-center">JUMLAH</td>
<td class="text-right font-bold">{{ formatRupiah($totalBelanjaAkhir) }}</td>
</tr>
</tbody>
</table>
</div>

{{-- MENGGANTI mt-12 (3rem) MENJADI mt-6 (1.5rem) AGAR FOOTER LEBIH NAIK --}}

<footer class="mt-6 text-sm">
{{-- Menggunakan lebar 1/3-print untuk setiap kolom agar sejajar tiga kolom --}}
<div class="flex justify-between w-full signature-row">

    {{-- KOLOM 1: Ketua Majelis --}}
    <div class="w-1/3-print text-center">
        <p class="font-semibold mb-2">Menyetujui,</p>
        <p class="mb-2">Ketua Majelis Dikdasmen Kota {{ $signerData['city'] ?? 'Mojokerto' }}</p>
        <div class="h-16"></div>
        <p class="font-bold underline text-base mt-4">{{ $signerData['ketuaMajelisName'] ?? 'Nama Ketua Majelis' }}</p>
        <p class="text-xs">NIP. {{ $signerData['ketuaMajelisNip'] ?? '1234567890' }}</p>
    </div>

    {{-- KOLOM 2: Kepala Sekolah --}}
    <div class="w-1/3-print text-center">
        {{-- Baris Tanggal --}}
        <p class="mb-2">{{ $signerData['city'] ?? 'Mojokerto' }}, {{ $tanggalLaporan }}</p>
        <p class="mb-2">Kepala Sekolah</p>
        <div class="h-16"></div>
        <p class="font-bold underline text-base mt-4">{{ $signerData['kepalaSekolahName'] ?? 'Nama Kepala Sekolah' }}</p>
        <p class="text-xs">NIP. {{ $signerData['kepalaSekolahNip'] ?? '1234567890' }}</p>
    </div>
    
    {{-- KOLOM 3: Bendahara Sekolah --}}
    <div class="w-1/3-print text-center">
        <p class="font-semibold mb-2">Dibuat Oleh,</p>
        <p class="mb-2">Bendahara Sekolah</p>
        <div class="h-16"></div>
        <p class="font-bold underline text-base mt-4">{{ $signerData['bendaharaName'] ?? 'Nama Bendahara' }}</p>
        <p class="text-xs">NIP. {{ $signerData['bendaharaNip'] ?? '1234567890' }}</p>
    </div>

</div>

</footer>

</body>

</html>