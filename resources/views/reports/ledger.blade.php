@extends('layouts.app')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">

    <div class="app-hero-header d-flex align-items-start">

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Laporan Buku Besar</li>
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
                                                @foreach(\App\Models\School::all() as $s)
                                                    <option value="{{ $s->id }}" {{ $school && $school->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="accountType" class="form-label">Tipe Akun</label>
                                        <select name="account_type" class="form-select" id="accountType">
                                            <option value="">Pilih Tipe Akun</option>
                                            @foreach (\App\Models\Account::whereNull('parent_id')->pluck('name', 'id') as $key => $type)
                                                <option value="{{ $type }}" {{ $accountType == $type ? 'selected' : '' }}>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="accountParent" class="form-label">Akun</label>
                                        <select name="account" class="form-select" id="accountParent">
                                            <option value="">Pilih Akun</option>
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
                                    <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('reports.ledger') : route('school-reports.ledger', $school) }}" class="btn btn-danger">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-3">
            <div class="col-xl-12">
                @if(count($accounts) > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Laporan Buku Besar {{ $school ? $school->name : 'Semua Sekolah' }} ({{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }})</h5>
                    </div>
                </div>
                @endif
                @foreach($accounts as $schoolAccounts)
                    @foreach($schoolAccounts as $item)
                    <div class="card">
                        <div class="card-header fw-bold">{{ $item['account']->code }} - {{ $item['account']->name }}</div>
                        <div class="card-body">
                            <div class="row gx-3">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="ledgerTable_{{ $item['account']->id }}" class="table table-striped" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Tanggal</th>
                                                    <th scope="col">Deskripsi</th>
                                                    <th scope="col" class="text-end">Pemasukan</th>
                                                    <th scope="col" class="text-end">Pengeluaran</th>
                                                    <th scope="col" class="text-end">Saldo</th>
                                                    <th class="text-center">Detail</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-start">Saldo Awal</th>
                                                    <th class="text-end">{{ number_format($item['opening_balance'], 0, ',', '.') }}</th>
                                                    <th></th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $runningBalance = $item['opening_balance']; @endphp
                                                @foreach($item['transactions'] as $trans)
                                                    @php $runningBalance += $trans['balance']; @endphp
                                                    <tr class="transaction-row"> 
                                                        <td>{{ \Carbon\Carbon::parse($trans['transaction']->date)->format('d-m-Y') }}</td>
                                                        <td>{{ $trans['transaction']->description ?? '-' }}</td>
                                                        <td class="text-end">{{ number_format($trans['transaction']->debit, 0, ',', '.') }}</td>
                                                        <td class="text-end">{{ number_format($trans['transaction']->credit, 0, ',', '.') }}</td>
                                                        <td class="text-end">{{ number_format($runningBalance, 0, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                $details = null;
                                                                
                                                                if ($trans['student_receivable'] && $trans['transaction']->credit > 0 && Str::startsWith($trans['transaction']->account->code, '1-12') && $trans['student_receivable']->student_receivable_details->isNotEmpty()) {
                                                                    $details = [
                                                                        'type' => 'Siswa',
                                                                        'name' => $trans['student_receivable']->student->name ?? 'N/A',
                                                                        'data' => $trans['student_receivable']->student_receivable_details->toArray(),
                                                                    ];
                                                                } elseif ($trans['teacher_receivable'] && $trans['transaction']->credit > 0 && Str::startsWith($trans['transaction']->account->code, '1-12') && $trans['teacher_receivable']->teacher_receivable_details->isNotEmpty()) {
                                                                    $details = [
                                                                        'type' => 'Guru',
                                                                        'name' => $trans['teacher_receivable']->teacher->name ?? 'N/A',
                                                                        'data' => $trans['teacher_receivable']->teacher_receivable_details->toArray(),
                                                                    ];
                                                                } elseif ($trans['employee_receivable'] && $trans['transaction']->credit > 0 && Str::startsWith($trans['transaction']->account->code, '1-12') && $trans['employee_receivable']->employee_receivable_details->isNotEmpty()) {
                                                                    $details = [
                                                                        'type' => 'Karyawan',
                                                                        'name' => $trans['employee_receivable']->employee->name ?? 'N/A',
                                                                        'data' => $trans['employee_receivable']->employee_receivable_details->toArray(),
                                                                    ];
                                                                }
                                                            @endphp

                                                            @if($details)
                                                                <button class="btn btn-sm btn-info detail-modal-btn" 
                                                                        type="button" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#transactionDetailModal"
                                                                        data-transaction-details="{{ json_encode($details) }}">
                                                                    Lihat Detail
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="fw-bold bg-light">
                                                <tr>
                                                    <td colspan="4">Saldo Akhir</td>
                                                    <td class="text-end">{{ number_format($item['closing_balance'], 0, ',', '.') }}</td>
                                                    <td></td> 
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionDetailModalLabel">Detail Transaksi Penerimaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Pihak Terkait: </strong><span id="detailRecipientName"></span> (<span id="detailRecipientType"></span>)</p>
                    <div id="detailContentTable" class="table-responsive">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>

    <script>
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(number);
        }

        $(document).ready(function() {
            $('#accountType').select2();
            $('#accountParent').select2();
            if (@json(auth()->user()->role) !== 'SchoolAdmin') {
                $('#schoolFilter').select2();
            }

            let accountType = @json($accountType);
            let singleAccount = @json($singleAccount);
            if (accountType) {
                getAccount(accountType, singleAccount);
            }
            $(document).on('change', '#accountType', function() {
                getAccount($(this).val(), null);
            });

            function getAccount(account, single) {
                const school = $('#schoolFilter').val();
                const accountType = account;
                if (accountType) {
                    $.ajax({
                        type:'POST',
                        url:'/transactions/account-parent',
                        data: {school, accountType},
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-csrf-token"]').attr('content')},
                        success:function(data){
                            let options = '<option value="">Pilih Akun</option>';
                            $.each(data, function(key, value) {
                                if (single && single.id === value['id']) {
                                    options += '<option value=' + value['id'] + ' selected>' + value['code'] + '-' + value['name'] + '</option>';
                                }
                                options += '<option value=' + value['id'] + '>' + value['code'] + '-' + value['name'] + '</option>';
                            });
                            $('#accountParent').empty();
                            $('#accountParent').append(options);
                        }
                    });
                }
            }
            
            $('[id^="ledgerTable_"]').each(function() {
                $.fn.dataTable.ext.errMode = 'throw'; 

                try {
                    $(this).DataTable({
                        "paging": true,
                        "searching": true,
                        "info": true,
                        "ordering": false,
                        "responsive": true,
                        "pageLength": 10
                    });
                } catch (e) {
                    console.error("Error initializing DataTable for table:", this.id, e);
                }
            });

            const detailModal = document.getElementById('transactionDetailModal');
            if (detailModal) {
                detailModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const detailJson = button.getAttribute('data-transaction-details');
                    
                    if (!detailJson) {
                        console.error("Data detail transaksi tidak ditemukan.");
                        return;
                    }

                    try {
                        const details = JSON.parse(detailJson);
                        
                        $('#detailRecipientName').text(details.name);
                        $('#detailRecipientType').text(details.type);

                        let tableHtml = `
                            <table class="table table-bordered table-striped w-100">
                                <thead>
                                    <tr>
                                        <th scope="col">Tanggal Pembayaran/Periode</th>
                                        <th scope="col">Deskripsi</th>
                                        <th scope="col" class="text-end">Jumlah Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                        details.data.forEach(item => {
                            tableHtml += `
                                <tr>
                                    <td>${item.period ? new Date(item.period).toLocaleDateString('id-ID') : '-'}</td>
                                    <td>${item.description || '-'}</td>
                                    <td class="text-end">Rp ${formatRupiah(item.amount)}</td>
                                </tr>
                            `;
                        });

                        tableHtml += `
                                </tbody>
                            </table>
                        `;
                        
                        $('#detailContentTable').html(tableHtml);

                    } catch (e) {
                        console.error("Gagal memparsing data JSON:", e);
                        $('#detailContentTable').html('<p class="text-danger">Gagal memuat detail transaksi.</p>');
                    }
                });
            }
        });
    </script>
@endsection
