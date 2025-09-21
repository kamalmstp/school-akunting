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
                                        <label for="accountFilter" class="form-label">Jenis Kas</label>
                                        <select name="account" class="form-select" id="accountFilter">
                                            <option value="masuk"{{ $accountType == 'masuk' ? ' selected' : '' }}>Masuk</option>
                                            <option value="keluar"{{ $accountType == 'keluar' ? ' selected' : '' }}>Keluar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('reports.cash-reports') : route('school-reports.cash-reports', $school) }}" class="btn btn-danger">Reset</a>
                                    <!-- <a href="{{ route('cash-reports', array_merge(['school' => $school ? $school->id : request()->query('school_id')], request()->except('school_id'), ['export' => 'excel', 'account' => request('account') ?? 'masuk'])) }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-success" id="exportBtn">Export Excel</a> -->
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Laporan Kas {{ @ucwords($accountType) }} {{ $school->name }} ({{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }})</h5>
                    </div>
                    <div class="card-body">
                        <!-- Row start -->
                        <div class="row gx-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Uraian</th>
                                                <th scope="col">No. Bukti</th>
                                                <th scope="col" class="text-end">PPDB</th>
                                                <th scope="col" class="text-end">DPP</th>
                                                <th scope="col" class="text-end">SPP</th>
                                                <th scope="col" class="text-end">UKS</th>
                                                <th scope="col" class="text-end">UIS</th>
                                                <th scope="col" class="text-end">UIG</th>
                                                <th scope="col" class="text-end">UIK</th>
                                                <th scope="col" class="text-end">UNIT USAHA</th>
                                                <th scope="col" class="text-end">PEMERINTAH</th>
                                                <th scope="col" class="text-end">SWASTA</th>
                                                <th scope="col" class="text-end">LAIN-LAIN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items['data'] as $item)
                                                <tr>
                                                    <td>{{ $item['no'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item['date'])->format('d-m-Y') }}</td>
                                                    <td>{{ $item['description'] }}</td>
                                                    <td>{{ $item['doc_number'] }}</td>
                                                    <td class="text-end">{{ number_format($item['ppdb'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['dpp'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['spp'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['uks'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['uis'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['uig'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['uik'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['unit_usaha'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['pemerintah'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['swasta'], 0, ',', '.') }}</td>
                                                    <td class="text-end">{{ number_format($item['lain_lain'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center" colspan="4" rowspan="2">Jumlah</td>
                                                <td class="text-end">{{ number_format($items['totals']['ppdb'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['dpp'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['spp'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['uks'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['uis'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['uig'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['uik'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['unit_usaha'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['pemerintah'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['swasta'], 0, ',', '.') }}</td>
                                                <td class="text-end">{{ number_format($items['totals']['lain_lain'], 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center" colspan="11">{{ number_format($items['totals']['grand_total'], 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Row end -->                        
                    </div>
                </div>
            </div>
        </div>
        <!-- Row end -->
    </div>
@endsection

@section('js')
    <script>
    $(document).ready(function() {
        var $exportBtn = $('#exportBtn');

        function updateExportUrl(schoolId, account, startDate, endDate) {
            var href = $exportBtn.attr('href');
            var url = new URL(href, window.location.origin);

            // Update path for schoolId if provided
            if (schoolId) {
                var segments = url.pathname.split('/');
                segments[3] = schoolId; // Replace the 4th segment
                url.pathname = segments.join('/');
            }

            // Update query params
            url.searchParams.set('account', account);
            if (startDate) url.searchParams.set('start_date', startDate);
            if (endDate) url.searchParams.set('end_date', endDate);

            $exportBtn.attr('href', url.toString());
        }

        // Account select
        $('#accountFilter').select2();
        $('#accountFilter').on('change', function() {
            var schoolId = $('#schoolFilter').length ? $('#schoolFilter').val() : null;
            var account = $(this).val();
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            updateExportUrl(schoolId, account, startDate, endDate);
        });

        // School filter (if exists)
        if ($('#schoolFilter').length) {
            $('#schoolFilter').select2();
            $('#schoolFilter').on('change', function() {
                var schoolId = $(this).val();
                var account = $('#accountFilter').val();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                updateExportUrl(schoolId, account, startDate, endDate);
            });
        }

        // Start date change
        $('#start_date').on('change', function() {
            var schoolId = $('#schoolFilter').length ? $('#schoolFilter').val() : null;
            var account = $('#accountFilter').val();
            var startDate = $(this).val();
            var endDate = $('#end_date').val();
            updateExportUrl(schoolId, account, startDate, endDate);
        });

        // End date change
        $('#end_date').on('change', function() {
            var schoolId = $('#schoolFilter').length ? $('#schoolFilter').val() : null;
            var account = $('#accountFilter').val();
            var startDate = $('#start_date').val();
            var endDate = $(this).val();
            updateExportUrl(schoolId, account, startDate, endDate);
        });
    });
    </script>
@endsection