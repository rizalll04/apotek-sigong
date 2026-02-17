<!-- resources/views/pembelian/edit.blade.php -->
@extends('app')

@section('content')
<div class="container-fluid">
    <h2>Edit Pembelian</h2>

    <!-- Form Edit Pembelian -->
    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="produk_id">Produk</label>
            <select name="produk_id" id="produk_id" class="form-control">
                @foreach($produks as $produk)
                    <option value="{{ $produk->id }}" {{ $pembelian->produk_id == $produk->id ? 'selected' : '' }}>
                        {{ $produk->nama }}
                    </option>
                @endforeach
            </select>
            @error('produk_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="qty">Jumlah</label>
            <input type="number" name="qty" id="qty" class="form-control" value="{{ old('qty', $pembelian->qty) }}">
            @error('qty') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="harga_beli">Harga Beli</label>
            <input type="number" name="harga_beli" id="harga_beli" class="form-control" value="{{ old('harga_beli', $pembelian->harga_beli) }}">
            @error('harga_beli') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="tanggal">Tanggal Pembelian</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', $pembelian->tanggal) }}">
            @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-warning mt-3">Update Pembelian</button>
    </form>
</div>
@endsection
