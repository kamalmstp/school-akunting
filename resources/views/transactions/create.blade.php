@extends('layouts.app')
@section('content')
<div class="app-hero-header d-flex align-items-start">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <i class="bi bi-pie-chart lh-1"></i>
            <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
        </li>
        <li class="breadcrumb-item" aria-current="page">Transaksi - Tambah</li>
    </ol>
</div>

<div class="app-body">
    <div class="row gx-3">
        <div class="col-xl-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Tambah Transaksi - {{ $school->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ auth()->user()->role == 'SuperAdmin' ? route('transactions.store') : route('school-transactions.store', $school) }}" method="POST">
                        @csrf

                        {{-- Nomor Dokumen & Tanggal --}}
                        <div class="row gx-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="doc_number" class="form-label">Nomor Dokumen</label>
                                    <input type="text" class="form-control @error('doc_number') is-invalid @enderror"
                                           id="doc_number" name="doc_number" value="{{ old('doc_number') }}">
                                    @error('doc_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                           id="date" name="date" value="{{ old('date') }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Transaksi</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="transaction_type" id="income"
                                       value="income" {{ old('transaction_type') == 'income' ? 'checked' : '' }}>
                                <label class="form-check-label" for="income">Pemasukan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="transaction_type" id="expense"
                                       value="expense" {{ old('transaction_type') == 'expense' ? 'checked' : '' }}>
                                <label class="form-check-label" for="expense">Pengeluaran</label>
                            </div>
                            @error('transaction_type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="transaction-fields" style="display:none;">
                            <div class="row gx-3 account-row0">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="left_select" id="left_label" class="form-label"></label>
                                        <select name="left_id" id="left_select" class="form-select select2"></select>
                                    </div>
                                    @error('account_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="right_select" id="right_label" class="form-label"></label>
                                        <select name="right_id" id="right_select" class="form-select select2"></select>
                                    </div>
                                    @error('cash_management_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="amount" id="amount_label" class="form-label"></label>
                                <input type="text" class="form-control" id="amount" name="amount" value="{{ old('amount') }}">
                            </div>
                        </div>

                        <div class="text-start">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('transactions.index') : route('school-transactions.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const incomeRadio = document.getElementById('income');
    const expenseRadio = document.getElementById('expense');
    const fieldsContainer = document.getElementById('transaction-fields');

    const leftLabel = document.getElementById('left_label') || document.getElementById('left-label');
    const rightLabel = document.getElementById('right_label') || document.getElementById('right-label');
    const leftSelect = document.getElementById('left_select') || document.getElementById('left-select');
    const rightSelect = document.getElementById('right_select') || document.getElementById('right-select');
    const amountLabel = document.getElementById('amount_label') || document.getElementById('amount-label');
    const amountInput = document.getElementById('amount');

    if (!incomeRadio || !expenseRadio || !fieldsContainer || !leftLabel || !rightLabel || !leftSelect || !rightSelect || !amountLabel || !amountInput) {
        return;
    }

    const accounts = @json($accounts);
    const cashes = @json($cashManagements);

    const oldAccount = "{{ old('account_id') }}";
    const oldCash = "{{ old('cash_management_id') }}";
    const oldType = "{{ old('type', old('transaction_type', '')) }}";

    function formatNumberToRupiah(number) {
        if (number === null || number === undefined || number === '') return '';
        const n = Number(number);
        if (isNaN(n)) return '';
        return new Intl.NumberFormat('id-ID').format(n);
    }

    function renderOptions(data, selected, isCash = false) {
        let html = '<option value="">Pilih</option>';
        data.forEach(function (d) {
            const id = d.id ?? '';
            let label = '';
            if (isCash) {
                // Gunakan balance untuk Cash Management
                const saldo = (d.balance !== undefined && d.balance !== null) ? ` (Rp${formatNumberToRupiah(d.balance)})` : ' (Rp0)';
                label = `${d.name ?? ''}${saldo}`;
            } else {
                // Format akun biasa
                const code = d.code ? d.code + ' - ' : '';
                label = `${code}${d.name ?? ''}`;
            }
            const isSelected = String(selected) === String(id) ? ' selected' : '';
            html += `<option value="${id}"${isSelected}>${label}</option>`;
        });
        return html;
    }

    function resetFields() {
        leftSelect.innerHTML = '<option value="">Pilih</option>';
        rightSelect.innerHTML = '<option value="">Pilih</option>';
    }

    function updateForm() {
        fieldsContainer.style.display = 'block';
        resetFields();

        if (incomeRadio.checked) {
            leftLabel.textContent = 'Akun';
            rightLabel.textContent = 'Kas Tujuan';
            amountLabel.textContent = 'Nominal Pemasukan';

            leftSelect.setAttribute('name', 'account_id');
            rightSelect.setAttribute('name', 'cash_management_id');

            leftSelect.innerHTML = renderOptions(accounts, oldAccount, false);
            rightSelect.innerHTML = renderOptions(cashes, oldCash, true);

        } else if (expenseRadio.checked) {
            leftLabel.textContent = 'Kas Sumber';
            rightLabel.textContent = 'Akun';
            amountLabel.textContent = 'Nominal Pengeluaran';

            leftSelect.setAttribute('name', 'cash_management_id');
            rightSelect.setAttribute('name', 'account_id');

            leftSelect.innerHTML = renderOptions(cashes, oldCash, true);
            rightSelect.innerHTML = renderOptions(accounts, oldAccount, false);
        }
        initSelect2();
    }

    function initSelect2() {
        $('#left_select').select2({
            placeholder: "Pilih opsi...",
            allowClear: true
        });
        $('#right_select').select2({
            placeholder: "Pilih opsi...",
            allowClear: true
        });
    }

    function formatInputRupiahInput(el) {
        let value = el.value.replace(/\D/g, '');
        el.value = value ? new Intl.NumberFormat('id-ID').format(Number(value)) : '';
    }

    amountInput.addEventListener('input', function () {
        formatInputRupiahInput(this);
    });

    const form = amountInput.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            const raw = amountInput.value.replace(/[^\d]/g, '');
            amountInput.value = raw;
        });
    }

    incomeRadio.addEventListener('change', updateForm);
    expenseRadio.addEventListener('change', updateForm);

    if (oldType) {
        if (oldType === 'income' && incomeRadio) incomeRadio.checked = true;
        if (oldType === 'expense' && expenseRadio) expenseRadio.checked = true;
        updateForm();
    } else {
        if (incomeRadio.checked || expenseRadio.checked) updateForm();
    }

    const oldAmountRaw = "{{ old('amount') }}";
    if (oldAmountRaw) {
        const digits = String(oldAmountRaw).replace(/\D/g, '');
        if (digits) {
            amountInput.value = new Intl.NumberFormat('id-ID').format(Number(digits));
        }
    }
});
</script>

@endsection