<?php
include_once("MysqlConnector.php");

$folio = $_GET['folio'] ?? null;
if (!$folio) {
    die("Folio no proporcionado.");
}

$db = new MysqlConnector();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT v.folio, v.fecha, vd.cantidad, vd.precio_unitario, a.Descripcion, c.Nombre AS cliente
    FROM Ventas v
    JOIN VentasDetalles vd ON v.folio = vd.folio
    JOIN Articulos a ON vd.idArticulo = a.idArticulo
    JOIN Clientes c ON v.idCliente = c.idCliente
    WHERE v.folio = ?
");
$stmt->bind_param("i", $folio);
$stmt->execute();
$result = $stmt->get_result();

$ticket = [];
$total = 0;
$cliente = "";
$fecha = "";

while ($row = $result->fetch_assoc()) {
    $cliente = $row['cliente'];
    $fecha = $row['fecha'];
    $ticket[] = $row;
    $total += $row['cantidad'] * $row['precio_unitario'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ticket de Compra</title>
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
      padding: 25px;
      text-align: center;
      font-size: 26px;
    }

    .container {
      max-width: 700px;
      margin: 40px auto;
      padding: 20px;
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border: 1px solid #b2dfdb;
    }

    .linea {
      border-bottom: 1px dashed #00796b;
      margin: 15px 0;
    }

    .item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
    }

    .total {
      text-align: right;
      font-weight: bold;
      font-size: 1.2em;
      margin-top: 20px;
      color: #004d40;
    }

    .botones {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 30px;
    }

    .botones a,
    .botones button {
      background-color: #00796b;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      text-decoration: none;
      text-align: center;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: background-color 0.3s ease;
    }

    .botones a:hover,
    .botones button:hover {
      background-color: #004d40;
    }

    .mensaje {
      font-size: 18px;
      color: #00695c;
      text-align: center;
      margin: 40px 0;
    }

    @media print {
      .botones {
        display: none;
      }
    }
  </style>
</head>
<body>

<header>
  <i class="fas fa-receipt"></i> Ticket de Compra
</header>

<div class="container">
  <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></p>
  <p><strong>Folio:</strong> <?= htmlspecialchars($folio) ?></p>
  <p><strong>Fecha:</strong> <?= htmlspecialchars($fecha) ?></p>

  <div class="linea"></div>

  <?php if (empty($ticket)): ?>
    <div class="mensaje">No se encontraron productos para este ticket.</div>
  <?php else: ?>
    <?php foreach ($ticket as $item): ?>
      <div class="item">
        <span><?= htmlspecialchars($item['Descripcion']) ?> (x<?= $item['cantidad'] ?>)</span>
        <span>$<?= number_format($item['precio_unitario'] * $item['cantidad'], 2) ?></span>
      </div>
    <?php endforeach; ?>
    <div class="linea"></div>
    <div class="total">Total: $<?= number_format($total, 2) ?></div>
  <?php endif; ?>

  <div class="botones">
    <?php if (!empty($ticket)): ?>
      <button onclick="window.print()"><i class="fas fa-print"></i> Imprimir Ticket</button>
    <?php endif; ?>
    <a href="carrito.php"><i class="fas fa-arrow-left"></i> Volver al Carrito</a>
    <a href="productos.php"><i class="fas fa-home"></i> Volver al Menú</a>
  </div>
</div>

</body>
</html>





