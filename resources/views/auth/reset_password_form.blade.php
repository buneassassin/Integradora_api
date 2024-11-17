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
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h1 {
            color: #333;
        }

        /* Estilo del contenedor del formulario */
        .form-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        /* Estilos del formulario */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input[type="password"] {
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            padding: 0.7rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Mensaje de error */
        .error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: none;
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
