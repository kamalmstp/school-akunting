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
			<li class="breadcrumb-item" aria-current="page">Kelola Dana</li>
		</ol>
		<!-- Breadcrumb end -->
	</div>
	<!-- App Hero header ends -->

	<!-- App body starts -->
	<div class="app-body">
		<!-- Row start -->
		<div class="row gx-3">
			<div class="col-xxl-12">
				<div class="card">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Daftar Dana</h5>
							@if(auth()->user()->role != 'AdminMonitor')
							<div>
                                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('fund-managements.create') : route('school-fund-managements.create', $school) }}" class="btn btn-primary" title="Tambah Dana">
									<span class="d-lg-block d-none">Tambah Dana</span>
									<span class="d-sm-block d-lg-none">
										<i class="bi bi-plus"></i>
									</span>
								</a>
                            </div>
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
										<th>No</th>
                                        @if(auth()->user()->role == 'SuperAdmin')<th>Sekolah</th>@endif
                                        <th>Nama</th>
                                        <th>Jumlah</th>
                                        @if(auth()->user()->role != 'AdminMonitor')<th></th>@endif
									</tr>
								</thead>
								<tbody>
									@forelse($funds as $index => $fund_management)
                                        <tr>
											<td>{{ $funds->currentPage() * 10 - (9 - $index) }}</td>
                                            @if(auth()->user()->role == 'SuperAdmin')<td>{{ $fund_management->school->name }}</td>@endif
                                            <td>{{ $fund_management->name }}</td>
                                            <td>Rp{{ number_format($fund_management->amount, 0, ',', '.') }}{{ $fund_management->account_id ? ' ('.$fund_management->account->code.' - '.$fund_management->account->name.')':'' }}</td>
											@if(auth()->user()->role != 'AdminMonitor')
												<td>
													<a href="{{ route('school-fund-managements.edit', [$fund_management->school, $fund_management]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
													<form action="{{ route('school-fund-managements.destroy', [$fund_management->school, $fund_management]) }}" method="POST" style="display:inline;">
														@csrf
														@method('DELETE')
														<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus siswa ini?')">Hapus</button>
													</form>
												</td>
											@endif
                                        </tr>
									@empty
										<tr>
											<td colspan="{{ auth()->user()->role != 'AdminMonitor' ? '5' : '4'}}">Belum ada dana</td>
										</tr>										
									@endempty
                                </tbody>
							</table>
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $funds->links() }}
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Row end -->

	</div>
	<!-- App body ends -->
@endsection
