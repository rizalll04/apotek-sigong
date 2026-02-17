@extends('app') <!-- Ganti dengan layout yang digunakan di aplikasi Anda -->

@section('content')
<div class="container-fluid">
    <h2>Edit Penjualan</h2>

    <!-- Menampilkan pesan sukses atau error jika ada -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form untuk edit penjualan -->
    <form action="{{ route('penjualan.update', $penjualan->id_penjualan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="produk_id">Produk</label>
            <select name="produk_id" id="produk_id" class="form-control">
                <option value="">Pilih Produk</option>
                @foreach($produk as $item)
                    <option value="{{ $item->id }}" {{ $item->id == $penjualan->produk_id ? 'selected' : '' }}>
                        {{ $item->nama }}
                    </option>
                @endforeach
            </select>
            @error('produk_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="jumlah">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ old('jumlah', $penjualan->jumlah) }}">
            @error('jumlah')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" name="harga" id="harga" class="form-control" value="{{ old('harga', $penjualan->harga) }}">
            @error('harga')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal">Tanggal Penjualan</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', $penjualan->tanggal->format('Y-m-d')) }}">


            @error('tanggal')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Penjualan</button>
            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
