<?php
session_start();
include_once("MysqlConnector.php");

$db = new MysqlConnector();
$conn = $db->connect();

$carrito = $_SESSION['carrito'] ?? [];
$clienteId = $_SESSION['idCliente'] ?? null;

if (!$clienteId || empty($carrito)) {
    echo "No hay cliente o el carrito está vacío.";
    exit;
}

// Calcular total
$total = 0;
foreach ($carrito as $item) {
    $total += $item['Precio'] * $item['Cantidad'];
}

// Insertar en Ordenes
$fecha = date('Y-m-d');
$query = "INSERT INTO Ordenes (idCliente, fecha, total, estado) VALUES (?, ?, ?, 'pendiente')";
$stmt = $conn->prepare($query);
$stmt->bind_param("isd", $clienteId, $fecha, $total);

if (!$stmt->execute()) {
    echo "Error al insertar orden: " . $stmt->error;
    exit;
}

$idOrden = $stmt->insert_id;

// Insertar detalles en OrdenesDetalles
foreach ($carrito as $item) {
    $query = "INSERT INTO OrdenesDetalles (idOrden, idArticulo, cantidad, precio_unitario)
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiid", $idOrden, $item['idArticulo'], $item['Cantidad'], $item['Precio']);

    if (!$stmt->execute()) {
        echo "Error al insertar detalle: " . $stmt->error;
        exit;
    }
}

// Limpiar carrito y redirigir al ticket
unset($_SESSION['carrito']);
header("Location: ticket.php?idOrden=" . $idOrden);
exit;
?>
