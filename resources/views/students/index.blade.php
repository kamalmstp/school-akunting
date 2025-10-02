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
			<li class="breadcrumb-item" aria-current="page">Kelola Siswa</li>
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
											<select name="school" class="form-select" id="schoolFilter">
												<option value="">Pilih Sekolah</option>
												@foreach(\App\Models\School::pluck('name', 'id') as $key => $schoolName)
													<option value="{{ $key }}" {{ $schoolId == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
												@endforeach
											</select>
										</div>
									</div>
								@endif
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="nis" class="form-label">NIS</label>
										<input type="text" class="form-control" id="nis" name="nis" value="{{ $studentNumber }}">
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="name" class="form-label">Nama Siswa</label>
										<input type="text" class="form-control" id="name" name="name" value="{{ $studentName }}">
									</div>
								</div>
							</div>							
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('students.index') : route('school-students.index', $school) }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Siswa</h5>
							@if(auth()->user()->role != 'AdminMonitor')
							<div>
                                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('students.create') : route('school-students.create', $school) }}" class="btn btn-primary" title="Tambah Siswa">
									<span class="d-lg-block d-none">Tambah Siswa</span>
									<span class="d-sm-block d-lg-none">
										<i class="bi bi-plus"></i>
									</span>
								</a>
                                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('students.import-form') : route('school-students.import-form', $school) }}" class="btn btn-success" title="Import Excel">
									<span class="d-lg-block d-none">Import Excel</span>
									<span class="d-sm-block d-lg-none">
										<i class="bi bi-upload"></i>
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
										<th>NIS</th>
										<th>NISN</th>
                                        <th>Nama</th>
                                        <th>Telepon</th>
                                        <th>Alamat</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        @if(auth()->user()->role != 'AdminMonitor')<th></th>@endif
									</tr>
								</thead>
								<tbody>
									@forelse($students as $index => $student)
                                        <tr>
											<td>{{ $students->currentPage() * 10 - (9 - $index) }}</td>
                                            <td>{{ $student->student_id_number }}</td>
                                            <td>{{ $student->national_student_number }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->phone }}</td>
                                            <td>{{ $student->address }}</td>
                                            <td>{{ $student->class }}</td>
                                            <td>{{ $student->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
											@if(auth()->user()->role != 'AdminMonitor')
												<td>
													<a href="{{ route('school-students.edit', [$student->school, $student]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
													<!-- <form action="{{ route('school-students.destroy', [$student->school, $student]) }}" method="POST" style="display:inline;">
														@csrf
														@method('DELETE')
														<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus siswa ini?')">Hapus</button>
													</form> -->
												</td>
											@endif
                                        </tr>
									@empty
										<tr>
											<td colspan="{{ auth()->user()->role != 'AdminMonitor' ? '9' : '8'}}">Belum ada siswa</td>
										</tr>										
									@endempty
                                </tbody>
							</table>
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $students->links() }}
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
			if (@json(auth()->user()->role != 'SchoolAdmin')) {
				$('#schoolFilter').select2();
			}
		})
	</script>
@endsection