@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-2"><i class="bi bi-people"></i> Kelola Pengguna</h1>
            <p class="page-subtitle">Atur akses dan peran pengguna sistem</p>
        </div>
        <a href="{{ route('user.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Pengguna
        </a>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Pengguna</h5>
            <span class="badge bg-primary">{{ count($users) }} Pengguna</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle">Nama</th>
                            <th class="align-middle">Username</th>
                            <th class="align-middle">Peran</th>
                            <th class="align-middle text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <code style="background-color: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">{{ $user->username }}</code>
                                </td>
                                <td class="align-middle">
                                    @if (strtolower($user->role) === 'admin')
                                        <span class="badge bg-danger"><i class="bi bi-shield-check me-1"></i> Administrator</span>
                                    @elseif (strtolower($user->role) === 'kasir')
                                        <span class="badge bg-info text-dark"><i class="bi bi-cash-coin me-1"></i> Kasir</span>
                                    @elseif (strtolower($user->role) === 'owner')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-briefcase me-1"></i> Owner</span>
                                    @elseif (strtolower($user->role) === 'apoteker')
                                        <span class="badge bg-success"><i class="bi bi-bandaid me-1"></i> Apoteker</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-person me-1"></i> {{ ucfirst($user->role) }}</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('user.edit', $user->user_id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('user.destroy', $user->user_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus pengguna ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada pengguna</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Optional: Jika Anda masih ingin menggunakan modal di masa depan, Anda bisa menggunakan kode berikut.
    // $('#editModal').on('show.bs.modal', function(event) {
    //     var button = $(event.relatedTarget); // Tombol yang diklik
    //     var userId = button.data('id');
    //     var name = button.data('name');
    //     var username = button.data('username');
    //     var role = button.data('role');

    //     var modal = $(this);
    //     modal.find('#name').val(name);
    //     modal.find('#username').val(username);
    //     modal.find('#role').val(role);

    //     var actionUrl = '/users/' + userId;
    //     modal.find('#editUserForm').attr('action', actionUrl);
    // });
</script>
@endsection
