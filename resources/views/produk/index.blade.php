@extends('app')

@section('content')
<div class="container-fluid">
<div class="container mt-4">
    <h1 class="text-center">Manajemen Produk</h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalProduk">Tambah Produk</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Satuan</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>HPP</th>
                <th>Harga Jual</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produk as $item)
            <tr>
                <td>{{ $item->kode_produk }}</td>
                <td>{{ $item->nama_produk }}</td>
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->kategori->nama ?? '-' }}</td>
                <td>{{ $item->stok }}</td>
                <td>{{ $item->hpp }}</td>
                <td>{{ $item->harga_jual }}</td>
                <td>
                    @if($item->gambar)
                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="Gambar" width="50">
                    @else
                    Tidak ada
                    @endif
                </td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditProduk{{ $item->id_produk }}">Edit</button>
                    <form action="{{ route('produk.destroy', $item->id_produk) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>

              <!-- Modal Edit Produk -->
              <div class="modal fade" id="modalEditProduk{{ $item->id_produk }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('produk.update', $item->id_produk) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Produk</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Kode Produk -->
                                <div class="mb-3">
                                    <label>Kode Produk</label>
                                    <input type="text" name="kode_produk" class="form-control" value="{{ $item->kode_produk }}" required>
                                </div>

                                <!-- Nama Produk -->
                                <div class="mb-3">
                                    <label>Nama Produk</label>
                                    <input type="text" name="nama_produk" class="form-control" value="{{ $item->nama_produk }}" required>
                                </div>

                                <!-- Satuan -->
                                <div class="mb-3">
                                    <label>Satuan</label>
                                    <input type="text" name="satuan" class="form-control" value="{{ $item->satuan }}" required>
                                </div>

                                <!-- Kategori -->
                                <div class="mb-3">
                                    <label>Kategori</label>
                                    <select name="kategori_produk" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategori as $kat)
                                        <option value="{{ $kat->id_kategori }}" @if($kat->id_kategori == $item->kategori_produk) selected @endif>{{ $kat->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Stok -->
                                <div class="mb-3">
                                    <label>Stok</label>
                                    <input type="number" name="stok" class="form-control" value="{{ $item->stok }}" required>
                                </div>

                                <!-- HPP -->
                                <div class="mb-3">
                                    <label>HPP</label>
                                    <input type="number" step="0.01" name="hpp" class="form-control" value="{{ $item->hpp }}" required>
                                </div>

                                <!-- Harga Jual -->
                                <div class="mb-3">
                                    <label>Harga Jual</label>
                                    <input type="number" step="0.01" name="harga_jual" class="form-control" value="{{ $item->harga_jual }}" required>
                                </div>

                                <!-- Gambar -->
                                <div class="mb-3">
                                    <label>Gambar</label>
                                    <input type="file" name="gambar" class="form-control">
                                    @if($item->gambar)
                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="Gambar" width="100" class="mt-2">
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Modal Tambah -->
<div class="modal fade" id="modalProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Kode Produk</label>
                        <input type="text" name="kode_produk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Satuan</label>
                        <input type="text" name="satuan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select name="kategori_produk" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategori as $kat)
                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>HPP</label>
                        <input type="number" step="0.01" name="hpp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga Jual</label>
                        <input type="number" step="0.01" name="harga_jual" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Gambar</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

