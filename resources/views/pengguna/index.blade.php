@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-person-circle"></i> Akun Saya</h1>
        <p class="page-subtitle">Kelola profil dan pengaturan akun Anda</p>
    </div>

    <!-- Welcome Card -->
    <div class="card border-0 shadow-sm mb-4 bg-primary-light">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-4">
                <div class="avatar" style="width: 80px; height: 80px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 2rem;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="mb-1" style="color: #0d6efd;">Selamat datang, <strong>{{ Auth::user()->name }}</strong></h3>
                    <p class="text-muted mb-0">
                        <i class="bi bi-tag me-1"></i>
                        Peran: <strong>{{ ucfirst(Auth::user()->role) }}</strong>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-at me-1"></i>
                        Username: <strong>{{ Auth::user()->username }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Actions -->
    <div class="row mb-4">
        <!-- Change Password Card -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-lock" style="font-size: 3rem; color: #0d6efd;"></i>
                    <h5 class="mt-3 mb-2">Ubah Kata Sandi</h5>
                    <p class="text-muted mb-3">Perbarui kata sandi akun Anda untuk keamanan lebih baik</p>
                    <a href="{{ route('password') }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Ubah Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Logout Card -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-box-arrow-right" style="font-size: 3rem; color: #dc3545;"></i>
                    <h5 class="mt-3 mb-2">Keluar</h5>
                    <p class="text-muted mb-3">Akhiri sesi dan keluar dari sistem</p>
                    <a href="{{ route('logout') }}" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Actions -->
    @if(Auth::guest())
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box-arrow-in-right" style="font-size: 3rem; color: #198754;"></i>
                        <h5 class="mt-3 mb-2">Login</h5>
                        <p class="text-muted mb-3">Masuk ke akun Anda</p>
                        <a href="{{ route('login') }}" class="btn btn-success">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Register & Create Profile -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-person-plus" style="font-size: 3rem; color: #0dcaf0;"></i>
                    <h5 class="mt-3 mb-2">Daftar Akun Baru</h5>
                    <p class="text-muted mb-3">Buat akun pengguna baru di sistem</p>
                    <a href="{{ route('register') }}" class="btn btn-info text-dark">
                        <i class="bi bi-person-plus me-1"></i>Daftar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-person" style="font-size: 3rem; color: #198754;"></i>
                    <h5 class="mt-3 mb-2">Buat Profil</h5>
                    <p class="text-muted mb-3">Lengkapi informasi profil Anda</p>
                    <a href="{{ route('profil.create') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-person me-1"></i>Buat Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
