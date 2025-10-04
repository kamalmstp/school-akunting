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
			<li class="breadcrumb-item" aria-current="page">Kelola Sekolah</li>
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
										<label for="school_id" class="form-label">Filter Sekolah</label>
										<select name="school_id" class="form-select" id="schoolFilter">
											<option value="">Pilih Sekolah</option>
											@foreach($allSchools as $key => $schoolName)
												<option value="{{ $key }}" {{ $schoolId == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="status" class="form-label">Status</label>
										<select name="status" class="form-select" id="status">
											<option value="">Pilih Status</option>
											<option value="1" {{ $status == '1' ? 'selected' : '' }}>Aktif</option>
											<option value="0" {{ $status == '0' ? 'selected' : '' }}>Tidak Aktif</option>
										</select>
									</div>
								</div>
							</div>
							<!-- Row end -->
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ route('schools.index') }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Sekolah</h5>
							@if(!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
                            <a href="{{ route('schools.create') }}" class="btn btn-primary" title="Tambah Sekolah">
								<span class="d-lg-block d-none">Tambah Sekolah</span>
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
										<th>No</th>
										<th>Nama Sekolah</th>
										<th>Email</th>
										<th>Telepon</th>
										<th>Alamat</th>
										<th>Status</th>
										@if(!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))<th></th>@endif
									</tr>
								</thead>
								<tbody>
									@forelse($schools as $index => $school)
										<tr>
											<td>{{ $schools->currentPage() * 10 - (9 - $index) }}</td>
											<td>{{ $school->name }}</td>
											<td>{{ $school->email ?? '-' }}</td>
											<td>{{ $school->phone ?? '-' }}</td>
											<td>{{ $school->address ?? '-' }}</td>
											<td>{{ $school->status ? 'Aktif' : 'Tidak Aktif' }}</td>
											@if(!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
											<td>
												<a href="{{ route('schools.edit', $school) }}" class="btn btn-sm btn-outline-primary">Edit</a>
												<form action="{{ route('schools.destroy', $school) }}" method="POST" style="display:inline;">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus sekolah ini?')">Hapus</button>
												</form>
											</td>
											@endif
										</tr>
									@empty
										<tr>
											<td colspan="7">Belum ada sekolah</td>
										</tr>										
									@endempty
                                </tbody>
							</table>
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $schools->links() }}
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
			$('#status').select2();
			$('#schoolFilter').select2();
		});
	</script>
@endsection