@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
			<li class="breadcrumb-item">
				<i class="bi bi-pie-chart lh-1"></i>
				<a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a>
			</li>
			<li class="breadcrumb-item" aria-current="page">Kelola Akun</li>
		</ol>
		<!-- Breadcrumb end -->
	</div>
	<!-- App Hero header ends -->

	<!-- App body starts -->
	<div class="app-body">
		<div class="row gx-3">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-body">
						<!-- Row start -->
						<form method="GET" class="mb-4">
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="accountFilter" class="form-label">Filter Akun</label>
										<select name="account" class="form-select" id="accountFilter">
											<option value="">Pilih Akun Induk</option>
											@foreach(\App\Models\Account::whereNull('parent_id')->pluck('name') as $accountName)
												<option value="{{ $accountName }}" {{ $account == $accountName ? 'selected' : '' }}>{{ $accountName }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="typeFilter" class="form-label">Saldo Normal</label>
										<select name="type" class="form-select" id="typeFilter">
											<option value="">Pilih Saldo Normal</option>
											<option value="Debit" {{ $type == 'Debit' ? 'selected' : '' }}>Pemasukan</option>
											<option value="Kredit" {{ $type == 'Kredit' ? 'selected' : '' }}>Pengeluaran</option>
										</select>
									</div>
								</div>
							</div>
							<!-- Row end -->
							
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ route('accounts.index') }}" class="btn btn-danger">Reset</a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Row start -->
		<div class="row gx-3">
			<div class="col-xxl-12">
				<div class="card">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Daftar Akun</h5>
                            <div>
                                <a href="{{ route('accounts.create') }}" class="btn btn-primary" title="Tambah Akun">
									<span class="d-lg-block d-none">Tambah Akun</span>
									<span class="d-sm-block d-lg-none">
										<i class="bi bi-plus"></i>
									</span>
								</a>
            					<a href="{{ route('accounts.import-form') }}" class="btn btn-success" title="Import Excel">
									<span class="d-lg-block d-none">Import Excel</span>
									<span class="d-sm-block d-lg-none">
										<i class="bi bi-upload"></i>
									</span>
								</a>
                            </div>
						</div>
					</div>
					<div class="card-body">
						@if(session('success'))
							<div class="alert alert-success">
								{{ session('success') }}
							</div>
						@endif
						@if(session('error'))
							<div class="alert alert-danger">
								{{ session('error') }}
							</div>
						@endif
						<div class="table-responsive">
							<table class="table align-middle" style="min-width: max-content;">
								<thead>
									<tr>
										<th>No</th>
										<th>Kode</th>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Saldo Normal</th>
                                        <th>Induk</th>
                                        <th></th>
									</tr>
								</thead>
								<tbody>
									@forelse($accounts as $index => $account)
                                        <tr>
											<td>{{ $accounts->currentPage() * 10 - (9 - $index) }}</td>
                                            <td>{{ $account->code }}</td>
                                            <td>{{ $account->parent ? str_repeat(' ', 4) : '' }}{{ $account->name }}</td>
                                            <td>{{ $account->account_type }}</td>
                                            <td>{{ $account->normal_balance }}</td>
                                            <td>{{ $account->parent ? $account->parent->name : '-' }}</td>
                                            <td>
                                                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('accounts.destroy', $account) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
									@empty
										<tr>
											<td colspan="6">Belum ada akun</td>
										</tr>										
									@endempty
                                </tbody>
							</table>
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $accounts->links() }}
						</div>
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
			$('#accountFilter').select2();
			$('#typeFilter').select2();
		})
	</script>
@endsection