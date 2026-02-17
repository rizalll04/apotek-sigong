@extends('app')

@section('content')
<div class="container mt-5">
    <h2>Import Data Produk dari Excel</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('produk.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel:</label>
            <input type="file" name="file" class="form-control" required accept=".xlsx, .xls, .csv">
        </div>
        <button type="submit" class="btn btn-primary">Import Data</button>
    </form>

    <div class="mt-3">
        <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali ke Daftar Produk</a>
    </div>
</div>
@endsection
