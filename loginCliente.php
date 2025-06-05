<?php
session_start();
include_once("MysqlConnector.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['CorreoElectronico']);
    $password = trim($_POST['Password']);

    $db = new MysqlConnector();
    $conn = $db->connect();

    if (!$conn) {
        die("Error al conectar a la base de datos: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM Clientes WHERE CorreoElectronico = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $cliente = $result->fetch_assoc();

        if (password_verify($password, $cliente['Password'])) {
            $_SESSION['idCliente'] = $cliente['idCliente'];
            $_SESSION['Nombre'] = $cliente['Nombre'];
            $_SESSION['carrito'] = [];

            // Redirige con mensaje de bienvenida
            header("Location: productos.php?bienvenida=1");
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "El correo no está registrado.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio de Sesión - Cliente</title>
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
      height: 100vh;
    }

    .login-box {
      background: #ffffff;
      padding: 40px;
      max-width: 600px;
      width: 100%;
      border-radius: 12px;
      border: 2px solid #004d40;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h2 {
      color: #004d40;
      margin-bottom: 30px;
      font-size: 28px;
    }

    table {
      width: 100%;
      border-spacing: 10px;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #00695c;
      text-align: left;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #e0f2f1;
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #00796b;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
      margin-bottom: 10px;
    }

    button:hover {
      background-color: #004d40;
    }

    .error-message {
      margin-top: 15px;
      color: red;
      font-weight: bold;
    }

    .back-button {
      background-color: #004d40;
    }

    .back-button:hover {
      background-color: #002921;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h2>Cliente Selecto</h2>

    <?php if (!empty($mensaje)) echo "<div class='error-message'>$mensaje</div>"; ?>

    <form method="POST" action="">
      <table>
        <tr>
          <td>
            <label for="CorreoElectronico">Correo Electrónico:</label>
            <input type="email" name="CorreoElectronico" id="CorreoElectronico" required>
          </td>
        </tr>
        <tr>
          <td>
            <label for="Password">Contraseña:</label>
            <input type="password" name="Password" id="Password" required>
          </td>
        </tr>
        <tr>
          <td>
            <button type="submit">Ingresar</button>
          </td>
        </tr>
      </table>
    </form>

    <a href="index.html">
      <button class="back-button">Regresar al Menú</button>
    </a>
  </div>

</body>
</html>



