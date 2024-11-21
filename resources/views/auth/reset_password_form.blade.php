<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd; /* Fondo azul claro */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h1 {
            color: #0d47a1; /* Azul fuerte */
        }

        /* Estilo del contenedor del formulario */
        .form-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Estilos del formulario */
        form {
            display: flex;
            flex-direction: column;
            margin-top: 1rem;
        }

        label {
            text-align: left;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #1565c0; /* Azul intermedio */
        }

        input[type="password"] {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #90caf9; /* Borde azul claro */
            border-radius: 5px;
            font-size: 1rem;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: #1e88e5; /* Azul más intenso */
            box-shadow: 0 0 4px rgba(30, 136, 229, 0.5);
        }

        button {
            padding: 0.8rem;
            background-color: #1e88e5; /* Azul medio */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1565c0; /* Azul más oscuro */
        }

        /* Mensaje de error */
        .error {
            color: #d32f2f; /* Rojo para destacar errores */
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: none;
        }

        /* Imagen decorativa */
        .form-container img {
            width: 100px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Restablecer Contraseña</h1>
        <form action="{{ url('api/reset-password/' . $user->id) }}" method="POST" onsubmit="return validateForm()">
            @csrf
            <div>
                <label for="password">Nueva Contraseña:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="password_confirmation">Confirmar Nueva Contraseña:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <div class="error" id="error-message">Las contraseñas no coinciden.</div>
            <button type="submit">Restablecer Contraseña</button>
        </form>
    </div>

    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const errorMessage = document.getElementById('error-message');

            if (password !== passwordConfirmation) {
                errorMessage.style.display = 'block';
                return false; // Evita que el formulario se envíe si las contraseñas no coinciden
            }

            errorMessage.style.display = 'none';
            return true; // Permite el envío del formulario si las contraseñas coinciden
        }
    </script>
</body>
</html>
