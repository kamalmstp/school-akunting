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
			<li class="breadcrumb-item" aria-current="page">Kelola Karyawan - Edit</li>
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
							<h5 class="card-title">Edit Karyawan - {{ $school->name }}</h5>
						</div>
					</div>
					<div class="card-body">
                        <form action="{{ route('school-employees.update', [$school, $employee]) }}" method="POST">
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
                                                    <label for="employee_id_number" class="form-label">NIK Karyawan</label>
                                                    <input type="text" class="form-control @error('employee_id_number') is-invalid @enderror" id="employee_id_number" name="employee_id_number" value="{{ old('employee_id_number', $employee->employee_id_number) }}">
                                                    @error('employee_id_number')
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
                                                    <label for="nik" class="form-label">NIK KTP Karyawan</label>
                                                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $employee->nik) }}">
                                                    @error('nik')
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
                                                    <label for="name" class="form-label">Nama Karyawan</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $employee->name) }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <!-- Pendidikan Terakhir -->
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="education" class="form-label">Pendidikan Terakhir</label>
                                                    <select class="form-select @error('education') is-invalid @enderror" id="education" name="education">
                                                        @php
                                                        $selectedEdu = old('education', $employee->education ?? '');
                                                        @endphp
                                                        <option value="" {{ $selectedEdu == '' ? 'selected' : '' }}>- Pilih -</option>
                                                        <option value="SD" {{ $selectedEdu == 'SD' ? 'selected' : '' }}>SD/MI</option>
                                                        <option value="SMP" {{ $selectedEdu == 'SMP' ? 'selected' : '' }}>SMP/MTs</option>
                                                        <option value="SMA" {{ $selectedEdu == 'SMA' ? 'selected' : '' }}>SMA/SMK/MA</option>
                                                        <option value="D1" {{ $selectedEdu == 'D1' ? 'selected' : '' }}>Diploma I (D1)</option>
                                                        <option value="D2" {{ $selectedEdu == 'D2' ? 'selected' : '' }}>Diploma II (D2)</option>
                                                        <option value="D3" {{ $selectedEdu == 'D3' ? 'selected' : '' }}>Diploma III (D3)</option>
                                                        <option value="D4" {{ $selectedEdu == 'D4' ? 'selected' : '' }}>Diploma IV (D4)</option>
                                                        <option value="S1" {{ $selectedEdu == 'S1' ? 'selected' : '' }}>Sarjana (S1)</option>
                                                        <option value="S2" {{ $selectedEdu == 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                                                        <option value="S3" {{ $selectedEdu == 'S3' ? 'selected' : '' }}>Doktor (S3)</option>
                                                    </select>
                                                    @error('education')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>

                                            <!-- TMT -->
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="tmt" class="form-label">TMT (Tahun Mulai Tugas)</label>
                                                    <input type="number" min="1950" max="{{ date('Y') }}" class="form-control @error('tmt') is-invalid @enderror" id="tmt" name="tmt" value="{{ old('tmt', $employee->tmt ?? '') }}">
                                                    @error('tmt')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Masa Kerja -->
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="work_period" class="form-label">Masa Kerja (Per 1 Juli)</label>
                                                    @php
                                                    use Carbon\Carbon;

                                                    $tmtYear = old('tmt', $employee->tmt ?? null);
                                                    $workPeriod = '';

                                                    if ($tmtYear && is_numeric($tmtYear)) {
                                                        // Mulai dari 1 Juli tahun TMT
                                                        $startDate = Carbon::create($tmtYear, 7, 1);
                                                        $today = Carbon::today();

                                                        if ($startDate->greaterThan($today)) {
                                                            $workPeriod = '0 tahun 0 bulan 0 hari';
                                                        } else {
                                                            $diff = $startDate->diff($today);
                                                            $workPeriod = "{$diff->y} tahun {$diff->m} bulan {$diff->d} hari";
                                                        }
                                                    }
                                                    @endphp
                                                    <input type="text" class="form-control" id="work_period" name="work_period" value="{{ $workPeriod }}" readonly>
                                                </div>
                                            </div>

                                            <!-- Sertifikasi -->
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="certification" class="form-label">Sertifikasi</label>
                                                    <select class="form-select @error('certification') is-invalid @enderror" id="certification" name="certification">
                                                        @php
                                                        $selectedCert = old('certification', $employee->certification ?? '');
                                                        @endphp
                                                        <option value="" {{ $selectedCert == '' ? 'selected' : '' }}>- Pilih -</option>
                                                        <option value="YA" {{ $selectedCert == 'YA' ? 'selected' : '' }}>YA</option>
                                                        <option value="TIDAK" {{ $selectedCert == 'TIDAK' ? 'selected' : '' }}>TIDAK</option>
                                                    </select>
                                                    @error('certification')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Status Kepegawaian -->
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="employment_status" class="form-label">Status Kepegawaian</label>
                                                    <select class="form-select @error('employment_status') is-invalid @enderror" id="employment_status" name="employment_status">
                                                        @php
                                                        $selectedStatus = old('employment_status', $employee->employment_status ?? '');
                                                        @endphp
                                                        <option value="" {{ $selectedStatus == '' ? 'selected' : '' }}>- Pilih -</option>
                                                        <option value="PTY" {{ $selectedStatus == 'PTY' ? 'selected' : '' }}>Pegawai Tetap Yayasan</option>
                                                        <option value="PTT" {{ $selectedStatus == 'PTT' ? 'selected' : '' }}>Pegawai Tidak Tetap</option>
                                                    </select>
                                                    @error('employment_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email Karyawan</label>
                                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->email) }}">
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
                                                    <label for="phone" class="form-label">Telepon Karyawan</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
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
                                                    <label for="address" class="form-label">Alamat</label>
                                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address">{{ old('address', $employee->address) }}</textarea>
                                                    @error('address')
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
                                                        <option value="1" {{ old('is_active', $employee->is_active) == true ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ old('is_active', $employee->is_active) == false ? 'selected' : '' }}>Tidak Aktif</option>
                                                    </select>
                                                    @error('is_active')
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
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('employees.index') : route('school-employees.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
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
        })
        document.getElementById('tmt').addEventListener('input', function () {
            const tmtYear = parseInt(this.value);
            const today = new Date();
            let workPeriod = '';

            if (!isNaN(tmtYear)) {
                // Asumsikan TMT adalah 1 Juli dari tahun yang diinput
                const startDate = new Date(tmtYear, 6, 1); // Bulan 6 = Juli (0-indexed)
                const diffTime = today - startDate;

                if (diffTime > 0) {
                    const diffDate = new Date(diffTime);
                    const years = diffDate.getUTCFullYear() - 1970;
                    const months = diffDate.getUTCMonth();
                    const days = diffDate.getUTCDate() - 1;
                    workPeriod = `${years} tahun ${months} bulan ${days} hari`;
                } else {
                    workPeriod = '0 tahun 0 bulan 0 hari';
                }
            }

            document.getElementById('work_period').value = workPeriod;
        });
    </script>
@endsection