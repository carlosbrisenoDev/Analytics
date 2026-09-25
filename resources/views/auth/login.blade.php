<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unlock Brands | Analytics Login</title>
    <style>
        :root {
            --bg: #080808;
            --panel: #111111;
            --text: #f3f3f0;
            --muted: #9b9b94;
            --line: #242424;
            --acid: #00ff9c;
            --radius: 12px;
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: system-ui, sans-serif;
            display: grid;
            place-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: var(--panel);
            border: 1px solid var(--line);
            padding: 32px;
            border-radius: var(--radius);
            width: 100%;
            max-width: 380px;
        }
        .login-box h2 {
            margin-top: 0;
            font-size: 24px;
            margin-bottom: 8px;
        }
        .login-box p {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 24px;
        }
        .input-group {
            margin-bottom: 16px;
        }
        .input-group label {
            display: block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            box-sizing: border-box;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--acid);
            color: #000;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .alert {
            background: rgba(255, 77, 77, 0.1);
            color: #ff4d4d;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            border: 1px solid rgba(255,77,77,0.3);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Analytics Dashboard</h2>
        <p>Inicia sesión para ver tus estadísticas.</p>
        
        @if ($errors->any())
            <div class="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="input-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" required autofocus>
            </div>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
