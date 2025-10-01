@php
    // Helper function untuk memformat Rupiah
    function formatRupiah($amount) {
        return number_format($amount, 0, ',', '.');
    }

    // Ambil data untuk sekolah yang sedang dipilih
    $schoolId = $school->id ?? 0;
    $ledgerData = $accounts->get($schoolId);
    $currentSchool = $school ?? (isset($schools) ? $schools->first() : null);

    // Tentukan URL logo (asumsi $school memiliki properti 'logo')
    $logoUrl = isset($currentSchool->logo) && !empty($currentSchool->logo) ? $currentSchool->logo : 'images/placeholder-logo.png';

    // Fungsi helper untuk memformat angka dan menambahkan keterangan D/K
    $formatBalance = function ($balance, $normalBalance) {
        $formatted = formatRupiah(abs($balance));
        if ($balance === 0) {
            return '-';
        } elseif ($balance > 0) {
            return $normalBalance === 'Debit' ? "Rp {$formatted} (D)" : "Rp {$formatted} (K)";
        } else { // $balance < 0
            return $normalBalance === 'Debit' ? "Rp {$formatted} (K)" : "Rp {$formatted} (D)";
        }
    };
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Buku Besar - {{ $currentSchool->name ?? 'Laporan Gabungan' }}</title>
    <style>
        /* Styling CSS untuk PDF */
        body {
            font-family: sans-serif;
            font-size: 10pt;
            margin: 0; /* Mengatur margin kustom di .wrapper */
        }
        .wrapper {
            margin: 0.5in; /* Margin standar untuk cetak */
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
            padding: 5px 8px;
            vertical-align: top;
            text-align: left;
            font-size: 9pt;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        /* Styling Laporan Buku Besar */
        .account-header td {
            background-color: #e6e6fa; /* Lavender */
            font-size: 10pt;
            font-weight: bold;
            text-align: left;
        }
        .opening-balance td, .closing-balance td {
            background-color: #ffe0b2; /* Light Orange/Peach */
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        .kop-table td {
            border: none;
            padding: 0;
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
<div class="wrapper">
    <!-- KOP SURAT -->
    <div class="header">
        <table class="kop-table" style="border: none;">
            <tr style="border: none;">
                <td style="width: 15%; text-align: center;">
                    <!-- Gambar Logo (pastikan path $logoUrl dapat diakses DomPDF) -->
                    <img src="{{ $logoUrl }}"
                        alt="Logo Sekolah"
                        style="width: 80px; height: 80px; display: block; margin: 0 auto;">
                </td>
                <td style="text-align: center; width: 70%;">
                    <h2>LAPORAN BUKU BESAR</h2>
                    <h3>{{ strtoupper($currentSchool->name ?? 'LAPORAN GABUNGAN') }}</h3>
                    <p>{{ $currentSchool->address ?? 'Laporan ini mencakup semua sekolah yang difilter' }}</p>
                </td>
                <td style="width: 15%;"></td>
            </tr>
        </table>
        
        <hr style="border: 1px solid #000; margin: 10px 0;">
    </div>
    
    <div style="text-align: center; margin-bottom: 10px; font-size: 10pt;">
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</p>
        @if ($singleAccount)
            <p><strong>Filter Akun:</strong> ({{ $singleAccount->code }}) - {{ $singleAccount->name }}</p>
        @elseif ($accountType)
            <p><strong>Filter Tipe Akun:</strong> {{ $accountType }}</p>
        @endif
    </div>
    <!-- END KOP SURAT -->

    @if (!$ledgerData)
        <div style="text-align: center; margin-top: 50px;">Tidak ada data Buku Besar yang ditemukan untuk periode dan filter ini.</div>
    @else
        @foreach ($ledgerData as $item)
            @php
                $account = $item['account'];
                $openingBalance = $item['opening_balance'];
                $transactions = $item['transactions'];
                $closingBalance = $item['closing_balance'];
                $currentBalance = $openingBalance;
                $normalBalance = $account->normal_balance;
            @endphp

            <table>
                <thead>
                    <tr class="account-header">
                        <td colspan="6">
                            ({{ $account->code }}) - {{ $account->name }} (Normal Balance: {{ $normalBalance }})
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 8%;">Tanggal</th>
                        <th style="width: 10%;">Nomor Bukti</th>
                        <th style="width: 35%;">Keterangan</th>
                        <th style="width: 15%;">Debit</th>
                        <th style="width: 15%;">Kredit</th>
                        <th style="width: 17%;">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal -->
                    <tr class="opening-balance">
                        <td class="text-center">{{ \Carbon\Carbon::parse($startDate)->subDay()->isoFormat('D MMM Y') }}</td>
                        <td class="text-center bold" colspan="2">SALDO AWAL</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                        <td class="text-right bold">
                            @if ($openingBalance !== 0)
                                {{ $formatBalance($openingBalance, $normalBalance) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    <!-- Detail Transaksi -->
                    @foreach ($transactions as $tData)
                        @php
                            $transaction = $tData['transaction'];

                            // Perhitungan Saldo Berjalan (mutasi)
                            $mutasi = $normalBalance === 'Debit' 
                                ? $transaction->debit - $transaction->credit
                                : $transaction->credit - $transaction->debit;
                            
                            $currentBalance += $mutasi;
                            
                            // Keterangan Tambahan untuk Piutang
                            $keteranganTambahan = '';
                            if ($tData['student_receivable']) {
                                $keteranganTambahan = " (Siswa: {$tData['student_receivable']->student->full_name})";
                            } elseif ($tData['teacher_receivable']) {
                                $keteranganTambahan = " (Guru: {$tData['teacher_receivable']->teacher->full_name})";
                            } elseif ($tData['employee_receivable']) {
                                $keteranganTambahan = " (Karyawan: {$tData['employee_receivable']->employee->full_name})";
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ \Carbon\Carbon::parse($transaction->date)->isoFormat('D MMM Y') }}</td>
                            <td class="text-center">{{ $transaction->transaction_no }}</td>
                            <td>
                                {{ $transaction->description }}{{ $keteranganTambahan }}
                            </td>
                            <td class="text-right">Rp {{ formatRupiah($transaction->debit) }}</td>
                            <td class="text-right">Rp {{ formatRupiah($transaction->credit) }}</td>
                            <td class="text-right">
                                {{ $formatBalance($currentBalance, $normalBalance) }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Saldo Akhir -->
                    <tr class="closing-balance">
                        <td class="text-center bold" colspan="3">SALDO AKHIR</td>
                        <td class="text-right" colspan="2"></td>
                        <td class="text-right bold">
                            {{ $formatBalance($closingBalance, $normalBalance) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            @if (!$loop->last)
                <!-- <div class="page-break"></div> -->
                 <br><br>
            @endif
        @endforeach
    @endif

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
                    <p>{{ $school->city ?? 'Kota' }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p>Dibuat Oleh,</p>
                    <br><br><br><br>
                    <p>({{ $school->bendahara ?? 'Nama' }})</p>
                    <p>Bendahara</p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
