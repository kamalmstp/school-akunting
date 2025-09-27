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
<li class="breadcrumb-item active" aria-current="page">Laporan RKAS</li>
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
<h5 class="card-title">Laporan RKAS Global</h5>
<p class="card-text">Periode: {{ optional($activePeriod)->name ?? 'Tidak Ada Periode Aktif' }} ({{ optional($activePeriod)->start_date ? \Carbon\Carbon::parse($activePeriod->start_date)->format('d M Y') : '' }} - {{ optional($activePeriod)->end_date ? \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') : '' }})</p>
</div>
<div class="card-body">
@if(isset($message))
<div class="alert alert-warning" role="alert">
{{ $message }}
</div>
@else
<h6 class="fw-bold">Ringkasan Total Keuangan</h6>
<hr>
<table class="table table-sm">
<tbody>
<tr>
<td>Total Pendapatan</td>
<td class="text-end fw-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
</tr>
<tr>
<td>Total Pengeluaran</td>
<td class="text-end fw-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
</tr>
<tr class="table-primary">
<td>Saldo Akhir</td>
<td class="text-end fw-bold">Rp {{ number_format($balance, 0, ',', '.') }}</td>
</tr>
</tbody>
</table>

                    <div class="row mt-4">
                        <h6 class="fw-bold">Rincian Per Sumber Dana</h6>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sumber Dana</th>
                                        <th class="text-end">Saldo Awal</th>
                                        <th class="text-end">Pendapatan</th>
                                        <th class="text-end">Pengeluaran</th>
                                        <th class="text-end">Saldo Akhir</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rkasData as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['name'] }}</td>
                                            <td class="text-end">Rp {{ number_format($item['initial_balance'], 0, ',', '.') }}</td>
                                            <td class="text-end text-success">Rp {{ number_format($item['income'], 0, ',', '.') }}</td>
                                            <td class="text-end text-danger">Rp {{ number_format($item['expense'], 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($item['balance'], 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('school-.rkas.detail', ['school' => auth()->user()->school_id, 'cashManagement' => $item['cashManagementId']]) }}" class="btn btn-sm btn-info text-white">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data RKAS yang ditemukan untuk periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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