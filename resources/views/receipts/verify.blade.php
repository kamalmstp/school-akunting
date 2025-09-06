<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnVgT+nF/PzXk/gS5tUq5fPzGk/Pz6vPz8tPzI" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" xintegrity="sha512-Fo3rlrNjH/n1x2w4yE5R0n10D+A/Q7A+z+E5R5h6/yR/8S+jP2W/E5R2D5R+aFfA5R2P5R2+j/z5R2D5R2P5R2/R/E5R2D5R2P5R2/R" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .card-qr-verification {
            max-width: 550px;
            width: 90%;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0c5cb1 100%);
            color: white;
            padding: 2.5rem 1.5rem;
            text-align: center;
        }
        .card-header-custom h4 {
            font-weight: 600;
        }
        .status-success {
            color: #198754;
        }
        .status-fail {
            color: #dc3545;
        }
        .icon-lg {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        .btn-custom {
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }
        .btn-custom:hover {
            background-color: #0c5cb1;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }
        .data-label {
            font-weight: 600;
            color: #495057;
        }
        .data-value {
            color: #212529;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

    <div class="card card-qr-verification">
        <div class="card-header-custom">
            <h4 class="mb-0">Verifikasi Data QR Code</h4>
        </div>
        <div class="card-body p-4">
            {{-- Menggunakan sintaksis if/else dari Blade atau sejenisnya --}}
            @if($status === 'success')
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle icon-lg status-success"></i>
                    <h5 class="status-success">Kwitansi Terverifikasi</h5>
                    <p class="text-muted">Data valid dan terdaftar di sistem.</p>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12 col-md-6 text-md-end">
                                <p class="mb-0 data-label">Nomor Invoice:</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-start">
                                <p class="mb-0 data-value">{{ $receipt->invoice_no }}</p>
                            </div>
                             <div class="col-12 col-md-6 text-md-end">
                                <p class="mb-0 data-label">Nama Siswa:</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-start">
                                <p class="mb-0 data-value">{{ $receipt->student->name }}</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-end">
                                <p class="mb-0 data-label">Sekolah:</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-start">
                                <p class="mb-0 data-value">{{ $receipt->school->name }}</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-end">
                                <p class="mb-0 data-label">Tanggal:</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-start">
                                <p class="mb-0 data-value">{{ \Carbon\Carbon::parse($receipt->created_at)->format('d M Y') }}</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-end">
                                <p class="mb-0 data-label">Jumlah:</p>
                            </div>
                            <div class="col-12 col-md-6 text-md-start">
                                <p class="mb-0 data-value">Rp {{ number_format($receipt->total_amount, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-times-circle icon-lg status-fail"></i>
                    <h5 class="status-fail">Verifikasi Gagal</h5>
                    <p class="text-muted">{{ $message }}</p>
                </div>
                <div class="alert alert-danger text-center mb-0" role="alert">
                    Data tidak valid atau tidak ditemukan dalam sistem.
                </div>
            @endif
        </div>
        <!-- <div class="card-footer bg-white border-0 text-center p-3">
            <a href="#" class="btn btn-custom w-75">Kembali ke Halaman Utama</a>
        </div> -->
    </div>

</body>
</html>
