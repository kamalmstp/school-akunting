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
			<li class="breadcrumb-item" aria-current="page">Piutang Karyawan</li>
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
										<label for="employeeFilter" class="form-label">Karyawan</label>
										<select name="employee_id" class="form-select" id="employeeFilter">
											<option value="">Pilih Karyawan</option>
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
									<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('employee-receivables.index') : route('school-employee-receivables.index', $school) }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Piutang Karyawan</h5>
							@if(!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
								<a href="{{ auth()->user()->role == 'SuperAdmin' ? route('employee-receivables.create') : route('school-employee-receivables.create', $school) }}" class="btn btn-primary" title="Tambah Penerimaan">
									<span class="d-lg-block d-none">Tambah Penerimaan</span>
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
							<table class="table align-middle">
								<thead>
									<tr>
										<th scope="col">No</th>
										<th scope="col">Sekolah</th>
                                        <th scope="col">Karyawan</th>
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
                                            <td>{{ $receivable->school->name }}</td>
                                            <td>
												<a href="javascript:void;" class="text-decoration-none" data-bs-toggle="collapse" data-bs-target="#details{{ $receivable->id }}">
													{{ $receivable->employee->name }} ({{ $receivable->employee->employee_id_number }})
												</a>
											</td>
                                            <td>{{ $receivable->account->code }} - {{ $receivable->account->name }}</td>
                                            <td class="text-end">{{ number_format($receivable->amount, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($receivable->paid_amount, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $receivable->status === 'Paid' ? 'success' : ($receivable->status === 'Partial' ? 'warning' : 'danger') }}">
                                                    {{ $receivable->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $receivable->due_date ? \Carbon\Carbon::parse($receivable->due_date)->format('d-m-Y') : '-' }}</td>
                                            @if (!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
                                                <td class="text-center">
                                                    @if($receivable->status !== 'Paid')
                                                        <a href="{{ route('school-employee-receivables.pay', [$receivable->school, $receivable]) }}" class="btn btn-sm btn-success">Bayar</a>
                                                    @endif
                                                    <a href="{{ route('school-employee-receivables.edit', [$receivable->school, $receivable]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <form action="{{ route('school-employee-receivables.destroy', [$receivable->school, $receivable]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus piutang ini?')">Hapus</button>
                                                    </form>
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
                                                        @forelse($receivable->employee_receivable_details as $detail)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($detail->period)->format('d-m-Y') }}</td>
                                                                <td>{{ $detail->description }}</td>
                                                                <td class="text-end">{{ number_format($detail->amount, 0, ',', '.') }}</td>
																<td>{{ $detail->reason ?? '-' }}</td>
																@if (!in_array(auth()->user()->role, ['AdminMonitor', 'Pengawas']))
																<td class="text-center">
																	<a href="{{ route('school-employee-receivables.receipt', [$receivable->school, $detail]) }}" class="btn btn-sm btn-info">Kwitansi</a>
																	<a href="{{ route('school-employee-receivables.edit-pay', [$receivable->school, $detail]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
																	<a href="https://wa.me/{{ preg_replace('/^0/', '62', $receivable->employee->phone) }}?text=halo%2C%20berikut%20kami%20sampaikan%20kwitansi%20pembayaran%20bpk%2Fibu" class="btn btn-sm btn-outline-success" target="_blank" title="Kirim WhatsApp">WA</a>
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
													<span><strong>Piutang</strong> : {{ number_format($receivable->amount, 0, ',', '.') }}</span>
													<span><strong>Terbayar</strong> : {{ number_format($receivable->paid_amount, 0, ',', '.') }}</span>
													<span><strong>Belum Terbayar</strong> : {{ number_format($receivable->amount - $receivable->paid_amount, 0, ',', '.') }}</span>
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
@endsection
@section('js')
	<script>
		$(document).ready(function(){
			const school = $('#schoolAdmin').val() || @json($school);
			if (@json(auth()->user()->role) != 'SchoolAdmin') {
				$('#schoolFilter').select2();
				$('#schoolFilter').on('change', function () {
					getEmployee($(this).val(), @json($employeeId));
				})
			}
			$('#accountFilter').select2();
			$('#statusFilter').select2();
			if (school) {
				getEmployee(school, @json($employeeId))
			} else {
				$('#employeeFilter').select2();
				$('#employeeFilter').prop("disabled", true);
			}

			function getEmployee(school, single) {
				$('#employeeFilter').select2();
				if (school) {
					$('#employeeFilter').prop("disabled", false);
					$.ajax({
						type:'POST',
						url:'/employee-receivables/employee/filter',
						data: {school},
						dataType: 'json',
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						success:function(data){
							let options = '<option value="">Pilih Karyawan</option>';
							$.each(data, function(key, value) {
								if (single && parseInt(single) === value['id']) {
									options += '<option value=' + value['id'] + ' selected>' + value['name'] + '</option>';
								} else {
									options += '<option value=' + value['id'] + '>' + value['name'] + '</option>';
								}
							});
							$('#employeeFilter').empty();
							$('#employeeFilter').append(options);	
						}
					});
				} else {
					$('#employeeFilter').prop("disabled", true);
				}
			}
		})
	</script>
@endsection