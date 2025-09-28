@extends('layouts.app')

@section('content')
    {{-- Memuat DataTables dan CSS Select2 untuk konsistensi --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" xintegrity="sha512-nMNlpuaDPr03RxBLj0DFl0rAEy/J6czQh+n7Gg1L0wNn7N5f4C1E1/p6jP7W7+E8JgWf7n7F9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- App hero header starts -->
    <div class="app-hero-header d-flex align-items-start">
    <!-- Breadcrumb start -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('school-rkas.global', ['school' => $school->id]) }}" class="text-decoration-none">Laporan RKAS</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Detail Kas</li>
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
                        <h5 class="card-title">{{ $title }}</h5>
                        <p class="card-text">
                            Periode: {{ optional($activePeriod)->name ?? 'Tidak Ada Periode Aktif' }} 
                            ({{ optional($activePeriod)->start_date ? \Carbon\Carbon::parse($activePeriod->start_date)->format('d M Y') : '' }} - 
                            {{ optional($activePeriod)->end_date ? \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') : '' }})
                        </p>
                    </div>
                    <div class="card-body">
                        
                        <!-- Ringkasan Saldo -->
                        <div class="row mb-4 gx-3">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card p-3 bg-light border-start border-3 border-info shadow-sm">
                                    <small class="text-muted">Saldo Awal</small>
                                    <h5 class="mb-0 fw-bold">Rp {{ number_format($initialBalance, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card p-3 bg-light border-start border-3 border-success shadow-sm">
                                    <small class="text-muted">Total Pemasukan</small>
                                    <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($totalDebit, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card p-3 bg-light border-start border-3 border-danger shadow-sm">
                                    <small class="text-muted">Total Pengeluaran</small>
                                    <h5 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalCredit, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card p-3 bg-light border-start border-3 border-primary shadow-sm">
                                    <small class="text-muted">Saldo Akhir</small>
                                    <h5 class="mb-0 fw-bold text-primary">Rp {{ number_format($finalBalance, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Detail Transaksi -->
                        <div class="table-responsive">
                            @php
                                $runningBalance = $initialBalance;
                                $i = 1;
                            @endphp
                            <table id="cashDetailTable" class="table table-bordered table-striped align-middle m-0 w-100">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="text-center">No</th>
                                        <th>Tanggal</th>
                                        <th>Uraian / Deskripsi</th>
                                        <th class="text-end">Pemasukan</th>
                                        <th class="text-end">Pengeluaran</th>
                                        <th class="text-end">Saldo Berjalan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-secondary">
                                        <td class="text-center">-</td>
                                        <td>-</td>
                                        <td class="fw-bold">SALDO AWAL</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end fw-bold text-info">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                                    </tr>
                                    @forelse ($transactions as $item)
                                        @php
                                            $runningBalance += $item['debit'] - $item['credit'];
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $i++ }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}</td>
                                            <td>{{ $item['description'] }}</td>
                                            <td class="text-end text-success" data-sort="{{ $item['debit'] }}">Rp {{ number_format($item['debit'], 0, ',', '.') }}</td>
                                            <td class="text-end text-danger" data-sort="{{ $item['credit'] }}">Rp {{ number_format($item['credit'], 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold" data-sort="{{ $runningBalance }}">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada transaksi tercatat untuk sumber kas ini dalam periode aktif.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <td colspan="3" class="text-end fw-bold ">TOTAL</td>
                                        <td class="text-end fw-bold ">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold ">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold ">Rp {{ number_format($finalBalance, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('school-rkas.global', ['school' => $school->id]) }}" class="btn btn-secondary">
                            &larr; Kembali ke Ringkasan Global
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- App body ends -->

@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js" xintegrity="sha512-H9YQ81rwKth0zWvF/P4Jp8Bv+7k7fP4MvO6z6xWzP5p75B1d5x0M2F8j0M+0qLg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            $('#cashDetailTable').DataTable({
                "paging": true,
                "searching": true,
                "info": true,
                "ordering": false,
                "responsive": true,
                "pageLength": 10
            });
        });
    </script>
@endsection
