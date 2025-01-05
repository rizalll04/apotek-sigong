@extends('app')

@section('content')
<div class="container-fluid">
    <h1>Manage Users</h1>

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        {{-- <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                            data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-username="{{ $user->username }}" data-role="{{ $user->role }}">
                            Edit
                        </button> --}}

                        <form action="{{ route('user.destroy', $user->user_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

  <!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Input for Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>

                    <!-- Input for Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>

                    <!-- Select Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" name="role" id="role" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    <!-- Input for Password (optional) -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (optional)</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

@endsection

@section('scripts')
    <script>
      // Mengisi data pengguna ke dalam modal ketika tombol Edit diklik
$('#editModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget); // Tombol yang diklik
    var userId = button.data('id');
    var name = button.data('name');
    var username = button.data('username');
    var role = button.data('role');

    var modal = $(this);
    modal.find('#name').val(name);
    modal.find('#username').val(username);
    modal.find('#role').val(role);

    var actionUrl = '/users/' + userId;
    modal.find('#editUserForm').attr('action', actionUrl);
});

    </script>
@endsection
