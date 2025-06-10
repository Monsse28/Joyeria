<?php
include_once("MysqlConnector.php");

$idOrden = $_GET['idOrden'] ?? null;
if (!$idOrden) {
    die("No se proporcionó un ID de orden.");
}

$db = new MysqlConnector();
$conn = $db->connect();

$sql = "
    SELECT o.idOrden, o.fecha, o.total, c.Nombre AS cliente,
           od.cantidad, od.precio_unitario, a.Descripcion
    FROM Ordenes o
    JOIN Clientes c ON o.idCliente = c.idCliente
    JOIN OrdenesDetalles od ON o.idOrden = od.idOrden
    JOIN Articulos a ON od.idArticulo = a.idArticulo
    WHERE o.idOrden = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación: " . $conn->error);
}
$stmt->bind_param("i", $idOrden);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;
$cliente = "";
$fecha = "";

while ($row = $result->fetch_assoc()) {
    $cliente = $row['cliente'];
    $fecha = $row['fecha'];
    $items[] = $row;
    $total = $row['total']; // total general
}

$stmt->close();
$conn->close();

if (empty($items)) {
    die("No se encontraron detalles para esta orden.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ticket de Compra</title>
<style>
  body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; }
  header { text-align: center; padding: 10px; background: #004d40; color: white; }
  .item { display: flex; justify-content: space-between; margin-bottom: 8px; }
  .total { font-weight: bold; text-align: right; margin-top: 20px; }
  button { margin-top: 20px; padding: 10px; background: #00796b; color: white; border: none; cursor: pointer; }
  button:hover { background: #004d40; }
  @media print {
    button { display: none; }
  }
</style>
</head>
<body>

<header>
  <h2>Ticket de Compra</h2>
</header>

<p><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></p>
<p><strong>Fecha:</strong> <?= htmlspecialchars($fecha) ?></p>
<p><strong>Orden:</strong> <?= htmlspecialchars($idOrden) ?></p>

<hr>

<?php foreach ($items as $item): ?>
  <div class="item">
    <span><?= htmlspecialchars($item['Descripcion']) ?> (x<?= $item['cantidad'] ?>)</span>
    <span>$<?= number_format($item['precio_unitario'] * $item['cantidad'], 2) ?></span>
  </div>
<?php endforeach; ?>

<hr>

<div class="total">Total: $<?= number_format($total, 2) ?></div>

<button onclick="window.print()">Imprimir</button>

</body>
</html>





