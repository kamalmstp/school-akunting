@extends('layouts.app')

@section('content')

<!-- App hero header starts -->

<div class="app-hero-header d-flex align-items-start">
<!-- Breadcrumb start -->
<ol class="breadcrumb">
<li class="breadcrumb-item">
<i class="bi bi-pie-chart lh-1"></i>
<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
</li>
<li class="breadcrumb-item" aria-current="page">
<a href="{{ route('rkas.global', $school->id) }}" class="text-decoration-none">Laporan RKAS Global</a>
</li>
<li class="breadcrumb-item active" aria-current="page">{{ $source }}</li>
</ol>
<!-- Breadcrumb end -->
</div>
<!-- App Hero header ends -->

<!-- App body starts -->

<div class="app-body">
<div class="row gx-3">
<div class="col-xxl-12">
<div class="card">
<div class="card-header">
<h5 class="card-title">Laporan RKAS - Dana {{ $source }}</h5>
{{-- $startDate dan $endDate dikirim dari controller detail method --}}
<p class="card-text">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
</div>
<div class="card-body">
@if(isset($message))
<div class="alert alert-warning" role="alert">
{{ $message }}
</div>
@else
<div class="row">
<div class="col-md-12">
<h6 class="fw-bold">Ringkasan Keuangan</h6>
<hr>
<table class="table table-sm">
<tbody>
{{-- Data diambil dari array reportData['initial_balance'] --}}
<tr>
<td>Saldo Awal</td>
<td class="text-end fw-bold">Rp {{ number_format(optional($reportData)['initial_balance'], 0, ',', '.') }}</td>
</tr>
{{-- Data diambil dari array reportData['income'] --}}
<tr>
<td>Total Pendapatan</td>
<td class="text-end fw-bold text-success">Rp {{ number_format(optional($reportData)['income'], 0, ',', '.') }}</td>
</tr>
{{-- Data diambil dari array reportData['expense'] --}}
<tr>
<td>Total Pengeluaran</td>
<td class="text-end fw-bold text-danger">Rp {{ number_format(optional($reportData)['expense'], 0, ',', '.') }}</td>
</tr>
{{-- Data diambil dari array reportData['balance'] --}}
<tr class="table-primary">
<td>Saldo Akhir</td>
<td class="text-end fw-bold">Rp {{ number_format(optional($reportData)['balance'], 0, ',', '.') }}</td>
</tr>
</tbody>
</table>
</div>
</div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold">Rincian Kegiatan</h6>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Uraian Kegiatan</th>
                                            <th class="text-center">Jenis</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Loop melalui rincian transaksi (items) yang dikirim oleh controller --}}
                                        @forelse($reportData['items'] as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item['description'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $item['type'] == 'Pendapatan' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $item['type'] }}
                                                    </span>
                                                </td>
                                                <td class="text-end">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada rincian kegiatan untuk periode ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Row end -->

</div>
<!-- App body ends -->

@endsection