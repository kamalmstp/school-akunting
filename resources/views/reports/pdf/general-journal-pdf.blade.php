@php

    $logoUrl = isset($school_data->logo) && !empty($school_data->logo) ? $school_data->logo : 'images/account3';

@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Jurnal Umum - {{ $school->name }}</title>
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
            vertical-align: top;
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
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($activePeriod->start_date)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($activePeriod->end_date)->isoFormat('D MMMM Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="35%">Keterangan (Uraian)</th>
                <th width="10%">Ref/Akun ID</th>
                <th width="35%" colspan="2">Akun</th>
                <th width="15%">Debet</th>
                <th width="15%">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php $currentDate = null; @endphp
            @forelse ($journalEntries as $entry)
                <tr>
                    {{-- Hanya tampilkan tanggal pada baris pertama transaksi --}}
                    <td class="text-center">
                        @if ($currentDate !== $entry->date)
                            {{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}
                            @php $currentDate = $entry->date; @endphp
                        @endif
                    </td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-center">{{ $entry->account_code ?? 'REF' }}</td> {{-- Asumsi ada account_code --}}

                    @if ($entry->debit > 0)
                        {{-- Baris Debet --}}
                        <td colspan="2">{{ $entry->account_name }}</td> {{-- Asumsi ada account_name --}}
                        <td class="text-right">Rp {{ number_format($entry->debit, 0, ',', '.') }}</td>
                        <td class="text-right">Rp 0</td>
                    @else
                        {{-- Baris Kredit (Biasanya diberi indentasi) --}}
                        <td style="padding-left: 20px;" colspan="2">{{ $entry->account_name }}</td>
                        <td class="text-right">Rp 0</td>
                        <td class="text-right">Rp {{ number_format($entry->credit, 0, ',', '.') }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi Jurnal Umum yang tercatat dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="5" class="text-center">JUMLAH TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-signatures">
        <table>
            <tr>
                <td class="text-center">
                    <p>{{ $school->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p>Mengetahui,</p>
                    <br><br><br><br>
                    <p>( ..................................................... )</p>
                    <p>Kepala Sekolah</p>
                </td>
                <td class="text-center">
                    <p></p>
                    <p>Dibuat oleh,</p>
                    <br><br><br><br>
                    <p>( ..................................................... )</p>
                    <p>Bendahara</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
