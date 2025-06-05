<?php
include_once("MysqlConnector.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idArticulo = $_POST['idArticulo'];
    $idTienda = $_POST['idTienda'];
    $cantidad = $_POST['cantidad'];

    $db = new MysqlConnector();
    $conn = $db->connect();

    $sql = "INSERT INTO Inventario (idArticulo, idTienda, cantidad) 
            VALUES ('$idArticulo', '$idTienda', '$cantidad')";

    if ($conn->query($sql) === TRUE) {
        echo "Existencias agregadas exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $db->close();
} else {
?>
<!DOCTYPE html>
<html lang="es">
<head><?php
include_once("MysqlConnector.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idArticulo = $_POST['idArticulo'];
    $idTienda = $_POST['idTienda'];
    $cantidad = $_POST['cantidad'];

    $db = new MysqlConnector();
    $conn = $db->connect();

    $sql = "INSERT INTO Inventario (idArticulo, idTienda, cantidad) 
            VALUES ('$idArticulo', '$idTienda', '$cantidad')";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Existencias agregadas exitosamente.";
        $color = "green";
    } else {
        $mensaje = "Error al agregar existencias: " . $conn->error;
        $color = "red";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Existencias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fce4ec;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #ad1457;
            font-size: 2em;
            margin-bottom: 20px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #880e4f;
        }

        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background-color: #ad1457;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #880e4f;
        }

        .mensaje {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }

        .btn-regresar {
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            background-color: #ad1457;
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .btn-regresar:hover {
            background-color: #880e4f;
        }
    </style>
</head>
<body>

<h1>Agregar Existencias</h1>

<?php if (isset($mensaje)): ?>
    <div class="mensaje" style="color: <?= $color ?>;"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="POST" action="FrmAddExistencia.php">
    <label for="idArticulo">ID del Artículo:</label>
    <input type="number" name="idArticulo" id="idArticulo" required>

    <label for="idTienda">ID de la Tienda:</label>
    <input type="number" name="idTienda" id="idTienda" required>

    <label for="cantidad">Cantidad:</label>
    <input type="number" name="cantidad" id="cantidad" required>

    <input type="submit" value="Agregar Existencias">
</form>

<a href="index.php" class="btn-regresar">Inicio</a>

</body>
</html>

    <meta charset="UTF-8">
    <title>Agregar Existencias</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Agregar Existencias</h1>
    <form method="POST" action="FrmAddExistencia.php">
        Artículo ID: <input type="number" name="idArticulo" required><br>
        Tienda ID: <input type="number" name="idTienda" required><br>
        Cantidad: <input type="number" name="cantidad" required><br>
        <input type="submit" value="Agregar Existencias">
    </form>
</body>
</html>
<?php
}
?>
