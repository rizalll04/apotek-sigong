@extends('app')

@section('content')
<div class="row">
    <div class="col-md-6">
        @if(session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
        @endif

        <h3>Profil Pengguna</h3>
        
        <div class="mb-3">
            <strong>Username:</strong> {{ Auth::user()->username }}
        </div>

        <div class="mb-3">
            <strong>Alamat:</strong> {{ $profil->alamat }}
        </div>

        <div class="mb-3">
            <strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($profil->tanggal_lahir)->format('d M Y') }}
        </div>

        @if($profil->foto)
        <div class="mb-3">
            <strong>Foto:</strong><br />
            <img src="{{ asset('storage/' . $profil->foto) }}" alt="Foto Profil" class="img-fluid" width="200">
        </div>
        
        @else
        <div class="mb-3">
            <strong>Foto:</strong> Tidak ada foto yang diunggah.
        </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('profil.edit', Auth::id()) }}" class="btn btn-warning">Edit Profil</a>
        </div>
    </div>
</div>
@endsection
