@extends('layouts.app')

@section('content')

    <div class="app-hero-header d-flex align-items-start">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('school-financial-periods.index', $school) }}" class="text-decoration-none">Kelola Periode Keuangan</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Tambah Transaksi</li>
        </ol>
        </div>
    <div class="app-body">
        <div class="row gx-3">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tambah Transaksi Baru</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('school-transactions.store', [$school, $financialPeriod]) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label">Tanggal Transaksi</label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', now()->toDateString()) }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" required>
                                </div>
                            </div>
                            
                            <hr class="my-4">

                            <div id="transaction-entries">
                                <div class="row gx-3 mb-3 entry-row">
                                    <div class="col-md-5">
                                        <label class="form-label">Akun</label>
                                        <select class="form-select account-select" name="accounts[0][account_id]" required>
                                            <option value="">Pilih Akun</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->code }} - {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Debit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control rupiah-input debit-input" name="accounts[0][debit]" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Kredit</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control rupiah-input credit-input" name="accounts[0][credit]" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-row" style="display:none;">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-success" id="add-row">
                                <i class="bi bi-plus-lg"></i> Tambah Baris
                            </button>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Total Debit</label>
                                    <p class="h4" id="total-debit">Rp 0</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <label class="form-label">Total Kredit</label>
                                    <p class="h4" id="total-credit">Rp 0</p>
                                </div>
                                <div class="col-12 mt-3 text-center">
                                    <span class="h5" id="balance-status">Jumlah belum seimbang</span>
                                </div>
                            </div>

                            <hr class="my-4">

                            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                            <a href="{{ route('school-transactions.index', [$school, $financialPeriod]) }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const transactionEntries = document.getElementById('transaction-entries');
            const addRowButton = document.getElementById('add-row');
            const totalDebitElement = document.getElementById('total-debit');
            const totalCreditElement = document.getElementById('total-credit');
            const balanceStatusElement = document.getElementById('balance-status');
            let entryIndex = transactionEntries.children.length;

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            const calculateTotals = () => {
                let totalDebit = 0;
                let totalCredit = 0;

                document.querySelectorAll('.entry-row').forEach(row => {
                    const debitInput = row.querySelector('.debit-input');
                    const creditInput = row.querySelector('.credit-input');

                    const debitValue = parseInt(debitInput.value.replace(/\D/g, '')) || 0;
                    const creditValue = parseInt(creditInput.value.replace(/\D/g, '')) || 0;

                    totalDebit += debitValue;
                    totalCredit += creditValue;
                });

                totalDebitElement.textContent = formatRupiah(totalDebit);
                totalCreditElement.textContent = formatRupiah(totalCredit);

                if (totalDebit === totalCredit && totalDebit > 0) {
                    balanceStatusElement.textContent = 'Jumlah sudah seimbang';
                    balanceStatusElement.classList.remove('text-danger');
                    balanceStatusElement.classList.add('text-success');
                } else {
                    balanceStatusElement.textContent = 'Jumlah belum seimbang';
                    balanceStatusElement.classList.remove('text-success');
                    balanceStatusElement.classList.add('text-danger');
                }
            };

            const addEventListeners = (element) => {
                element.querySelectorAll('.rupiah-input').forEach(input => {
                    input.addEventListener('keyup', (e) => {
                        const originalValue = e.target.value;
                        const cleanValue = originalValue.replace(/\D/g, '');
                        e.target.value = new Intl.NumberFormat('id-ID').format(cleanValue);
                        calculateTotals();
                    });

                    // Disable one input if the other is filled
                    input.addEventListener('input', (e) => {
                        const isDebitInput = e.target.classList.contains('debit-input');
                        const partnerInput = isDebitInput ? e.target.closest('.entry-row').querySelector('.credit-input') : e.target.closest('.entry-row').querySelector('.debit-input');

                        if (e.target.value.replace(/\D/g, '').length > 0) {
                            partnerInput.disabled = true;
                        } else {
                            partnerInput.disabled = false;
                        }
                    });
                });
            };

            const createNewRow = () => {
                const newRow = document.createElement('div');
                newRow.classList.add('row', 'gx-3', 'mb-3', 'entry-row');
                newRow.innerHTML = `
                    <div class="col-md-5">
                        <select class="form-select account-select" name="accounts[${entryIndex}][account_id]" required>
                            <option value="">Pilih Akun</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->code }} - {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control rupiah-input debit-input" name="accounts[${entryIndex}][debit]" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control rupiah-input credit-input" name="accounts[${entryIndex}][credit]" value="">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-row">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                `;
                transactionEntries.appendChild(newRow);
                entryIndex++;
                addEventListeners(newRow);
                updateRemoveButtons();
            };

            const updateRemoveButtons = () => {
                const removeButtons = document.querySelectorAll('.remove-row');
                if (removeButtons.length > 1) {
                    removeButtons.forEach(btn => btn.style.display = 'block');
                } else {
                    removeButtons.forEach(btn => btn.style.display = 'none');
                }
            };

            addRowButton.addEventListener('click', createNewRow);

            transactionEntries.addEventListener('click', (e) => {
                if (e.target.closest('.remove-row')) {
                    e.target.closest('.entry-row').remove();
                    calculateTotals();
                    updateRemoveButtons();
                }
            });

            // Initial setup
            addEventListeners(transactionEntries);
            calculateTotals();
            updateRemoveButtons();
        });
    </script>
@endsection