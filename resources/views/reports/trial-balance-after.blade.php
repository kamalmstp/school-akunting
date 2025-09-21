@extends('layouts.app')

@section('content')
    <div class="app-hero-header d-flex align-items-start">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Neraca Saldo Akhir</li>
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
                                            <label for="schoolFilter" class="form-label">Filter Sekolah</label>
                                            <select name="school" class="form-select" id="schoolFilter">
                                                @foreach($schools as $s)
                                                    <option value="{{ $s->id }}" {{ $school && $school->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="dateFilter" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="dateFilter" name="date" value="{{ $date }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('reports.trial-balance-after') : route('school-reports.trial-balance-after', $school) }}" class="btn btn-danger">Reset</a>
                                    <!-- <a href="{{ route('trial-balance-after', array_merge(['school' => $school ? $school->id : request()->query('school_id')], request()->except('school_id'), ['export' => 'excel'])) }}" class="btn btn-success">Export Excel</a> -->
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
                        <h5 class="card-title">Neraca Saldo Akhir {{ $school ? $school->name : 'Semua Sekolah' }} ({{ \Carbon\Carbon::parse($date)->format('d-m-Y') }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @forelse($trialBalance as $schoolAccounts)
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Akun</th>
                                            <th scope="col" class="text-end">Pemasukan</th>
                                            <th scope="col" class="text-end">Pengeluaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schoolAccounts as $item)
                                            <tr>
                                                <td>{{ $item['account']->code }} - {{ $item['account']->name }}</td>
                                                <td class="text-end">{{ number_format($item['debit'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($item['credit'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td class="text-end">{{ number_format($schoolAccounts->sum('debit'), 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($schoolAccounts->sum('credit'), 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @empty
                                <p>Tidak ada data untuk ditampilkan.</p>
                            @endforelse
                        </div>
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
                $('#schoolFilter').select2();
            }
        });
    </script>
@endsection