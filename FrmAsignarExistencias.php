<?php
include_once("MysqlConnector.php");

if (!isset($_GET['idArticulo']) || !is_numeric($_GET['idArticulo'])) {
    die("Artículo no especificado.");
}

$idArticulo = (int)$_GET['idArticulo'];

$db = new MysqlConnector();
$conn = $db->connect();

// Obtener nombre del artículo
$stmt = $conn->prepare("SELECT Descripcion FROM Articulos WHERE idArticulo = ?");
$stmt->bind_param("i", $idArticulo);
$stmt->execute();
$stmt->bind_result($descripcionArticulo);
if (!$stmt->fetch()) {
    die("Artículo no encontrado.");
}
$stmt->close();

// Obtener todas las tiendas
$Tiendas = $conn->query("SELECT idTienda, Descripcion FROM Tiendas");

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($_POST['Inventario'] as $idTienda => $Cantidad) {
        $idTienda = (int)$idTienda;
        $Cantidad = (int)$Cantidad;

        if ($Cantidad >= 0) {
            // Verificar si ya existe una fila
            $check = $conn->prepare("SELECT idInventario FROM Inventario WHERE idArticulo = ? AND idTienda = ?");
            $check->bind_param("ii", $idArticulo, $idTienda);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                // Actualizar
                $update = $conn->prepare("UPDATE Inventario SET Cantidad = ? WHERE idArticulo = ? AND idTienda = ?");
                $update->bind_param("iii", $Cantidad, $idArticulo, $idTienda);
                $update->execute();
                $update->close();
            } else {
                // Insertar
                $insert = $conn->prepare("INSERT INTO Inventario (idArticulo, idTienda, Cantidad) VALUES (?, ?, ?)");
                $insert->bind_param("iii", $idArticulo, $idTienda, $Cantidad);
                $insert->execute();
                $insert->close();
            }
            $check->close();
        }
    }

    echo "<p class='success'>Existencias actualizadas correctamente.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Existencias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f8f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2e7d32;
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input[type=number] {
            width: 100%;
            padding: 10px;
            border: 1px solid #a5d6a7;
            border-radius: 5px;
            background-color: #f9fbe7;
        }

        .tienda {
            border: 1px solid #c8e6c9;
            padding: 15px;
            border-radius: 8px;
            background-color: #e8f5e9;
        }

        button {
            background-color: #388e3c;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #2e7d32;
        }

        .success {
            color: #388e3c;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .back-button {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            background-color: #00695c;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #004d40;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Asignar existencias para:<br> <?= htmlspecialchars($descripcionArticulo) ?></h1>

        <form method="POST">
            <?php while ($t = $Tiendas->fetch_assoc()): ?>
                <div class="tienda">
                    <label><?= htmlspecialchars($t['Descripcion']) ?>:</label>
                    <input type="number" name="Inventario[<?= $t['idTienda'] ?>]" min="0" value="0">
                </div>
            <?php endwhile; ?>
            <button type="submit">Guardar existencias</button>
        </form>

        <a href="FrmAddArticulos.php" class="back-button">← Regresar al menú</a>
    </div>
</body>
</html>

