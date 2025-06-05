<?php
session_start();
include_once("MysqlConnector.php");

$db = new MysqlConnector();
$conn = $db->connect();

// Validar datos del formulario
if (!isset($_POST['idArticulo'], $_POST['idTienda'], $_POST['Cantidad'])) {
    die("Faltan datos del producto");
}

$idArticulo = (int) $_POST['idArticulo'];
$idTienda   = (int) $_POST['idTienda'];
$cantidad   = (int) $_POST['Cantidad'];

// Validar existencia del producto en inventario
$sql = "SELECT e.cantidad, a.Descripcion, a.Precio, a.imagen 
        FROM Inventario e 
        JOIN Articulos a ON e.idArticulo = a.idArticulo
        WHERE e.idArticulo = ? AND e.idTienda = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idArticulo, $idTienda);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Producto no encontrado en el inventario.");
}

$row = $result->fetch_assoc();

if ($cantidad > $row['cantidad']) {
    die("No hay suficiente stock disponible.");
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Generar clave única para el carrito por tienda y artículo
$key = $idTienda . '-' . $idArticulo;

if (isset($_SESSION['carrito'][$key])) {
    $_SESSION['carrito'][$key]['Cantidad'] += $cantidad;
} else {
    $_SESSION['carrito'][$key] = [
        'idArticulo' => $idArticulo,
        'idTienda'   => $idTienda,
        'Descripcion'=> $row['Descripcion'],
        'Precio'     => $row['Precio'],
        'imagen'     => $row['imagen'],
        'Cantidad'   => $cantidad
    ];
}

$stmt->close();
$conn->close();

// Redirigir al carrito o de vuelta al catálogo
header("Location: carrito.php");
exit;
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <style>
        body { font-family: "Segoe UI", sans-serif; background-color: #fef5f7; margin: 0; padding: 20px; }
        .producto { border: 1px solid #ddd; padding: 15px; margin: 15px; border-radius: 8px; width: 250px; display: inline-block; vertical-align: top; background: #fff; }
        img { width: 100%; height: 180px; object-fit: cover; border-radius: 5px; }
        h3 { margin: 10px 0 5px; }
        form { margin-top: 10px; }
        input[type=number] { width: 60px; padding: 5px; }
        button { background: #e91e63; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #d81b60; }
    </style>
</head>
<body>
    <h1>Catálogo de Productos</h1>

    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="producto">
        <img src="uploads/<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['Descripcion']) ?>">
        <h3><?= htmlspecialchars($row['Descripcion']) ?></h3>
        <p><strong>Precio:</strong> $<?= number_format($row['Precio'], 2) ?></p>
        <p><strong>Tienda:</strong> <?= htmlspecialchars($row['descripcionTienda']) ?></p>

        <form method="POST" action="agregar_al_carrito.php">
            <input type="hidden" name="idArticulo" value="<?= $row['idArticulo'] ?>">
            <input type="hidden" name="idTienda" value="<?= $row['idTienda'] ?>">
            <label>Cantidad:</label>
            <input type="number" name="Cantidad" value="1" min="1" required>
            <button type="submit">Agregar al carrito</button>
        </form>
    </div>
    <?php endwhile; ?>

</body>
</html>

<?php
$conn->close();
?>
