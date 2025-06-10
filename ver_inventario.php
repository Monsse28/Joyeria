<?php
session_start();
include_once("MysqlConnector.php");

// Verificar sesión
if (!isset($_SESSION['Admin_id'])) {
    header("Location: login.php");
    exit;
}

$db   = new MysqlConnector();
$conn = $db->connect();

$sql = "SELECT 
            a.idArticulo,
            a.descripcion,
            a.precio,
            i.cantidad,
            t.nombre AS tienda,
            t.idTienda
        FROM Articulos a
        LEFT JOIN Inventario i ON a.idArticulo = i.idArticulo
        LEFT JOIN Tiendas t ON i.idTienda = t.idTienda
        ORDER BY t.nombre, a.descripcion";

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f1f8e9;
            color: #33691e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #c5e1a5;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #aed581;
        }
        tr:nth-child(even) {
            background-color: #f9fbe7;
        }
        a.btn {
            text-decoration: none;
            color: white;
            background-color: #558b2f;
            padding: 8px 12px;
            border-radius: 5px;
        }
        a.btn:hover {
            background-color: #33691e;
        }
    </style>
</head>
<body>

<h1>Inventario General</h1>

<table>
    <thead>
        <tr>
            <th>Tienda</th>
            <th>Artículo</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['tienda'] ?? 'Sin asignar') ?></td>
            <td><?= htmlspecialchars($row['descripcion']) ?></td>
            <td>$<?= number_format($row['precio'], 2) ?></td>
            <td><?= htmlspecialchars($row['cantidad'] ?? 0) ?></td>
            <td>
                <a class="btn" href="editar_inventario.php?id=<?= $row['idArticulo'] ?>&tienda=<?= $row['idTienda'] ?>">Editar</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>


