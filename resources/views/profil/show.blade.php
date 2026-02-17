@extends('app')

@section('content')
<div class="container-fluid" >
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        @if($profil->foto)
                        <img src="{{ asset('storage/' . $profil->foto) }}" alt="Foto Profil" 
                             class="rounded-circle shadow-sm" 
                             style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #f0f0f0;">
                        @else
                        <div class="rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px; background-color: #ddd; font-size: 20px; color: #555;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        @endif
                    </div>

                    <h2 class="text-center" style="font-size: 24px; font-weight: 700;">{{ Auth::user()->username }}</h2>
                    <p class="text-center text-muted" style="font-size: 14px;">Pengguna Terdaftar</p>

                    <hr style="border-top: 1px solid #eaeaea;">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Alamat:</strong>
                            <p class="text-muted">{{ $profil->alamat }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Tanggal Lahir:</strong>
                            <p class="text-muted">{{ \Carbon\Carbon::parse($profil->tanggal_lahir)->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('profil.edit', Auth::id()) }}" class="btn btn-primary px-4 py-2" style="border-radius: 25px;">
                            <i class="bi bi-pencil"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
