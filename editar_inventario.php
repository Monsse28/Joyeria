<?php
session_start();
include_once("MysqlConnector.php");

// Verificar sesión
if (!isset($_SESSION['Admin_id'])) {
    header("Location: ver_inventario.php");
    exit;
}

$db   = new MysqlConnector();
$conn = $db->connect();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idArticulo = isset($_POST['idArticulo']) ? (int)$_POST['idArticulo'] : 0;
    $idTienda   = isset($_POST['idTienda']) ? (int)$_POST['idTienda'] : 0;
    $cantidad   = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    $precio     = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;

    if ($idArticulo > 0 && $idTienda >= 0) {
        // Actualizar cantidad en Inventario
        $sql = "UPDATE Inventario SET cantidad = ? WHERE idArticulo = ? AND idTienda = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iii", $cantidad, $idArticulo, $idTienda);
            $stmt->execute();
            $stmt->close();
        }

        // Actualizar precio en Articulos
        $sqlPrecio = "UPDATE Articulos SET precio = ? WHERE idArticulo = ?";
        $stmtPrecio = $conn->prepare($sqlPrecio);
        if ($stmtPrecio) {
            $stmtPrecio->bind_param("di", $precio, $idArticulo);
            $stmtPrecio->execute();
            $stmtPrecio->close();
        }

        $conn->close();
        header("Location: ver_inventario.php");
        exit;
    } else {
        echo "<script>alert('Datos inválidos.'); window.location.href='ver_inventario.php';</script>";
        exit;
    }
}

// Obtener datos
if (isset($_GET['id']) && isset($_GET['tienda'])) {
    $idArticulo = (int)$_GET['id'];
    $idTienda   = (int)$_GET['tienda'];

    $sql = "SELECT a.descripcion, a.precio, COALESCE(i.cantidad, 0) as cantidad
            FROM Articulos a
            LEFT JOIN Inventario i ON a.idArticulo = i.idArticulo AND i.idTienda = ?
            WHERE a.idArticulo = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }
    $stmt->bind_param("ii", $idTienda, $idArticulo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Artículo no encontrado.'); window.location.href = 'ver_inventario.php';</script>";
        exit;
    }

    $product = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
} else {
    header("Location: ver_inventario.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f2f1;
            color: #004d40;
            padding: 30px;
        }
        h1 {
            text-align: center;
            color: #004d40;
            font-size: 2rem;
        }
        form {
            max-width: 400px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="number"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #b2dfdb;
            border-radius: 8px;
            margin-top: 5px;
        }
        input[type="submit"] {
            background-color: #004d40;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #00796b;
        }
        .btn-volver {
            display: block;
            text-align: center;
            margin-top: 25px;
            text-decoration: none;
            background-color: #004d40;
            color: white;
            padding: 12px;
            border-radius: 10px;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-volver:hover {
            background-color: #00796b;
        }
    </style>
</head>
<body>

<h1>Editar Inventario</h1>

<form method="POST" action="editar_inventario.php">
    <input type="hidden" name="idArticulo" value="<?= htmlspecialchars($idArticulo) ?>">
    <input type="hidden" name="idTienda" value="<?= htmlspecialchars($idTienda) ?>">

    <label for="cantidad">Cantidad:</label>
    <input type="number" name="cantidad" value="<?= htmlspecialchars($product['cantidad']) ?>" min="0" required>

    <label for="precio">Precio:</label>
    <input type="text" name="precio" value="<?= htmlspecialchars($product['precio']) ?>" required>

    <input type="submit" value="Actualizar Inventario">
</form>

<a class="btn-volver" href="ver_inventario.php">← Volver al Inventario</a>

</body>
</html>
