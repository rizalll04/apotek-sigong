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
                        <!-- Link untuk mengedit pengguna -->
                        <a href="{{ route('user.edit', $user->user_id) }}" class="btn btn-warning btn-sm">
                            Edit
                        </a>                        

                        <!-- Form untuk menghapus pengguna -->
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
