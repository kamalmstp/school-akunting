<!DOCTYPE html>
<html lang="id">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Sistem Akuntansi Sekolah - Login</title>

		<link rel="shortcut icon" href="{{ asset('images/account3.png') }}" />

		<!-- *************
			************ CSS Files *************
		************* -->
		<link rel="stylesheet" href="{{ asset('fonts/bootstrap/bootstrap-icons.css') }}" />
		<link rel="stylesheet" href="{{ asset('css/main.min.css') }}" />
	</head>

	<body class="bg-white">
		<!-- Container start -->
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-4 col-lg-5 col-sm-6 col-12">
					<form action="{{ route('login') }}" class="my-5" method="POST">
                        @csrf
						<div class="border rounded-2 p-4 mt-5">
							<div class="login-form">
								<a href="{{ route('login') }}" class="mb-2 d-flex justify-content-center">
									<img src="{{ asset('images/account3.png') }}" class="img-fluid login-logo" alt="" />
								</a>
								<h5 class="fw-light mb-5 text-center">Login untuk akses dashboard.</h5>
								@if(session('error'))
									<div class="alert alert-danger">
										{{ session('error') }}
									</div>
								@endif
								<div class="mb-3">
									<label class="form-label">Email</label>
									<input type="text" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email" value="{{ old('email') }}" />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
								</div>
								<div class="mb-3 position-relative">
									<label class="form-label">Kata Sandi</label>
									<input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi" />
                                    <div class="position-absolute" style="right: 10px; top: 35px; cursor: pointer;" id="showPass">
                                        <i class="bi bi-eye"></i>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
								</div>
								<div class="d-grid py-3 mt-4">
									<button type="submit" class="btn btn-lg btn-primary">
										Login
									</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- Container end -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('#showPass').on('click', function () {
                    var password = $('#password')[0];
                    if (password.type === 'password') {
                        password.type = 'text';
                    } else {
                        password.type = 'password';
                    }
                })

				setTimeout(() => {
					$('.alert').hide();
				}, 3000);
            })
        </script>
	</body>

</html>