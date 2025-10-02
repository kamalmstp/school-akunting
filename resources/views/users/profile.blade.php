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
			<li class="breadcrumb-item" aria-current="page">Profil</li>
        </ol>
        <!-- Breadcrumb end -->

    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <div class="app-body">

        <!-- Row start -->
        <div class="row justify-content-center">
            <div class="col-xxl-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Row start -->
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="{{ asset('images/avatar.png') }}" class="img-5xx rounded-circle" alt="" />
                            </div>
                            <div class="col">
                                <h6 class="text-primary">{{ auth()->user()->role }}</h6>
                                <h4 class="m-0">{{ auth()->user()->name }}</h4>
                            </div>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Row end -->

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-xxl-3 col-sm-6 col-12 order-xxl-1 order-xl-2 order-lg-2 order-md-2 order-sm-2">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Profil</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="d-flex align-items-center mb-3">
                            <i class="bi bi-envelope fs-2 me-2"></i>
                            <span>{{ auth()->user()->email }}</span>
                        </h6>
                        <h6 class="d-flex align-items-center mb-3">
                            <i class="bi bi-building fs-2 me-2"></i>
                            <span>{{ auth()->user()->school_id ? auth()->user()->school->name : '-' }}</span>
                        </h6>
                        <h6 class="d-flex align-items-center mb-3">
                            <i class="bi bi-telephone fs-2 me-2"></i>
                            <span>{{ auth()->user()->phone }}</span>
                        </h6>
                        <h6 class="d-flex align-items-center mb-3">
                            <i class="bi bi-map fs-2 me-2"></i>
                            <span>{{ auth()->user()->school_id ? auth()->user()->school->city.' - ' : ' ' }} {{ auth()->user()->school_id ? auth()->user()->school->address : '-' }}</span>
                        </h6>


                    </div>
                </div>
            </div>
            @if (auth()->user()->school_id)
            <div class="col-xxl-3 col-sm-6 col-12 order-xxl-1 order-xl-2 order-lg-2 order-md-2 order-sm-2">
                <div class="card shadow-sm mb-3">
                    <!-- Header -->
                    <div class="card-header ">
                        <h5 class="card-title mb-0">Infomasi Lainnya</h5>
                    </div>

                    <!-- Body -->
                    <div class="card-body text-center">
                        <!-- Logo -->
                        @if(auth()->user()->school->logo)
                            <div class="mb-3">
                                <img src="{{ asset('/' . auth()->user()->school->logo) }}" 
                                    alt="Logo Sekolah" 
                                    class="img-fluid rounded shadow-sm" 
                                    style="max-height: 120px;">
                            </div>
                        @else
                            <div class="mb-3">
                                <span class="badge bg-secondary">Belum ada logo</span>
                            </div>
                        @endif

                        <hr>

                        <!-- Info Sekolah -->
                        <div class="text-start">
                            <p class="mb-2">
                                <strong>Ketua Majelis Dikdasmen:</strong><br>
                                <span>{{ auth()->user()->school->dikdasmen ?? '-' }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Kepala Sekolah:</strong><br>
                                <span>{{ auth()->user()->school->kepsek ?? '-' }}</span>
                            </p>
                            <p class="mb-0">
                                <strong>Bendahara:</strong><br>
                                <span>{{ auth()->user()->school->bendahara ?? '-' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-xxl-9 col-sm-12 col-12 order-xxl-2 order-xl-1 order-lg-1 order-md-1 order-sm-1">
                <div class="card mb-3">
                    <div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Edit Kata Sandi</h5>
						</div>
					</div>
                    <div class="card-body">
                        @if(session('success'))
							<div class="alert alert-success">
								{{ session('success') }}
							</div>
						@endif
                        <form action="{{ route('users.resetPassword') }}" method="POST">
                            @csrf
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-12 col-12">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Kata Sandi">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Kata Sandi">
                                                    @error('password_confirmation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-12" style="margin-bottom: 12px;">
                                    <div class="text-end">
                                        <a href="javascript:void;" class="profilModal btn btn-primary" data-id="{{ auth()->user()->id }}">
											Edit Profil
										</a>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row end -->

    </div>

    <!-- Profil Modal -->
	<div class="modal fade" id="profilModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('users.edit-profile', auth()->user()) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
                        <input type="hidden" name="form_source" value="edit-user-{{ auth()->user()->id }}">

                        <div class="row g-3">
                            <!-- Nama -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', auth()->user()->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', auth()->user()->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Telepon -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (auth()->user()->school_id)
                                <!-- Kota -->
                                <div class="col-md-6">
                                    <label for="city" class="form-label">Kota</label>
                                    <input type="text" 
                                        class="form-control @error('city') is-invalid @enderror" 
                                        id="city" 
                                        name="city" 
                                        value="{{ old('city', auth()->user()->school->city ?? '') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ketua Majelis -->
                                <div class="col-md-6">
                                    <label for="dikdasmen" class="form-label">Nama Ketua Majelis Dikdasmen</label>
                                    <input type="text" 
                                        class="form-control @error('dikdasmen') is-invalid @enderror" 
                                        id="dikdasmen" 
                                        name="dikdasmen" 
                                        value="{{ old('dikdasmen', auth()->user()->school->dikdasmen ?? '') }}">
                                    @error('dikdasmen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Kepala Sekolah -->
                                <div class="col-md-6">
                                    <label for="kepsek" class="form-label">Nama Kepala Sekolah</label>
                                    <input type="text" 
                                        class="form-control @error('kepsek') is-invalid @enderror" 
                                        id="kepsek" 
                                        name="kepsek" 
                                        value="{{ old('kepsek', auth()->user()->school->kepsek ?? '') }}">
                                    @error('kepsek')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bendahara -->
                                <div class="col-md-6">
                                    <label for="bendahara" class="form-label">Nama Bendahara</label>
                                    <input type="text" 
                                        class="form-control @error('bendahara') is-invalid @enderror" 
                                        id="bendahara" 
                                        name="bendahara" 
                                        value="{{ old('bendahara', auth()->user()->school->bendahara ?? '') }}">
                                    @error('bendahara')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Alamat (full width) -->
                                <div class="col-12">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                            id="address" 
                                            name="address">{{ old('address', auth()->user()->school->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Logo (full width) -->
                                <div class="col-12">
                                    <label for="logo" class="form-label">Upload Logo Sekolah (Maks. 2MB)</label>
                                    @if(auth()->user()->school->logo)
                                        <div class="mb-2">
                                            <img src="{{ asset('/' . auth()->user()->school->logo) }}" 
                                                alt="Logo Sekolah" 
                                                style="max-height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" 
                                        class="form-control @error('logo') is-invalid @enderror" 
                                        id="logo" 
                                        name="logo" 
                                        accept="image/*">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
	<script>
		$(document).ready(function(){
			$(document).on('click', '.profilModal', function (e) {
				e.preventDefault();
				$('#profilModal').modal('show');
			});
		});

        document.addEventListener('DOMContentLoaded', function() {
            const oldForm = @json(old('form_source'));
            const valueId = @json('edit-user-' . auth()->user()->id);
            if (oldForm === valueId) {
                const modal = new bootstrap.Modal(document.getElementById('profilModal'));
                modal.show();
            }
        });
	</script>
@endsection
