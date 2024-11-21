<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd; /* Azul claro */
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Contenedor principal del email */
        .email-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        /* Título principal */
        h1 {
            color: #0d47a1; /* Azul fuerte */
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        /* Párrafos */
        p {
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        /* Botón */
        a {
            display: inline-block;
            background-color: #1e88e5; /* Azul medio */
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.2rem;
            border-radius: 5px;
            font-size: 1rem;
            margin-top: 1rem;
            font-weight: bold;
        }

        a:hover {
            background-color: #1565c0; /* Azul más oscuro */
        }   

        /* Pie de página */
        .footer {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #757575; /* Gris claro */
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Recuperar Contraseña</h1>
        <p>Hola {{ $user->usuario_nom }},</p>
        <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
        <a href="{{ $url }}">Restablecer Contraseña</a>
        <p>Este enlace expirará en 5 minutos.</p>
        <div class="footer">
            <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        </div>
    </div>
</body>
</html>
