@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
			<li class="breadcrumb-item">
				<i class="bi bi-pie-chart lh-1"></i>
				<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
			</li>
			<li class="breadcrumb-item" aria-current="page">Transaksi</li>
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
								@if (auth()->user()->role != 'SchoolAdmin')
									<div class="col-xl-4 col-md-6 col-12">
										<div class="mb-3">
											<label for="schoolFilter" class="form-label">Filter Sekolah</label>
											<select name="school_id" class="form-select" id="schoolFilter">
												<option value="">Pilih Sekolah</option>
												@foreach($schools as $key => $schoolName)
													<option value="{{ $key }}" {{ $schoolId == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
												@endforeach
											</select>
										</div>
									</div>
								@else
									<input type="hidden" id="schoolFilter" value="{{ auth()->user()->school_id }}" />
								@endif
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="accountType" class="form-label">Tipe Akun</label>
										<select name="account_type" class="form-select" id="accountType">
											<option value="">Pilih Tipe Akun</option>
											@foreach (\App\Models\Account::whereNull('parent_id')->pluck('name', 'id') as $key => $type)
												<option value="{{ $type }}" {{ $accountType == $type ? 'selected' : '' }}>{{ $type }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="accountParent" class="form-label">Akun</label>
										<select name="account" class="form-select" id="accountParent">
											<option value="">Pilih Akun</option>
										</select>
									</div>
								</div>
							</div>
							<!-- Row end -->
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="dateFilter" class="form-label">Tanggal Mulai</label>
										<input type="date" class="form-control" id="startFilter" name="start_date" value="{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('Y-m-d') : '' }}">
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="dateFilter" class="form-label">Tanggal Akhir</label>
										<input type="date" class="form-control" id="endFilter" name="end_date" value="{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('Y-m-d') : '' }}">
									</div>
								</div>
							</div>
							<!-- Row end -->
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ auth()->user()->role == 'SchoolAdmin' ? route('school-transactions.index', $school) : route('transactions.index') }}" class="btn btn-danger">Reset</a>
									<a href="{{ route('export-transaction', array_merge(['school' => $school ? $school->id : request()->query('school_id')], request()->except('school_id'), ['export' => 'excel'])) }}" class="btn btn-success">Export Excel</a>
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
							<h5 class="card-title">Daftar Transaksi</h5>
                            @if(auth()->user()->role != 'AdminMonitor')
                                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('transactions.create') : route('school-transactions.create', $school) }}" class="btn btn-primary" title="Tambah Transaksi">
									<span class="d-lg-block d-none">Tambah Transaksi</span>
									<span class="d-sm-block d-lg-none">
										<i class="bi bi-plus"></i>
									</span>
								</a>
                            @endif
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
										<th scope="col">No</th>
										<th scope="col">Tanggal</th>
                                        <th scope="col">Sekolah</th>
                                        <th scope="col">Akun</th>
                                        <th scope="col">Deskripsi</th>
                                        <th scope="col" class="text-end">Pemasukan</th>
                                        <th scope="col" class="text-end">Pengeluaran</th>
                                        @if (auth()->user()->role === 'SchoolAdmin') <th scope="col"></th> @endif
									</tr>
								</thead>
								<tbody>
									@forelse($transactions as $index => $transaction)
                                        <tr>
											<td>{{ $transactions->currentPage() * 10 - (9 - $index) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
                                            <td>{{ $transaction->school->name }}</td>
                                            <td>{{ $transaction->account->code }} - {{ $transaction->account->name }}</td>
                                            <td>{{ $transaction->description ?? '-' }}</td>
                                            <td class="text-end">{{ number_format($transaction->debit, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($transaction->credit, 0, ',', '.') }}</td>
                                            @if (auth()->user()->role != 'AdminMonitor')
                                                <td class="text-center">
                                                    <a href="{{ route('school-transactions.edit', [$transaction->school, $transaction]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <form action="{{ route('school-transactions.destroy', [$transaction->school, $transaction]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
									@empty
										<tr>
											<td colspan="7">Belum ada transaksi</td>
										</tr>										
									@endempty
                                </tbody>
							</table>
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $transactions->links() }}
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
		$(document).ready(function(){
			$('#accountType').select2();
			$('#accountParent').select2();
			if (@json(auth()->user()->role) === 'SuperAdmin' || @json(auth()->user()->role) === 'AdminMonitor') {
				$('#schoolFilter').select2();
			}
			let accountType = @json($accountType);
			let singleAccount = @json($singleAccount);
			if (accountType) {
				getAccount(accountType, singleAccount);
			}
			$(document).on('change', '#accountType', function() {
				getAccount($(this).val(), null);
				
			})

			function getAccount(account, single) {
				const school = $('#schoolFilter').val();
				const accountType = account;
				if (accountType) {
					$.ajax({
						type:'POST',
						url:'/transactions/account-parent',
						data: {school, accountType},
						dataType: 'json',
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						success:function(data){
							let options = '<option value="">Pilih Akun</option>';
							$.each(data, function(key, value) {
								if (single && single.id === value['id']) {
									options += '<option value=' + value['id'] + ' selected>' + value['code'] + '-' + value['name'] + '</option>';
								}
								options += '<option value=' + value['id'] + '>' + value['code'] + '-' + value['name'] + '</option>';
							});
							$('#accountParent').empty();
							$('#accountParent').append(options);
							
						}
					});
				}
			}
		})
	</script>
@endsection