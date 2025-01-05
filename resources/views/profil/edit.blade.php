@extends('app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h3>Edit Profil Pengguna</h3>

        @if(session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
        @endif

        <!-- Form untuk mengedit profil -->
        <form action="{{ route('profil.update', Auth::id()) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Untuk mengindikasikan update -->

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ old('alamat', $profil->alamat) }}" required>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profil->tanggal_lahir) }}" required>
                @error('tanggal_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto Profil</label>
                <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto">
                @error('foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tampilkan foto lama jika ada -->
            @if($profil->foto)
            <div class="mb-3">
                <strong>Foto Profil Lama:</strong><br />
                <img src="{{ asset('storage/' . $profil->foto) }}" alt="Foto Profil" class="img-fluid" width="200">
            </div>
            @endif

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection
