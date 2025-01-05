@extends('app')

@section('content')
<div class="container-fluid">
<div class="container">
    <h2>Daftar Kategori</h2>
    <button class="btn btn-primary" id="btn-add">Tambah Kategori</button>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kategori as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama }}</td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $item->id_kategori }}">Edit</button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id_kategori }}">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="kategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="kategoriForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="kategoriModalLabel">Tambah/Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="kategoriId" name="id">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = new bootstrap.Modal(document.getElementById('kategoriModal'));
        const form = document.getElementById('kategoriForm');
        let actionUrl = '';

        // Tambah Kategori
        document.getElementById('btn-add').addEventListener('click', () => {
            form.reset();
            document.getElementById('kategoriId').value = '';
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
                      .then(data => alert(data.message))
                      .then(() => location.reload());
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
              .then(data => alert(data.message))
              .then(() => location.reload());
        });
    });
</script>
@endsection
