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
			<li class="breadcrumb-item" aria-current="page">Aset Tetap - Penyusutan</li>
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
							<h5 class="card-title">Catat Penyusutan - {{ $school->name }}</h5>
						</div>
					</div>
					<div class="card-body">
                        <p><strong>Nama Aset:</strong> {{ $fixedAsset->name }}</p>
                        <p><strong>Biaya Perolehan:</strong> {{ number_format($fixedAsset->acquisition_cost, 0, ',', '.') }}</p>
                        <p><strong>Akumulasi Penyusutan:</strong> {{ number_format($fixedAsset->accumulated_depriciation, 0, ',', '.') }}</p>
                        <p class="mb-4"><strong>Nilai Sisa:</strong> {{ number_format($fixedAsset->acquisition_cost - $fixedAsset->accumulated_depriciation, 0, ',', '.') }}</p>
                        @if ($errors->has('amount'))
                        <div class="row gx-3">
                            <div class="col-sm-6 col-12">
                                <div class="alert alert-danger">
                                    {{ $errors->first('amount') }}
                                </div>
                            </div>
                        </div>
                        @endif
                        <form action="{{ route('school-fixed-assets.depreciate', [$school, $fixedAsset]) }}" method="POST">
                        @csrf
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        @if($fixedAsset->depreciations->isEmpty())
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    <!-- Form group start -->
                                                    <div class="mb-3">
                                                        <label for="account_id" class="form-label">Akun Biaya Penyusutan</label>
                                                        <select class="form-select @error('account_id') is-invalid @enderror" id="account_id" name="account_id">
                                                            <option value="">Pilih Akun Biaya</option>
                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
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
                                                    <label for="date" id="labelDate" class="form-label">Tanggal Penyusutan</label>
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
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('fixed-assets.index') : route('school-fixed-assets.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
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
</div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#account_id0').select2();
            var i = 1;
            var html = '';
            $('#addrow').on('click', function(e) {
                e.preventDefault();
                let options = '';
                $.each(@json($accounts), function(key, value) {
                    options += '<option value="' + value['id'] + '">' + value['code'] + '-' + value['name'] + '</option>';
                })
                html = 
                '<div class="row gx-3 mb-3 account-row'+i+'">' +
                    '<div class="col-5">' +
                        '<label for="account_id" class="form-label">Akun</label>' +
                        '<select class="form-select account-select" id="account_id'+ i +'" name="account_id[]"><option value="">Pilih Akun</option>' + options + '</select>' +
                    '</div>' +
                    '<div class="col-3">' +
                        '<label for="debit" class="form-label">Pemasukan</label>' +
                        '<input type="text" class="form-control angka" id="debit" name="debit[]">' +
                    '</div>' +
                    '<div class="col-3">' +
                        '<label for="credit" class="form-label">Pengeluaran</label>' +
                        '<input type="text" class="form-control angka" id="credit" name="credit[]">' +
                    '</div>' +
                    '<div class="col-1" style="margin-top: 30px;">' +
                        '<button type="button" id="'+i+'" class="btn btn-danger btn-sm btn_remove">X</button>' +
                    '</div>' +
                '</div>';
                $('#account-container').append(html);
                $('#account_id' + i).select2();
                i++;
            });

            $(document).on('click', '.btn_remove', function(){
                const button_id = $(this).attr("id");
                $('.account-row' + button_id).remove();
            });
        });
    </script>
@endsection