<?php
session_start();
include_once("MysqlConnector.php");

$db = new MysqlConnector();
$conn = $db->connect();

$mensaje = "";

// Eliminar tienda si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id = (int)$_POST['eliminar_id'];
    $stmtDel = $conn->prepare("DELETE FROM Tiendas WHERE idTienda = ?");
    if ($stmtDel) {
        $stmtDel->bind_param("i", $id);
        if ($stmtDel->execute()) {
            $mensaje = "Tienda con ID $id eliminada exitosamente.";
        } else {
            $mensaje = "Error al eliminar la tienda: " . $stmtDel->error;
        }
        $stmtDel->close();
    } else {
        $mensaje = "Error al preparar la consulta de eliminación.";
    }
}

$result = $conn->query("SELECT * FROM Tiendas");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tiendas - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f2f1; /* verde claro */
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #004d40;
            color: white;
            padding: 30px;
            text-align: center;
        }

        header h1 {
            font-size: 36px;
            margin: 0;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .container {
            padding: 30px;
        }

        .msg {
            margin-bottom: 20px;
            color: #2e7d32;
            font-weight: bold;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 20px;
            background-color: white;
        }

        th {
            background-color: #004d40;
            color: white;
            padding: 12px;
        }

        td {
            padding: 12px;
            text-align: center;
            background-color: #ffffff;
            border-bottom: 1px solid #b2dfdb;
        }

        tr:hover td {
            background-color: #e0f7fa;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            background-color: #00796b;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #004d40;
        }

        .back-button {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            background-color: #004d40;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
        }

        .back-button:hover {
            background-color: #00796b;
        }

        form {
            display: inline;
        }
    </style>
</head>
<body>

    <header>
        <h1>Registro De Tiendas</h1>
    </header>

    <div class="container">
        <?php if ($mensaje): ?>
            <div class="msg"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Ciudad</th>
                <th>Dirección</th>
                <th>Código Postal</th>
                <th>Horario</th>
                <th>Acciones</th>
            </tr>
            <?php while ($tienda = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $tienda['idTienda'] ?></td>
                <td><?= htmlspecialchars($tienda['Descripcion']) ?></td>
                <td><?= htmlspecialchars($tienda['Ciudad']) ?></td>
                <td><?= htmlspecialchars($tienda['Direccion']) ?></td>
                <td><?= htmlspecialchars($tienda['CodigoPostal']) ?></td>
                <td><?= htmlspecialchars($tienda['Horario']) ?></td>
                <td>
                    <a href="editar_tienda.php?id=<?= $tienda['idTienda'] ?>" class="btn">Actualizar</a>
                    <form method="post" onsubmit="return confirm('¿Eliminar esta tienda?');">
                        <input type="hidden" name="eliminar_id" value="<?= $tienda['idTienda'] ?>">
                        <button type="submit" class="btn">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <a href="index.php" class="back-button">← Regresar al Menú</a>
    </div>

</body>
</html>
<?php $conn->close(); ?>
