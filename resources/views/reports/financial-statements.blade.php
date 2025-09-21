@extends('layouts.app')

@section('content')
    <div class="app-hero-header d-flex align-items-start">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Laporan Keuangan</li>
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
                                    <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('reports.financial-statements') : route('school-reports.financial-statements', $school) }}" class="btn btn-danger">Reset</a>
                                    <!-- <a href="{{ route('financial-statements', array_merge(['school' => $school ? $school->id : request()->query('school_id')], request()->except('school_id'), ['export' => 'excel'])) }}" class="btn btn-success">Export Excel</a> -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laba Rugi -->
        @foreach($profitLoss as $item)            
            <div class="row gx-3">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Laba Rugi {{ $school ? $school->name : 'Semua Sekolah' }} ({{ \Carbon\Carbon::parse($date)->format('d-m-Y') }})</h5>
                        </div>
                        <div class="card-body">
                            @if($item['revenues']->isNotEmpty())
                                <h5>Pendapatan</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Akun</th>
                                                <th scope="col" class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['revenues'] as $revenue)
                                                <tr>
                                                    <td>{{ $revenue['account']->code }} - {{ $revenue['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($revenue['amount'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total Pendapatan</strong></td>
                                                <td class="text-end"><strong>{{ number_format($item['revenues']->sum('amount'), 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                            @if($item['expenses']->isNotEmpty())
                                <h5>Biaya</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Akun</th>
                                                <th scope="col" class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['expenses'] as $expense)
                                                <tr>
                                                    <td>{{ $expense['account']->code }} - {{ $expense['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($expense['amount'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total Biaya</strong></td>
                                                <td class="text-end"><strong>{{ number_format($item['expenses']->sum('amount'), 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @if($profitLoss->isEmpty())
            <div class="row gx-3 mt-4">
                <div class="col-xl-12">
                    <p>Tidak ada data Laba Rugi untuk ditampilkan.</p>
                </div>
            </div>
        @endif

        <!-- Neraca -->
        @foreach($balanceSheet as $item)       
            <div class="row gx-3">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Neraca {{ $school ? $school->name : 'Semua Sekolah' }} ({{ \Carbon\Carbon::parse($date)->format('d-m-Y') }})</h5>
                        </div>
                        <div class="card-body">
                            @if($item['currentAssets']->isNotEmpty())
                                <h5>Aset Lancar</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Akun</th>
                                                <th scope="col" class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['currentAssets'] as $asset)
                                                <tr>
                                                    <td>{{ $asset['account']->code }} - {{ $asset['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($asset['balance'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total Aset Lancar</strong></td>
                                                <td class="text-end"><strong>{{ number_format($item['currentAssets']->sum('balance'), 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                            @if($item['fixAssets']->isNotEmpty())
                                <h5>Aset Tetap</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Akun</th>
                                                <th scope="col" class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['fixAssets'] as $asset)
                                                <tr>
                                                    <td>{{ $asset['account']->code }} - {{ $asset['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($asset['balance'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total Aset Tetap</strong></td>
                                                <td class="text-end"><strong>{{ number_format($item['fixAssets']->sum('balance'), 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                            @if($item['investments']->isNotEmpty())
                                <h5>Investasi</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Akun</th>
                                                <th scope="col" class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['investments'] as $asset)
                                                <tr>
                                                    <td>{{ $asset['account']->code }} - {{ $asset['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($asset['balance'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total Investasi</strong></td>
                                                <td class="text-end"><strong>{{ number_format($item['investments']->sum('balance'), 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                            @if($item['liabilities']->isNotEmpty())
                                <h5>Kewajiban</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Akun</th>
                                                <th scope="col" class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['liabilities'] as $liability)
                                                <tr>
                                                    <td>{{ $liability['account']->code }} - {{ $liability['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($liability['balance'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total Kewajiban</strong></td>
                                                <td class="text-end"><strong>{{ number_format($item['liabilities']->sum('balance'), 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                            @if($item['equity']->isNotEmpty())
                                <h5>Aset Neto</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Akun</th>
                                                <th scope="col" class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item['equity'] as $equity)
                                                <tr>
                                                    <td>{{ $equity['account']->code }} - {{ $equity['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($equity['balance'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total Aset Neto</strong></td>
                                                <td class="text-end"><strong>{{ number_format($item['equity']->sum('balance'), 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @if($balanceSheet->isEmpty())
            <div class="row gx-3">
                <div class="col-xl-12">
                    <p>Tidak ada data Neraca untuk ditampilkan.</p>
                </div>
            </div>
        @endif
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