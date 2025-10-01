<!DOCTYPE html>
<html>
<head>
    <title>Laporan Buku Besar - {{ $school->name }}</title>
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
            /* Memastikan tabel tidak terpotong di tengah akun */
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
        .account-header {
            margin-top: 15px;
            margin-bottom: 5px;
            padding: 5px 0;
            font-weight: bold;
            font-size: 11pt;
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding-left: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>LAPORAN BUKU BESAR</h3>
        <p><strong>{{ strtoupper($school->name) }}</strong></p>
        <p>{{ $school->address ?? 'Alamat Sekolah' }}</p>
        <hr style="border: 1px solid #000; margin: 10px 0;">
    </div>

    <div class="info-box">
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($activePeriod->start_date)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($activePeriod->end_date)->isoFormat('D MMMM Y') }}</p>
    </div>

    @php $isFirstAccount = true; @endphp
    @forelse ($ledgerData as $accountData)
        @if (!$isFirstAccount)
            {{-- Tambahkan pemisah halaman di antara setiap akun --}}
            <div class="page-break"></div> 
        @endif
        
        @php
            $isFirstAccount = false;
            // Mendapatkan saldo awal, atau 0 jika tidak ada
            $initialBalance = $accountData['initial_balance'] ?? 0;
            $runningBalance = $initialBalance;
        @endphp

        <div class="account-header">
            Nama Akun: {{ $accountData['account_name'] }} ({{ $accountData['account_code'] }})
        </div>

        <table>
            <thead>
                <tr>
                    <th width="10%">Tanggal</th>
                    <th width="35%">Keterangan</th>
                    <th width="10%">Ref Jurnal</th>
                    <th width="15%">Debet</th>
                    <th width="15%">Kredit</th>
                    <th width="15%">Saldo</th>
                </tr>
            </thead>
            <tbody>
                {{-- Saldo Awal --}}
                <tr>
                    <td class="text-center" colspan="4">SALDO AWAL ({{ \Carbon\Carbon::parse($activePeriod->start_date)->format('d/m/Y') }})</td>
                    <td class="text-center">-</td>
                    <td class="text-right">Rp {{ number_format($initialBalance, 0, ',', '.') }}</td>
                </tr>

                {{-- Transaksi --}}
                @forelse ($accountData['transactions'] as $transaction)
                    @php
                        $debit = (float)($transaction['debit'] ?? 0);
                        $credit = (float)($transaction['credit'] ?? 0);
                        // Saldo berjalan: Saldo lama + Debet - Kredit
                        $runningBalance = $runningBalance + $debit - $credit;
                    @endphp
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}</td>
                        <td>{{ $transaction['description'] }}</td>
                        <td class="text-center">{{ $transaction['journal_ref'] ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($debit, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($credit, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada transaksi yang tercatat untuk akun ini dalam periode berjalan.</td>
                    </tr>
                @endforelse

                {{-- Saldo Akhir --}}
                <tr>
                    <td colspan="5" class="text-right" style="font-weight: bold; background-color: #f9ff99;">SALDO AKHIR</td>
                    <td class="text-right" style="font-weight: bold; background-color: #f9f9f9;">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @empty
        <p class="text-center">Tidak ada data Buku Besar yang ditemukan untuk periode ini.</p>
    @endforelse

    {{-- Tanda Tangan hanya di akhir dokumen --}}
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
