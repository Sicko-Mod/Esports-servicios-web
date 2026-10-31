<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · {{ config('club.nombre') }}</title>
    <link rel="stylesheet" href="{{ asset('css/jaguares.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="admin-login">
        <h1 class="admin-titulo">Panel de administración</h1>
        <p>Solo cuentas autorizadas del club.</p>

        @if ($errors->any())
            <div class="admin-errores" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.login.store') }}" class="admin-form">
            @csrf
            <label>
                <span>Correo</span>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>
            </label>
            <label>
                <span>Contraseña</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <div class="admin-form__fila">
                <input type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
                <label for="remember" style="margin:0">Recordarme</label>
            </div>
            <button type="submit" class="admin-btn">Entrar</button>
        </form>

        <p style="margin-top:1.5rem"><a href="{{ route('inicio') }}">← Volver al sitio</a></p>
    </div>
</body>
</html>
