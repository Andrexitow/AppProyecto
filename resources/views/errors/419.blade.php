<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Redirección automática por si el usuario no hace clic --}}
    <meta http-equiv="refresh" content="4;url={{ url('/login') }}">
    <title>Sesión expirada — NussoraPos</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Inter, system-ui, sans-serif;
            background:
                radial-gradient(120% 140% at 15% 10%, #0ea5e9 0%, transparent 55%),
                radial-gradient(120% 140% at 85% 90%, #a855f7 0%, transparent 55%),
                linear-gradient(135deg, #0b1a4d 0%, #16227a 45%, #3312a3 100%);
            color: #e2e8f0;
        }

        .card {
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 36px;
            max-width: 380px;
            width: calc(100% - 40px);
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: rgba(96, 165, 250, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h1 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #fff;
        }

        p {
            font-size: 13px;
            color: #cbd5e1;
            line-height: 1.5;
            margin: 0 0 24px;
        }

        a.btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(90deg, #2563eb, #4338ca 55%, #a855f7);
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            padding: 11px 22px;
            border-radius: 10px;
        }

        .hint {
            margin-top: 16px;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2">
                <circle cx="12" cy="12" r="9" />
                <path d="M12 7v5l3 2" />
            </svg>
        </div>
        <h1>Tu sesión expiró</h1>
        <p>
            Por seguridad, la página que tenías abierta ya no es válida
            (esto pasa si vuelves a una pantalla anterior después de
            cerrar sesión, o si estuvo inactiva mucho tiempo).
            Vuelve a iniciar sesión para continuar.
        </p>
        <a class="btn" href="{{ url('/login') }}">Ir a iniciar sesión</a>
        <p class="hint">Te llevaremos ahí automáticamente en unos segundos...</p>
    </div>
</body>

</html>
