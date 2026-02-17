@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-graph-up"></i> Analisis Produk</h1>
        <p class="page-subtitle">Analisis penjualan dan prediksi pembelian produk</p>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('manajemen.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">
                            <i class="bi bi-search me-1"></i>Nama Produk
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Cari nama produk..." 
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">
                            <i class="bi bi-calendar me-1"></i>Bulan
                        </label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                    {{ \DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Produk Terjual</h5>
            <span class="badge bg-primary">{{ count($produkTerjual) }} Produk</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle" style="width: 60px;">#</th>
                            <th class="align-middle">Nama Produk</th>
                            <th class="align-middle text-center">Jumlah Terjual</th>
                            <th class="align-middle text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produkTerjual as $produkId => $data)
                            <tr>
                                <td class="align-middle fw-bold">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); border-radius: 0.5rem; color: white;">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <strong>{{ $data['produk'] }}</strong>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-success" style="font-size: 0.95rem;">{{ $data['jumlah_terjual'] }} unit</span>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('manajemen.prediksi', $produkId) }}" class="btn btn-sm btn-outline-primary" title="Lihat Prediksi">
                                        <i class="bi bi-bar-chart-line"></i> Prediksi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada data produk yang terjual</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
