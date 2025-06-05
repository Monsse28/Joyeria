<?php
include_once("MysqlConnector.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];

    if (!empty($usuario) && !empty($contrasena)) {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $db = new MysqlConnector();
        $conn = $db->connect();

        // Verificamos si el usuario ya existe
        $stmt = $conn->prepare("SELECT * FROM Admi WHERE usuario = ?");
        $stmt->bind_param("i", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "Ese usuario ya está registrado.";
        } else {
            // Insertamos el nuevo admin
            $stmt = $conn->prepare("INSERT INTO Admi (usuario, contrasena) VALUES (?, ?)");
            $stmt->bind_param("ss", $usuario, $hash);
            if ($stmt->execute()) {
                // Redirigir si todo fue exitoso
                header("Location: index.php");
                exit();
            } else {
                $mensaje = "Error al registrar: " . $stmt->error;
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Administrador | Juárez Joyería</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #b89b5e;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #b89b5e;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            margin-top: 25px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #a88c4e;
        }
        .mensaje {
            margin-top: 20px;
            text-align: center;
            color: #c0392b;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Registrar Nuevo Administrador</h2>
        <form method="POST" action="registrar_admins.php">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" required>
            
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" required>
            
            <input type="submit" value="Registrar">
        </form>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
    </div>

</body>
</html>
