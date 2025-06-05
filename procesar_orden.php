<?php
session_start();
include_once("MysqlConnector.php");

if (!isset($_SESSION['cliente_id']) || empty($_SESSION['carrito'])) {
    // Si no hay cliente logueado o el carrito está vacío, redirige a la página del carrito
    header("Location: carrito.php");
    exit();
}

$cliente_id = $_SESSION['cliente_id'];
$fecha = date('Y-m-d H:i:s');  // Fecha y hora de la compra

$db = new MysqlConnector();
$conn = $db->connect();

// Obtener la tienda del carrito (suponemos que todos los artículos en el carrito son de la misma tienda)
$idTienda = $_SESSION['carrito'][0]['idTienda'];  

// 1. Insertar la venta en la tabla Ventas
$queryVenta = "INSERT INTO Ventas (idCliente, idTienda, fecha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($queryVenta);
$stmt->bind_param("iis", $cliente_id, $idTienda, $fecha);
$stmt->execute();
$folio = $stmt->insert_id;  // Obtener el folio generado para la venta

// 2. Insertar los detalles de la venta
$queryDetalles = "INSERT INTO VentasDetalles (folio, idArticulo, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
$stmtDetalles = $conn->prepare($queryDetalles);

foreach ($_SESSION['carrito'] as $idArticulo => $item) {
    $cantidad = $item['Cantidad'];
    $precio_unitario = $item['Precio'];
    $stmtDetalles->bind_param("iiid", $folio, $idArticulo, $cantidad, $precio_unitario);
    $stmtDetalles->execute();
}

// 3. Limpiar el carrito después de la compra
unset($_SESSION['carrito']);

// Cerrar la conexión con la base de datos
$stmt->close();
$stmtDetalles->close();
$conn->close();

// Redirigir al usuario a la página de "Mis Compras"
header("Location: ver_compras.php");
exit();
