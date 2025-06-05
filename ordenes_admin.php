<?php
session_start();
include_once("MysqlConnector.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new MysqlConnector();
$conn = $db->connect();

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// CONSULTA: Obtener todas las ventas (órdenes)
$sql = "SELECT v.folio AS idOrden, v.fecha, v.total, v.estado, c.nombre 
        FROM Ventas v 
        LEFT JOIN Clientes c ON v.idCliente = c.idCliente";

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}

// Procesar acciones (Aceptar o Rechazar orden)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idOrden'], $_POST['accion'])) {
    $idOrden = intval($_POST['idOrden']);
    $accion = $_POST['accion'];

    if ($accion === 'aceptar') {
        $stmt = $conn->prepare("UPDATE Ventas SET estado = 'aceptada' WHERE folio = ?");
    } elseif ($accion === 'rechazar') {
        $stmt = $conn->prepare("UPDATE Ventas SET estado = 'rechazada' WHERE folio = ?");
    }

    if ($stmt) {
        $stmt->bind_param("i", $idOrden);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            die("Error al actualizar el estado: " . $stmt->error);
        }
        $stmt->close();
    } else {
        die("Error al preparar la consulta: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Órdenes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f2f1;
            margin: 0;
            padding: 0;
            color: #212121;
        }

        header {
            background-color: #004d40;
            color: white;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin-top: 30px;
            text-align: center;
            color: #004d40;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #b2dfdb;
            text-align: center;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        tr:hover {
            background-color: #f1f8f6;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .btn-aceptar {
            background-color: #00796b;
            color: white;
        }

        .btn-aceptar:hover {
            background-color: #004d40;
        }

        .btn-rechazar {
            background-color: #b71c1c;
            color: white;
        }

        .btn-rechazar:hover {
            background-color: #880e0e;
        }

        .menu-button {
            display: block;
            width: fit-content;
            margin: 40px auto;
            padding: 12px 30px;
            background-color: #004d40;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: 0.3s ease;
        }

        .menu-button:hover {
            background-color: #00796b;
        }
    </style>
</head>
<body>
<header>
    <h2>Órdenes</h2>
</header>

<h1>Listado de Órdenes</h1>

<?php if ($result->num_rows === 0): ?>
    <p style="text-align: center;">No hay órdenes registradas.</p>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['idOrden']) ?></td>
                <td><?= htmlspecialchars($row['fecha']) ?></td>
                <td><?= htmlspecialchars($row['nombre'] ?? 'Sin cliente') ?></td>
                <td>$<?= number_format($row['total'], 2) ?></td>
                <td><?= htmlspecialchars($row['estado']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="idOrden" value="<?= $row['idOrden'] ?>">
                        <input type="hidden" name="accion" value="aceptar">
                        <button type="submit" class="btn btn-aceptar">Aceptar</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="idOrden" value="<?= $row['idOrden'] ?>">
                        <input type="hidden" name="accion" value="rechazar">
                        <button type="submit" class="btn btn-rechazar">Rechazar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<a href="index.php" class="menu-button">Volver al menú principal</a>

<?php $conn->close(); ?>
</body>
</html>
