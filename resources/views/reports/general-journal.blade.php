@extends('layouts.app')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">

    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-start">

        <!-- Breadcrumb start -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Laporan Jurnal Umum</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <div class="app-body">
        <div class="row gx-3">
            <div class="col-xl-12">
                <div class="card">

                    <div class="card-body">
                        <!-- Row start -->
                        <form method="GET" class="mb-4">
                            <div class="row gx-3">
                                @if(auth()->user()->role !== 'SchoolAdmin')
                                    <div class="col-xl-4 col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="schoolFilter" class="form-label">Filter Sekolah</label>
                                            <select name="school" class="form-select" id="schoolFilter">
                                                @foreach(\App\Models\School::all() as $s)
                                                    <option value="{{ $s->id }}" {{ $school && $school->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                                min="{{ $activePeriod->start_date }}"
                                                max="{{ $activePeriod->end_date }}"
                                                value="{{ $startDate }}">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                                min="{{ $activePeriod->start_date }}"
                                                max="{{ $activePeriod->end_date }}"
                                                value="{{ $endDate }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('reports.general-journal') : route('school-reports.general-journal', $school) }}" class="btn btn-danger">Reset</a>
                                    <!-- <a href="{{ route('general-journal', array_merge(['school' => $school ? $school->id : request()->query('school_id')], request()->except('school_id'), ['export' => 'excel'])) }}" class="btn btn-success">Export Excel</a> -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-xl-12">
                @foreach($transactions as $schoolId => $schoolTransactions)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Laporan Jurnal Umum {{ \App\Models\School::find($schoolId)->name }} ({{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <!-- Row start -->
                            <div class="row gx-3">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="journalTable_{{ $schoolId }}" class="table table-striped dt-responsive nowrap" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Tanggal</th>
                                                    <th scope="col">No Dokumen</th>
                                                    <th scope="col">Akun</th>
                                                    <th scope="col">Deskripsi</th>
                                                    <th scope="col" class="text-end">Pemasukan</th>
                                                    <th scope="col" class="text-end">Pengeluaran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($schoolTransactions as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
                                                        <td>{{ $transaction->doc_number }}</td>
                                                        <td>{{ $transaction->account->code }} - {{ $transaction->account->name }}</td>
                                                        <td>{{ $transaction->description ?? '-' }}</td>
                                                        <td class="text-end">{{ number_format($transaction->debit, 0, ',', '.') }}</td>
                                                        <td class="text-end">{{ number_format($transaction->credit, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Row end -->                        
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Row end -->
    </div>
    <!-- App body ends -->
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            if (@json(auth()->user()->role) !== 'SchoolAdmin') {
				$('#schoolFilter').select2();
			}

            $('[id^="journalTable_"]').each(function() {
                $(this).DataTable({
                    "paging": true,
                    "searching": true,
                    "info": true,
                    "ordering": false,
                    "responsive": true,
                    "pageLength": 10,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
                    }
                });
            });
        })
    </script>
@endsection