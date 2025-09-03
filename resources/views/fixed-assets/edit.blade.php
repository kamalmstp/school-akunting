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
			<li class="breadcrumb-item" aria-current="page">Aset Tetap - Edit</li>
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
							<h5 class="card-title">Edit Aset Tetap - {{ $school->name }}</h5>
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
                        <form action="{{ route('school-fixed-assets.update', [$school, $fixedAsset]) }}" method="POST">
                        @csrf
                        @method('PUT')
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama Aset</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $fixedAsset->name) }}">
                                                    @error('name')
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
                                                    <label for="acquisition_date" class="form-label">Tanggal Perolehan</label>
                                                    <input type="date" class="form-control @error('acquisition_date') is-invalid @enderror" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date', \Carbon\Carbon::parse($fixedAsset->acquisition_date)->format('Y-m-d')) }}">
                                                    @error('acquisition_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="useful_life" class="form-label">Umur Manfaat (Tahun)</label>
                                                    <input type="number" class="form-control @error('useful_life') is-invalid @enderror" id="useful_life" name="useful_life" value="{{ old('useful_life', $fixedAsset->useful_life) }}">
                                                    @error('useful_life')
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
                                                    <label for="depreciation_percentage" class="form-label">Persentase Penyusutan (%)</label>
                                                    <input type="number" step="0.01" class="form-control @error('depreciation_percentage') is-invalid @enderror" id="depreciation_percentage" name="depreciation_percentage" value="{{ old('depreciation_percentage', $fixedAsset->depreciation_percentage) }}">
                                                    @error('depreciation_percentage')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div id="account-container">
                                            <!-- Form group start -->
                                            @foreach($transactions as $i => $transaction)        
                                            <div class="row gx-3 mb-3 account-row0">
                                                <div class="col-8 mb-3">
                                                    <label for="account_id" class="form-label">Akun</label>
                                                    <select class="form-select account-select" id="account_id{{$i}}" name="account_id[]">
                                                        <option value="">Pilih Akun</option>
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}" {{ $transaction->account_id == $account->id ? 'selected' : ''}}>
                                                                {{ $account->name }} ({{ $account->code }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-4 mb-3">
                                                    <label for="condition" class="form-label">Kondisi Saat Ini</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control angka" id="condition" name="condition" min="0" max="100" value="{{ old('depreciation_percentage', $fixedAsset->depreciation_percentage) }}" readonly>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-6">
                                                    <label for="debit" class="form-label">Pemasukan</label>
                                                    <input type="text" class="form-control angka" id="debit" name="debit[]" value="{{ number_format($transaction->debit, 0, ',', '.') }}">
                                                </div>
                                                <div class="col-6">
                                                    <label for="credit" class="form-label">Pengeluaran</label>
                                                    <input type="text" class="form-control angka" id="credit" name="credit[]" value="{{ number_format($transaction->credit, 0, ',', '.') }}">
                                                </div> -->
                                            </div>
                                            @endforeach
                                            <!-- Form group end -->
                                        </div>
                                        <!-- <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <button type="button" id="addrow" class="btn btn-outline-success btn-sm">+ Tambah Akun</button>
                                                </div>
                                            </div>
                                        </div> -->
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
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            var transactions = @json($transactions);
            $.each(transactions, function(key, val) {
                $('#account_id' + key).select2();
            })
            var i = transactions.length + 1;
            var html = '';
            $('#addrow').on('click', function(e) {
                e.preventDefault();
                let options = '';
                $.each(@json($accounts), function(key, value) {
                    options += '<option value="' + value['id'] + '">' + value['code'] + '-' + value['name'] + '</option>';
                })
                html = 
                '<div class="row gx-3 mb-3 account-row'+i+'">' +
                    '<div class="col-8 mb-3">' +
                        '<label for="account_id" class="form-label">Akun</label>' +
                        '<select class="form-select account-select" id="account_id'+ i +'" name="account_id[]"><option value="">Pilih Akun</option>' + options + '</select>' +
                    '</div>' +
                    '<div class="col-4 mb-3">'+
                        '<label for="condition" class="form-label">Kondisi Saat Ini</label>' +
                        '<div class="input-group">' +
                            '<input type="number" class="form-control angka" id="condition" name="condition" min="0" max="100"  value="100" readonly>' +
                            '<span class="input-group-text">%</span>' +
                        '</div>' +
                    '</div>' +
                    '<!--<div class="col-1" style="margin-top: 30px;">' +
                        '<button type="button" id="'+i+'" class="btn btn-danger btn-sm btn_remove">X</button>' +
                    '</div>' +
                    '<div class="col-6">' +
                        '<label for="debit" class="form-label">Pemasukan</label>' +
                        '<input type="text" class="form-control angka" id="debit" name="debit[]">' +
                    '</div>' +
                    '<div class="col-6">' +
                        '<label for="credit" class="form-label">Pengeluaran</label>' +
                        '<input type="text" class="form-control angka" id="credit" name="credit[]">' +
                    '</div>-->' +
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