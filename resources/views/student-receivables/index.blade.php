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
			<li class="breadcrumb-item" aria-current="page">Piutang Siswa</li>
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
													<option value="{{ $key }}" {{ $schoolId == $key ? 'selected' : '' }}>{{ $schoolName }}</option>
												@endforeach
											</select>
										</div>
									</div>
								@else
									<input type="hidden" id="schoolAdmin" value="{{ auth()->user()->school_id }}" />
								@endif
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
											@if(auth()->user()->role == 'SchoolAdmin')
												@foreach (\App\Models\Account::where('code', 'like', '1-12%')->where('school_id', '=', auth()->user()->school_id)->get() as $key => $accountData)
													<option value="{{ $accountData->id }}" {{ $account == $accountData->id ? 'selected' : '' }}>{{ $accountData->code }} - {{ $accountData->name }}</option>
												@endforeach
											@else
												@if($schoolId)
													@foreach (\App\Models\Account::where('code', 'like', '1-12%')->where('school_id', $schoolId)->get() as $key => $accountData)
														<option value="{{ $accountData->id }}" {{ $account == $accountData->id ? 'selected' : '' }}>{{ $accountData->code }} - {{ $accountData->name }}</option>
													@endforeach
												@endif
											@endif
										</select>
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="dateFilter" class="form-label">Jatuh Tempo</label>
										<input type="date" class="form-control" id="dateFilter" name="date" value="{{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('Y-m-d') : '' }}">
									</div>
								</div>
								<div class="col-xl-4 col-md-6 col-12">
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
									<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('student-receivables.index') : route('school-student-receivables.index', $school) }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Piutang Siswa</h5>
							@if(!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
								<div class="d-flex gap-2">
									<a href="{{ auth()->user()->role == 'SuperAdmin' ? route('student-receivables.create') : route('school-student-receivables.create', $school) }}" class="btn btn-primary" title="Tambah Penerimaan">
										<span class="d-lg-block d-none">Tambah Penerimaan</span>
										<span class="d-sm-block d-lg-none">
											<i class="bi bi-plus"></i>
										</span>
									</a>
									@if(auth()->user()->role == 'SchoolAdmin')
										<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal" title="Import Pembayaran">
											<span class="d-lg-block d-none">Import Pembayaran</span>
											<span class="d-sm-block d-lg-none">
												<i class="bi bi-upload"></i>
											</span>
										</button>
									@endif
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
							<table class="table align-middle">
								<thead>
									<tr>
										<th scope="col">No</th>
										@if (auth()->user()->role != 'SchoolAdmin') <th scope="col">Sekolah</th> @endif
                                        <th scope="col">Siswa</th>
                                        <th scope="col">Akun</th>
                                        <th scope="col" class="text-end">Jumlah</th>
                                        <th scope="col" class="text-end">Terbayar</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Jatuh Tempo</th>
                                        @if (!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas'])) <th scope="col"></th> @endif
									</tr>
								</thead>
								<tbody>
									@forelse($receivables as $index => $receivable)
                                        <tr>
											<td>{{ $receivables->currentPage() * 10 - (9 - $index) }}</td>
                                            @if (auth()->user()->role != 'SchoolAdmin')<td>{{ $receivable->school ? $receivable->school->name : 'N/A' }}</td>@endif
                                            <td>
												<a href="javascript:void;" class="text-decoration-none" data-bs-toggle="collapse" data-bs-target="#details{{ $receivable->id }}">
													{{ $receivable->student ? $receivable->student->name : 'N/A' }} ({{ $receivable->student ? $receivable->student->student_id_number : 'N/A' }})
												</a>
											</td>
                                            <td>{{ $receivable->account ? $receivable->account->code . ' - ' . $receivable->account->name : 'N/A' }}</td>
                                            <td class="text-end">{{ number_format($receivable->total_payable, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($receivable->paid_amount, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $receivable->status === 'Paid' ? 'success' : ($receivable->status === 'Partial' ? 'warning' : 'danger') }}">
                                                    {{ $receivable->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $receivable->due_date ? \Carbon\Carbon::parse($receivable->due_date)->format('d-m-Y') : '-' }}</td>
                                            @if (!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
                                                <td class="text-center">
                                                    @if($receivable->status !== 'Paid' && $receivable->school)
                                                        <a href="{{ route('school-student-receivables.pay', [$receivable->school, $receivable]) }}" class="btn btn-sm btn-success">Bayar</a>
													@else
														@if($receivable->school)
															<a href="{{ route('school-student-receivables.receipt-all', [$receivable->school, $receivable]) }}" class="btn btn-sm btn-primary">Kwitansi</a>
														@endif
                                                    @endif
                                                    @if($receivable->school)
														<a href="{{ route('school-student-receivables.edit', [$receivable->school, $receivable]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    @endif
                                                    @if($receivable->school)
														<form action="{{ route('school-student-receivables.destroy', [$receivable->school, $receivable]) }}" method="POST" style="display:inline;">
															@csrf
															@method('DELETE')
															<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus Piutang ini?')">Hapus</button>
														</form>
													@endif
                                                </td> 
                                            @endif
                                        </tr>
										<tr class="collapse" id="details{{ $receivable->id }}">
                                            <td colspan="{{!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']) ? '9' : '8' }}">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Tanggal Pembayaran</th>
                                                            <th scope="col">Deskripsi</th>
                                                            <th scope="col" class="text-end">Jumlah Pembayaran</th>
															<th scope="col">Alasan Perubahan</th>
															@if (!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))<th scope="col"></th>@endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                        @forelse($receivable->student_receivable_details as $detail)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($detail->period)->format('d-m-Y') }}</td>
                                                                <td>{{ $detail->description }}</td>
                                                                <td class="text-end">{{ number_format($detail->amount, 0, ',', '.') }}</td>
																<td>{{ $detail->reason ?? '-' }}</td>
																@if (!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
																<td class="text-center">
																	@if($receivable->school)
																		<a href="{{ route('school-student-receivables.receipt', [$receivable->school, $detail]) }}" class="btn btn-sm btn-info">Kwitansi</a>
																		<a href="{{ route('school-student-receivables.edit-pay', [$receivable->school, $detail]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
																	@endif
																	<a href="https://wa.me/{{ preg_replace('/^0/', '62', $receivable->student->phone) }}?text=halo%2C%20berikut%20kami%20sampaikan%20kwitansi%20pembayaran%20bpk%2Fibu" class="btn btn-sm btn-outline-success" target="_blank" title="Kirim WhatsApp">WA</a>
																</td>
																@endif
                                                            </tr>
														@empty
															<tr>
																<td colspan="{{ !in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']) ? '5' : '4' }}">Belum pembayaran piutang</td>
															</tr>
                                                        @endforelse
                                                        </tr>
                                                    </tbody>
                                                </table>
												<div class="d-flex gap-4">
													<span><strong>Piutang</strong> : {{ number_format($receivable->total_payable, 0, ',', '.') }}</span>
													<span><strong>Terbayar</strong> : {{ number_format($receivable->paid_amount, 0, ',', '.') }}</span>
													<span><strong>Belum Terbayar</strong> : {{ number_format($receivable->total_payable - $receivable->paid_amount, 0, ',', '.') }}</span>
												</div>
                                            </td>
                                        </tr>
									@empty
										<tr>
											<td colspan="{{ !in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']) ? '9' : '8' }}">Belum ada piutang</td>
										</tr>										
									@endempty
                                </tbody>
							</table>	
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $receivables->links() }}
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

	<!-- Import Modal -->
	<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Import Piutang Siswa</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="importForm" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<div class="mb-3">
							<label for="importFile" class="form-label">Pilih File Excel</label>
							<div class="input-group">
								<input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls" required>
								@if(auth()->user()->role == 'SchoolAdmin' && isset($school))
									<a href="{{ asset('templates/template_import_piutang_siswa.xlsx') }}" class="btn btn-outline-secondary" title="Download Template" target="_blank" rel="noopener">
										<i class="bi bi-download"></i> Template
									</a>
								@endif
							</div>
							<small class="form-text text-muted">Format yang didukung: Excel (.xlsx, .xls)</small>
						</div>
						<div class="alert alert-info" role="alert">
							<strong>Format File:</strong>
							<table class="table table-sm mb-0 mt-2">
								<thead>
									<tr>
										<th>Kolom</th>
										<th>Nama</th>
										<th>Contoh</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>Nomor Induk Siswa</td>
										<td>001234</td>
									</tr>
									<tr>
										<td>2</td>
										<td>Kode Akun Piutang</td>
										<td>1-120001-1</td>
									</tr>
									<tr>
										<td>3</td>
										<td>Kode Akun Pendapatan</td>
										<td>4-120001-1</td>
									</tr>
									<tr>
										<td>4</td>
										<td>Jumlah Piutang</td>
										<td>500000</td>
									</tr>
									<tr>
										<td>5</td>
										<td>Deskripsi (Opsional)</td>
										<td>SPP November 2025</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-primary">Import</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@section('js')
	<script>
		$(document).ready(function(){
			const school = $('#schoolAdmin').val() || @json($school ? $school->id : null);
			if (@json(auth()->user()->role) != 'SchoolAdmin') {
				$('#schoolFilter').select2();
				$('#schoolFilter').on('change', function () {
					getStudent($(this).val(), @json($studentId));
					getAccount($(this).val(), @json($account));
				})
			}
			$('#accountFilter').select2();
			$('#statusFilter').select2();
			if (school) {
				getStudent(school, @json($studentId))
				getAccount(school, @json($account))
			} else {
				$('#studentFilter').select2(); 
				$('#studentFilter').prop("disabled", true);
			}

			// Handle Import Form
			$('#importForm').on('submit', function(e) {
				e.preventDefault();
				const formData = new FormData(this);
				const school = $('#schoolAdmin').val() || @json($school->id ?? null);
				const url = '/schools/' + school + '/student-receivables/import';
				
				$.ajax({
					type: 'POST',
					url: url,
					data: formData,
					processData: false,
					contentType: false,
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: function(response) {
						$('#importModal').modal('hide');
						$('#importForm')[0].reset();
						setTimeout(() => {
							location.reload();
						}, 1000);
					},
					error: function(xhr) {
						let message = 'Terjadi kesalahan saat import file';
						if (xhr.responseJSON && xhr.responseJSON.message) {
							message = xhr.responseJSON.message;
						}
						alert(message);
					}
				});
			});

			function getStudent(school, single) {
				$('#studentFilter').select2();
				if (school) {
					$('#studentFilter').prop("disabled", false);
					$.ajax({
						type:'POST',
						url:'/student-receivables/student/filter',
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

			function getAccount(school, single) {
				if (school) {
					$.ajax({
						type:'POST',
						url:'/student-receivables/account/filter',
						data: {school},
						dataType: 'json',
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						success:function(data){
							let options = '<option value="">Pilih Akun</option>';
							$.each(data, function(key, value) {
								if (single && parseInt(single) === value['id']) {
									options += '<option value=' + value['id'] + ' selected>' + value['code'] + ' - ' + value['name'] + '</option>';
								} else {
									options += '<option value=' + value['id'] + '>' + value['code'] + ' - ' + value['name'] + '</option>';
								}
							});
							$('#accountFilter').empty();
							$('#accountFilter').append(options);	
						}
					});
				} else {
					$('#accountFilter').empty();
					$('#accountFilter').append('<option value="">Pilih Akun</option>');
				}
			}
		})
	</script>
@endsection