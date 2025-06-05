<?php
session_start();
include_once("MysqlConnector.php");

$db = new MysqlConnector();
$conn = $db->connect();

$nombre = $_SESSION['Nombre'] ?? 'Invitado';

$query = "
SELECT v.folio, v.fecha, v.total, 
       c.Nombre AS cliente, 
       a.nombre AS articulo, 
       vd.cantidad, vd.precio_unitario
FROM Ventas v
JOIN Clientes c ON v.idCliente = c.idCliente
JOIN VentasDetalles vd ON v.folio = vd.folio
JOIN Articulos a ON vd.idArticulo = a.idArticulo
ORDER BY v.fecha DESC, v.folio DESC
";

$result = $conn->query($query);

$compras = [];
while ($row = $result->fetch_assoc()) {
    $folio = $row['folio'];
    if (!isset($compras[$folio])) {
        $compras[$folio] = [
            'fecha' => $row['fecha'],
            'cliente' => $row['cliente'],
            'total' => $row['total'],
            'detalles' => []
        ];
    }
    $compras[$folio]['detalles'][] = [
        'articulo' => $row['articulo'],
        'cantidad' => $row['cantidad'],
        'precio' => $row['precio_unitario']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Compras</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
      padding: 20px;
      text-align: center;
      font-size: 1.7rem;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #00695c;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
    }

    th, td {
      border: 1px solid #b2dfdb;
      padding: 12px;
      text-align: left;
    }

    th {
      background-color: #004d40;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f1fefc;
    }

    .total {
      font-weight: bold;
      color: #00796b;
    }

    .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 10px 18px;
      background-color: #004d40;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-size: 15px;
      transition: background 0.3s;
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
      font-size: 14px;
    }

    footer a {
      color: #b2dfdb;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
      color: #e0f7fa;
    }
  </style>
</head>
<body>

<header>
  Historial de Compras de <?= htmlspecialchars($nombre) ?>
</header>

<div class="container">
  <h2>Compras registradas</h2>

  <?php if (empty($compras)): ?>
    <p style="color:#00796b; text-align:center;">No hay compras registradas aún.</p>
  <?php else: ?>
    <?php foreach ($compras as $folio => $compra): ?>
      <h3 style="color:#004d40; margin-bottom: 10px;">Folio #<?= $folio ?></h3>
      <p><strong>Fecha:</strong> <?= $compra['fecha'] ?> &nbsp; | &nbsp; <strong>Cliente:</strong> <?= htmlspecialchars($compra['cliente']) ?></p>

      <table>
        <thead>
          <tr>
            <th>Artículo</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($compra['detalles'] as $detalle): ?>
            <tr>
              <td><?= htmlspecialchars($detalle['articulo']) ?></td>
              <td><?= $detalle['cantidad'] ?></td>
              <td>$<?= number_format($detalle['precio'], 2) ?></td>
              <td>$<?= number_format($detalle['cantidad'] * $detalle['precio'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="total">Total de la compra</td>
            <td class="total">$<?= number_format($compra['total'], 2) ?></td>
          </tr>
        </tfoot>
      </table>
    <?php endforeach; ?>
  <?php endif; ?>

  <a class="btn" href="productos.php"><i class="fas fa-box"></i> Volver al menu</a>
</div>

<footer>
  &copy; 2025 Joyería Suárez &nbsp; 
</footer>

</body>
</html>





