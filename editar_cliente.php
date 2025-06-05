<?php
session_start();
include_once("MysqlConnector.php");

// Verifica si el cliente ha iniciado sesión
if (!isset($_SESSION['idCliente'])) {
    header("Location: loginCliente.php");
    exit();
}

$db = new MysqlConnector();
$conn = $db->connect();

$cliente_id = $_SESSION['idCliente'];

// Si se envió el formulario, actualiza los datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevoNombre = $_POST['nombre'];
    $nuevoApellido = $_POST['apellidos'];
    $nuevoCorreo = $_POST['correo'];
    $nuevaDireccion = $_POST['direccion'];
    $nuevoTelefono = $_POST['telefono'];
    $nuevaColonia = $_POST['colonia'];
    $nuevaCiudad = $_POST['ciudad'];
    $nuevoEstado = $_POST['estado'];
    $nuevoPais = $_POST['pais'];

    $query = "UPDATE Clientes SET Nombre = ?, Apellidos = ?, CorreoElectronico = ?, DireccionPostal = ?, Colonia = ?, Ciudad = ?, Estado = ?, Pais = ?, Telefono = ? WHERE idCliente = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssi", $nuevoNombre, $nuevoApellido, $nuevoCorreo, $nuevaDireccion, $nuevaColonia, $nuevaCiudad, $nuevoEstado, $nuevoPais, $nuevoTelefono, $cliente_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();
    header("Location: perfil.php");
    exit();
}

// Obtener los datos actuales del cliente
$query = "SELECT Nombre, Apellidos, CorreoElectronico, DireccionPostal, Telefono, Colonia, Ciudad, Estado, Pais FROM Clientes WHERE idCliente = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil - Joyería Suárez</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e0f2f1;
      margin: 0;
      padding: 0;
      color: #004d40;
    }

    header {
      background-color: #004d40;
      color: white;
      padding: 20px 0;
      text-align: center;
      font-size: 28px;
      letter-spacing: 1px;
    }

    .container {
      max-width: 850px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 40px;
      border: 1px solid #a7ffeb;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      color: #00695c;
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
    }

    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px 30px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    label {
      margin-bottom: 5px;
      color: #004d40;
      font-weight: bold;
    }

    input[type="text"],
    input[type="email"] {
      padding: 12px;
      border: 1px solid #b2dfdb;
      border-radius: 6px;
      font-size: 16px;
      background-color: #f1fefc;
      color: #004d40;
    }

    .full-width {
      grid-column: span 2;
    }

    button {
      background-color: #004d40;
      color: white;
      border: none;
      padding: 14px 24px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      transition: background 0.3s;
      grid-column: span 2;
      margin-top: 20px;
    }

    button:hover {
      background-color: #00796b;
    }

    .btn-volver {
      display: block;
      margin-top: 20px;
      text-align: center;
      color: #004d40;
      text-decoration: none;
      font-weight: bold;
    }

    .btn-volver:hover {
      text-decoration: underline;
      color: #00796b;
    }

    footer {
      background-color: #004d40;
      color: white;
      text-align: center;
      padding: 15px 0;
      margin-top: 50px;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      form {
        grid-template-columns: 1fr;
      }

      button {
        grid-column: span 1;
      }
    }
  </style>
</head>
<body>

<header>
  Editar Perfil
</header>

<div class="container">
  <h2>Actualiza tus datos personales</h2>
  <form method="post">
    <div class="form-group">
      <label for="nombre">Nombre:</label>
      <input type="text" name="nombre" value="<?= htmlspecialchars($cliente['Nombre']) ?>" required>
    </div>

    <div class="form-group">
      <label for="apellidos">Apellidos:</label>
      <input type="text" name="apellidos" value="<?= htmlspecialchars($cliente['Apellidos']) ?>" required>
    </div>

    <div class="form-group">
      <label for="correo">Correo electrónico:</label>
      <input type="email" name="correo" value="<?= htmlspecialchars($cliente['CorreoElectronico']) ?>" required>
    </div>

    <div class="form-group">
      <label for="direccion">Dirección:</label>
      <input type="text" name="direccion" value="<?= htmlspecialchars($cliente['DireccionPostal']) ?>" required>
    </div>

    <div class="form-group">
      <label for="colonia">Colonia:</label>
      <input type="text" name="colonia" value="<?= htmlspecialchars($cliente['Colonia']) ?>" required>
    </div>

    <div class="form-group">
      <label for="ciudad">Ciudad:</label>
      <input type="text" name="ciudad" value="<?= htmlspecialchars($cliente['Ciudad']) ?>" required>
    </div>

    <div class="form-group">
      <label for="estado">Estado:</label>
      <input type="text" name="estado" value="<?= htmlspecialchars($cliente['Estado']) ?>" required>
    </div>

    <div class="form-group">
      <label for="pais">País:</label>
      <input type="text" name="pais" value="<?= htmlspecialchars($cliente['Pais']) ?>" required>
    </div>

    <div class="form-group full-width">
      <label for="telefono">Teléfono:</label>
      <input type="text" name="telefono" value="<?= htmlspecialchars($cliente['Telefono']) ?>" required>
    </div>

    <button type="submit">Guardar cambios</button>
  </form>

  <a class="btn-volver" href="perfil.php">← Volver al perfil</a>
</div>

<footer>
  &copy; <?= date('Y') ?> Joyería Suárez.
</footer>

</body>
</html>
