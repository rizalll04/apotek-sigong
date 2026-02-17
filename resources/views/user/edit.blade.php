@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-pencil"></i> Edit Pengguna</h1>
        <p class="page-subtitle">Perbarui informasi dan role pengguna</p>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
            <form action="{{ route('user.update', $user->user_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Nama -->
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label fw-6 mb-2">Nama Lengkap</label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name"
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="col-md-6 mb-4">
                        <label for="username" class="form-label fw-6 mb-2">Username</label>
                        <input 
                            type="text" 
                            class="form-control @error('username') is-invalid @enderror" 
                            id="username"
                            name="username" 
                            value="{{ old('username', $user->username) }}" 
                            required
                            placeholder="Masukkan username">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Role -->
                    <div class="col-md-6 mb-4">
                        <label for="role" class="form-label fw-6 mb-2">Peran (Role)</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                <i class="bi bi-shield-check"></i> Administrator
                            </option>
                            <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>
                                <i class="bi bi-cash-coin"></i> Kasir
                            </option>
                            <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>
                                <i class="bi bi-briefcase"></i> Owner
                            </option>
                            <option value="apoteker" {{ old('role', $user->role) == 'apoteker' ? 'selected' : '' }}>
                                <i class="bi bi-bandaid"></i> Apoteker
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

                    <!-- Status Badge -->
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-6 mb-2">Peran Saat Ini</label>
                        <div>
                            @if (strtolower($user->role) === 'admin')
                                <span class="badge bg-danger fs-6"><i class="bi bi-shield-check me-1"></i> Administrator</span>
                            @elseif (strtolower($user->role) === 'kasir')
                                <span class="badge bg-info text-dark fs-6"><i class="bi bi-cash-coin me-1"></i> Kasir</span>
                            @elseif (strtolower($user->role) === 'owner')
                                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-briefcase me-1"></i> Owner</span>
                            @elseif (strtolower($user->role) === 'apoteker')
                                <span class="badge bg-success fs-6"><i class="bi bi-bandaid me-1"></i> Apoteker</span>
                            @else
                                <span class="badge bg-secondary fs-6">{{ ucfirst($user->role) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <!-- Password -->
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label fw-6 mb-2">Password Baru <span class="text-muted">(Opsional)</span></label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password"
                            name="password" 
                            placeholder="Biarkan kosong jika tidak ingin mengubah">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label fw-6 mb-2">Konfirmasi Password</label>
                        <input 
                            type="password" 
                            class="form-control @error('password_confirmation') is-invalid @enderror" 
                            id="password_confirmation"
                            name="password_confirmation" 
                            placeholder="Konfirmasi password baru">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Description Script -->
<script>
    const roleDescriptions = {
        'admin': 'Akses penuh ke semua fitur sistem (Dashboard, Manajemen User, Produk, Transaksi, Laporan)',
        'kasir': 'Akses untuk input transaksi dan melihat riwayat (Keranjang, Riwayat Transaksi)',
        'owner': 'Akses read-only untuk laporan dan peramalan (Riwayat, Peramalan, Laporan)',
        'apoteker': 'Akses untuk kelola produk dan stok (Produk, Peramalan, Laporan Stok)'
    };

    document.getElementById('role').addEventListener('change', function(e) {
        const desc = roleDescriptions[this.value] || 'Pilih peran untuk menentukan akses pengguna';
        document.getElementById('roleDescription').textContent = desc;
    });

    // Set initial description
    const initialRole = document.getElementById('role').value;
    if (initialRole) {
        document.getElementById('roleDescription').textContent = roleDescriptions[initialRole];
    }
</script>
@endsection
