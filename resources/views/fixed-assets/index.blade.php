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
			<li class="breadcrumb-item" aria-current="page">Aset Tetap</li>
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
								@if (auth()->user()->role == 'SuperAdmin')
									<div class="col-xl-4 col-md-6 col-12">
										<div class="mb-3">
											<label for="schoolFilter" class="form-label">Filter Sekolah</label>
											<select name="school" class="form-select" id="schoolFilter">
												<option value="">Pilih Sekolah</option>
												@foreach($schools as $key => $schoolName)
													<option value="{{ $key }}" {{ $school == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
												@endforeach
											</select>
										</div>
									</div>
								@endif
							</div>
							<!-- Row end -->
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="accountFilter" class="form-label">Akun</label>
										<select name="account" class="form-select" id="accountFilter">
											<option value="">Pilih Akun</option>
											@foreach (\App\Models\Account::where('account_type', 'Aset Tetap')->where('name', 'not like', '%akumulasi%')->get() as $key => $accountData)
												<option value="{{ $accountData->id }}" {{ $account == $accountData->id ? 'selected' : '' }}>{{ $accountData->code }} - {{ $accountData->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="dateFilter" class="form-label">Tanggal Perolehan</label>
										<input type="date" class="form-control" id="dateFilter" name="date" value="{{ $acqDate ? \Carbon\Carbon::parse($acqDate)->format('Y-m-d') : '' }}">
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="nameFilter" class="form-label">Nama Aset</label>
										<input type="text" class="form-control" id="nameFilter" name="name" value="{{ $assetName }}">
									</div>
								</div>
							</div>							
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('fixed-assets.index') : route('school-fixed-assets.index', $school) }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Aset Tetap</h5>
                            @if(auth()->user()->role != 'AdminMonitor')
                                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('fixed-assets.create') : route('school-fixed-assets.create', $school) }}" class="btn btn-primary" title="Tambah Aset Tetap">
									<span class="d-lg-block d-none">Tambah Aset Tetap</span>
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
						<div class="table-responsive" style="white-space: nowrap;">
							<table class="table align-middle">
								<thead>
									<tr>
										<th scope="col">No</th>
										<th scope="col">Sekolah</th>
                                        <th scope="col">Nama Aset</th>
                                        <th scope="col">Akun</th>
                                        <th scope="col">Tanggal Perolehan</th>
                                        <th scope="col" class="text-end">Biaya Perolehan</th>
                                        <th scope="col" class="text-end">Umur Manfaat (Tahun)</th>
                                        <th scope="col" class="text-end">Persentase Penyusutan (%)</th>
                                        <th scope="col" class="text-end">Akumulasi Penyusutan</th>
                                        <th scope="col" class="text-end">Nilai Buku</th>
                                        @if (auth()->user()->role != 'AdminMonitor')<th></th>@endif
									</tr>
								</thead>
								<tbody>
									@forelse($fixedAssets as $index => $fixedAsset)
                                        <tr>
											<td>{{ $fixedAssets->currentPage() * 10 - (9 - $index) }}</td>
                                            <td>{{ $fixedAsset->school->name }}</td>
											<td>
												<a href="javascript:void;" class="text-decoration-none" data-bs-toggle="collapse" data-bs-target="#details{{ $fixedAsset->id }}">
													{{ $fixedAsset->name }}
												</a>
											</td>
											<td>{{ $fixedAsset->account->code }} - {{ $fixedAsset->account->name }}</td>
											<td>{{ \Carbon\Carbon::parse($fixedAsset->acquisition_date)->format('d-m-Y') }}</td>
											<td class="text-end">{{ number_format($fixedAsset->acquisition_cost, 0, ',', '.') }}</td>
											<td class="text-end">{{ $fixedAsset->useful_life }}</td>
											<td class="text-end">{{ number_format($fixedAsset->depreciation_percentage, 2, ',', '.') }}</td>
											<td class="text-end">{{ number_format($fixedAsset->accumulated_depriciation, 0, ',', '.') }}</td>
											<td class="text-end">{{ number_format($fixedAsset->acquisition_cost - $fixedAsset->accumulated_depriciation, 0, ',', '.') }}</td>
											@if (auth() ->user()->role != 'AdminMonitor')
												<td class="text-center">
													@if($fixedAsset->useful_life != $fixedAsset->depreciations()->count())
														<a href="{{ route('school-fixed-assets.depreciate', [$fixedAsset->school, $fixedAsset]) }}" class="btn btn-sm btn-success">Susutkan</a>
														<a href="{{ route('school-fixed-assets.edit', [$fixedAsset->school, $fixedAsset]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
													@endif
													<form action="{{ route('school-fixed-assets.destroy', [$fixedAsset->school, $fixedAsset]) }}" method="POST" style="display:inline;">
														@csrf
														@method('DELETE')
														<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus aset ini?')">Hapus</button>
													</form>
												</td>
											@endif
                                        </tr>
										<tr class="collapse" id="details{{ $fixedAsset->id }}">
                                            <td colspan="{{auth()->user()->role != 'AdminMonitor' ? '7' : '6' }}">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Tanggal Penyusutan</th>
                                                            <th scope="col">Deskripsi</th>
                                                            <th scope="col" class="text-end">Nilai Buku Awal</th>
                                                            <th scope="col" class="text-end">Jumlah Penyusutan</th>
                                                            <th scope="col" class="text-end">Nilai Buku Akhir</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                        @forelse($fixedAsset->depreciations as $detail)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($detail->date)->format('d-m-Y') }}</td>
                                                                <td>{{ $detail->description }}</td>
                                                                <td class="text-end">{{ number_format($detail->balance + $detail->amount, 0, ',', '.') }}</td>
                                                                <td class="text-end">{{ number_format($detail->amount, 0, ',', '.') }}</td>
                                                                <td class="text-end">{{ number_format($detail->balance, 0, ',', '.') }}</td>
                                                            </tr>
														@empty
															<tr>
																<td colspan="5">Belum penyusutan</td>
															</tr>
                                                        @endforelse
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
									@empty
										<tr>
											<td colspan="{{ auth()->user()->role != 'AdminMonitor' ? '10' : '9' }}">Belum ada aset tetap</td>
										</tr>										
									@endempty
                                </tbody>
							</table>
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $fixedAssets->links() }}
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
			if (@json(auth()->user()->role) != 'SchoolAdmin') {
				$('#schoolFilter').select2();
			}
			$('#accountFilter').select2();
		})
	</script>
@endsection