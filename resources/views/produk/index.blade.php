@extends('app') <!-- Sesuaikan dengan layout Anda -->

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Daftar Produk</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex">
            @if(Auth::check() && (Auth::user()->role == 'admin'))
                <!-- Tombol Tambah Produk -->
                <div class="me-3">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">Tambah Produk</button>
                </div>
            @endif
        
            <!-- Form Import Excel -->
            <div class="d-flex align-items-center">
                <form action="{{ route('produk.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center">
                    @csrf
                    <div class="mb-0 d-flex align-items-center me-3">
                        <input type="file" name="file" class="form-control form-control-sm" required accept=".xlsx, .xls, .csv">
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm">Import Data</button>
                </form>
            </div>
        </div>
        
    
        <!-- Form Pencarian -->
        <form method="GET" action="{{ route('produk.index') }}" class="d-flex">
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk...">
                <button class="btn btn-primary btn-sm" type="submit">Cari</button>
            </div>
        </form>
        <div>  
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteAllModal">Hapus Semua</button>
    </div>
    </div>
    
    
    <!-- Alert -->
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Stok</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Kategori</th>
            
                <th>Tanggal Kadaluarsa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produk as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->stok }}</td>
                <td>{{ 'Rp ' . number_format($item->harga_beli, 0, ',', '.') }}</td>
                <td>{{ 'Rp ' . number_format($item->harga_jual, 0, ',', '.') }}</td>
                <td>{{ $item->kategori }}</td>
             
                <td>{{ $item->tanggal_kadaluarsa }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">Edit</button>
                    <form action="{{ route('produk.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                    </form>
                </td>
            </tr>



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
        </tbody>
    </table>
</div>





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
