@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-person-plus"></i> Tambah Pengguna</h1>
        <p class="page-subtitle">Buat akun pengguna baru dengan peran yang sesuai</p>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>Ada kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('user.store') }}" method="POST" novalidate>
                @csrf

                <div class="row">
                    <!-- Nama -->
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label fw-6 mb-2">Nama Lengkap <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name"
                            name="name" 
                            value="{{ old('name') }}" 
                            required
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="col-md-6 mb-4">
                        <label for="username" class="form-label fw-6 mb-2">Username <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('username') is-invalid @enderror" 
                            id="username"
                            name="username" 
                            value="{{ old('username') }}" 
                            required
                            placeholder="Masukkan username (unik)">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Role -->
                    <div class="col-md-6 mb-4">
                        <label for="role" class="form-label fw-6 mb-2">Peran (Role) <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                Administrator
                            </option>
                            <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>
                                Kasir
                            </option>
                            <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>
                                Owner
                            </option>
                            <option value="apoteker" {{ old('role') == 'apoteker' ? 'selected' : '' }}>
                                Apoteker
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <!-- Role Description -->
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i>
                            <span id="roleDescription">Pilih peran untuk menentukan akses pengguna</span>
                        </small>
                    </div>

                    <!-- Role Badge Preview -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-6 mb-2">Preview Peran</label>
                        <div>
                            <span class="badge bg-secondary fs-6" id="roleBadge">
                                <i class="bi bi-person me-1"></i> Belum Dipilih
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <!-- Password -->
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label fw-6 mb-2">Password <span class="text-danger">*</span></label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password"
                            name="password" 
                            required
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label fw-6 mb-2">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input 
                            type="password" 
                            class="form-control @error('password_confirmation') is-invalid @enderror" 
                            id="password_confirmation"
                            name="password_confirmation" 
                            required
                            placeholder="Masukkan ulang password">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Role Info Cards -->
                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Informasi Peran:</strong>
                            <ul class="mb-0 mt-2 ms-3">
                                <li><strong>Administrator:</strong> Akses penuh ke semua fitur (Dashboard, Manajemen User, Produk, Transaksi, Laporan)</li>
                                <li><strong>Kasir:</strong> Akses input transaksi dan riwayat (Keranjang, Riwayat Transaksi)</li>
                                <li><strong>Owner:</strong> Akses read-only untuk laporan dan peramalan (Riwayat, Peramalan, Laporan)</li>
                                <li><strong>Apoteker:</strong> Akses kelola produk dan stok (Produk, Peramalan, Laporan Stok)</li>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i> Buat Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Description & Badge Script -->
<script>
    const roleInfo = {
        'admin': {
            description: 'Akses penuh ke semua fitur sistem (Dashboard, Manajemen User, Produk, Transaksi, Laporan)',
            badge: '<span class="badge bg-danger fs-6"><i class="bi bi-shield-check me-1"></i> Administrator</span>'
        },
        'kasir': {
            description: 'Akses untuk input transaksi dan melihat riwayat (Keranjang, Riwayat Transaksi)',
            badge: '<span class="badge bg-info text-dark fs-6"><i class="bi bi-cash-coin me-1"></i> Kasir</span>'
        },
        'owner': {
            description: 'Akses read-only untuk laporan dan peramalan (Riwayat, Peramalan, Laporan)',
            badge: '<span class="badge bg-warning text-dark fs-6"><i class="bi bi-briefcase me-1"></i> Owner</span>'
        },
        'apoteker': {
            description: 'Akses untuk kelola produk dan stok (Produk, Peramalan, Laporan Stok)',
            badge: '<span class="badge bg-success fs-6"><i class="bi bi-bandaid me-1"></i> Apoteker</span>'
        }
    };

    document.getElementById('role').addEventListener('change', function(e) {
        if (this.value && roleInfo[this.value]) {
            document.getElementById('roleDescription').textContent = roleInfo[this.value].description;
            document.getElementById('roleBadge').innerHTML = roleInfo[this.value].badge;
        } else {
            document.getElementById('roleDescription').textContent = 'Pilih peran untuk menentukan akses pengguna';
            document.getElementById('roleBadge').innerHTML = '<span class="badge bg-secondary fs-6"><i class="bi bi-person me-1"></i> Belum Dipilih</span>';
        }
    });
</script>
@endsection
