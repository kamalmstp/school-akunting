@php
    function formatRupiah($amount) {
        return number_format($amount, 0, ',', '.');
    }

    $item = $balanceSheet->first(); 
    $profitLossItem = $profitLoss->first(); 
    $currentSchool = $school ?? $item['school'] ?? null;
    $currentSchoolName = $currentSchool->name ?? 'NAMA SEKOLAH';
    
    $assets = collect()
        ->merge($item['currentAssets'] ?? collect())
        ->merge($item['fixAssets'] ?? collect())
        ->merge($item['investments'] ?? collect());
        
    $liabilities = $item['liabilities'] ?? collect();
    $equity = $item['equity'] ?? collect();

    $totalAssets = $assets->sum('balance');
    $totalLiabilities = $liabilities->sum('balance');
    $totalEquity = $equity->sum('balance');
    
    $revenues = $profitLossItem['revenues'] ?? collect();
    $expenses = $profitLossItem['expenses'] ?? collect();

    $totalRevenue = $revenues->sum('amount');
    $totalExpense = $expenses->sum('amount');

    $logoPath = isset($currentSchool->logo) && !empty($currentSchool->logo) ? $currentSchool->logo : 'images/account3';

    if (strpos($logoPath, 'http') === false && function_exists('public_path')) {
        $logoUrl = public_path($logoPath);
    } else {
        $logoUrl = $logoPath;
    }

    $netIncome = $totalRevenue - $totalExpense; 
    $netIncomeText = $netIncome >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Keuangan - {{ $currentSchoolName }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            margin: 0;
        }
        h2, h3 {
            margin: 0;
            padding: 0;
            text-align: center;
        }
        h2 {
            font-size: 14pt;
            margin-bottom: 5px;
        }
        h3 {
            font-size: 12pt;
            margin-bottom: 5px;
        }
        p {
            margin: 0 0 4px 0;
            text-align: center;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: auto;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px; 
            vertical-align: top;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .footer-signatures {
            margin-top: 50px;
            width: 100%;
        }
        .footer-signatures table {
            width: 100%;
            border: none;
        }
        .footer-signatures td {
            border: none;
            width: 50%;
            padding: 10px;
            vertical-align: top;
            text-align: center;
        }
        /* Custom styles Laporan Keuangan (dipertahankan) */
        .page-break { page-break-after: always; }
        .content { margin: 0 10px; } 

        .section-title { 
            margin-top: 25px; 
            margin-bottom: 10px; 
            border-bottom: 3px solid #000; 
            padding-bottom: 5px; 
            font-size: 14pt; 
            color: #000; 
            text-align: left;
        }
        .summary-row td { 
            background-color: #e8f5e9; 
            font-weight: bold; 
            border-top: 2px solid #000; 
            padding: 5px;
        }
        .total-final td { 
            background-color: #c8e6c9; 
            font-weight: bold; 
            border: 2px solid #000; 
            font-size: 11pt; 
            padding: 5px;
        }
    </style>
</head>
<body>

<div class="content">

    <div class="header" style="margin-bottom: 10px;">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 15%;">
                    <img src="{{ $logoUrl }}"
                        alt="Logo Sekolah"
                        style="width: 80px; height: 80px; display: block; margin: 0 auto;">
                </td>
                <td style="text-align: center; border: none; width: 70%;">
                    <h2>LAPORAN KEUANGAN</h2>
                    <h3>{{ strtoupper($currentSchoolName) }}</h3>
                    <p>Periode: 
                        @if(isset($startDate) && isset($endDate) && $startDate && $endDate)
                            {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}
                        @else
                            Sampai Tanggal: {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') }}
                        @endif
                    </p>
                </td>
                <td style="border: none; width: 15%;"></td>
            </tr>
        </table>
        <hr style="border: 1px solid #000; margin: 10px 0;">
    </div>

    <div class="header">
        <h2 style="font-size: 14pt; margin-bottom: 5px;">LAPORAN LABA RUGI (PROFIT & LOSS)</h2>
    </div>
    
    <h3 class="section-title">Pendapatan (Revenue)</h3>
    <table>
        <tbody>
            @forelse ($revenues as $revenue)
                <tr>
                    <td>{{ $revenue['account']->name }}</td>
                    <td class="text-right">Rp {{ formatRupiah($revenue['amount']) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Tidak ada data Pendapatan.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="summary-row">
                <td>TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ formatRupiah($totalRevenue) }}</td>
            </tr>
        </tfoot>
    </table>

    <h3 class="section-title">Beban (Expense)</h3>
    <table>
        <tbody>
            @forelse ($expenses as $expense)
                <tr>
                    <td>{{ $expense['account']->name }}</td>
                    <td class="text-right">Rp {{ formatRupiah($expense['amount']) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Tidak ada data Beban.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="summary-row">
                <td>TOTAL BEBAN</td>
                <td class="text-right">Rp {{ formatRupiah($totalExpense) }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="margin-top: 15px;">
        <thead>
            <tr class="total-final">
                <td style="width: 75%;">{{ $netIncomeText }}</td>
                <td class="text-right">Rp {{ formatRupiah(abs($netIncome)) }}</td>
            </tr>
        </thead>
    </table>
    
    <div class="page-break"></div> 

    <div class="header">
        <h2 style="font-size: 14pt; margin-bottom: 5px;">LAPORAN NERACA (BALANCE SHEET)</h2>
        <p>Per Tanggal: {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') }}</p>
        <hr style="border: 1px solid #000; margin: 10px 0;">
    </div>
    
    <table style="border: none; margin-bottom: 0px;">
        <tr style="border: none;">
            <td style="border: none; width: 50%; padding-right: 15px;">
                <h3 class="section-title" style="margin-top: 0px;">A. ASET (ASSETS)</h3>
                <table>
                    <tbody>
                        @forelse ($assets as $asset)
                            <tr>
                                <td>{{ $asset['account']->name }}</td>
                                <td class="text-right">Rp {{ formatRupiah($asset['balance']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">Tidak ada data Aset.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="summary-row">
                            <td>TOTAL ASET</td>
                            <td class="text-right">Rp {{ formatRupiah($totalAssets) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>

            <td style="border: none; width: 50%; padding-left: 15px;">
                <h3 class="section-title" style="margin-top: 0px;">B. KEWAJIBAN (LIABILITIES)</h3>
                <table>
                    <tbody>
                        @forelse ($liabilities as $liability)
                            <tr>
                                <td>{{ $liability['account']->name }}</td>
                            <td class="text-right">Rp {{ formatRupiah($liability['balance']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">Tidak ada data Kewajiban.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="summary-row">
                            <td>TOTAL KEWAJIBAN</td>
                            <td class="text-right">Rp {{ formatRupiah($totalLiabilities) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <h3 class="section-title">C. EKUITAS (EQUITY)</h3>
                <table>
                    <tbody>
                        @forelse ($equity as $equityItem)
                            <tr>
                                <td>{{ $equityItem['account']->name }}</td>
                                <td class="text-right">Rp {{ formatRupiah($equityItem['balance']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">Tidak ada data Ekuitas.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="summary-row">
                            <td>TOTAL EKUITAS (Termasuk {{ $netIncomeText }})</td>
                            <td class="text-right">Rp {{ formatRupiah($totalEquity + $netIncome) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>

    <div style="clear: both; margin-top: 30px;">
        <table style="width: 100%;">
            <thead>
                <tr class="total-final">
                    <td style="width: 50%;">TOTAL ASET</td>
                    <td class="text-right" style="width: 50%;">Rp {{ formatRupiah($totalAssets) }}</td>
                </tr>
                <tr class="total-final">
                    <td>TOTAL KEWAJIBAN & EKUITAS</td>
                    <td class="text-right">Rp {{ formatRupiah($totalLiabilities + $totalEquity + $netIncome) }}</td>
                </tr>
            </thead>
        </table>
        
        <p style="margin-top: 15px; font-size: 9pt; color: #555; text-align: left;">
            <span class="bold">Catatan:</span> Nilai Laba Bersih (atau Rugi Bersih) dari Laporan Laba Rugi sudah otomatis ditambahkan ke dalam bagian Ekuitas untuk penyeimbangan Neraca.
        </p>
    </div>
    
    @php
        $signatureSchool = $currentSchool;
    @endphp
    <div class="footer-signatures" style="margin-top: 50px;">
        <table style="border: none;">
            <tr style="border: none;">
                <td class="text-center" style="width: 50%;">
                    <p style="margin-bottom: 20px;"></p>
                    <p style="margin-bottom: 5px;">Mengetahui,</p>
                    <br><br><br><br>
                    <p style="text-decoration: underline; margin-bottom: 5px;">({{ $signatureSchool->kepsek ?? 'Nama Kepala Sekolah' }})</p>
                    <p style="font-size: 9pt;">Kepala Sekolah</p>
                </td>
                <td class="text-center" style="width: 50%;">
                    <p style="margin-bottom: 5px;">{{ $signatureSchool->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
                    <p style="margin-bottom: 5px;">Dibuat Oleh,</p>
                    <br><br><br><br>
                    <p style="text-decoration: underline; margin-bottom: 5px;">({{ $signatureSchool->bendahara ?? 'Nama Bendahara' }})</p>
                    <p style="font-size: 9pt;">Bendahara</p>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
