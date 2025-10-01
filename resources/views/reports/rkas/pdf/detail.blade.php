@php

    $logoUrl = isset($school_data->logo) && !empty($school_data->logo) ? $school_data->logo : 'images/account3';

@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Detail RKAS - {{ $school_data->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header table {
            text-align: center;
            border: 0px;
        }
        .header h3 {
            margin: 0;
            font-size: 14pt;
        }
        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }
        .info-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .info-box p {
            margin: 0;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            page-break-inside: auto;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
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
        }
        .totals-row td {
            font-weight: bold;
            background-color: #f9f9f9;
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
                    <h3>LAPORAN KEGIATAN DAN ANGGARAN SEKOLAH (RKAS)</h3>
                    <p><strong>{{ strtoupper($school_data->name) }}</strong></p>
                    <p>{{ $school_data->address ?? 'Alamat Sekolah' }}</p>
                </td>
                <td style="border: none; width: 15%;">

                </td>
            </tr>
        </table>
        
        <hr style="border: 1px solid #000; margin: 10px 0;">
    </div>

    <div class="info-box">
        <p><strong>Nama Laporan:</strong> {{ $title }}</p>
        <p><strong>Sekolah:</strong> {{ $school_data->name }}</p>
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($activePeriod->start_date)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($activePeriod->end_date)->isoFormat('D MMMM Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" rowspan="2">No.</th>
                <th width="10%" rowspan="2">Tanggal</th>
                <th width="40%" rowspan="2">Uraian / Keterangan</th>
                <th width="25%" colspan="2">Transaksi</th>
                <th width="20%" rowspan="2">Saldo</th>
            </tr>
            <tr>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5"><strong>SALDO AWAL KAS</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($initialBalance, 0, ',', '.') }}</strong></td>
            </tr>

            @php $currentBalance = $initialBalance; @endphp
            @forelse ($transactions as $index => $item)
                @php
                    $debit = (float)($item['debit'] ?? 0);
                    $credit = (float)($item['credit'] ?? 0);
                    $currentBalance = $currentBalance + $debit - $credit;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-right">Rp {{ number_format($debit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($credit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($currentBalance, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada transaksi yang tercatat dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="3" class="text-center">JUMLAH TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($finalBalance, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-signatures">
        <table>
            <tr>
                <td>
                    <p>Menyetujui,</p>
                    <p>Ketua Majelis Dikdasmen Kota {{$school_data->city ?? 'Nama Kota'}}</p>
                    <br><br><br><br>
                    <p>( ..................................................... )</p>
                    <p>{{ $school_data->dikdasmen ?? 'Nama' }}</p>
                </td>
                <td class="text-center">
                    <p></p>
                    <p>Kepala Sekolah</p>
                    <br><br><br><br>
                    <p>( ..................................................... )</p>
                    <p>{{ $school_data->kepsek ?? 'Kepala Sekolah' }}</p>
                </td>
                <td class="text-center">
                    <p>{{ $school_data->city ?? 'Kota'}}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p>Dibuat oleh,</p>
                    <br><br><br><br>
                    <p>( ..................................................... )</p>
                    <p>{{ $school_data->bendahara ?? 'Bendahara' }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
