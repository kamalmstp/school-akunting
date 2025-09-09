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
        <li class="breadcrumb-item">
            <a href="{{ route('school-financial-periods.index', $school) }}" class="text-decoration-none">Kelola Periode Keuangan</a>
        </li>
        <li class="breadcrumb-item" aria-current="page">Daftar Saldo Awal</li>
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
                        <h5 class="card-title">Daftar Saldo Awal Periode {{ $financialPeriod->name }}</h5>
                        @if(auth()->user()->role != 'AdminMonitor')
                            <div>
                                <a href="{{ route('school-initial-balances.edit', [$school, $financialPeriod]) }}" class="btn btn-primary" title="Tambah/Edit Saldo Awal">
                                    <span class="d-lg-block d-none">Tambah/Edit Saldo</span>
                                    <span class="d-sm-block d-lg-none">
                                        <i class="bi bi-pencil-square"></i>
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
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Saldo Awal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $index => $account)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $account->code }}</td>
                                        <td>{{ $account->name }}</td>
                                        <td>
                                            Rp {{ number_format($account->initialBalances->first() ? $account->initialBalances->first()->amount : 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada Saldo Awal yang dimasukkan.</td>
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