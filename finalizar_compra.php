<?php
// Mostrar errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();
include_once("MysqlConnector.php");

// Conexión a la base de datos
$db = new MysqlConnector();
$conn = $db->connect();
if (!$conn) {
    die("Error en la conexión a la base de datos.");
}

// Obtener datos de sesión
$carrito = $_SESSION['carrito'] ?? [];
$clienteId = $_SESSION['idCliente'] ?? null;

// Verificar si hay datos válidos
if (!$clienteId || empty($carrito)) {
    echo "⚠️ No hay cliente o el carrito está vacío.";
    exit;
}

// Calcular total
$total = 0;
foreach ($carrito as $item) {
    if (!isset($item['Precio'], $item['Cantidad'])) {
        echo "Error: Datos incompletos en el carrito.";
        exit;
    }
    $total += $item['Precio'] * $item['Cantidad'];
}

// Insertar en Ordenes
$fecha = date('Y-m-d');
$query = "INSERT INTO Ordenes (idCliente, fecha, total, estado) VALUES (?, ?, ?, 'pendiente')";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "Error al preparar la consulta de orden: " . $conn->error;
    exit;
}
$stmt->bind_param("isd", $clienteId, $fecha, $total);

if (!$stmt->execute()) {
    echo "Error al insertar orden: " . $stmt->error;
    exit;
}

$idOrden = $stmt->insert_id;

// Insertar detalles de orden
foreach ($carrito as $item) {
    if (!isset($item['idArticulo'], $item['Cantidad'], $item['Precio'])) {
        echo "Error: Datos faltantes en un artículo del carrito.";
        exit;
    }

    $query = "INSERT INTO OrdenesDetalles (idOrden, idArticulo, cantidad, precio_unitario)
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Error al preparar detalle: " . $conn->error;
        exit;
    }

    $stmt->bind_param("iiid", $idOrden, $item['idArticulo'], $item['Cantidad'], $item['Precio']);

    if (!$stmt->execute()) {
        echo "Error al insertar detalle: " . $stmt->error;
        exit;
    }
}

// Limpiar carrito
unset($_SESSION['carrito']);

// Redirigir al ticket
header("Location: ticket.php?idOrden=" . $idOrden);
exit;
?>
