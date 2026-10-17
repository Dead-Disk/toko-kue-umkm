<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Penjual - Toko Kue</title>
</head>
<body>
    <h1>Login Penjual</h1>

    <p><a href="{{ route('customer.index') }}">Kembali ke Web Pembeli</a></p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('login.store') }}" method="POST">
        @csrf

        <p>
            <label for="username">Username</label><br>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
        </p>

        <p>
            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required>
        </p>

        <p>
            <label>
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                Ingat saya
            </label>
        </p>

        <p>
            <button type="submit">Masuk</button>
        </p>
    </form>
</body>
</html>
