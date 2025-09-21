@extends('layouts.app')

@section('content')
    <div class="app-hero-header d-flex align-items-start">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Jurnal Penyesuaian</li>
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
                                        <label for="startFilter" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="startFilter" name="start_date" value="{{ $startDate }}">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="endFilter" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="endFilter" name="end_date" value="{{ $endDate }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('reports.adjusting-entries') : route('school-reports.adjusting-entries', $school) }}" class="btn btn-danger">Reset</a>
                                    <!-- <a href="{{ route('adjusting-entries', array_merge(['school' => $school ? $school->id : request()->query('school_id')], request()->except('school_id'), ['export' => 'excel'])) }}" class="btn btn-success">Export Excel</a> -->
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
                        <h5 class="card-title">Jurnal Penyesuaian {{ $school ? $school->name : 'Semua Sekolah' }} ({{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Akun</th>
                                        <th scope="col">Deskripsi</th>
                                        <th scope="col" class="text-end">Pemasukan</th>
                                        <th scope="col" class="text-end">Pengeluaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
                                            <td>{{ $transaction->account->code }} - {{ $transaction->account->name }}</td>
                                            <td>{{ $transaction->description ?? '-' }}</td>
                                            <td class="text-end">{{ number_format($transaction->debit, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($transaction->credit, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">Belum ada transaksi penyesuaian</td>
                                        </tr>
                                    @endempty
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-2">
                                {{ $transactions->links() }}
                            </div>
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