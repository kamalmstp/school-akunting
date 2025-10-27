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
							<table id="receiptTable" class="table table-bordered align-middle">
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
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control start-date" id="startDate-{{ $row->id }}">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control end-date" id="endDate-{{ $row->id }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100 filter-btn" data-student="{{ $row->id }}" data-school="{{ $school->id }}">
                                    Tampilkan
                                </button>
                            </div>
                        </div>

                        <div id="receipt-content-{{ $row->id }}">
                            <div class="text-center text-muted py-3">Silakan pilih rentang tanggal untuk melihat data.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#receiptTable').DataTable({
                "paging": true,
                "searching": true,
                "info": true,
                "ordering": false,
                "responsive": true,
                "pageLength": 10
            });

            $(document).on('click', '.filter-btn', function() {
                let studentId = $(this).data('student');
                let schoolId = $(this).data('school');
                let startDate = $('#startDate-' + studentId).val();
                let endDate = $('#endDate-' + studentId).val();

                if (!startDate || !endDate) {
                    alert('Silakan pilih tanggal awal dan akhir!');
                    return;
                }

                $.ajax({
                    url: '/schools/' + schoolId + '/student-receipts/' + studentId + '/receipts/filter',
                    type: 'GET',
                    data: { start_date: startDate, end_date: endDate },
                    beforeSend: function() {
                        $('#receipt-content-' + studentId).html('<div class="text-center text-muted py-3">Memuat data...</div>');
                    },
                    success: function(response) {
                        $('#receipt-content-' + studentId).html(response);
                    },
                    error: function() {
                        $('#receipt-content-' + studentId).html('<div class="text-danger text-center py-3">Gagal memuat data.</div>');
                    }
                });
            });
        });
    </script>
@endsection