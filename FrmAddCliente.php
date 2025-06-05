<?php
include_once("MysqlConnector.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $Nombre = $_POST['Nombre'];
    $Apellidos = $_POST['Apellidos'];
    $CorreoElectronico = $_POST['CorreoElectronico'];
    $DireccionPostal = $_POST['DireccionPostal'];
    $Colonia = $_POST['Colonia'];
    $Ciudad = $_POST['Ciudad'];
    $Estado = $_POST['Estado'];
    $Pais = $_POST['Pais'];
    $CodigoPostal = $_POST['CodigoPostal'];
    $Telefono = $_POST['Telefono'];
    $Password = $_POST['Password'];

    // Crear el hash de la contraseña
   $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);


    // Establecer conexión con la base de datos
    $db = new MysqlConnector();
    $conn = $db->connect();

    // Escapar los datos para evitar inyección SQL
    $Nombre = mysqli_real_escape_string($conn, $Nombre);
    $Apellidos = mysqli_real_escape_string($conn, $Apellidos);
    $CorreoElectronico = mysqli_real_escape_string($conn, $CorreoElectronico);
    $DireccionPostal = mysqli_real_escape_string($conn, $DireccionPostal);
    $Colonia = mysqli_real_escape_string($conn, $Colonia);
    $Ciudad = mysqli_real_escape_string($conn, $Ciudad);
    $Estado = mysqli_real_escape_string($conn, $Estado);
    $Pais = mysqli_real_escape_string($conn, $Pais);
    $CodigoPostal = mysqli_real_escape_string($conn, $CodigoPostal);
    $Telefono = mysqli_real_escape_string($conn, $Telefono);
    $Password = mysqli_real_escape_string($conn, $Password);

    // SQL para insertar los datos
    $sql = "INSERT INTO Clientes (Nombre, Apellidos, CorreoElectronico, DireccionPostal, Colonia, Ciudad, Estado, Pais, CodigoPostal, Telefono, Password) 
            VALUES ('$Nombre', '$Apellidos', '$CorreoElectronico', '$DireccionPostal', '$Colonia', '$Ciudad', '$Estado', '$Pais', '$CodigoPostal', '$Telefono','$hashedPassword')";

    // Ejecutar la consulta
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Si la inserción es exitosa, redirigir al usuario
        header("Location: productos.php");
        exit(); // Importante para detener la ejecución
    } else {
        // Si ocurre un error, mostrar el mensaje
        echo "<div style='color: red; font-weight: bold; text-align: center;'>Error: " . mysqli_error($conn) . "</div>";
    }

    // Cerrar la conexión a la base de datos
    $db->close();
} else {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Cliente | Joyería SUAREZ</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #d0f5e4, #b2dfdb);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            background: #ffffff;
            padding: 40px;
            width: 100%;
            max-width: 850px;
            border: 2px solid #004d40;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #004d40;
            font-size: 30px;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: #333;
            font-size: 15px;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1 1 calc(50% - 20px);
            display: flex;
            flex-direction: column;
        }

        .form-group-full {
            flex: 1 1 100%;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 12px;
            border: 1px solid #ccc;
            font-size: 16px;
            background-color: #e0f2f1;
            border-radius: 6px;
        }

        input[type="submit"],
        .back-button {
            width: 100%;
            background-color: #00796b;
            color: white;
            padding: 16px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type="submit"]:hover,
        .back-button:hover {
            background-color: #004d40;
        }

        .menu-link {
            margin-top: 10px;
            text-align: center;
        }

        .menu-link a {
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .form-group {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>SELECTEDSUAREZ</h2>
        <p>Le invitamos a formar parte de la exclusiva comunidad que le permitirá acceder en primicia a todo el contenido, noticias, eventos, nuevas colecciones, ventas privadas y otros beneficios de Suarez.</p>
        
        <form method="POST" action="FrmAddCliente.php">
            <div class="form-group">
                <input type="text" name="Nombre" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <input type="text" name="Apellidos" placeholder="Apellidos" required>
            </div>
            <div class="form-group">
                <input type="email" name="CorreoElectronico" placeholder="Correo Electrónico" required>
            </div>
            <div class="form-group">
                <input type="text" name="DireccionPostal" placeholder="Dirección" required>
            </div>
            <div class="form-group">
                <input type="text" name="Colonia" placeholder="Colonia" required>
            </div>
            <div class="form-group">
                <input type="text" name="Ciudad" placeholder="Ciudad" required>
            </div>
            <div class="form-group">
                <input type="text" name="Estado" placeholder="Estado" required>
            </div>
            <div class="form-group">
                <input type="text" name="Pais" placeholder="País" required>
            </div>
            <div class="form-group">
                <input type="text" name="CodigoPostal" placeholder="Código Postal" required>
            </div>
            <div class="form-group">
                <input type="text" name="Telefono" placeholder="Teléfono" required>
            </div>
            <div class="form-group form-group-full">
                <input type="password" name="Password" placeholder="Contraseña" required>
            </div>
            <div class="form-group form-group-full">
                <input type="submit" value="Registrar">
            </div>
        </form>

        <div class="menu-link">
            <a href="index.html">
                <button class="back-button">Regresar al Menú</button>
            </a>
        </div>
    </div>
</body>
</html>

<?php
}
?>
