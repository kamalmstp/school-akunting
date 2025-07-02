<!DOCTYPE html>
<html lang="id">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Sistem Akuntansi Sekolah</title>

		<link rel="shortcut icon" href="{{ asset('images/account3.png') }}" />

		<!-- *************
			************ CSS Files *************
		************* -->
		<link rel="stylesheet" href="{{ asset('fonts/bootstrap/bootstrap-icons.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/main.min.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/app.css') }}" />

		<!-- *************
			************ Vendor Css Files *************
		************ -->

		<!-- Select2 CSS -->
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

		<!-- Scrollbar CSS -->
		<link rel="stylesheet" href="{{ asset('css/OverlayScrollbars.min.css') }}" />

		<!-- Toastify CSS -->
		<link rel="stylesheet" href="{{ asset('css/toastify.css') }}" />

	</head>

	<body>

		<!-- Page wrapper start -->
		<div class="page-wrapper">

			<!-- App header starts -->
			<div class="app-header d-flex align-items-center">

				<!-- Toggle buttons start -->
				<div class="d-flex">
					<button class="toggle-sidebar" id="toggle-sidebar">
						<i class="bi bi-list lh-1"></i>
					</button>
					<button class="pin-sidebar" id="pin-sidebar">
						<i class="bi bi-list lh-1"></i>
					</button>
				</div>
				<!-- Toggle buttons end -->

				<!-- App brand starts -->
				<div class="app-brand py-2 ms-3">
					<a href="{{ redirect('/') }}" class="d-sm-block d-none">
						<img src="{{ asset('images/account3.png') }}" class="logo" alt="Bootstrap Gallery" />
					</a>
					<a href="{{ redirect('/') }}" class="d-sm-none d-block">
						<img src="{{ asset('images/account3.png') }}" class="logo" alt="Bootstrap Gallery" />
					</a>
				</div>
				<!-- App brand ends -->

				<!-- App header actions start -->
				<div class="header-actions col">
					<div class="dropdown ms-2">
						<a id="userSettings" class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none" href="#!"
							role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<img src="{{ asset('images/avatar.png') }}" class="rounded-2 img-3x" alt="" />
							<span class="ms-2 text-truncate d-lg-block d-none">{{ auth()->user()->name }}</span>
						</a>
						<div class="dropdown-menu dropdown-menu-end shadow-lg">
							<div class="mx-3 mt-2 d-grid">
								<form action="{{ route('logout') }}" method="POST">
									@csrf
									<button class="btn btn-primary btn-sm w-100">Logout</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- App header actions end -->

			</div>
			<!-- App header ends -->

			<!-- Main container start -->
			<div class="main-container">

				<!-- Sidebar wrapper start -->
				<nav id="sidebar" class="sidebar-wrapper">

					<!-- Sidebar menu starts -->
					<div class="sidebarMenuScroll">
						<ul class="sidebar-menu">
                            @if(auth()->user()->role === 'SuperAdmin')
								<li class="@if(Route::is('dashboard')) active current-page @endif">
									<a href="{{ route('dashboard') }}">
										<i class="bi bi-pie-chart"></i>
										<span class="menu-text">Dashboard</span>
									</a>
								</li>
                                <li class="@if(Route::is('schools.index')) active current-page @endif">
                                    <a href="{{ route('schools.index') }}">
                                        <i class="bi bi-building"></i>
                                        <span class="menu-text">Kelola Sekolah</span>
                                    </a>
                                </li>
								<li class="treeview master">
                                    <a href="#!">
                                        <i class="bi bi-window-stack"></i>
                                        <span class="menu-text">Data Master</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('teachers.index') }}" class="m-first">Kelola Guru</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('employees.index') }}" class="m-second">Kelola Karyawan</a>
                                        </li>
										<li>
                                            <a href="{{ route('school-majors.index') }}" class="m-fourth">Kelola Jurusan</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('students.index') }}" class="m-third">Kelola Siswa</a>
                                        </li>
										<li>
                                            <a href="{{ route('schedules.index') }}" class="m-fifth">Kelola Pembayaran</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="@if(Route::is('accounts.index')) active current-page @endif">
                                    <a href="{{ route('accounts.index') }}">
                                        <i class="bi bi-list"></i>
                                        <span class="menu-text">Kelola Akun</span>
                                    </a>
                                </li>
                                <li class="treeview transaction">
                                    <a href="#!">
                                        <i class="bi bi-cash"></i>
                                        <span class="menu-text">Transaksi</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('transactions.index') }}" class="tr-first">Semua Transaksi</a>
                                        </li>
                                        <li class="tr-third">
                                            <a href="{{ route('fixed-assets.index') }}" class="tr-second">Aset Tetap</a>
                                        </li>
                                    </ul>
                                </li>
								<li class="treeview receivable">
                                    <a href="#!">
                                        <i class="bi bi-cash-coin"></i>
                                        <span class="menu-text">Piutang</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('student-receivables.index') }}" class="p-first">Piutang Siswa</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('teacher-receivables.index') }}" class="p-second">Piutang Guru</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('employee-receivables.index') }}" class="p-third">Piutang Karyawan</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="treeview report">
                                    <a href="#!">
                                        <i class="bi bi-journal"></i>
                                        <span class="menu-text">Laporan</span>
                                    </a>
                                    <ul class="treeview-menu">
										<li>
											<a href="{{ route('reports.beginning-balance') }}" class="rp-first">Saldo Awal</a>
										</li>
										<li>
											<a href="{{ route('reports.general-journal') }}" class="rp-second">Jurnal Umum</a>
										</li>
										<li>
											<a href="{{ route('reports.ledger') }}" class="rp-third">Buku Besar</a>
										</li>
										<li>
											<a href="{{ route('reports.trial-balance-before') }}" class="rp-fourth">Neraca Saldo Awal</a>
										</li>
										<li>
											<a href="{{ route('reports.adjusting-entries') }}" class="rp-fifth">Jurnal Penyesuaian</a>
										</li>
										<li>
											<a href="{{ route('reports.trial-balance-after') }}" class="rp-sixth">Neraca Saldo Akhir</a>
										</li>
										<li>
											<a href="{{ route('reports.financial-statements') }}" class="rp-seventh">Laporan Keuangan</a>
										</li>
                                    </ul>
                                </li>
                                <li class="@if(Route::is('users.index')) active current-page @endif">
                                    <a href="{{ route('users.index') }}">
                                        <i class="bi bi-people"></i>
                                        <span class="menu-text">Kelola Pengguna</span>
                                    </a>
                                </li>
							@elseif(auth()->user()->role === 'AdminMonitor')
								<li class="@if(Route::is('dashboard')) active current-page @endif">
									<a href="{{ route('dashboard') }}">
										<i class="bi bi-pie-chart"></i>
										<span class="menu-text">Dashboard</span>
									</a>
								</li>
                                <li class="@if(Route::is('schools.index')) active current-page @endif">
                                    <a href="{{ route('schools.index') }}">
                                        <i class="bi bi-building"></i>
                                        <span class="menu-text">Kelola Sekolah</span>
                                    </a>
                                </li>
								<li class="treeview master">
                                    <a href="#!">
                                        <i class="bi bi-window-stack"></i>
                                        <span class="menu-text">Data Master</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('teachers.index') }}" class="m-first">Kelola Guru</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('employees.index') }}" class="m-second">Kelola Karyawan</a>
                                        </li>
										<li>
                                            <a href="{{ route('school-majors.index') }}" class="m-fourth">Kelola Jurusan</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('students.index') }}" class="m-third">Kelola Siswa</a>
                                        </li>
										<li>
                                            <a href="{{ route('schedules.index') }}" class="m-fifth">Kelola Pembayaran</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="@if(Route::is('accounts.index')) active current-page @endif">
                                    <a href="{{ route('accounts.index') }}">
                                        <i class="bi bi-list"></i>
                                        <span class="menu-text">Kelola Akun</span>
                                    </a>
                                </li>
								<li class="treeview transaction">
                                    <a href="#!">
                                        <i class="bi bi-cash"></i>
                                        <span class="menu-text">Transaksi</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('transactions.index') }}" class="tr-first">Semua Transaksi</a>
                                        </li>
                                        <li class="tr-third">
                                            <a href="{{ route('fixed-assets.index') }}" class="tr-second">Aset Tetap</a>
                                        </li>
                                    </ul>
                                </li>
								<li class="treeview receivable">
                                    <a href="#!">
                                        <i class="bi bi-cash-coin"></i>
                                        <span class="menu-text">Piutang</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('student-receivables.index') }}" class="p-first">Piutang Siswa</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('teacher-receivables.index') }}" class="p-second">Piutang Guru</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('employee-receivables.index') }}" class="p-third">Piutang Karyawan</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="treeview report">
                                    <a href="#!">
                                        <i class="bi bi-journal"></i>
                                        <span class="menu-text">Laporan</span>
                                    </a>
                                    <ul class="treeview-menu">
										<li>
											<a href="{{ route('reports.beginning-balance') }}" class="rp-first">Saldo Awal</a>
										</li>
										<li>
											<a href="{{ route('reports.general-journal') }}" class="rp-second">Jurnal Umum</a>
										</li>
										<li>
											<a href="{{ route('reports.ledger') }}" class="rp-third">Buku Besar</a>
										</li>
										<li>
											<a href="{{ route('reports.trial-balance-before') }}" class="rp-fourth">Neraca Saldo Awal</a>
										</li>
										<li>
											<a href="{{ route('reports.adjusting-entries') }}" class="rp-fifth">Jurnal Penyesuaian</a>
										</li>
										<li>
											<a href="{{ route('reports.trial-balance-after') }}" class="rp-sixth">Neraca Saldo Akhir</a>
										</li>
										<li>
											<a href="{{ route('reports.financial-statements') }}" class="rp-seventh">Laporan Keuangan</a>
										</li>
                                    </ul>
                                </li>
                            @elseif(auth()->user()->role === 'SchoolAdmin')
								<li class="@if(Route::is('dashboard.index')) active current-page @endif">
									<a href="{{ route('dashboard.index', auth()->user()->school_id) }}">
										<i class="bi bi-pie-chart"></i>
										<span class="menu-text">Dashboard</span>
									</a>
								</li>
								<li class="treeview master">
                                    <a href="#!">
                                        <i class="bi bi-window-stack"></i>
                                        <span class="menu-text">Data Master</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('school-teachers.index', auth()->user()->school_id) }}" class="m-first">Kelola Guru</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('school-employees.index', auth()->user()->school_id) }}" class="m-second">Kelola Karyawan</a>
                                        </li>
										<li>
                                            <a href="{{ route('school-school-majors.index', auth()->user()->school_id) }}" class="m-fourth">Kelola Jurusan</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('school-students.index', auth()->user()->school_id) }}" class="m-third">Kelola Siswa</a>
                                        </li>
										<li>
                                            <a href="{{ route('school-schedules.index', auth()->user()->school_id) }}" class="m-fifth">Kelola Pembayaran</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="treeview transaction">
                                    <a href="#!">
                                        <i class="bi bi-cash"></i>
                                        <span class="menu-text">Transaksi</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('school-transactions.index', auth()->user()->school_id) }}" class="tr-first">Transaksi</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('school-fixed-assets.index', auth()->user()->school_id) }}" class="tr-second">Aset Tetap</a>
                                        </li>
                                    </ul>
                                </li>
								<li class="treeview receivable">
                                    <a href="#!">
                                        <i class="bi bi-cash-coin"></i>
                                        <span class="menu-text">Piutang</span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{ route('school-student-receivables.index', auth()->user()->school_id) }}" class="p-first">Piutang Siswa</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('school-teacher-receivables.index', auth()->user()->school_id) }}" class="p-second">Piutang Guru</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('school-employee-receivables.index', auth()->user()->school_id) }}" class="p-third">Piutang Karyawan</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="treeview report">
                                    <a href="#!">
                                        <i class="bi bi-journal"></i>
                                        <span class="menu-text">Laporan</span>
                                    </a>
                                    <ul class="treeview-menu">
										<li>
											<a href="{{ route('school-reports.beginning-balance', auth()->user()->school_id) }}" class="rp-first">Saldo Awal</a>
										</li>
										<li>
											<a href="{{ route('school-reports.general-journal', auth()->user()->school_id) }}" class="rp-second">Jurnal Umum</a>
										</li>
										<li>
											<a href="{{ route('school-reports.ledger', auth()->user()->school_id) }}" class="rp-third">Buku Besar</a>
										</li>
										<li>
											<a href="{{ route('school-reports.trial-balance-before', auth()->user()->school_id) }}" class="rp-fourth">Neraca Saldo Awal</a>
										</li>
										<li>
											<a href="{{ route('school-reports.adjusting-entries', auth()->user()->school_id) }}" class="rp-fifth">Jurnal Penyesuaian</a>
										</li>
										<li>
											<a href="{{ route('school-reports.trial-balance-after', auth()->user()->school_id) }}" class="rp-sixth">Neraca Saldo Akhir</a>
										</li>
										<li>
											<a href="{{ route('school-reports.financial-statements', auth()->user()->school_id) }}" class="rp-seventh">Laporan Keuangan</a>
										</li>
                                    </ul>
                                </li>
                            @endif
							<li class="@if(Route::is('users.profile')) active current-page @endif">
								<a href="{{ route('users.profile') }}">
									<i class="bi bi-person-square"></i>
									<span class="menu-text">Profil</span>
								</a>
							</li>
						</ul>
					</div>
					<!-- Sidebar menu ends -->

				</nav>
				<!-- Sidebar wrapper end -->

				<!-- App container starts -->
				<div class="app-container">
					@yield('content')

					<!-- App footer start -->
					<div class="app-footer">
						<span>© Sistem Akuntasi</span>
					</div>
					<!-- App footer end -->

				</div>
				<!-- App container ends -->

			</div>
			<!-- Main container end -->

		</div>
		<!-- Page wrapper end -->

		<!-- *************
			************ JavaScript Files *************
		************* -->
		<!-- Required jQuery first, then Bootstrap Bundle JS -->
		<script src="{{ asset('js/jquery.min.js') }}"></script>
		<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
		<!-- Select2 JS -->
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<script src="{{ asset('js/moment.min.js') }}"></script>

		<!-- *************
			************ Vendor Js Files *************
		************* -->

		<!-- Overlay Scroll JS -->
		<script src="{{ asset('js/jquery.overlayScrollbars.min.js') }}"></script>
		<script src="{{ asset('js/custom-scrollbar.js') }}"></script>

		<!-- Toastify JS -->
		<script src="{{ asset('js/toastify.js') }}"></script>

		<!-- Custom JS files -->
		<script src="{{ asset('js/custom.js') }}"></script>
		<script src="{{ asset('js/todays-date.js') }}"></script>
		<script type="text/javascript">
			$(document).ready(function () {
				const url = window.location.href;
				if (url.includes('transactions')) {
					$('.transaction').addClass('active current-page');
					$('.transaction > ul').addClass('menu-open');
					$('.tr-first').addClass('active-sub');
				}
				if (url.includes('fixed-assets')) {
					$('.transaction').addClass('active current-page');
					$('.transaction > ul').addClass('menu-open');
					$('.tr-second').addClass('active-sub');
				}
				if (url.includes('beginning-balance')) {
					$('.report').addClass('active current-page');
					$('.report > ul').addClass('menu-open');
					$('.rp-first').addClass('active-sub');
				}
				if (url.includes('general-journal')) {
					$('.report').addClass('active current-page');
					$('.report > ul').addClass('menu-open');
					$('.rp-second').addClass('active-sub');
				}
				if (url.includes('ledger')) {
					$('.report').addClass('active current-page');
					$('.report > ul').addClass('menu-open');
					$('.rp-third').addClass('active-sub');
				}
				if (url.includes('trial-balance-before')) {
					$('.report').addClass('active current-page');
					$('.report > ul').addClass('menu-open');
					$('.rp-fourth').addClass('active-sub');
				}
				if (url.includes('adjusting-entries')) {
					$('.report').addClass('active current-page');
					$('.report > ul').addClass('menu-open');
					$('.rp-fifth').addClass('active-sub');
				}
				if (url.includes('trial-balance-after')) {
					$('.report').addClass('active current-page');
					$('.report > ul').addClass('menu-open');
					$('.rp-sixth').addClass('active-sub');
				}
				if (url.includes('financial-statements')) {
					$('.report').addClass('active current-page');
					$('.report > ul').addClass('menu-open');
					$('.rp-seventh').addClass('active-sub');
				}
				if (url.includes('teachers')) {
					$('.master').addClass('active current-page');
					$('.master > ul').addClass('menu-open');
					$('.m-first').addClass('active-sub');
				}
				if (url.includes('employees')) {
					$('.master').addClass('active current-page');
					$('.master > ul').addClass('menu-open');
					$('.m-second').addClass('active-sub');
				}
				if (url.includes('students')) {
					$('.master').addClass('active current-page');
					$('.master > ul').addClass('menu-open');
					$('.m-third').addClass('active-sub');
				}
				if (url.includes('majors')) {
					$('.master').addClass('active current-page');
					$('.master > ul').addClass('menu-open');
					$('.m-fourth').addClass('active-sub');
				}
				if (url.includes('payments')) {
					$('.master').addClass('active current-page');
					$('.master > ul').addClass('menu-open');
					$('.m-fifth').addClass('active-sub');
				}
				if (url.includes('student-receivables')) {
					$('.receivable').addClass('active current-page');
					$('.receivable > ul').addClass('menu-open');
					$('.p-first').addClass('active-sub');
				}
				if (url.includes('teacher-receivables')) {
					$('.receivable').addClass('active current-page');
					$('.receivable > ul').addClass('menu-open');
					$('.p-second').addClass('active-sub');
				}
				if (url.includes('employee-receivables')) {
					$('.receivable').addClass('active current-page');
					$('.receivable > ul').addClass('menu-open');
					$('.p-third').addClass('active-sub');
				}

				$(document).on('input', '.angka', function () {
					let val = $(this).val().replace(/\D/g, '');
					let format = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
					$(this).val(format);
				});

				$(document).on('keyup keypress', '.angka',function (e) {
					if (e.which < 48 || e.which > 57) {
						e.preventDefault();
					}
				});

				setTimeout(() => {
					$('.alert').hide();
				}, 3000);
			})
		</script>
		@yield('js')
	</body>

</html>