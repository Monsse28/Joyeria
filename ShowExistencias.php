<?php
include_once("MysqlConnector.php");
$db = new MysqlConnector();
$conn = $db->connect();

// Si se envió un formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idinventario'], $_POST['cantidad'])) {
    $idInventario = (int)$_POST['idinventario'];
    $cantidad = (int)$_POST['cantidad'];

    $update = $conn->prepare("UPDATE Inventario SET cantidad = ? WHERE idInventario = ?");
    $update->bind_param("ii", $cantidad, $idInventario);
    if ($update->execute()) {
        echo "<p style='color: green;'>Inventario actualizado correctamente.</p>";
    } else {
        echo "<p style='color: red;'>Error al actualizar inventario: " . $conn->error . "</p>";
    }
}

// Consulta inventario
$sql = "SELECT i.idinventario, i.cantidad, t.descripcion AS tienda, a.descripcion AS articulo, a.imagen AS imagen_articulo
        FROM Inventario i
        JOIN Tiendas t ON i.idTienda = t.idTienda
        JOIN Articulos a ON i.idArticulo = a.idArticulo
        ORDER BY t.descripcion, a.descripcion";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Existencias por Tienda</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background-color: #e0f2f1;
    }

    h1 {
        text-align: center;
        color: #004d40;
        font-size: 2em;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    th, td {
        padding: 10px;
        border: 1px solid #80cbc4;
        text-align: center;
    }

    th {
        background-color: #004d40;
        color: white;
    }

    td img {
        max-width: 100px;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    input[type="number"] {
        width: 60px;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    input[type="submit"] {
        padding: 6px 12px;
        background-color: #004d40;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #00796b;
    }

    .btn-regresar {
        display: block;
        background-color: #004d40;
        color: white;
        padding: 12px 20px;
        text-decoration: none;
        font-size: 16px;
        border-radius: 8px;
        margin: 40px auto 20px;
        text-align: center;
        width: 220px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .btn-regresar:hover {
        background-color: #00796b;
    }
  </style>
</head>
<body>

<h1>Existencias por Sucursal</h1>

<table>
    <tr>
        <th>Sucursal</th>
        <th>Producto</th>
        <th>Imagen</th>
        <th>Editar Cantidad</th>
        <th></th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['tienda']) ?></td>
        <td><?= htmlspecialchars($row['articulo']) ?></td>
        <td>
            <?php if (!empty($row['imagen_articulo'])): ?>
                <img src="uploads/<?= htmlspecialchars($row['imagen_articulo']) ?>" alt="<?= htmlspecialchars($row['articulo']) ?>">
            <?php else: ?>
                <span>No disponible</span>
            <?php endif; ?>
        </td>
        <td>
            <form method="POST">
                <input type="hidden" name="idinventario" value="<?= $row['idinventario'] ?>">
                <input type="number" name="cantidad" value="<?= $row['cantidad'] ?>" min="0">
        </td>
        <td>
            <input type="submit" value="Actualizar">
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<a href="index.php" class="btn-regresar">Regresar al menu</a>

</body>
</html>

<?php $conn->close(); ?>
