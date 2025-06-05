<?php
session_start();
include_once("MysqlConnector.php");

if (!isset($_SESSION['Admin_id'])) {
    header("Location: loginAdmin.php");
    exit;
}

$db = new MysqlConnector();
$conn = $db->connect();

$mensaje = "";

// Verifica si se quiere eliminar un cliente
if (isset($_GET['eliminar'])) {
    $idCliente = $_GET['eliminar'];

    // Verificar si el cliente tiene ventas
    $checkVentas = $conn->prepare("SELECT COUNT(*) FROM Ventas WHERE idCliente = ?");
    $checkVentas->bind_param("i", $idCliente);
    $checkVentas->execute();
    $checkVentas->bind_result($ventasCount);
    $checkVentas->fetch();
    $checkVentas->close();

    if ($ventasCount > 0) {
        $mensaje = "❌ No se puede eliminar: el cliente tiene ventas registradas.";
    } else {
        // Eliminar cliente (las órdenes se eliminarán por ON DELETE CASCADE)
        $stmt = $conn->prepare("DELETE FROM Clientes WHERE idCliente = ?");
        if ($stmt) {
            $stmt->bind_param("i", $idCliente);
            if ($stmt->execute()) {
                $mensaje = "✅ Cliente eliminado correctamente.";
            } else {
                $mensaje = "❌ Error al eliminar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensaje = "❌ Error en la preparación: " . $conn->error;
        }
    }
}

// Obtener todos los clientes
$stmt = $conn->prepare("SELECT * FROM Clientes");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Ver Clientes</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #e0f2f1; /* mismo fondo que el menú */
      color: #004d40; /* texto principal */
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
    }

    .header {
      text-align: center;
      padding: 30px 10px 20px;
      background-color: #004d40; /* verde oscuro */
      color: white;
      width: 100%;
      box-sizing: border-box;
      border-bottom: 4px solid #00796b;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
    }

    .menu-button {
      display: inline-block;
      margin: 30px auto 0;
      padding: 15px 30px;
      background-color: #004d40; /* mismo verde que header */
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transition: background-color 0.3s ease;
    }

    .menu-button:hover {
      background-color: #00796b; /* verde medio para hover */
    }

    .mensaje {
      margin: 20px auto;
      padding: 15px;
      width: 90%;
      max-width: 1000px;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
    }

    .exito {
      background-color: #d0f8ce;
      color: #004d40;
      border: 2px solid #00796b;
    }

    .error {
      background-color: #ffcdd2;
      color: #c62828;
      border: 2px solid #ef9a9a;
    }

    table {
      width: 90%;
      max-width: 1000px;
      border-collapse: collapse;
      margin: 20px 0;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      background-color: white;
      border-radius: 15px;
      overflow: hidden;
    }

    th {
      background-color: #004d40;
      color: white;
      padding: 15px;
      font-size: 16px;
      text-align: left;
      border-bottom: 3px solid #00796b;
    }

    td {
      padding: 12px;
      border-bottom: 1px solid #b2dfdb;
      color: #004d40;
    }

    tr:hover {
      background-color: #b2dfdb;
      cursor: default;
    }

    .boton-eliminar {
      color: #00796b;
      font-weight: bold;
      text-decoration: none;
      transition: color 0.3s;
    }

    .boton-eliminar:hover {
      color: #004d40;
      text-decoration: underline;
    }

    .center {
      text-align: center;
      width: 100%;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>Registro De Clientes </h1>
  </div>

  <?php if (!empty($mensaje)): ?>
    <div class="mensaje <?= strpos($mensaje, '✅') !== false ? 'exito' : 'error' ?>">
      <?= $mensaje ?>
    </div>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Direccion</th>
        <th>Ciudad</th>
        <th>Colonia</th>
        <th>Estado</th>
        <th>País</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($cliente = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($cliente['Nombre']) ?></td>
          <td><?= htmlspecialchars($cliente['Apellidos']) ?></td>
          <td><?= htmlspecialchars($cliente['CorreoElectronico']) ?></td>
          <td><?= htmlspecialchars($cliente['Telefono']) ?></td>
          <td><?= htmlspecialchars($cliente['DireccionPostal']) ?></td>
          <td><?= htmlspecialchars($cliente['Ciudad']) ?></td>
          <td><?= htmlspecialchars($cliente['Colonia']) ?></td>
          <td><?= htmlspecialchars($cliente['Estado']) ?></td>
          <td><?= htmlspecialchars($cliente['Pais']) ?></td>
          <td>
            <a class="boton-eliminar" href="ver_clientes.php?eliminar=<?= $cliente['idCliente'] ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?')">Eliminar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="center">
    <a href="index.php" class="menu-button">Volver al menú principal</a>
  </div>

</body>
</html>


<?php
$stmt->close();
$conn->close();
?>
