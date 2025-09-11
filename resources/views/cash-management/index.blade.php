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
                                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('cash-managements.create') : route('school-cash-managements.create', $school) }}" class="btn btn-primary" title="Tambah Dana">
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
                                        <th>Saldo</th>
                                        @if(auth()->user()->role != 'AdminMonitor')<th></th>@endif
									</tr>
								</thead>
								<tbody>
									@forelse($cashes as $index => $cash)
                                        <tr>
											<td>{{ $loop->iteration }}</td>
                                            @if(auth()->user()->role == 'SuperAdmin')<td>{{ $cash->school->name }}</td>@endif
                                            <td>{{ $cash->name }}</td>
                                            <td>Rp{{ number_format($cash->balance, 0, ',', '.') }}{{ $cash->account_id ? ' ('.$cash->account->code.' - '.$cash->account->name.')':'' }}</td>
											@if(auth()->user()->role != 'AdminMonitor')
												<td>
													<a href="{{ route('school-cash-managements.edit', [$cash->school, $cash]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
													<form action="{{ route('school-cash-managements.destroy', [$cash->school, $cash]) }}" method="POST" style="display:inline;">
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

					</div>
				</div>
			</div>
		</div>
		<!-- Row end -->

	</div>
	<!-- App body ends -->
@endsection
