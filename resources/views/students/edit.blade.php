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
			<li class="breadcrumb-item" aria-current="page">Kelola Siswa - Edit</li>
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
							<h5 class="card-title">Edit Siswa - {{ $school->name }}</h5>
						</div>
					</div>
					<div class="card-body">
                        <form action="{{ route('school-students.update', [$school, $student]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="student_id_number" class="form-label">NIS</label>
                                                    <input type="text" class="form-control @error('student_id_number') is-invalid @enderror" id="student_id_number" name="student_id_number" value="{{ old('student_id_number', $student->student_id_number) }}">
                                                    @error('student_id_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="national_student_number" class="form-label">NISN</label>
                                                    <input type="text" class="form-control @error('national_student_number') is-invalid @enderror" id="national_student_number" name="national_student_number" value="{{ old('national_student_number', $student->national_student_number) }}">
                                                    @error('national_student_number')
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
                                                    <label for="name" class="form-label">Nama Siswa</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $student->name) }}">
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
                                                    <label for="phone" class="form-label">Telepon Siswa</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
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
                                                    <label for="parent_name" class="form-label">Nama Orang Tua</label>
                                                    <input type="text" class="form-control @error('parent_name') is-invalid @enderror" id="parent_name" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}">
                                                    @error('parent_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="parent_phone" class="form-label">Telepon Orang Tua</label>
                                                    <input type="text" class="form-control @error('parent_phone') is-invalid @enderror" id="parent_phone" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}">
                                                    @error('parent_phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="parent_mail" class="form-label">Email Orang Tua</label>
                                                    <input type="email" class="form-control @error('parent_mail') is-invalid @enderror" id="parent_mail" name="parent_mail" value="{{ old('parent_mail', $student->parent_mail) }}">
                                                    @error('parent_mail')
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
                                                    <label for="parent_job" class="form-label">Pekerjaan Orang Tua</label>
                                                    <input type="text" class="form-control @error('parent_job') is-invalid @enderror" id="parent_job" name="parent_job" value="{{ old('parent_job', $student->parent_job) }}">
                                                    @error('parent_job')
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
                                                    <label for="address" class="form-label">Alamat</label>
                                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address">{{ old('address', $student->address) }}</textarea>
                                                    @error('address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="class" class="form-label">Kelas</label>
                                                    <select class="form-select @error('class') is-invalid @enderror" id="class" name="class">
                                                        <option value="">Pilih Kelas</option>
                                                        @foreach($classes as $class)
                                                            <option value="{{ $class }}" {{ old('class', $student->class) == $class ? 'selected' : '' }}>{{ $class }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('is_active')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="year" class="form-label">Tahun Masuk</label>
                                                    <input type="number" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year', $student->year) }}" min="2000" max="2025">
                                                    @error('year')
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
                                                    <label for="is_active" class="form-label">Status</label>
                                                    <select class="form-select @error('is_active') @enderror" id="is_active" name="is_active">
                                                        <option value="1" {{ old('is_active', $student->is_active) == true ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ old('is_active', $student->is_active) == false ? 'selected' : '' }}>Tidak Aktif</option>
                                                    </select>
                                                    @error('is_active')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="is_alumni" class="form-label">Kategori</label>
                                                    <select class="form-select @error('is_alumni') is-invalid @enderror" id="is_alumni" name="is_alumni">
                                                        <option value="">Pilih Kategori</option>
                                                        <option value="0" {{ old('is_alumni', $student->is_alumni) == 0 ? 'selected' : '' }}>Peserta Didik</option>
                                                        <option value="1" {{ old('is_alumni', $student->is_alumni) == 1 ? 'selected' : '' }}>Alumni</option>
                                                    </select>
                                                    @error('is_alumni')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="graduation_year" class="form-label">Tahun Lulus</label>
                                                    <input type="number" class="form-control @error('graduation_year') is-invalid @enderror" id="graduation_year" name="graduation_year" value="{{ old('graduation_year', $student->graduation_year) }}" min="2000" max="{{ date('Y') }}">
                                                    @error('graduation_year')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('students.index') : route('school-students.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
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
            $('#is_active').select2();
            $('#is_alumni').select2();
            $('#class').select2();
        })
    </script>
@endsection