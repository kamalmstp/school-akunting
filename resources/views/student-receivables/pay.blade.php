@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
			<li class="breadcrumb-item">
				<i class="bi bi-pie-chart lh-1"></i>
				<a href="{{ auth()->user()->role == 'SuperAdmin' ? route('dashboard') : route('dashboard.index', $school) }}" class="text-decoration-none">Dashboard</a>
			</li>
			<li class="breadcrumb-item" aria-current="page">Piutang Siswa - Bayar</li>
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
							<h5 class="card-title">Bayar Piutang - {{ $school->name }}</h5>
						</div>
					</div>
					<div class="card-body">
                        <p><strong>Siswa:</strong> {{ $receivable->student->name }} ({{ $receivable->student->student_id_number }})</p>
                        <p><strong>Akun:</strong> {{ $receivable->account->name }} ({{ $receivable->account->code }})</p>
                        <p><strong>Jumlah Piutang:</strong> {{ number_format($receivable->total_payable, 0, ',', '.') }}</p>
                        <p><strong>Terbayar:</strong> {{ number_format($receivable->paid_amount, 0, ',', '.') }}</p>
                        <p class="mb-4"><strong>Sisa:</strong> {{ number_format($receivable->total_payable - $receivable->paid_amount, 0, ',', '.') }}</p>
                        @if ($errors->has('amount'))
                        <div class="row gx-3">
                            <div class="col-sm-6 col-12">
                                <div class="alert alert-danger">
                                    {{ $errors->first('amount') }}
                                </div>
                            </div>
                        </div>
                        @endif
                        <form action="{{ route('school-student-receivables.pay', [$school, $receivable]) }}" method="POST">
                        @csrf
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        @if($receivable->student_receivable_details->isEmpty())
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    <!-- Form group start -->
                                                    <div class="mb-3">
                                                        <label for="cash_account_id" class="form-label">Akun Kas</label>
                                                        <select class="form-select @error('cash_account_id') is-invalid @enderror" id="cash_account_id" name="cash_account_id">
                                                            <option value="">Pilih Akun Kas</option>
                                                            @foreach($cashAccounts as $account)
                                                                <option value="{{ $account->id }}" {{ old('cash_account_id') == $account->id ? 'selected' : '' }}>
                                                                    {{ $account->code }} - {{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('cash_account_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- Form group end -->
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Deskripsi</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                                                    @error('description')
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
                                                    <label for="amount" class="form-label">Jumlah Pembayaran</label>
                                                    <input type="text" class="form-control angka @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $receivable->total_payable - $receivable->paid_amount) }}">
                                                    @error('amount')
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
                                                    <label for="date" class="form-label">Tanggal Pembayaran</label>
                                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}">
                                                    @error('date')
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
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('student-receivables.index') : route('school-student-receivables.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
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
            $('#cash_account_id').select2();
        });
    </script>
@endsection