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
			<li class="breadcrumb-item" aria-current="page">Transaksi - Tambah</li>
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
							<h5 class="card-title">Tambah Transaksi - {{ $school->name }}</h5>
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
                        <form action="{{ auth()->user()->role == 'SuperAdmin' ? route('transactions.store') : route('school-transactions.store', $school) }}" method="POST">
                        @csrf
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <input class="form-check-input" type="checkbox" value="true" name="type" id="type">
                                                    <label class="form-check-label" for="type">
                                                        Penyesuaian
                                                    </label>
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        @if(auth()->user()->role == 'SuperAdmin')
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    <div class="mb-3">
                                                        <label for="school_id" class="form-label">Sekolah</label>
                                                        <select name="school_id" class="form-select" id="school_id">
                                                            <option value="">Pilih Sekolah</option>
                                                            @foreach(\App\Models\School::pluck('name', 'id') as $key => $schoolName)
                                                                <option value="{{ $key }}">{{ $schoolName }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="date" class="form-label">Tanggal</label>
                                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date') }}">
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
                                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div id="account-container">
                                            <!-- <div class="col-sm-12 col-12"> -->
                                                <!-- Form group start -->
                                                    @if(old('account_id'))
                                                        @foreach(old('account_id') as $i => $accountId)
                                                            <div class="row gx-3 mb-3 account-row{{$i}}">
                                                                <div class="col-5">
                                                                    <label for="account_id" class="form-label">Akun</label>
                                                                    <select class="form-select account-select @error('account_id.' . $i) is-invalid @enderror" id="account_id{{$i}}" name="account_id[]">
                                                                        <option value="">Pilih Akun</option>
                                                                        @foreach($accounts as $account)
                                                                            <option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>
                                                                                {{ $account->code }} - {{ $account->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('account_id.' . $i)
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-3">
                                                                    <label for="debit" class="form-label">Pemasukan</label>
                                                                    <input type="text" class="form-control angka @error('debit.' . $i) is-invalid @enderror" id="debit" name="debit[]" value="{{ old('debit.' . $i, 0) }}">
                                                                    @error('debit.' . $i)
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-3">
                                                                    <label for="credit" class="form-label">Pengeluaran</label>
                                                                    <input type="text" class="form-control angka @error('credit.' . $i) is-invalid @enderror" id="credit" name="credit[]" value="{{ old('credit.' . $i, 0) }}">
                                                                    @error('credit.' . $i)
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                @if($i > 0)
                                                                <div class="col-1" style="margin-top: 30px;">
                                                                    <button type="button" id="{{ $i }}" class="btn btn-danger btn-sm btn_remove">X</button>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row gx-3 mb-3 account-row0">
                                                            <div class="col-5">
                                                                <label for="account_id" class="form-label">Akun</label>
                                                                <select class="form-select account-selec" id="account_id0" name="account_id[]">
                                                                    <option value="">Pilih Akun</option>
                                                                    @foreach($accounts as $account)
                                                                        <option value="{{ $account->id }}">
                                                                            {{ $account->code }} - {{ $account->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-3">
                                                                <label for="debit" class="form-label">Pemasukan</label>
                                                                <input type="text" class="form-control angka" id="debit" name="debit[]">
                                                            </div>
                                                            <div class="col-3">
                                                                <label for="credit" class="form-label">Pengeluaran</label>
                                                                <input type="text" class="form-control angka" id="credit" name="credit[]">
                                                            </div>
                                                        </div>
                                                    @endif
                                                <!-- Form group end -->
                                            <!-- </div> -->
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <button type="button" id="addrow" class="btn btn-outline-success btn-sm">+ Tambah Akun</button>
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
            $('#account_id0').select2();
            $('#payment_method').select2();
            var i = 1;
            var html = '';
            $('#addrow').on('click', function(e) {
                e.preventDefault();
                let options = '';
                $.each(@json($accounts), function(key, value) {
                    options += '<option value="' + value['id'] + '">' + value['code'] + ' - ' + value['name'] + '</option>';
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