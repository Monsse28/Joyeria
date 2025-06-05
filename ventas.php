<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ventas Completadas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #e0f2f1;
            color: #004d40;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        h1 {
            text-align: center;
            color: #004d40;
            margin-bottom: 20px;
        }

        table {
            width: 90%;
            max-width: 1000px;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            overflow: hidden;
            align-self: center;
        }

        th, td {
            border: 1px solid #b2dfdb;
            padding: 12px;
            text-align: center;
            color: #004d40;
        }

        th {
            background-color: #004d40;
            color: white;
        }

        .btn-imprimir {
            margin: 20px auto 10px;
            background-color: #004d40;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
            display: block;
            align-self: center;
        }

        .btn-imprimir:hover {
            background-color: #00796b;
        }

        .total-venta {
            width: 90%;
            max-width: 1000px;
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
            color: #004d40;
            align-self: center;
        }

        .btn-regresar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #004d40;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
            text-align: center;
            text-decoration: none;
            z-index: 1000;
        }

        .btn-regresar:hover {
            background-color: #00796b;
        }

        @media print {
            .btn-imprimir, .btn-regresar { display: none; }
        }
    </style>
</head>
<body>

<h1>Ventas Realizadas</h1>

<button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir</button>

<?php
include_once("MysqlConnector.php");
$db = new MysqlConnector();
$conn = $db->connect();

$sql = "
SELECT v.folio, v.fecha, v.idTienda, v.total, c.nombre AS cliente,
       a.descripcion AS articulo, dv.cantidad, dv.precio_unitario,
       (dv.cantidad * dv.precio_unitario) AS subtotal
FROM Ventas v
JOIN VentasDetalles dv ON v.folio = dv.folio
JOIN Clientes c ON v.idCliente = c.idCliente
JOIN Articulos a ON dv.idArticulo = a.idArticulo
ORDER BY v.folio DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("❌ Error en la consulta SQL: " . $conn->error);
}

$ventas = [];
$totalGeneral = 0;

while ($row = $result->fetch_assoc()) {
    $ventas[] = $row;
    $totalGeneral += $row['subtotal'];
}
?>

<table>
  <thead>
    <tr>
      <th>Folio</th>
      <th>Artículo</th>
      <th>Cantidad</th>
      <th>Precio Unitario</th>
      <th>Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ventas as $venta): ?>
      <tr>
        <td><?= htmlspecialchars($venta['folio']) ?></td>
        <td><?= htmlspecialchars($venta['articulo']) ?></td>
        <td><?= htmlspecialchars($venta['cantidad']) ?></td>
        <td>$<?= number_format($venta['precio_unitario'], 2) ?></td>
        <td>$<?= number_format($venta['subtotal'], 2) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="total-venta">
    Total general vendido: $<?= number_format($totalGeneral, 2) ?>
</div>

<a href="index.php" class="btn-regresar">← Volver al menú principal</a>

</body>
</html>
