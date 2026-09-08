<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf

        <label for="email">Email</label><br>
        <input type="email" name="email" id="email" value="{{ old('email') }}"><br><br>

        <label for="password">Password</label><br>
        <input type="password" name="password" id="password"><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>