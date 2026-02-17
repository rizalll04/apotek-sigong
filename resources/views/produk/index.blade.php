@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-box-seam"></i> Daftar Produk</h1>
        <p class="page-subtitle">Kelola semua produk di apotek Anda</p>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Controls & Search Bar -->
    <div class="row mb-4 g-3">
        <div class="col-lg-6">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'apoteker']))
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Produk
                    </button>
                @endif
                
                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'apoteker']))
                    <form action="{{ route('produk.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                        @csrf
                        <input type="file" name="file" class="form-control" style="width: 200px;" required accept=".xlsx, .xls, .csv">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-upload me-1"></i> Import
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex gap-2 align-items-center flex-wrap justify-content-lg-end">
                <form method="GET" action="{{ route('produk.index') }}" class="flex-grow-1 flex-lg-grow-0 d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama produk...">
                    </div>
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
                @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'apoteker']))
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteAllModal">
                        <i class="bi bi-trash me-1"></i> Hapus Semua
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Produk</h5>
            <span class="badge bg-primary">{{ count($produk) }} Produk</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle">#</th>
                            <th class="align-middle">Nama Produk</th>
                            <th class="align-middle text-center">Stok</th>
                            <th class="align-middle text-end">Harga Beli</th>
                            <th class="align-middle text-end">Harga Jual</th>
                            <th class="align-middle">Kategori</th>
                            <th class="align-middle">Masa Berlaku</th>
                            <th class="align-middle text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produk as $item)
                            <tr>
                                <td class="align-middle fw-bold">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div>
                                            <div class="fw-600">{{ $item->nama }}</div>
                                            <small class="text-muted">{{ $item->keterangan ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    @if($item->stok > 0)
                                        <span class="badge bg-success">{{ $item->stok }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $item->stok }}</span>
                                    @endif
                                </td>
                                <td class="align-middle text-end">
                                    <strong>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</strong>
                                </td>
                                <td class="align-middle text-end">
                                    <strong class="text-primary">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</strong>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info text-dark">{{ $item->kategori }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($item->tanggal_kadaluarsa)
                                        @php
                                            $expireDate = \Carbon\Carbon::parse($item->tanggal_kadaluarsa);
                                            $daysLeft = $expireDate->diffInDays(now());
                                            $isExpired = $expireDate->isPast();
                                        @endphp
                                        @if($isExpired)
                                            <span class="badge bg-danger">Kadaluarsa</span>
                                        @elseif($daysLeft <= 30)
                                            <span class="badge bg-warning text-dark">{{ $daysLeft }} hari</span>
                                        @else
                                            <small class="text-muted">{{ $expireDate->format('d M Y') }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('produk.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada produk</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals - Outside Table -->
@foreach ($produk as $item)
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('produk.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" value="{{ $item->nama }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control" name="stok" value="{{ $item->stok }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="harga_beli" class="form-label">Harga Beli</label>
                            <input type="number" class="form-control" name="harga_beli" value="{{ $item->harga_beli }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <input type="number" class="form-control" name="harga_jual" value="{{ $item->harga_jual }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" class="form-control" name="kategori" value="{{ $item->kategori }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3">{{ $item->keterangan }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                            <input type="date" class="form-control" name="tanggal_kadaluarsa" value="{{ $item->tanggal_kadaluarsa }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach





</div>
   



 <!-- Modal Konfirmasi Hapus Semua -->
 <div class="modal fade" id="confirmDeleteAllModal" tabindex="-1" aria-labelledby="confirmDeleteAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteAllModalLabel">Konfirmasi Hapus Semua</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus semua produk? Tindakan ini tidak bisa dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <form action="{{ route('produk.deleteAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Hapus Semua</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('produk.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" name="stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli</label>
                        <input type="number" class="form-control" name="harga_beli" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <input type="number" class="form-control" name="harga_jual" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" name="kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                        <input type="date" class="form-control" name="tanggal_kadaluarsa">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
