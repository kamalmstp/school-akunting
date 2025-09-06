<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi</title>
    <style>
        @page {
            margin: 20px;
            size: A5 landscape;
        }
        body {
            font-family: sans-serif;
            font-size: 13px;
            margin: 20px;
        }
        .header-table, .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo {
            width: 90px;
        }
        .section {
            margin-top: 15px;
        }
        .label {
            width: 170px;
            vertical-align: top;
        }
        .amount-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .amount-table td {
            border: none;
            padding: 6px;
        }
        .bold {
            font-weight: bold;
        }
        .big {
            font-size: 20px;
        }
        .text-right {
            text-align: right;
        }
        hr.thin {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0;
        }
        .striped {
            background: url('images/strip-bg.png');
            background-repeat: repeat-x;
            background-size: contain;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <table>
                    <tr>
                        <td>
                            <img src="{{ public_path($company['logo']) }}" class="logo" alt="Logo">
                        </td>
                        <td style="padding-left: 10px;">
                            <div class="bold">{{ $company['name'] }}</div>
                            <div>Telp: {{ $company['telp'] }}</div>
                            <div>Email: {{ $company['email'] }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="text-align: right;">
                <div class="bold" style="font-size: 16px;">KWITANSI</div>
                <div>Invoice No. {{ $invoice_no }}</div>
            </td>
        </tr>
    </table>
    <hr class="thin">

    <!-- CONTENT -->
    <div class="section">
        <table class="content-table">
            <tr>
                <td class="label">Telah Menerima Dari</td>
                <td>: {{ $from }}<hr class="thin"></td>
            </tr>
            <tr>
                <td class="label">Banyaknya Uang</td>
                <td class="striped" style="background-repeat: repeat-x;">: {{ $amount_words }}<hr class="thin"></td>
            </tr>
            <tr>
                <td class="label">Untuk Pembayaran</td>
                <td>
                    @foreach($details as $d)
                        - {{ $d->description }} (Rp {{ number_format($d->amount, 2, ',', '.') }})<br>
                    @endforeach
                    <hr class="thin">
                </td>
            </tr>
        </table>
    </div>
    <hr class="thin">

    <!-- AMOUNT -->
    <table class="amount-table">
        <tr>
            <td class="bold" style="width: 150px;">Jumlah Rp</td>
            <td class="bold big">
                <div class="striped">
                    {{ number_format($amount, 2, ',', '.') }}
                </div>
            </td>
            <td class="text-right" style="width: 150px;">{{ $date }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td class="text-right">
                <div style="text-align: center; margin-top: 20px;">
                    <img src="{{ public_path($qrCode) }}" style="width: 50%" alt="">
                </div>
            </td>
        </tr>
    </table>
</body>
</html>