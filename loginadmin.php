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
            $_SESSION['Admi_id'] = $row['idAdmin'];
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
    <title>Inicio de Sesión</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #d0f5e4, #b2dfdb);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            background: #ffffff;
            padding: 50px;
            width: 100%;
            max-width: 500px;
            border: 2px solid #004d40;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .login-box h2 {
            text-align: center;
            color: #004d40;
            margin-bottom: 30px;
            font-size: 30px;
        }

        .login-box label {
            display: block;
            margin-bottom: 8px;
            color: #004d40;
            font-weight: bold;
            font-size: 16px;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            font-size: 16px;
            background-color: #e0f2f1;
            border-radius: 6px;
        }

        .login-box input[type="submit"],
        .back-button {
            width: 100%;
            padding: 14px;
            background-color: #00796b;
            color: white;
            font-size: 16px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            border-radius: 6px;
            margin-top: 10px;
            transition: background 0.3s ease;
        }

        .login-box input[type="submit"]:hover,
        .back-button:hover {
            background-color: #004d40;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
            font-size: 15px;
        }

        .menu-link {
            margin-top: 15px;
            text-align: center;
        }

        .menu-link a {
            text-decoration: none;
            display: inline-block;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Inicio de Sesión</h2>

    <form method="POST" action="login_admin.php">
        <label for="usuario">Usuario</label>
        <input type="text" name="usuario" id="usuario" required>

        <label for="contrasena">Contraseña</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <input type="submit" value="Ingresar">
    </form>

    <?php
    if (isset($error_message)) {
        echo "<div class='error-message'>$error_message</div>";
    }
    ?>

    <div class="menu-link">
        <a href="index.html">
            <button class="back-button">Regresar al Menú</button>
        </a>
    </div>
</div>

</body>
</html>
