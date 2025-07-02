@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-center">
		<!-- Breadcrumb start -->
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<i class="bi bi-pie-chart lh-1 pe-3 me-3 border-end border-dark"></i>
				Dashboard
			</li>
		</ol>
	    <!-- Breadcrumb end -->
	</div>
	<!-- App Hero header ends -->
	<!-- App body starts -->
	<div class="app-body">
		<div class="row gx-3">
			<div class="col-xl-12">
				<div class="card mb-3">
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
											@foreach($schools as $s)
												<option value="{{ $s->id }}" {{ $schoolId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								@endif
								<div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="start_date" class="form-label">Tanggal Mulai</label>
                                    	<input type="date" class="form-control" id="start_date" name="start_date" value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}">
									</div>
                                </div>
                                <div class="col-xl-4 col-md-6 col-12">
									<div class="mb-3">
										<label for="end_date" class="form-label">Tanggal Akhir</label>
                                    	<input type="date" class="form-control" id="end_date" name="end_date" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}">
									</div>
                                </div>
							</div>
							<div class="row gx-3">
                                <div class="col-xl-4 col-md-6 col-12">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
									<a href="{{ auth()->user()->role != 'SchoolAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="btn btn-danger">Reset</a>
                                </div>
                            </div>
							<!-- Row end -->
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Row start -->
		<div class="row gx-3">
			<div class="col-xl-4 col-sm-6 col-12">
				<div class="card mb-3">
					<div class="card-body">
						<div class="mb-2 d-flex align-items-center justify-content-between">
							<i class="bi bi-bar-chart fs-1 text-primary lh-1"></i>
							<h5 class="m-0 text-secondary fw-normal">Total Aset</h5>
						</div>
						<div class="d-flex align-items-center justify-content-end">
							<h3 class="m-0 text-primary">{{ number_format($totalAssets, 0, ',', '.') }}</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12">
				<div class="card mb-3">
					<div class="card-body">
						<div class="mb-2 d-flex align-items-center justify-content-between">
							<i class="bi bi-bag-check fs-1 text-primary lh-1"></i>
							<h5 class="m-0 text-secondary fw-normal">Laba Bersih</h5>
						</div>
    					<div class="d-flex align-items-center justify-content-end">
							<h3 class="m-0 text-primary">{{ number_format($netIncome, 0, ',', '.') }}</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12">
				<div class="card mb-3">
					<div class="card-body">
						<div class="mb-2 d-flex align-items-center justify-content-between">
							<i class="bi bi-box-seam fs-1 text-primary lh-1"></i>
							<h5 class="m-0 text-secondary fw-normal">Arus Kas Bersih</h5>
						</div>
						<div class="d-flex align-items-center justify-content-end">
							<h3 class="m-0 text-primary">{{ number_format($cashFlow, 0, ',', '.') }}</h3>
						</div>
					</div>
				</div>
			</div>
        </div>
		<!-- Row end -->

		<!-- Row start -->
		<div class="row gx-3">
			<div class="col-xxl-12">
				<div class="card mb-3">
					<div class="card-body">
						<!-- Row start -->
						<div class="row gx-3">
							<div class="col-lg-12 col-sm-12 col-12">
								<h6 class="text-center mb-3">Pendapatan vs Biaya (6 Bulan Terakhir)</h6>
								<div id="financial"></div>
							</div>
						</div>
						<!-- Row ends -->
					</div>
				</div>
			</div>
		</div>
		<!-- Row ends -->

		<!-- Row start -->
		<div class="row gx-3">
			<div class="col-xxl-12">
				<div class="card mb-3">
					<div class="card-body">
						<!-- Row start -->
						<div class="row gx-3">
							<div class="col-lg-12 col-sm-12 col-12">
								<h6 class="text-center mb-3">Piutang Siswa (6 Bulan Terakhir)</h6>
								<div id="receivable"></div>
							</div>
						</div>
						<!-- Row ends -->
					</div>
				</div>
			</div>
		</div>
		<!-- Row ends -->
	</div>
	<!-- App body ends -->

	<!-- Apex Charts -->
	<script src="{{ asset('js/apex/apexcharts.min.js') }}"></script>
	<script>
		var options = {
			chart: {
				height: 300,
				type: "bar",
				toolbar: {
					show: false,
				},
			},
			dataLabels: {
				enabled: false,
			},
			stroke: {
				width: 0,
			},
			legend: {
				show: false,
			},
			series: [
				{
					name: "Pendapatan",
					data: @json($chartData['revenues']),
				},
				{
					name: "Biaya",
					data: @json($chartData['expenses']),
				},
			],
			grid: {
				xaxis: {
					lines: {
						show: false,
					},
				},
				yaxis: {
					lines: {
						show: false,
					},
				},
				padding: {
					top: -30,
					right: -20,
					bottom: 0,
					left: -20,
				},
			},
			xaxis: {
				categories: @json($chartData['labels']),
			},
			yaxis: {
				labels: {
					show: false,
				},
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
					}
				}
			},
			colors: ["#007deb", "rgba(255, 99, 132, 0.2)"],
		};

		var chart = new ApexCharts(document.querySelector("#financial"), options);

		chart.render();

		var studentOptions = {
			chart: {
				height: 300,
				type: "area",
				toolbar: {
					show: false,
				},
			},
			dataLabels: {
				enabled: false,
			},
			stroke: {
				curve: "smooth",
				width: 1,
			},
			series: [
				{
					name: "Piutang",
					data: @json($chartStudentData['receivables']),
				},
			],
			grid: {
				borderColor: "#dae1ea",
				strokeDashArray: 5,
				xaxis: {
					lines: {
						show: true,
					},
				},
				yaxis: {
					lines: {
						show: false,
					},
				},
				padding: {
					top: -30,
					right: 65,
					bottom: 0,
					left: 65,
				},
			},
			xaxis: {
				categories: @json($chartStudentData['labels']),
			},
			yaxis: {
				labels: {
					show: false,
				},
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
					}
				}
			},
			colors: ["#e4052e", "#0073d8"],
			fill: {
				type: "gradient",
				gradient: {
					shadeIntensity: 1,
					opacityFrom: 0.4,
					opacityTo: 0.2,
					stops: [0, 90, 100],
				},
			},
		};

		var studentChart = new ApexCharts(document.querySelector("#receivable"), studentOptions);

		studentChart.render();
	</script>
@endsection
@section('js')
	<script>
		$(document).ready(function() {
			if (@json(auth()->user()->role) != 'SchoolAdmin') {
				$('#schoolFilter').select2();
			}
		})
	</script>
@endsection