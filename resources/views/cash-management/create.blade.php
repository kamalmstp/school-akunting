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
			<li class="breadcrumb-item" aria-current="page">Kelola Dana - Tambah</li>
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
							<h5 class="card-title">Tambah Dana @if(auth()->user()->role == 'SchoolAdmin') - {{ $school->name }} @endif</h5>
						</div>
					</div>
					<div class="card-body">
                        <form action="{{ auth()->user()->role == 'SuperAdmin' ? route('fund-managements.store') : route('school-fund-managements.store', $school) }}" method="POST">
                        @csrf
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        @if(auth()->user()->role == 'SuperAdmin')
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    <div class="mb-3">
                                                        <label for="school_id" class="form-label">Sekolah</label>
                                                        <select name="school_id" class="form-select @error('school_id') is-invalid @enderror" id="school_id">
                                                            <option value="">Pilih Sekolah</option>
                                                            @foreach(\App\Models\School::pluck('name', 'id') as $key => $schoolName)
                                                                <option value="{{ $key }}" {{ old('school_id') == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('school_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama Dana</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <label for="account_id" class="form-label">Ambil dari Akun</label>
                                                    <select class="form-select" id="account_id" name="account_id">
                                                        <option value="">Input Manual</option>
                                                        @foreach($accounts as $account)
                                                        <option value="{{ $account->id }}">
                                                            {{ $account->code }} - {{ $account->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="amount" class="form-label">Jumlah</label>
                                                    <input type="text" class="form-control angka @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}">
                                                    @error('amount')
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
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('fund-managements.index') : route('school-fund-managements.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
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
            // Cek saat pertama kali halaman dimuat
            toggleAmountField();

            // Cek setiap kali selection berubah
            $('#account_id').on('change', function() {
                toggleAmountField();
            });

            function toggleAmountField() {
                const selectedValue = $('#account_id').val();
                if (selectedValue && selectedValue !== '') {
                    $('#amount').prop('disabled', true);
                } else {
                    $('#amount').prop('disabled', false);
                }
            }

            if(@json(auth()->user()->role == 'SuperAdmin')) {
                $('#school_id').select2();
            }
        })
    </script>
@endsection