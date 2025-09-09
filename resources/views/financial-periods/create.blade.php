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
        <li class="breadcrumb-item">
            <a href="{{ route('school-financial-periods.index', $school) }}" class="text-decoration-none">Kelola Periode Keuangan</a>
        </li>
        <li class="breadcrumb-item" aria-current="page">Tambah Periode</li>
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
                    <h5 class="card-title">Tambah Periode Keuangan</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('school-financial-periods.store', $school) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Periode</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Jadikan periode aktif</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('school-financial-periods.index', $school) }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->
</div>
<!-- App body ends -->

@endsection