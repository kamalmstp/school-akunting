@extends('layouts.app')

@section('content')
    <div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="bi bi-pie-chart lh-1"></i>
                <a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Kelola Akun - Tambah</li>
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
							<h5 class="card-title">Tambah Akun</h5>
						</div>
					</div>
					<div class="card-body">
                        <form action="{{ route('accounts.store') }}" method="POST">
                        @csrf
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
                                                            <option value="{{ $type }}" {{ old('account_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
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
                                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}">
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
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
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
                                                        <option value="Debit" {{ old('normal_balance') == 'Debit' ? 'selected' : '' }}>Pemasukan</option>
                                                        <option value="Kredit" {{ old('normal_balance') == 'Kredit' ? 'selected' : '' }}>Pengeluaran</option>
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
                                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }} ({{ $parent->code }})</option>
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
                                        <a href="{{ route('accounts.index') }}" class="btn btn-outline-success ms-1">Batal</a>
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
            'Investasi': 'Contoh: 7-110001 atau 7-110001-1',
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
		$(document).ready(function() {
			$('#account_type').select2();
			$('#normal_balance').select2();
            $('#parent_id').select2();
		})
	</script>
@endsection