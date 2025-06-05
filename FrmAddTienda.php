<?php
include_once("MysqlConnector.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $Descripcion = $_POST['Descripcion'];
    $Ciudad = $_POST['Ciudad'];
    $Direccion = $_POST['Direccion'];
    $CodigoPostal = $_POST['CodigoPostal'];
    $Horario = $_POST['Horario'];

    // Crear una instancia de la clase MysqlConnector
    $db = new MysqlConnector();
    // Conectar a la base de datos
    $conn = $db->connect(); 

    // Verificar que la conexión no es nula
    if ($conn) {
        // Preparar la consulta SQL
        $sql = "INSERT INTO Tiendas (Descripcion, Ciudad, Direccion, CodigoPostal, Horario) 
                VALUES ('$Descripcion', '$Ciudad', '$Direccion', '$CodigoPostal', '$Horario')";

        // Ejecutar la consulta
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>Tienda agregada exitosamente.</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }

        // Cerrar la conexión
        $db->close();
    } else {
        echo "<p style='color: red;'>Error de conexión a la base de datos.</p>";
    }
} else {
?>
<!-- Formulario HTML con estilo -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Tienda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f2f1;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #004d40;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            gap: 40px;
            padding: 30px;
        }

        form {
            background-color: #ffffff;
            border: 2px solid #004d40;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #004d40;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #80cbc4;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="submit"], .btn-regresar {
            background-color: #004d40;
            color: white;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            font-weight: bold;
        }

        input[type="submit"]:hover, .btn-regresar:hover {
            background-color: #00796b;
        }

        .image-container {
            max-width: 600px;
            flex: 1;
            text-align: center;
        }

        .image-container img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<header>
    <h1>Nueva Tienda</h1>
</header>

<div class="container">
    <form method="POST" action="FrmAddTienda.php">
        <label for="Descripcion">Descripción:</label>
        <input type="text" name="Descripcion" required>

        <label for="Ciudad">Ciudad:</label>
        <input type="text" name="Ciudad" required>

        <label for="Direccion">Dirección:</label>
        <input type="text" name="Direccion" required>

        <label for="CodigoPostal">Código Postal:</label>
        <input type="text" name="CodigoPostal" required>

        <label for="Horario">Horario:</label>
        <input type="text" name="Horario" required>

        <input type="submit" value="Agregar Tienda">
        <a href="index.php"><button type="button" class="btn-regresar">Regresar al Menú</button></a>
    </form>

    <div class="image-container">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSC3T8tchPgNIIHeHz1m8w9TJIwZJnyP64Lg&s" alt="Imagen decorativa tienda">
    </div>
</div>

</body>
</html>



<?php
}
?>
