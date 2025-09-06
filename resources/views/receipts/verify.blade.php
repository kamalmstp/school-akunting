@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">

            @if($status === 'error')
                <div class="alert alert-danger text-center">
                    {{ $message }}
                </div>
            @else
                <div class="card shadow">
                    <div class="card-header text-center bg-success text-white">
                        <h4>Kwitansi Terverifikasi ✅</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Nomor Invoice:</strong> {{ $receipt->invoice_no }}</p>
                        <p><strong>Nama Siswa:</strong> {{ $receipt->student->name }}</p>
                        <p><strong>Sekolah:</strong> {{ $receipt->school->name }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($receipt->date)->format('d M Y') }}</p>
                        <p><strong>Jumlah:</strong> Rp {{ number_format($receipt->amount, 2, ',', '.') }}</p>
                        <hr>
                        <p class="text-muted text-center">
                            Kwitansi ini valid dan terdaftar di sistem.
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection