<?php
session_start();
include_once("MysqlConnector.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $db = new MysqlConnector();
    $conn = $db->connect();

    // Seguridad: consulta preparada
    $stmt = $conn->prepare("SELECT * FROM Admi WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Corrección aquí: usar $contrasena en lugar de $password
        if (password_verify($contrasena, $row['contrasena'])) {
            // Guardar info del admin en sesión
          $_SESSION['Admin_id'] = $row['idAdmi'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = 'Admi';

            // Redirigir al dashboard
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario de administrador no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- Formulario HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>
    <style>
        :root {
            --rosa-fuerte: #d81b60;
            --rosa-suave: #ffe4ec;
            --rosa-oscuro: #880e4f;
            --gris-borde: #e0e0e0;
        }

        body {
            font-family: 'Georgia', serif;
            background-color: var(--rosa-suave); /* Fondo rosa suave */
            padding: 60px 20px;
            margin: 0;
        }

        h2 {
            text-align: center;
            color: var(--rosa-oscuro);
            font-size: 28px;
            margin-bottom: 30px;
        }

        form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(136, 14, 79, 0.2);
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--rosa-oscuro);
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid var(--gris-borde);
            border-radius: 6px;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: var(--rosa-fuerte);
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: var(--rosa-oscuro);
        }

        p {
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Acceso Administrador</h2>

<form method="POST" action="login_admin.php">
    <label for="usuario">Usuario:</label>
    <input type="text" id="usuario" name="usuario" required>

    <label for="contrasena">Contraseña:</label>
    <input type="password" id="contrasena" name="contrasena" required>

    <input type="submit" value="Ingresar">
</form>




<?php
if (isset($error_message)) {
    echo "<p>$error_message</p>";
}
?>

</body>
</html>


