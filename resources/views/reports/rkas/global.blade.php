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
    @if (auth()->user()->role != 'SchoolAdmin')
    <div class="row gx-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Row start -->
                    <form method="GET" class="mb-4">
                        <div class="row gx-3">
                            <div class="col-xl-4 col-md-6 col-12">
                                <div class="mb-3">
                                    <label for="schoolFilter" class="form-label">Filter Sekolah</label>
                                    <select name="school" class="form-select" id="schoolFilter">
                                        @foreach($schools as $s)
                                            <option value="{{ $s->id }}" {{ $school && $school->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>							
                        <div class="row gx-3">
                            <div class="col-xl-4 col-md-6 col-12">
                                <button type="submit" class="btn btn-primary">Tampilkan</button>
                                <a href="" class="btn btn-danger">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row gx-3">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">Laporan RKAS Global</h5> <br>
                        @if (!empty($rkasData))
                            @php
                                $isSchoolAdmin = auth()->user()->role == 'SchoolAdmin';
                                $routeName = $isSchoolAdmin ? 'school-reports.rkas-global' : 'reports.rkas-global';
                                $params = request()->query();
                                $params['type'] = 'pdf';
                                
                                if ($isSchoolAdmin) {
                                    $params['school'] = auth()->user()->school_id;
                                }
                                
                                $printUrl = route($routeName, $params);
                            @endphp
                            <a href="{{ $printUrl }}" 
                                target="_blank" class="btn btn-success" title="Cetak PDF">
                                <span class="d-lg-block d-none">Cetak PDF</span>
                                <span class="d-sm-block d-lg-none">
                                    <i class="bi bi-file-pdf"></i>
                                </span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($message))
                        <div class="alert alert-warning" role="alert">
                        {{ $message }}
                        </div>
                    @else
                        <h6 class="fw-bold">Ringkasan Total Keuangan</h6>
                        <br>
                        <p class="fw-bold">Periode: {{ optional($activePeriod)->name ?? 'Tidak Ada Periode Aktif' }} ({{ optional($activePeriod)->start_date ? \Carbon\Carbon::parse($activePeriod->start_date)->format('d M Y') : '' }} - {{ optional($activePeriod)->end_date ? \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') : '' }})</p>
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
                                                @if (auth()->user()->role != 'SchoolAdmin')
                                                    <a href="{{ route('reports.rkas-detail', ['school' => $school->id, 'cashManagement' => $item['cashManagementId']]) }}" class="btn btn-sm btn-info text-white">Lihat Detail</a>
                                                @else
                                                    <a href="{{ route('school-reports.rkas-detail', ['school' => auth()->user()->school_id, 'cashManagement' => $item['cashManagementId']]) }}" class="btn btn-sm btn-info text-white">Lihat Detail</a>
                                                @endif
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