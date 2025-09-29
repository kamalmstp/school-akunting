<!DOCTYPE html>
<html lang="id">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Sistem Akuntansi Sekolah</title>

		<link rel="shortcut icon" href="{{ asset('images/account3.png') }}" />
		<link rel="stylesheet" href="{{ asset('fonts/bootstrap/bootstrap-icons.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/main.min.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/app.css') }}" />
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
	    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">
		<link rel="stylesheet" href="{{ asset('css/OverlayScrollbars.min.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/toastify.css') }}" />
	</head>

	<body>
		<div class="page-wrapper">
			<div class="app-header d-flex align-items-center">
				<div class="d-flex">
					<button class="toggle-sidebar" id="toggle-sidebar">
						<i class="bi bi-list lh-1"></i>
					</button>
					<button class="pin-sidebar" id="pin-sidebar">
						<i class="bi bi-list lh-1"></i>
					</button>
				</div>
				<div class="app-brand py-2 ms-3">
					<a href="{{ redirect('/') }}" class="d-sm-block d-none">
						<img src="{{ asset('images/account3.png') }}" class="logo" alt="Bootstrap Gallery" />
					</a>
					<a href="{{ redirect('/') }}" class="d-sm-none d-block">
						<img src="{{ asset('images/account3.png') }}" class="logo" alt="Bootstrap Gallery" />
					</a>
				</div>
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
			</div>

			<div class="main-container">

				@include('layouts.partials.sidebar')
				
				<div class="app-container">
					@yield('content')

					<div class="app-footer">
						<span>© Sistem Akuntasi</span>
					</div>
				</div>
			</div>
		</div>

		<script src="{{ asset('js/jquery.min.js') }}"></script>
		<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js" xintegrity="sha512-H9YQ81rwKth0zWvF/P4Jp8Bv+7k7fP4MvO6z6xWzP5p75B1d5x0M2F8j0M+0qLg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="{{ asset('js/moment.min.js') }}"></script>

		<script src="{{ asset('js/jquery.overlayScrollbars.min.js') }}"></script>
		<script src="{{ asset('js/custom-scrollbar.js') }}"></script>

		<script src="{{ asset('js/toastify.js') }}"></script>
		<script src="{{ asset('js/custom.js') }}"></script>
		<script src="{{ asset('js/todays-date.js') }}"></script>

		<script src="{{ asset('js/nav-active.js') }}"></script>
		<script type="text/javascript">
			$(document).ready(function () {

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