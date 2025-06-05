<?php
session_start();
include_once("MysqlConnector.php");

if (!isset($_SESSION['idCliente'])) {
    header("Location: loginCliente.php");
    exit();
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_SESSION['cliente_id'];
    $contrasena_actual = $_POST['contrasena_actual'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    $db = new MysqlConnector();
    $conn = $db->connect();

    $stmt = $conn->prepare("SELECT contrasena FROM clientes WHERE idCliente = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $stmt->bind_result($contrasenaGuardada);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($contrasena_actual, $contrasenaGuardada)) {
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE clientes SET contrasena = ? WHERE idCliente = ?");
        $stmt->bind_param("si", $hash, $cliente_id);
        $stmt->execute();
        $stmt->close();
        $mensaje = "✅ Contraseña actualizada con éxito.";
    } else {
        $mensaje = "❌ La contraseña actual es incorrecta.";
    }

    $conn->close();
}
?>


<<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambiar Contraseña - Joyería Suárez</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom right, #e0f2f1, #ffffff);
      margin: 0;
      padding: 0;
      color: #004d40;
    }

    header {
      background-color: #004d40;
      color: white;
      padding: 30px 20px;
      text-align: center;
      font-size: 26px;
      letter-spacing: 1px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .container {
      max-width: 450px;
      margin: 60px auto;
      background-color: #ffffff;
      border-radius: 14px;
      padding: 40px 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #00695c;
    }

    label {
      font-weight: 600;
      margin-top: 15px;
      display: block;
    }

    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      margin-bottom: 20px;
      border: 1px solid #b2dfdb;
      border-radius: 8px;
      background-color: #f1fefc;
      font-size: 15px;
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #004d40;
    }

    .mensaje {
      margin-top: 20px;
      text-align: center;
      font-weight: bold;
      color: #d32f2f;
    }

    .volver {
      text-align: center;
      margin-top: 25px;
    }

    .volver a {
      color: #00796b;
      text-decoration: none;
      font-weight: 600;
      font-size: 14px;
    }

    .volver a:hover {
      color: #004d40;
      text-decoration: underline;
    }

    .icono {
      text-align: center;
      font-size: 40px;
      color: #00796b;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<header>
  <i class="fas fa-lock"></i> Cambiar Contraseña
</header>

<div class="container">
  <div class="icono">
    <i class="fas fa-user-shield"></i>
  </div>

  <form method="post">
    <label for="contrasena_actual"><i class="fas fa-key"></i> Contraseña actual:</label>
    <input type="password" name="contrasena_actual" id="contrasena_actual" required>

    <label for="nueva_contrasena"><i class="fas fa-unlock-alt"></i> Nueva contraseña:</label>
    <input type="password" name="nueva_contrasena" id="nueva_contrasena" required>

    <button type="submit"><i class="fas fa-sync-alt"></i> Actualizar Contraseña</button>
  </form>

  <?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <div class="volver">
    <a href="perfil.php"><i class="fas fa-arrow-left"></i> Volver al perfil</a>
  </div>
</div>

</body>
</html>
