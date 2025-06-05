<?php
session_start();
include_once("MysqlConnector.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['idCliente'])) {
    header("Location: loginCliente.php");
    exit();
}

$cliente_id = $_SESSION['idCliente'];
$cliente_nombre = $_SESSION['Nombre'];

$db = new MysqlConnector();
$conn = $db->connect();

// Se agregó 'v.estado' al SELECT
$query = "
    SELECT v.folio, v.fecha, v.estado, vd.idArticulo, vd.cantidad, a.descripcion, vd.precio_unitario 
    FROM Ventas v
    JOIN VentasDetalles vd ON v.folio = vd.folio
    JOIN Articulos a ON vd.idArticulo = a.idArticulo
    WHERE v.idCliente = ?
    ORDER BY v.fecha DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$result = $stmt->get_result();

$ordenes = [];
while ($row = $result->fetch_assoc()) {
    $folio = $row['folio'];
    if (!isset($ordenes[$folio])) {
        $ordenes[$folio] = [
            'fecha' => $row['fecha'],
            'estado' => $row['estado'],
            'detalles' => []
        ];
    }
    $ordenes[$folio]['detalles'][] = [
        'descripcion' => $row['descripcion'],
        'cantidad' => $row['cantidad'],
        'precio' => $row['precio_unitario']
    ];
}

$stmt->close();
$conn->close();
?>

<!-- Aquí empieza el HTML (con estilo verde igual al del admin) -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mi Perfil</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #e0f2f1;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #004d40;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .container {
      max-width: 900px;
      margin: 30px auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2, h3 {
      color: #004d40;
    }

    .orden {
      border: 1px solid #b2dfdb;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      background-color: #e0f7fa;
    }

    .dropdown-menu {
      background: #b2dfdb;
      border: 1px solid #009688;
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 100;
      width: 200px;
      border-radius: 5px;
      display: none;
    }

    .dropdown-menu a {
      display: block;
      padding: 10px;
      text-decoration: none;
      color: #004d40;
    }

    .dropdown-menu a:hover {
      background: #80cbc4;
    }

    .btn {
      padding: 10px 20px;
      background-color: #004d40;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #00796b;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      padding: 15px;
      background-color: #004d40;
      color: white;
    }

    footer a {
      color: #b2dfdb;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .dropdown-menu {
        position: static;
        width: 100%;
      }
    }
  </style>
  <script>
    function toggleMenu() {
      var menu = document.getElementById('menuOpciones');
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }
  </script>
</head>
<body>

<header>
  Bienvenido, <?= htmlspecialchars($cliente_nombre) ?>
</header>

<div class="container">
  <h2>Mi Perfil</h2>

  <div style="position: relative; display: inline-block; margin-bottom: 30px;">
    <button onclick="toggleMenu()" class="btn">Opciones de Perfil ▼</button>
    <div id="menuOpciones" class="dropdown-menu">
      <a href="editar_cliente.php">Editar mi perfil</a>
      <a href="cambiar_contraseña.php">Cambiar mi contraseña</a>
      <a href="ver_compras.php">Ver compras</a>
    </div>
  </div>

  <h3>Mis Órdenes</h3>
  <?php if (empty($ordenes)): ?>
    <p>No tienes compras registradas.</p>
  <?php else: ?>
    <?php foreach ($ordenes as $folio => $orden): ?>
      <div class="orden">
        <strong>Folio:</strong> <?= $folio ?><br>
        <strong>Fecha:</strong> <?= $orden['fecha'] ?><br>
        <strong>Estado:</strong> <?= htmlspecialchars($orden['estado']) ?><br><br>
        <ul>
          <?php foreach ($orden['detalles'] as $detalle): ?>
            <li><?= $detalle['descripcion'] ?> - <?= $detalle['cantidad'] ?> x $<?= number_format($detalle['precio'], 2) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<footer>
  <a href="productos.php"><i class="fas fa-box"></i> Ir a Productos</a><br>
  &copy; <?= date("Y") ?> Joyería SUÁREZ
</footer>

</body>
</html>



