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
			<li class="breadcrumb-item" aria-current="page">Transaksi - Edit</li>
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
							<h5 class="card-title">Edit Transaksi - {{ $school->name }}</h5>
						</div>
					</div>
					<div class="card-body">
                        @if ($errors->has('balance'))
                        <div class="row gx-3">
                            <div class="col-sm-6 col-12">
                                <div class="alert alert-danger">
                                    {{ $errors->first('balance') }}
                                </div>
                            </div>
                        </div>
                        @endif
                        <form action="{{ route('school-transactions.update', [$school, $transaction]) }}" method="POST">
                        @csrf
                        @method('PUT')
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        <div class="row gx-3 mb-3 account-row0">
                                            <div class="col-6">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="doc_number" class="form-label">No Dokumen</label>
                                                    <input type="text" class="form-control @error('doc_number') is-invalid @enderror" id="doc_number" name="doc_number" value="{{ old('doc_number', $transaction->doc_number) }}">
                                                    @error('doc_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-6">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="date" class="form-label">Tanggal</label>
                                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', \Carbon\Carbon::parse($transaction->date)->format('Y-m-d')) }}">
                                                    @error('date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Deskripsi</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $transaction->description) }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="account_id" class="form-label">Akun</label>
                                                    <select class="form-select @error('account_id') is-invalid @enderror" id="account_id" name="account_id">
                                                        <option value="">Pilih Akun</option>
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>
                                                                {{ $account->code }} - {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('account_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3" id="sumber-dana">
                                                    <label for="fund_management_id" class="form-label">Sumber Dana</label>
                                                    <select class="form-select @error('fund_management_id') is-invalid @enderror" id="fund_management_id" name="fund_management_id">
                                                        <option value="">Pilih Sumber Dana</option>
                                                        @foreach($funds as $fund)
                                                            <option value="{{ $fund->id }}" data-amount="{{ $fund->amount }}" {{ old('fund_management_id', $transaction->fund_management_id) == $fund->id ? 'selected' : '' }}>
                                                                {{ $fund->name }} (Rp{{ number_format($fund->amount, 0, ',', '.') }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('fund_management_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="debit" class="form-label">Pemasukan</label>
                                                    <input type="text" class="form-control angka @error('debit') is-invalid @enderror" id="debit" name="debit" value="{{ old('debit', number_format($transaction->debit, 0, ',', '.')) }}">
                                                    @error('debit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="credit" class="form-label">Pengeluaran</label>
                                                    <input type="text" class="form-control angka @error('credit') is-invalid @enderror" id="credit" name="credit" value="{{ old('credit', number_format($transaction->credit, 0, ',', '.')) }}">
                                                    @error('credit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-12">
                                    <div class="text-start">
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('transactions.index') : route('school-transactions.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
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
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#account_id').select2();
            $('#fund_management_id').select2();
            $('#payment_method').select2();

            function toggleFundManagement(selectElement) {
                const selectedText = selectElement.find('option:selected').text().toLowerCase();
                const fundField = $('#fund_management_id').closest('.col-sm-6, .col-6, .col-5');

                if (selectedText.toLowerCase().includes('infaq')) {
                    fundField.hide();
                } else {
                    fundField.show();
                }
            }

            // Jalankan saat pertama kali halaman dimuat
            toggleFundManagement($('#account_id'));

            // Jalankan saat user mengubah akun
            $('#account_id').on('change', function () {
                toggleFundManagement($(this));
            });
        });
    </script>
@endsection