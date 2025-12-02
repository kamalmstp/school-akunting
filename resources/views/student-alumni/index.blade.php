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
			<li class="breadcrumb-item" aria-current="page">Alumni Siswa</li>
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
												@foreach($schools as $key => $schoolName)
													<option value="{{ $key }}" {{ $school == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
												@endforeach
											</select>
										</div>
									</div>
								@else
									<input type="hidden" id="schoolAdmin" value="{{ auth()->user()->school_id }}" />
								@endif
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="yearFilter" class="form-label">Alumni</label>
										<select name="year" class="form-select" id="yearFilter">
											<option value="">Pilih Alumni</option>
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="studentFilter" class="form-label">Siswa</label>
										<select name="student_id" class="form-select" id="studentFilter">
											<option value="">Pilih Siswa</option>
										</select>
									</div>
								</div>
							</div>
							<!-- Row end -->
							<div class="row gx-3 mt-3">
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="accountFilter" class="form-label">Akun</label>
										<select name="account" class="form-select" id="accountFilter">
											<option value="">Pilih Akun</option>
											@foreach (\App\Models\Account::where('code', 'like', '1-12%')->get() as $key => $accountData)
												<option value="{{ $accountData->id }}" {{ $account == $accountData->id ? 'selected' : '' }}>{{ $accountData->code }} - {{ $accountData->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-3 col-12">
									<div class="mb-3">
										<label for="dateFilter" class="form-label">Jatuh Tempo</label>
										<input type="date" class="form-control" id="dateFilter" name="date" value="{{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('Y-m-d') : '' }}">
									</div>
								</div>
								<div class="col-xl-4 col-md-3 col-12">
									<div class="mb-3">
										<label for="statusFilter" class="form-label">Status</label>
										<select name="status" class="form-select" id="statusFilter">
											<option value="">Pilih Status</option>
											@foreach (['Unpaid', 'Partial', 'Paid'] as $statusOption)
												<option value="{{ $statusOption }}" {{ $status == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>							
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('student-alumni.index') : route('school-student-alumni.index', $school) }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Alumni Siswa</h5>
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
							<table class="table align-middle">
								<thead>
									<tr>
										<th scope="col">No</th>
										@if (auth()->user()->role != 'SchoolAdmin')
										<th scope="col">Sekolah</th>
										@endif
                                        <th scope="col">Alumni</th>
                                        <th scope="col">Siswa</th>
										<th scope="col" class="text-end">Total Tagihan</th>
										<th scope="col" class="text-end">Terbayar</th>
										<th scope="col" class="text-end">Outstanding</th>
										<th scope="col" class="text-center">Status</th>
										<th scope="col" class="text-center">Aksi</th>
									</tr>
								</thead>
								<tbody>
									@forelse($students as $index => $student)
										<tr>
											<td>{{ $students->currentPage() * 10 - (9 - $index) }}</td>
											@if (auth()->user()->role != 'SchoolAdmin')
											<td>{{ $schools[$student->school_id] ?? '-' }}</td>
											@endif
											<td>{{ $student->year }}</td>
											<td>{{ $student->name }} ({{ $student->student_id_number }})</td>
											@php 
												$totalPayable = $student->receivables_sum_total_payable ?? 0;
												$totalPaid = $student->receivables_sum_paid_amount ?? 0;
												$outstanding = $totalPayable - $totalPaid;
											@endphp
											<td class="text-end">{{ number_format($totalPayable, 0, ',', '.') }}</td>
											<td class="text-end">{{ number_format($totalPaid, 0, ',', '.') }}</td>
											<td class="text-end">{{ number_format($outstanding, 0, ',', '.') }}</td>
										<td class="text-center">
											@if($outstanding <= 0)
												<span class="badge bg-success">Lunas</span>
											@else
												<span class="badge bg-danger">Tertunggak</span>
											@endif
										</td>
										<td class="text-center">
											@if($outstanding <= 0)
												<!-- Button untuk student lunas (ijazah) -->
												<button class="btn btn-sm btn-certificate" 
													data-student-id="{{ $student->id }}" 
													data-student-name="{{ $student->name }}"
													data-certificate-status="{{ $student->certificate_status }}"
													@if (auth()->user()->role == 'SchoolAdmin')
														data-school-id="{{ $school->id }}"
													@endif>
													@if($student->certificate_status === 'taken')
														<i class="bi bi-check-circle-fill"></i> Sudah Diambil
													@else
														<i class="bi bi-circle"></i> Belum Diambil
													@endif
												</button>
											@else
												<!-- Info tertunggak -->
												<span class="text-danger">
													<i class="bi bi-exclamation-circle"></i> Tertunggak
												</span>
											@endif
										</td>
										<td class="text-center">
											@if (auth()->user()->role == 'SchoolAdmin')
												<a href="{{ route('school-student-receivables.index', $school) }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-primary">Lihat Rincian</a>
											@else
												<a href="{{ route('student-receivables.index') }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-primary">Lihat Rincian</a>
											@endif
										</td>
										</tr>
									@empty
										<tr>
											<td colspan="9">Tidak ada siswa ditemukan</td>
										</tr>
									@endforelse
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
	<!-- Detail Modal -->
	<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Detail Pembayaran</h5>
				</div>
				<div class="modal-body"></div>
			</div>
		</div>
	</div>

	<!-- Certificate Status Modal -->
	<div class="modal fade" id="certificateModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Status Pengambilan Ijazah</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Nama Siswa: <strong id="modalStudentName"></strong></p>
					<div class="alert alert-info">
						Pilih status pengambilan ijazah untuk siswa ini:
					</div>
					<div class="btn-group w-100" role="group">
						<input type="radio" class="btn-check" name="certificateStatus" id="notTaken" value="not_taken" autocomplete="off">
						<label class="btn btn-outline-danger w-50" for="notTaken">
							<i class="bi bi-circle"></i> Belum Diambil
						</label>

						<input type="radio" class="btn-check" name="certificateStatus" id="taken" value="taken" autocomplete="off">
						<label class="btn btn-outline-success w-50" for="taken">
							<i class="bi bi-check-circle-fill"></i> Sudah Diambil
						</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="button" class="btn btn-primary" id="saveCertificateBtn">Simpan</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('js')
	<script>
		let currentStudentId = null;
		let currentSchoolId = @json(auth()->user()->role == 'SchoolAdmin' ? $school->id : null);

		$(document).ready(function(){
			const school = $('#schoolAdmin').val() || @json($school);
			if (@json(auth()->user()->role) != 'SchoolAdmin') {
				$('#schoolFilter').select2();
				$('#schoolFilter').on('change', function () {
					getStudent($(this).val(), @json($studentId));
					getYear($(this).val());
				})
			}
			$('#accountFilter').select2();
			$('#statusFilter').select2();
			if (school) {
				getStudent(school, @json($studentId))
				getYear(school);
			} else {
				$('#studentFilter').select2();
				$('#studentFilter').prop("disabled", true);
				$('#yearFilter').select2();
				$('#yearFilter').prop("disabled", true);
			}

			function getStudent(school, single) {
				$('#studentFilter').select2();
				if (school) {
					$('#studentFilter').prop("disabled", false);
					$.ajax({
						type:'POST',
						url:'/student-alumni/student/filter',
						data: {school},
						dataType: 'json',
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						success:function(data){
							let options = '<option value="">Pilih Siswa</option>';
							$.each(data, function(key, value) {
								if (single && parseInt(single) === value['id']) {
									options += '<option value=' + value['id'] + ' selected>' + value['name'] + '</option>';
								} else {
									options += '<option value=' + value['id'] + '>' + value['name'] + '</option>';
								}
							});
							$('#studentFilter').empty();
							$('#studentFilter').append(options);	
						}
					});
				} else {
					$('#studentFilter').prop("disabled", true);
				}
			}
			function getYear(school) {
				$('#yearFilter').select2();
				if (school) {
					$('#yearFilter').prop("disabled", false);
					$.ajax({
						type:'POST',
						url:'/student-alumni/year/filter',
						data: {school},
						dataType: 'json',
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						success:function(data){
							let options = '<option value="">Pilih Alumni</option>';
							$.each(data, function(key, value) {
								options += '<option value=' + value['year'] + (value['year']==@json($year)?' selected':'') + '>' + value['year'] + '</option>';
							});
							$('#yearFilter').empty();
							$('#yearFilter').append(options);	
						}
					});
				} else {
					$('#studentFilter').prop("disabled", true);
				}
			}

			// Certificate Status Button Handler
			$(document).on('click', '.btn-certificate', function() {
				currentStudentId = $(this).data('student-id');
				const studentName = $(this).data('student-name');
				const status = $(this).data('certificate-status');
				
				$('#modalStudentName').text(studentName);
				
				// Set radio button
				$('input[name="certificateStatus"][value="' + status + '"]').prop('checked', true);
				
				const certificateModal = new bootstrap.Modal(document.getElementById('certificateModal'));
				certificateModal.show();
			});

			// Save Certificate Status
			$('#saveCertificateBtn').click(function() {
				const status = $('input[name="certificateStatus"]:checked').val();
				
				if (!status) {
					alert('Pilih status terlebih dahulu');
					return;
				}

				const url = currentSchoolId 
					? `/schools/${currentSchoolId}/student-alumni/${currentStudentId}/certificate-status`
					: `/schools/1/student-alumni/${currentStudentId}/certificate-status`; // default untuk superadmin

				$.ajax({
					type: 'POST',
					url: url,
					data: {
						certificate_status: status,
						_token: $('meta[name="csrf-token"]').attr('content')
					},
					success: function(response) {
						// Update button UI
						const btn = $(`button[data-student-id="${currentStudentId}"]`);
						if (status === 'taken') {
							btn.html('<i class="bi bi-check-circle-fill"></i> Sudah Diambil');
							btn.removeClass('btn-outline-danger').addClass('btn-outline-success');
						} else {
							btn.html('<i class="bi bi-circle"></i> Belum Diambil');
							btn.removeClass('btn-outline-success').addClass('btn-outline-danger');
						}
						btn.data('certificate-status', status);

						// Close modal and show success
						const certificateModal = bootstrap.Modal.getInstance(document.getElementById('certificateModal'));
						certificateModal.hide();
						
						const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
							'Status ijazah berhasil diperbarui' +
							'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
							'</div>');
						$('.card-body').prepend(successAlert);
						setTimeout(() => successAlert.fadeOut(() => successAlert.remove()), 3000);
					},
					error: function(xhr) {
						alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Gagal memperbarui status'));
					}
				});
			});
		})
	</script>
@endsection