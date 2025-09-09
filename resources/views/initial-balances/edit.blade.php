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
        <li class="breadcrumb-item" aria-current="page">Edit Saldo Awal</li>
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
                    <h5 class="card-title">Edit Saldo Awal Periode {{ $financialPeriod->name }}</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('school-initial-balances.update', [$school, $financialPeriod]) }}" method="POST">
                        @csrf
                        @method('PUT')
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
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control rupiah-input" value="{{ number_format($account->initialBalances->first() ? $account->initialBalances->first()->amount : 0, 0, ',', '.') }}">
                                                    <input type="hidden" name="balances[{{ $account->id }}]" value="{{ $account->initialBalances->first() ? $account->initialBalances->first()->amount : 0 }}">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada akun yang ditemukan. Silakan buat akun terlebih dahulu.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Saldo Awal</button>
                        <a href="{{ route('school-initial-balances.index', [$school, $financialPeriod]) }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- App body ends -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rupiahInputs = document.querySelectorAll('.rupiah-input');
        
        rupiahInputs.forEach(input => {
            input.addEventListener('keyup', function(e) {
                let value = e.target.value.replace(/\./g, '');
                
                // Allow only digits
                value = value.replace(/\D/g, '');

                // Update the hidden input with the clean number
                e.target.nextElementSibling.value = value;
                
                // Format the visible input with thousands separator
                let formattedValue = '';
                if (value) {
                    formattedValue = new Intl.NumberFormat('id-ID').format(value);
                }
                e.target.value = formattedValue;
            });
        });
    });
</script>

@endsection