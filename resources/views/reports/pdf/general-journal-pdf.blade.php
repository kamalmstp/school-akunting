@php
    $isGlobal = $transactionsBySchool->count() > 1;
    function formatRupiah($amount) {
        return number_format($amount, 0, ',', '.');
    }
    $logoUrl = isset($school->logo) && !empty($school->logo) ? $school->logo : 'images/account3';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Jurnal Umum - {{ $school->name ?? 'Laporan Global' }}</title>
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
        .sub-header {
            font-weight: bold;
            font-size: 11pt;
            background-color: #e6e6e6;
            padding: 5px;
            text-align: left;
        }
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
    </style>
</head>
<body>

    <div class="header">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 15%;">
                    <img src="{{ $logoUrl }}"
                        alt="Logo Sekolah"
                        style="width: 80px; height: 80px; display: block; margin: 0 auto;">
                </td>
                <td style="text-align: center; border: none; width: 70%;">
                    <h2>LAPORAN JURNAL UMUM</h2>
                    <h3>{{ strtoupper($school->name ?? 'LAPORAN GABUNGAN') }}</h3>
                    <p>{{ $school->address ?? 'Laporan ini mencakup semua sekolah yang difilter' }}</p>
                </td>
                <td style="border: none; width: 15%;"></td>
            </tr>
        </table>
        
        <hr style="border: 1px solid #000; margin: 10px 0;">
    </div>
    
    <div style="text-align: center; margin-bottom: 10px; font-size: 10pt;">
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</p>
    </div>

    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 20%;">Ref/Kode Akun</th>
                <th style="width: 35%;">Uraian / Akun</th>
                <th style="width: 15%;">Pemasukan</th>
                <th style="width: 15%;">Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowNumber = 1;
                $currentSchoolId = null;
            @endphp

            @forelse ($transactionsBySchool as $schoolId => $transactions)
                @php
                    $currentSchool = $transactions->first()->school;
                @endphp
                @php $transactionIndex = 1; @endphp
                @foreach ($transactions as $transaction)
                    
                    <tr>
                        <td class="text-center">{{ $rowNumber++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($transaction->date)->isoFormat('D/MM/Y') }}</td>
                        <td class="text-center">{{ $transaction->account->code.' - '.$transaction->account->name ?? 'N/A' }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td class="text-right">
                            @if($transaction->debit == 0)
                                -
                            @else
                                Rp {{ formatRupiah($transaction->debit) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if($transaction->credit == 0)
                                -
                            @else
                                Rp {{ formatRupiah($transaction->credit) }}
                            @endif
                        </td>
                    </tr>
                    @php $transactionIndex++; @endphp
                @endforeach
            @empty
                <tr>
                    <td colspan="{{ $isGlobal ? '7' : '6' }}" class="text-center">Tidak ada transaksi Jurnal Umum ditemukan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{ $isGlobal ? '5' : '4' }}" class="text-right text-bold">TOTAL KESELURUHAN</td>
                <td class="text-right text-bold">Rp {{ formatRupiah($totalDebit ?? 0) }}</td>
                <td class="text-right text-bold">Rp {{ formatRupiah($totalCredit ?? 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-signatures">
        <table>
            <tr>
                <td class="text-center">
                    <p></p>
                    <p>Mengetahui,</p>
                    <br><br><br><br>
                    <p>({{ $school->kepsek ?? 'Nama' }})</p>
                    <p>Kepala Sekolah</p>
                </td>
                <td class="text-center">
                    <p>{{ $school->city ?? 'Mojokerto' }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p>Dibuat Oleh,</p>
                    <br><br><br><br>
                    <p>({{ $school->bendahara ?? 'Nama' }})</p>
                    <p>Bendahara</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>