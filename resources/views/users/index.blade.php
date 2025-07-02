@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
			<li class="breadcrumb-item">
				<i class="bi bi-pie-chart lh-1"></i>
				<a href="{{ auth()->user()->role == 'SuperAdmin' ? route('dashboard') : route('dashboard.index') }}" class="text-decoration-none">Dashboard</a>
			</li>
			<li class="breadcrumb-item" aria-current="page">Kelola Pengguna</li>
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
										<label for="schoolFilter" class="form-label">Filter Sekolah</label>
										<select name="school" class="form-select" id="schoolFilter">
											<option value="">Pilih Sekolah</option>
											@foreach(\App\Models\School::pluck('name', 'id') as $key => $schoolName)
												<option value="{{ $key }}" {{ $school == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="role" class="form-label">Role</label>
										<select name="role" class="form-select" id="role">
											<option value="">Pilih Role</option>
											<option value="SuperAdmin" {{ $role == 'SuperAdmin' ? 'selected' : '' }}>Super Admin</option>
											<option value="AdminMonitor" {{ $role == 'AdminMonitor' ? 'selected' : '' }}>Admin Monitor</option>
											<option value="SchoolAdmin" {{ $role == 'SchoolAdmin' ? 'selected' : '' }}>Admin Sekolah</option>
										</select>
									</div>
								</div>
							</div>
							<!-- Row end -->
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ route('users.index') }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Pengguna</h5>
							<a href="{{ route('users.create') }}" class="btn btn-primary" title="Tambah Pengguna">
								<span class="d-lg-block d-none">Tambah Pengguna</span>
								<span class="d-sm-block d-lg-none">
									<i class="bi bi-plus"></i>
								</span>
							</a>
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
										<th>Nama</th>
                                        <th>Email</th>
										<th>Telepon</th>
                                        <th>Role</th>
                                        <th>Sekolah</th>
										<th>Status</th>
                                        <th></th>
									</tr>
								</thead>
								<tbody>
									@forelse($users as $index => $user)
                                        <tr>
											<td>{{ $users->currentPage() * 10 - (9 - $index) }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
											<td>{{ $user->phone }}</td>
                                            <td>
												@if ($user->role == 'SuperAdmin')
													Super Admin
												@elseif ($user->role == 'AdminMonitor')
													Admin Monitor
												@else
													Admin Sekolah
												@endif
											</td>
                                            <td>{{ $user->school ? $user->school->name : '-' }}</td>
											<td>{{ $user->status ? 'Aktif' : 'Tidak Aktif' }}</td>
                                            <td>
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</button>
                                                </form>
                                                {{-- <a href="{{ route('users.reset-password', $user) }}" class="btn btn-sm btn-secondary" onclick="return confirm('Yakin ingin reset kata sandi?')">Reset Kata Sandi</a> --}}
                                            </td>
                                        </tr>
									@empty
										<tr>
											<td colspan="5">Belum ada pengguna</td>
										</tr>										
									@endempty
                                </tbody>
							</table>	
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $users->links() }}
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
			$('#schoolFilter').select2();
			$('#role').select2();
		})
	</script>
@endsection