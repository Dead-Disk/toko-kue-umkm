<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Admin Toko Kue</title>
</head>
<body>
    <h1>Tambah User</h1>

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

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <p>
            <label for="name">Nama</label><br>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </p>

        <p>
            <label for="username">Username (dipakai untuk login)</label><br>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </p>

        <p>
            <label for="role">Role</label><br>
            <select id="role" name="role" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', 'staff') === $role)>{{ $role }}</option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="password">Password (minimal 8 karakter)</label><br>
            <input type="password" id="password" name="password" required>
        </p>

        <p>
            <label for="password_confirmation">Ulangi Password</label><br>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </p>

        <p>
            <button type="submit">Simpan User</button>
        </p>
    </form>
</body>
</html>
