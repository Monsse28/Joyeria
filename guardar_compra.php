<?php
session_start();
include_once("MysqlConnector.php");

// Verificar que el cliente haya iniciado sesión
if (!isset($_SESSION['idCliente'])) {
    header("Location: loginCliente.php");
    exit;
}

// Verificar que el carrito no esté vacío
if (empty($_SESSION['carrito'])) {
    echo "<p>Tu carrito está vacío. <a href='productos.php'>Volver</a></p>";
    exit;
}

$cliente_id = $_SESSION['idCliente'];
$ids = array_map('intval', array_keys($_SESSION['carrito']));
$idList = implode(',', $ids);

$db = new MysqlConnector();
$conn = $db->connect();

// Iniciar transacción
$conn->begin_transaction();

try {
    // Obtener datos de los artículos en el carrito
    $sql = "SELECT idArticulo, Descripcion, Precio FROM Articulos WHERE idArticulo IN ($idList)";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error al obtener artículos: " . $conn->error);
    }

    $items = [];
    $total = 0;

    while ($row = $result->fetch_assoc()) {
        $idArt = $row['idArticulo'];

        // Asegurarse de que exista en el carrito
        if (!isset($_SESSION['carrito'][$idArt])) continue;

        $cantidad = $_SESSION['carrito'][$idArt]['cantidad']; // Usamos 'cantidad' en minúsculas
        $precio = $row['Precio'];
        $subtotal = $cantidad * $precio;
        $total += $subtotal;

        $items[] = [
            'idArticulo' => $idArt,
            'cantidad' => $cantidad,
            'precio' => $precio
        ];
    }

    // Obtener nuevo folio de forma segura
    $resFolio = $conn->query("SELECT MAX(folio) AS maxFolio FROM Ventas");
    $rowFolio = $resFolio->fetch_assoc();
    $folio = isset($rowFolio['maxFolio']) ? ((int)$rowFolio['maxFolio'] + 1) : 1;

    $fecha = date('Y-m-d');
    $idEnvio = null;
    $idTienda = 1;

    // Insertar en Ventas
    $stmtVenta = $conn->prepare("
        INSERT INTO Ventas (folio, idCliente, fecha, idTienda, idEnvio)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmtVenta->bind_param("iiisi", $folio, $idCliente, $fecha, $idTienda, $idEnvio);
    $stmtVenta->execute();
    $stmtVenta->close();

    // Insertar en VentasDetalles
    foreach ($items as $item) {
        $stmtDetalle = $conn->prepare("
            INSERT INTO VentasDetalles (folio, idArticulo, cantidad, precio_unitario)
            VALUES (?, ?, ?, ?)
        ");
        $stmtDetalle->bind_param("iiid", $folio, $item['idArticulo'], $item['cantidad'], $item['precio']);
        $stmtDetalle->execute();
        $stmtDetalle->close();
    }

    // Confirmar y vaciar carrito
    $conn->commit();
    unset($_SESSION['carrito']);

    header("Location: mis_compras.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "<p>Error al registrar la compra: " . $e->getMessage() . "</p>";
} finally {
    $conn->close();
}
?>

