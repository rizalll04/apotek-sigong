@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-receipt"></i> Riwayat Transaksi</h1>
        <p class="page-subtitle">Lihat dan kelola semua transaksi penjualan</p>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Controls & Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 mb-3">
                <!-- Filter Form -->
                <div class="col-lg-6">
                    <form method="GET" action="{{ route('penjualan.index') }}" class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <select name="bulan" id="bulan" class="form-select">
                                <option value="">Semua Bulan</option>
                                @foreach ($months as $key => $month)
                                    <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromFormat('m', $key)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <select name="tahun" id="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($years as $year => $data)
                                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </form>
                </div>

                <!-- Import & Delete -->
                <div class="col-lg-6 d-flex gap-2 justify-content-end">
                    <form action="{{ route('penjualan.import.process') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                        @csrf
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-upload me-1"></i>Import
                        </button>
                    </form>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteAllModal">
                            <i class="bi bi-trash me-1"></i>Hapus Semua
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Transaksi</h5>
            <span class="badge bg-primary">{{ count($penjualan) }} Transaksi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle" style="width: 60px;">#</th>
                            <th class="align-middle">Produk</th>
                            <th class="align-middle text-center">Jumlah</th>
                            <th class="align-middle text-end">Harga Satuan</th>
                            <th class="align-middle text-end">Total Harga</th>
                            <th class="align-middle">Metode Pembayaran</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle">Tanggal</th>
                            <th class="align-middle text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualan as $item)
                            <tr>
                                <td class="align-middle fw-bold">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <strong>{{ $item->produk->nama }}</strong>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-info">{{ $item->jumlah }}</span>
                                </td>
                                <td class="align-middle text-end">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="align-middle text-end">
                                    <strong class="text-primary">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong>
                                </td>
                                <td class="align-middle">
                                    @if($item->metode_pembayaran === 'Tunai' || $item->metode_pembayaran === 'Cash')
                                        <span class="badge bg-success">Tunai</span>
                                    @elseif($item->metode_pembayaran)
                                        <span class="badge bg-warning text-dark">{{ $item->metode_pembayaran }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @php
                                        $status = $item->payment_status;
                                        $label = $status === 'paid' ? 'Lunas' : ($status === 'pending' ? 'Pending' : $status);
                                    @endphp
                                    @if($label === 'Lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @elseif($label === 'Pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $label ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                <td class="align-middle text-center">
                                    @php
                                        $isNonTunai = ($item->metode_pembayaran === 'Non Tunai');
                                        $buktiUrl = $item->bukti_transfer ? asset('storage/'.$item->bukti_transfer) : null;
                                    @endphp

                                    @if($isNonTunai && $buktiUrl)
                                        <a href="{{ $buktiUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Lihat Bukti Transfer">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif

                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                        <form action="{{ route('penjualan.destroy', $item->id_penjualan) }}" method="POST" class="d-inline ms-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin?')" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$isNonTunai || !$buktiUrl)
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada transaksi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($penjualan) > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="align-middle text-end">
                                <strong>Total Penjualan:</strong>
                            </th>
                            <th class="align-middle text-end">
                                <strong class="text-primary" style="font-size: 1.1rem;">
                                    Rp {{ number_format($penjualan->sum('total_harga'), 0, ',', '.') }}
                                </strong>
                            </th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@if(Auth::check() && Auth::user()->role === 'admin')
<!-- Modal Konfirmasi Hapus Semua -->
<div class="modal fade" id="confirmDeleteAllModal" tabindex="-1" aria-labelledby="confirmDeleteAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteAllModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Semua
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus semua transaksi? <strong>Tindakan ini tidak bisa dibatalkan.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('penjualan.deleteAll') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
