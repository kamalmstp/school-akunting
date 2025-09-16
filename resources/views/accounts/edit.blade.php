@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
    <div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Kelola Akun - Edit</li>
		</ol>
		<!-- Breadcrumb end -->
	</div>
	<!-- App Hero header ends -->

	<!-- App body starts -->
	<div class="app-body">

		<!-- Row start -->
		<div class="row gx-3">
			<div class="col-xl-12">
				<div class="card mb-3">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Edit Akun - {{ $school->name }}</h5>
						</div>
					</div>
					<div class="card-body">
                        <form action="{{ route('school-accounts.update', [$school, $account]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <label for="account_type" class="form-label">Tipe Akun</label>
                                                    <select class="form-select @error('account_type') is-invalid @enderror" id="account_type" name="account_type">
                                                        <option value="">Pilih Tipe</option>
                                                        @foreach(['Aset Lancar', 'Aset Tetap', 'Kewajiban', 'Aset Neto', 'Pendapatan', 'Biaya', 'Investasi'] as $type)
                                                            <option value="{{ $type }}" {{ old('account_type', $account->account_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('account_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <label for="code" class="form-label">Kode Akun</label>
                                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $account->code) }}">
                                                    <small id="code_hint" class="form-text text-muted"></small>
                                                    @error('code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama Akun</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $account->name) }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <label for="normal_balance" class="form-label">Saldo Normal</label>
                                                    <select class="form-select @error('normal_balance') is-invalid @enderror" id="normal_balance" name="normal_balance">
                                                        <option value="Debit" {{ old('normal_balance', $account->normal_balance) == 'Debit' ? 'selected' : '' }}>Pemasukan</option>
                                                        <option value="Kredit" {{ old('normal_balance', $account->normal_balance) == 'Kredit' ? 'selected' : '' }}>Pengeluaran</option>
                                                    </select>
                                                    @error('normal_balance')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <label for="parent_id" class="form-label">Akun Induk</label>
                                                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                                        <option value="">Tidak Ada</option>
                                                        @foreach($accounts as $parent)
                                                            <option value="{{ $parent->id }}" {{ old('parent_id', $account->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }} ({{ $parent->code }})</option>
                                                        @endforeach
                                                    </select>
                                                    @error('parent_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-12">
                                    <div class="text-start">
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('accounts.index') : route('school-accounts.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
                                    </div>
                                </div>
                            </div>
                        </form>
					</div>
				</div>
			</div>
		</div>
		<!-- Row end -->

	</div>
	<!-- App body ends -->

    <script>
        const codeHints = {
            'Aset Lancar': 'Contoh: 1-110001 atau 1-110001-1',
            'Aset Tetap': 'Contoh: 1-210001 atau 1-210001-1',
            'Kewajiban': 'Contoh: 2-110001 atau 2-110001-1',
            'Aset Neto': 'Contoh: 3-110001 atau 3-110001-1',
            'Pendapatan': 'Contoh: 4-110001 atau 4-110001-1',
            'Biaya': 'Contoh: 5-110001 atau 5-110001-1',
            'Investasi': 'Contoh: 7-110001 atau 7-110001-1'
        };

        const accountTypeSelect = document.getElementById('account_type');
        const codeInput = document.getElementById('code');
        const codeHint = document.getElementById('code_hint');
        const parentSelect = document.getElementById('parent_id');

        function updateCodeHint() {
            const accountType = accountTypeSelect.value;
            const parentId = parentSelect.value;
            if (parentId) {
                const parentOption = parentSelect.options[parentSelect.selectedIndex];
                const parentCode = parentOption.text.match(/\((\S+)\)/)[1];
                codeHint.textContent = `Kode harus dimulai dengan ${parentCode} (contoh: ${parentCode}-1)`;
            } else if (accountType && codeHints[accountType]) {
                codeHint.textContent = codeHints[accountType];
            } else {
                codeHint.textContent = '';
            }
        }

        accountTypeSelect.addEventListener('change', updateCodeHint);
        parentSelect.addEventListener('change', updateCodeHint);
        updateCodeHint();
    </script>
@endsection
@section('js')
<script>
$(function() {
    $('#account_type').select2();
    $('#normal_balance').select2();
    $('#parent_id').select2();

    const codeHints = {
        'Aset Lancar': 'Contoh: 1-110001 atau 1-110001-1',
        'Aset Tetap': 'Contoh: 1-210001 atau 1-210001-1',
        'Kewajiban': 'Contoh: 2-110001 atau 2-110001-1',
        'Aset Neto': 'Contoh: 3-110001 atau 3-110001-1',
        'Pendapatan': 'Contoh: 4-110001 atau 4-110001-1',
        'Biaya': 'Contoh: 5-110001 atau 5-110001-1',
        'Investasi': 'Contoh: 7-110001 atau 7-110001-1'
    };

    const allAccountsRaw = @json($accounts->toArray());

    const currentAccountId = @json($account->id);

    function normalizeAccounts(raw) {
        if (!raw) return [];
        if (raw.data && Array.isArray(raw.data)) return raw.data;
        if (Array.isArray(raw)) return raw;
        if (typeof raw === 'object') {
            return Object.values(raw).filter(v => typeof v === 'object' && (v.code || v.account_type || (v.attributes && (v.attributes.code || v.attributes.account_type))));
        }
        return [];
    }

    const accountsArray = normalizeAccounts(allAccountsRaw);

    function getProp(obj, key) {
        if (!obj) return undefined;
        if (obj[key] !== undefined) return obj[key];
        if (obj.attributes && obj.attributes[key] !== undefined) return obj.attributes[key];
        if (obj.data && obj.data[key] !== undefined) return obj.data[key];
        return undefined;
    }

    function codeKey(s) {
        if (typeof s !== 'string') return s;
        return s.split('-').map(part => {
            const n = parseInt(part, 10);
            return isNaN(n) ? part : String(n).padStart(12, '0');
        }).join('-');
    }

    function getLastCodeForType(type) {
        const filtered = accountsArray.filter(acc => {
            const accType = getProp(acc, 'account_type') || getProp(acc, 'type') || getProp(acc, 'accountType');
            const accId = getProp(acc, 'id') || getProp(acc, 'account_id') || getProp(acc, 'accountId');
            if (!accType) return false;
            if (String(accId) === String(currentAccountId)) return false;
            return accType === type;
        });

        if (!filtered.length) return null;

        const codes = filtered.map(acc => getProp(acc, 'code') || getProp(acc, 'kode') || '').filter(Boolean);

        if (!codes.length) return null;

        codes.sort((a, b) => {
            const ka = codeKey(a);
            const kb = codeKey(b);
            if (ka < kb) return -1;
            if (ka > kb) return 1;
            return 0;
        });

        return codes[codes.length - 1];
    }

    // elemen
    const $accountType = $('#account_type');
    const $parent = $('#parent_id');
    const $codeInput = $('#code');
    const $codeHint = $('#code_hint');

    const originalCode = $codeInput.val();

    function updateCodeHint() {
        const accountType = $accountType.val();
        const parentId = $parent.val();

        if (parentId) {
            const parentText = $parent.find('option:selected').text() || '';
            const m = parentText.match(/\((\S+)\)/);
            if (m && m[1]) {
                const parentCode = m[1];
                $codeHint.text(`Kode harus dimulai dengan ${parentCode} (contoh: ${parentCode}-1)`);
                if ($codeInput.val() === '' || $codeInput.val() === originalCode) {
                    $codeInput.val(parentCode + '-');
                }
                return;
            }
        }

        if (!accountType) {
            $codeHint.text('');
            return;
        }

        const lastCode = getLastCodeForType(accountType);

        if (lastCode) {
            $codeHint.text(`Kode terakhir: ${lastCode}. Lanjutkan dengan nomor berikutnya.`);
            if ($codeInput.val() === '' || $codeInput.val() === originalCode) {
                $codeInput.val(lastCode + '-');
            }
        } else if (codeHints[accountType]) {
            $codeHint.text(codeHints[accountType]);
            if ($codeInput.val() === '' || $codeInput.val() === originalCode) {
                $codeInput.val('');
            }
        } else {
            $codeHint.text('');
        }
    }

    $accountType.on('change select2:select', updateCodeHint);
    $parent.on('change select2:select', updateCodeHint);

    setTimeout(updateCodeHint, 80);
});
</script>
@endsection