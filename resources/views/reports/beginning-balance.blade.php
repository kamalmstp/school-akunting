@extends('layouts.app')

@section('content')
    <div class="app-hero-header d-flex align-items-start">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', $school) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Neraca Awal</li>
        </ol>
    </div>

    <div class="app-body">
        <div class="row gx-3">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="mb-4">
                            <div class="row gx-3">
                                @if(auth()->user()->role !== 'SchoolAdmin')
                                    <div class="col-xl-4 col-md-6 col-12">
                                        <div class="mb-3">
                                            <label for="school_id" class="form-label">Filter Sekolah</label>
                                            <select name="school" class="form-select" id="school_id">
                                                @foreach($schools as $s)
                                                    <option value="{{ $s->id }}" {{ $school && $school->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Periode</label>
                                        <input type="month" class="form-control" id="date" name="date" value="{{ \Carbon\Carbon::parse($date)->format('Y-m') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('reports.beginning-balance') : route('school-reports.beginning-balance', $school) }}" class="btn btn-danger">Reset</a>
                                    <!-- <a href="{{ route('beginning-balance', array_merge(['school' => $school ? $school->id : request()->query('school_id')], request()->except('school_id'), ['export' => 'excel'])) }}" class="btn btn-success">Export Excel</a> -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-3">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Saldo Awal {{ $school ? $school->name : 'Semua Sekolah' }} ({{ \Carbon\Carbon::parse($date)->translatedFormat('F Y') }})</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($transactionsBySchool) && !empty($transactionsBySchool))
                            @foreach($transactionsBySchool as $schoolData)
                                <h6 class="mb-3 fw-bold">{{ $schoolData['school']->name }}</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Sekolah</th>
                                                <th scope="col">Akun</th>
                                                <th scope="col">Deskripsi</th>
                                                <th scope="col" class="text-end">Pemasukan</th>
                                                <th scope="col" class="text-end">Pengeluaran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($schoolData['transactions']->isEmpty())
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Tidak ada transaksi untuk sekolah ini.</td>
                                                </tr>
                                            @else
                                                @foreach($schoolData['transactions'] as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction['date'])->translatedFormat('d-m-Y') }}</td>
                                                        <td>{{ $schoolData['school']->name }}</td>
                                                        <td>{{ $transaction['account'] }}</td>
                                                        <td>{{ $transaction['description'] }}</td>
                                                        <td class="text-end">{{ number_format($transaction['debit'], 0, ',', '.') }}</td>
                                                        <td class="text-end">{{ number_format($transaction['credit'], 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="fw-bold">
                                                    <td colspan="4">Total</td>
                                                    <td class="text-end">{{ number_format($schoolData['total_debit'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($schoolData['total_credit'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tanggal</th>
                                            @if(!$school)
                                                <th scope="col">Sekolah</th>
                                            @endif
                                            <th scope="col">Akun</th>
                                            <th scope="col">Deskripsi</th>
                                            <th scope="col" class="text-end">Pemasukan</th>
                                            <th scope="col" class="text-end">Pengeluaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($transactions->isEmpty())
                                            <tr>
                                                <td colspan="{{ $school ? 5 : 6 }}" class="text-center text-muted">Tidak ada transaksi untuk periode ini.</td>
                                            </tr>
                                        @else
                                            @foreach($transactions as $transaction)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($transaction['date'])->translatedFormat('d-m-Y') }}</td>
                                                    @if(!$school)
                                                        <td>{{ $transaction['school']->name }}</td>
                                                    @endif
                                                    <td>{{ $transaction['account'] }}</td>
                                                    <td>{{ $transaction['description'] }}</td>
                                                    <td class="text-end">{{ number_format($transaction['debit'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($transaction['credit'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="fw-bold">
                                                <td colspan="{{ $school ? 3 : 4 }}">Total</td>
                                                <td class="text-end">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($totalCredit, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            if (@json(auth()->user()->role) !== 'SchoolAdmin') {
                $('#school_id').select2();
            }
        });
    </script>
@endsection