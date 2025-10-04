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
			<li class="breadcrumb-item" aria-current="page">Kelola Pengguna - Edit</li>
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
							<h5 class="card-title">Edit Pengguna</h5>
						</div>
					</div>
					<div class="card-body">
                        <form action="{{ route('users.update', $user) }}" method="POST">
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
                                                    <label for="name" class="form-label">Nama</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
                                                    @error('name')
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
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                                                    @error('email')
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
                                                    <label for="phone" class="form-label">Telepon</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                                    @error('phone')
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
                                                    <label for="password" class="form-label">Kata Sandi Baru (opsional)</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                                    @error('password')
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
                                                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
                                                    @error('password_confirmation')
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
                                                    <label for="role" class="form-label">Role</label>
                                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                                        <option value="SuperAdmin" {{ old('role', $user->role) === 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                                                        <option value="Pengawas" {{ old('role', $user->role) === 'Pengawas' ? 'selected' : '' }}>Pengawas</option>
                                                        <option value="AdminMonitor" {{ old('role', $user->role) === 'AdminMonitor' ? 'selected' : '' }}>AdminMonitor</option>
                                                        <option value="SchoolAdmin" {{ old('role', $user->role) === 'SchoolAdmin' ? 'selected' : '' }}>SchoolAdmin</option>
                                                    </select>
                                                    @error('role')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3" id="school_id_container">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="school_id" class="form-label">Sekolah</label>
                                                    <select class="form-select @error('school_id') is-invalid @enderror" id="school_id" name="school_id">
                                                        <option value="">Pilih Sekolah</option>
                                                        @foreach($schools as $school)
                                                            <option value="{{ $school->id }}" {{ old('school_id', $user->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('school_id')
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
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" id="status" name="status">
                                                        <option value="1" {{ $user->status ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ $user->status ? '' : 'selected' }}>Tidak Aktif</option>
                                                    </select>
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-12">
                                    <div class="text-start">
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                        <a href="{{ route('users.index') }}" class="btn btn-outline-success ms-1">Batal</a>
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

    <script>
        document.getElementById('role').addEventListener('change', function() {
            const schoolIdContainer = document.getElementById('school_id_container');
            schoolIdContainer.style.display = this.value === 'SchoolAdmin' ? 'block' : 'none';
        });
        document.getElementById('role').dispatchEvent(new Event('change'));
    </script>
@endsection
@section('js')
	<script>
		$(document).ready(function() {
			$('#role').select2();
            $('#status').select2();
            const role = $('#role').val();
            if (role === 'SchoolAdmin') {
                $('#school_id').select2();
            }
		})
	</script>
@endsection