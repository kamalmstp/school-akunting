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
                                        <th scope="col">Akun</th>
                                        <th scope="col" class="text-end">Jumlah</th>
                                        <th scope="col" class="text-end">Terbayar</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Jatuh Tempo</th>
									</tr>
								</thead>
								<tbody>
									@forelse($students as $index => $student)
										@php $rowSpan = max(count($student->receivables), 1); @endphp
									    @if(count($student->receivables))
									        @foreach($student->receivables as $i => $receivable)
									            <tr>
									                @if($i == 0)
									                    <td rowspan="{{ $rowSpan }}">{{ $students->currentPage() * 10 - (9 - $index) }}</td>
									                    @if (auth()->user()->role != 'SchoolAdmin')
											            <td rowspan="{{ $rowSpan }}">{{ $schools[$student->school_id] ?? '-' }}</td>
											            @endif
									                    <td rowspan="{{ $rowSpan }}">{{ $student->year }}</td>
									                    <td rowspan="{{ $rowSpan }}">{{ $student->name }} ({{ $student->student_id_number }})</td>
									                @endif
									                <td>{{ $receivable->account->code }} - {{ $receivable->account->name }}</td>
									                <td class="text-end">{{ number_format($receivable->total_payable, 0, ',', '.') }}</td>
									                <td class="text-end">{{ number_format($receivable->paid_amount, 0, ',', '.') }}</td>
									                <td class="text-center">
									                    <span class="badge bg-{{ $receivable->status === 'Paid' ? 'success' : ($receivable->status === 'Partial' ? 'warning' : 'danger') }}">
									                        {{ $receivable->status }}
									                    </span>
									                </td>
									                <td class="text-center">{{ \Carbon\Carbon::parse($receivable->due_date)->format('d-m-Y') }}</td>
									            </tr>
									        @endforeach
									    @else
									        <tr>
									            <td>{{ $students->currentPage() * 10 - (9 - $index) }}</td>
									            @if (auth()->user()->role != 'SchoolAdmin')
									            <td>{{ $schools[$student->school_id] ?? '-' }}</td>
									            @endif
									            <td>{{ $student->year }}</td>
									            <td>{{ $student->name }} ({{ $student->student_id_number }})</td>
									            <td colspan="5" class="text-center">Tidak ada piutang</td>
									        </tr>
									    @endif
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
@endsection
@section('js')
	<script>
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
		})
	</script>
@endsection