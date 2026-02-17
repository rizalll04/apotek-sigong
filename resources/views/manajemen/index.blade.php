@extends('app')

@section('content')
<div class="container-fluid">
    <h1>Manajemen Produk</h1>

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Pencarian -->
    <form action="{{ route('manajemen.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Cari nama produk" 
                    value="{{ request('search') }}">
            </div>
           <div class="col-md-3">
                <select name="bulan" class="form-control">
                    <option value="">Pilih Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div> 
              <!--<div class="col-md-3">
                <select name="tahun" class="form-control">
                    <option value="">Pilih Tahun</option>
                    @for ($i = date('Y'); $i >= 2000; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div> 
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Cari</button>
            </div>
        </div>
    </form>



    <!-- Table of products -->
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nama</th>
                <th scope="col">Jumlah Terjual</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($produkTerjual as $produkId => $data)
            <tr>
                <th scope="row">{{ $no++ }}</th>
                <td>{{ $data['produk'] }}</td>
                <td>{{ $data['jumlah_terjual'] }}</td>
                <td>
                    <a href="{{ route('manajemen.prediksi', $produkId) }}" class="btn btn-info btn-sm">
                        <i class="bi bi-bar-chart-line"></i> Prediksi Pembelian
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
