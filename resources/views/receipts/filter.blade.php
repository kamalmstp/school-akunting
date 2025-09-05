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
			<li class="breadcrumb-item" aria-current="page">Cetak Kwitansi Siswa</li>
		</ol>
		<!-- Breadcrumb end -->
	</div>
	<!-- App Hero header ends -->

    <!-- App body starts -->
	<div class="app-body">

        <!--
		<div class="row gx-3">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-body">
                        <form class="mb-4" method="POST" action="{{ route('school-student-receipts.print', $school) }}">
                            @csrf
                            <div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
                                        <label for="studentFilter" class="form-label">Pilih Siswa</label>
                                        <select name="student_id" class="form-select" id="studentFilter" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                                            @endforeach
                                        </select>
									</div>
								</div>

                                <div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
                                        <label for="dateFilter" class="form-label">Pilih Tanggal</label>
                                        <input class="form-control" id="dateFilter" type="date" name="date" required>
									</div>
								</div>
                            </div>

                            <div class="row gx-3">
								<div class="col-xl-4 col-md-6 col-12">
									<button type="submit" class="btn btn-primary">Cetak Kwitansi</button>
								</div>
							</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        -->

        <div class="row gx-3">
			<div class="col-xxl-12">
				<div class="card">
					<div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">List Pembayaran Siswa</h5>
						</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
							<table class="table align-middle">
								<thead>
									<tr>
										<th scope="col">No</th>
                                        <th scope="col">Siswa</th>
                                        <th></th>
									</tr>
								</thead>
								<tbody>
									@foreach($students as $student)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#previewModal-{{ $student->id }}">
                                                    Lihat Pembayaran
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
							</table>	
						</div>
						
                    </div>
                </div>
            </div>
        </div>

    </div>

    @foreach($students as $row)
    <!-- Modal -->
    <div class="modal fade" id="previewModal-{{ $row->id }}" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Kwitansi {{ $row->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <?php 
                    $studentCoba = $row;
                    $schoolCoba = $school;
                    $receivables = \App\Models\StudentReceivables::where('school_id', $school->id)
                                        ->where('student_id', $row->id)
                                        ->orderBy('created_at', 'asc')
                                        ->get()
                                        ->groupBy(fn($r) => \Carbon\Carbon::parse($r->created_at)->format('Y-m-d'));
                    
                ?>
                @forelse($receivables as $date => $items)
                    <h5 class="mt-3">Tanggal Pembayaran: {{ $date }}</h5>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Jenis Tagihan</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->account->code.' - '.$item->account->name }}</td>
                                    <td>Rp {{ number_format($item->amount, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-light">
                                <td><strong>Total</strong></td>
                                <td><strong>Rp {{ number_format($items->sum('amount'), 2, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="{{ route('school-student-receipts.printByStudentAndDate', [$schoolCoba, $studentCoba, $date]) }}" target="_blank" class="btn btn-success btn-sm">
                        Cetak Kwitansi
                    </a>
                    <hr>
                @empty
                    <h5>Belum Ada Transaksi</h5>
                @endforelse

            </div>
            </div>
        </div>
    </div>
@endforeach


@endsection