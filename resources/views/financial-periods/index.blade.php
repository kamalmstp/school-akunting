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
        <li class="breadcrumb-item" aria-current="page">Kelola Periode Keuangan</li>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Daftar Periode Keuangan</h5>
                        @if(auth()->user()->role != 'AdminMonitor')
                            <div>
                                <a href="{{ route('school-financial-periods.create', $school) }}" class="btn btn-primary" title="Tambah Periode">
                                    <span class="d-lg-block d-none">Tambah Periode</span>
                                    <span class="d-sm-block d-lg-none">
                                        <i class="bi bi-plus"></i>
                                    </span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="table-responsive">
                        <table class="table align-middle" style="min-width: max-content;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Periode</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Akhir</th>
                                    <th>Status</th>
                                    @if(auth()->user()->role != 'AdminMonitor')<th>Aksi</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periods as $index => $period)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $period->name }}</td>
                                        <td>{{ $period->start_date->format('d M Y') }}</td>
                                        <td>{{ $period->end_date->format('d M Y') }}</td>
                                        <td>
                                            @if($period->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->role != 'AdminMonitor')
                                            <td>
                                                <a href="{{ route('school-financial-periods.edit', [$school, $period]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                
                                                @if(!$period->is_active)
                                                    <form action="{{ route('school-financial-periods.copy-balances', [$school, $period]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Yakin ingin menyalin saldo dari periode sebelumnya?')">Salin Saldo Awal</button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('school-financial-periods.destroy', [$school, $period]) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus periode ini?')">Hapus</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada Periode Keuangan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- App body ends -->

@endsection