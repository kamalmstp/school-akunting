@php
    $signerData = $signerData ?? [
        'ketuaMajelisName' => 'Nama Ketua Majelis', 'ketuaMajelisNip' => '1234567890',
        'kepalaSekolahName' => 'Nama Kepala Sekolah', 'kepalaSekolahNip' => '1234567890',
        'city' => 'Mojokerto'
    ];

    $tanggalLaporan = \Carbon\Carbon::now()->isoFormat('D MMMM YYYY');
    $maxItems = count($rkasData);
    $rowsToDisplay = max($maxItems, 6);
    $totalBelanjaAkhir = $totalIncome;

    function formatRupiah($amount, $withSymbol = true) {
        $formatted = number_format($amount, 0, ',', '.');
        return $withSymbol ? 'Rp. ' . $formatted : $formatted;
    }
    $logoUrl = isset($school->logo) && !empty($school->logo) ? $school->logo : 'images/account3';

@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RKAS Global - Cetak PDF</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 5px;
            background-color: white;
            font-size: 10pt;
        }

        .report-container {
            width: 100%;
            min-height: auto;
            background-color: white;
            padding: 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 10pt;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        .kop-surat-table {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .kop-surat-table td {
            padding: 0;
            vertical-align: middle;
        }

        .kop-surat-table .logo-col {
            width: 15%;
            padding-right: 3px;
        }

        .kop-surat-table .text-col {
            width: 85%;
            text-align: center;
        }

        .kop-surat-table p {
            margin: 0;
            line-height: 1.1;
        }

        .table-print {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1.5rem;
        }

        .table-print th, .table-print td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10pt;
            vertical-align: top;
        }

        .bg-header { background-color: #e5e5e5; }
        .bg-total { background-color: #f3f3f3; }

        .separator-col {
            width: 0%;
            border-right: 2px solid #000;
            padding: 0;
        }

        .signature-table {
            width: 100%;
            margin-top: 2rem;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            padding: 0 10px;
            vertical-align: top;
        }

        .signature-space {
            height: 4rem;
        }

        .signature-label {
            margin: 0;
            line-height: 1.2;
            font-weight: normal;
        }

        .signature-name {
            margin-top: 1rem;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 0;
        }
        .no-margin {
            margin: 0;
            line-height: 1.2;
        }

    </style>
</head>
<body>

    <div class="report-container">
        <table class="kop-surat-table">
            <tr>
                <td class="logo-col">
                    <img src="{{ $logoUrl }}"
                        alt="Logo Sekolah"
                        style="width: 80px; height: 80px; display: block; margin: 0 auto;">
                </td>
                <td class="text-col">
                    <p style="font-size: 0.8rem; font-weight: bold;">LAPORAN KEGIATAN DAN ANGGARAN SEKOLAH</p>
                    <p style="font-size: 1.2rem; font-weight: bold; text-transform: uppercase;">{{ $school->name ?? 'NAMA SEKOLAH' }}</p>
                    <p style="font-size: 1rem;">{{ $activePeriod->name ?? 'TAHUN PELAJARAN' }}</p>
                </td>
            </tr>
        </table>


        <div>
            <table class="table-print">
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
                        <td style="text-align: center; font-weight: bold;">I</td>
                        <td style="font-weight: bold;">PENDAPATAN</td>
                        <td></td>
                        <td class="separator-col"></td>
                        <td style="text-align: center; font-weight: bold;">I</td>
                        <td style="font-weight: bold;">PENGELUARAN</td>
                        <td></td>
                    </tr>

                    @for ($i = 0; $i < $rowsToDisplay; $i++)
                        @php
                            $dataItem = $rkasData[$i] ?? [];
                            $nomor = '1.' . ($i + 1);
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ isset($dataItem['name']) ? $nomor : '' }}</td>
                            <td>{{ $dataItem['name'] ?? '' }}</td>
                            <td style="text-align: right;">{{ isset($dataItem['income']) ? formatRupiah($dataItem['income']) : '' }}</td>
                            <td class="separator-col"></td>
                            <td style="text-align: center;">{{ isset($dataItem['name']) ? $nomor : '' }}</td>
                            <td>{{ $dataItem['name'] ?? '' }}</td>
                            <td style="text-align: right;">{{ isset($dataItem['expense']) ? formatRupiah($dataItem['expense']) : '' }}</td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="bg-total">
                        <td colspan="2" style="font-weight: bold; text-align: center;">Total Pendapatan</td>
                        <td style="text-align: right; font-weight: bold;">{{ formatRupiah($totalIncome) }}</td>
                        <td class="separator-col"></td>
                        <td colspan="2" style="font-weight: bold; text-align: center;">Total Pengeluaran</td>
                        <td style="text-align: right; font-weight: bold;">{{ formatRupiah($totalExpense) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td class="separator-col"></td>
                        <td colspan="2" style="font-weight: bold; text-align: center;">Sisa Saldo</td>
                        <td style="text-align: right; font-weight: bold;">{{ formatRupiah($balance) }}</td>
                    </tr>
                    <tr class="bg-header">
                        <td colspan="2" style="font-weight: bold; text-align: center;">JUMLAH</td>
                        <td style="text-align: right; font-weight: bold;">{{ formatRupiah($totalIncome) }}</td>
                        <td class="separator-col"></td>
                        <td colspan="2" style="font-weight: bold; text-align: center;">JUMLAH</td>
                        <td style="text-align: right; font-weight: bold;">{{ formatRupiah($totalBelanjaAkhir) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <p class="signature-label">Menyetujui,</p>
                    <p class="signature-label no-margin">Ketua Majelis Dikdasmen Kota {{ $signerData['city'] ?? 'Mojokerto' }}</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $signerData['ketuaMajelisName'] ?? 'Nama Ketua Majelis' }}</p>
                    <p class="no-margin" style="font-size: 0.75rem;">NIP. {{ $signerData['ketuaMajelisNip'] ?? '1234567890' }}</p>
                </td>
                <td>
                    <p class="signature-label no-margin">{{ $signerData['city'] ?? 'Mojokerto' }}, {{ $tanggalLaporan }}</p>
                    <p class="signature-label no-margin">Kepala Sekolah</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $signerData['kepalaSekolahName'] ?? 'Nama Kepala Sekolah' }}</p>
                    <p class="no-margin" style="font-size: 0.75rem;">NIP. {{ $signerData['kepalaSekolahNip'] ?? '1234567890' }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
