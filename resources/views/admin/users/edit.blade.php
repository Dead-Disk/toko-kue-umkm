<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Toko Kue</title>
</head>
<body>
    <h1>Edit User</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.users.index') }}">Kelola User / Staf</a>
    </p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="name">Nama</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </p>

        <p>
            <label for="username">Username (dipakai untuk login)</label><br>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </p>

        <p>
            <label for="role">Role</label><br>
            <select id="role" name="role" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $role }}</option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="password">Password Baru (kosongkan bila tidak ingin reset)</label><br>
            <input type="password" id="password" name="password">
        </p>

        <p>
            <label for="password_confirmation">Ulangi Password Baru</label><br>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </p>

        <p>
            <button type="submit">Simpan Perubahan</button>
        </p>
    </form>
</body>
</html>
