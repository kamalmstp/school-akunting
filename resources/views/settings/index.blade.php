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
			<li class="breadcrumb-item" aria-current="page">Kelola Pembayaran</li>
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
								@endif
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="accountFilter" class="form-label">Akun Piutang</label>
										<select name="account" class="form-select" id="accountFilter">
											<option value="">Pilih Akun Piutang</option>
											@foreach($accounts as $account)
												<option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
                                <div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="statusFilter" class="form-label">Status</label>
										<select name="status" class="form-select" id="statusFilter">
											<option value="">Pilih Status</option>
											<option value="1" {{ $status == '1' ? 'selected' : '' }}>Aktif</option>
											<option value="0" {{ $status == '0' ? 'selected' : '' }}>Tidak Aktif</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="userFilter" class="form-label">Tipe User</label>
										<select name="user_type" class="form-select" id="userFilter">
											<option value="">Pilih Tipe User</option>
											<option value="Siswa" {{ $userType == 'Siswa' ? 'selected' : '' }}>Siswa</option>
											<option value="Guru" {{ $userType == 'Guru' ? 'selected' : '' }}>Guru</option>
											<option value="Karyawan" {{ $userType == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
										</select>
									</div>
								</div>
                                <div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="scheduleFilter" class="form-label">Tipe Pembayaran</label>
										<select name="pay_type" class="form-select" id="scheduleFilter">
											<option value="">Pilih Tipe Pembayaran</option>
											<option value="Bulanan" {{ $payType == 'Bulanan' ? 'selected' : '' }}>Bulanan</option>
											<option value="Non Bulanan" {{ $payType == 'Non Bulanan' ? 'selected' : '' }}>Non Bulanan</option>
										</select>
									</div>
								</div>
							</div>
							<!-- Row end -->						
							<div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('schedules.index') : route('school-schedules.index', $school) }}" class="btn btn-danger">Reset</a>
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
							<h5 class="card-title">Daftar Pembayaran</h5>
							@if(auth()->user()->role !== 'AdminMonitor')
                            <div class="d-flex gap-2">
								<a href="{{ auth()->user()->role == 'SuperAdmin' ? route('schedules.create') : route('school-schedules.create', $school) }}" class="btn btn-primary" title="Tambah Jadwal">
									<span class="d-lg-block d-none">Tambah Pembayaran</span>
									<span class="d-sm-block d-lg-none">
										<i class="bi bi-plus"></i>
									</span>
								</a>
                                <form method="POST" action="/run-command" id="runCommandForm">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Generate">
                                        <span class="d-lg-block d-none">Run Command</span>
                                        <span class="d-sm-block d-lg-none">
                                            <i class="bi bi-gear"></i>
                                        </span>
                                    </button>
                                </form>
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
										<th scope="col">Sekolah</th>
                                        <th scope="col">Akun Piutang</th>
                                        <th scope="col">Akun Pendapatan</th>
                                        <th scope="col">Tipe User</th>
                                        <th scope="col">Tipe Pembayaran</th>
                                        <th scope="col" class="text-end">Jumlah</th>
                                        <th scope="col" class="text-center">Status</th>
                                        @if (auth()->user()->role != 'AdminMonitor') <th></th> @endif
									</tr>
								</thead>
								<tbody>
									@forelse($schedules as $index => $schedule)
                                        <tr>
											<td>{{ $schedules->currentPage() * 10 - (9 - $index) }}</td>
                                            <td>{{ $schedule->school->name }}</td>
                                            <td>{{ $schedule->account->code }} - {{ $schedule->account->name }}</td>
                                            <td>{{ $schedule->income_account->code }} - {{ $schedule->income_account->name }}</td>
                                            <td>{{ $schedule->user_type }}</td>
                                            <td>{{ $schedule->schedule_type }}</td>
                                            <td class="text-end">{{ number_format($schedule->amount, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $schedule->status ? 'Aktif' : 'Tidak Aktif' }}</td>
                                            @if (auth()->user()->role != 'AdminMonitor')
                                                <td class="text-center">
                                                    <a href="{{ route('school-schedules.edit', [$schedule->school, $schedule]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <form action="{{ route('school-schedules.destroy', [$schedule->school, $schedule]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</button>
                                                    </form>
                                                </td> 
                                            @endif
                                        </tr>
									@empty
										<tr>
											<td colspan="{{ auth()->user()->role != 'AdminMonitor' ? '9' : '8' }}">Belum ada data pembayaran</td>
										</tr>										
									@endempty
                                </tbody>
							</table>	
						</div>
						<div class="d-flex justify-content-end mt-2">
							{{ $schedules->links() }}
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Row end -->

	</div>
@endsection
@section('js')
	<script>
		$(document).ready(function(){
			if (@json(auth()->user()->role) != 'SchoolAdmin') {
				$('#schoolFilter').select2();
			}
			$('#userFilter').select2();
			$('#accountFilter').select2();
			$('#scheduleFilter').select2();
			$('#statusFilter').select2();
		})
	</script>
@endsection