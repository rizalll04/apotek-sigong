@extends('app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-tag"></i> Kelola Kategori</h1>
        <p class="page-subtitle">Atur kategori produk di apotek Anda</p>
    </div>

    <!-- Add Button -->
    <div class="mb-4">
        <button class="btn btn-primary" id="btn-add">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
        </button>
    </div>

    <!-- Categories Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Kategori</h5>
            <span class="badge bg-primary" id="kategoriCount">{{ count($kategori) }} Kategori</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle" style="width: 60px;">#</th>
                            <th class="align-middle">Nama Kategori</th>
                            <th class="align-middle text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kategoriTableBody">
                        @forelse ($kategori as $item)
                            <tr>
                                <td class="align-middle fw-bold">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); border-radius: 0.5rem; color: white;">
                                            <i class="bi bi-tag"></i>
                                        </div>
                                        <strong>{{ $item->nama }}</strong>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <button class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $item->id_kategori }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $item->id_kategori }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada kategori</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="kategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="kategoriForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="kategoriModalLabel">
                        <i class="bi bi-tag me-2"></i>Tambah/Edit Kategori
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="kategoriId" name="id">
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-600">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama" name="nama" required placeholder="Masukkan nama kategori">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = new bootstrap.Modal(document.getElementById('kategoriModal'));
        const form = document.getElementById('kategoriForm');
        let actionUrl = '';

        // Tambah Kategori
        document.getElementById('btn-add').addEventListener('click', () => {
            form.reset();
            document.getElementById('kategoriId').value = '';
            document.getElementById('kategoriModalLabel').innerHTML = '<i class="bi bi-tag me-2"></i>Tambah Kategori';
            actionUrl = "{{ route('kategori.store') }}";
            modal.show();
        });

        // Edit Kategori
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                fetch(`{{ url('kategori/edit') }}/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('kategoriId').value = id;
                        document.getElementById('nama').value = data.nama;
                        document.getElementById('kategoriModalLabel').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Kategori';
                        actionUrl = `{{ url('kategori/update') }}/${id}`;
                        modal.show();
                    });
            });
        });

        // Hapus Kategori
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                    fetch(`{{ url('kategori/delete') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => response.json())
                      .then(data => {
                          alert(data.message);
                          location.reload();
                      });
                }
            });
        });

        // Submit Form (Tambah/Edit)
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
              .then(data => {
                  alert(data.message);
                  location.reload();
              });
        });
    });
</script>
@endsection
