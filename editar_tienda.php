<?php
session_start();
include_once("MysqlConnector.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de tienda no válido.";
    exit;
}

$db = new MysqlConnector();
$conn = $db->connect();
$idTienda = (int)$_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Descripcion  = $_POST['Descripcion'];
    $Ciudad       = $_POST['Ciudad'];
    $Direccion    = $_POST['Direccion'];
    $CodigoPostal = $_POST['CodigoPostal'];
    $Horario      = $_POST['Horario'];

    $stmt = $conn->prepare("UPDATE Tiendas SET Descripcion = ?, Ciudad = ?, Direccion = ?, CodigoPostal = ?, Horario = ? WHERE idTienda = ?");
    $stmt->bind_param("sssssi", $Descripcion, $Ciudad, $Direccion, $CodigoPostal, $Horario, $idTienda);

    if ($stmt->execute()) {
        header("Location: ver_tiendas.php");
        exit;
    } else {
        echo "Error al actualizar la tienda.";
    }
}

$stmt = $conn->prepare("SELECT * FROM Tiendas WHERE idTienda = ?");
$stmt->bind_param("i", $idTienda);
$stmt->execute();
$result = $stmt->get_result();
$tienda = $result->fetch_assoc();

if (!$tienda) {
    echo "Tienda no encontrada.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Tienda</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #e0f2f1; /* Verde claro */
        margin: 0;
        padding: 0;
        color: #212121;
    }

    header {
        background-color: #004d40; /* Verde oscuro */
        color: white;
        padding: 20px;
        text-align: center;
    }

    h1 {
        margin-top: 30px;
        text-align: center;
        color: #004d40;
    }

    form {
        width: 90%;
        max-width: 600px;
        margin: 30px auto;
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    label {
        display: block;
        margin-top: 15px;
        color: #00796b;
        font-weight: bold;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    input[readonly] {
        background-color: #f1f1f1;
    }

    input[type="submit"] {
        margin-top: 25px;
        padding: 12px 25px;
        background-color: #00796b;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #004d40;
    }

    .menu-button {
        display: block;
        width: fit-content;
        margin: 20px auto;
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
    <h2>Modificar Tienda</h2>
  </header>

  <form method="post">
    <label>ID Tienda:
      <input type="text" name="idTienda" value="<?= $idTienda ?>" readonly>
    </label>
    <label>Descripción:
      <input type="text" name="Descripcion" value="<?= htmlspecialchars($tienda['Descripcion']) ?>" required>
    </label>
    <label>Ciudad:
      <input type="text" name="Ciudad" value="<?= htmlspecialchars($tienda['Ciudad']) ?>" required>
    </label>
    <label>Dirección:
      <input type="text" name="Direccion" value="<?= htmlspecialchars($tienda['Direccion']) ?>" required>
    </label>
    <label>Código Postal:
      <input type="text" name="CodigoPostal" value="<?= htmlspecialchars($tienda['CodigoPostal']) ?>" required>
    </label>
    <label>Horario:
      <input type="text" name="Horario" value="<?= htmlspecialchars($tienda['Horario']) ?>" required>
    </label>
    <input type="submit" value="Guardar Cambios">
  </form>

  <a href="ver_tiendas.php" class="menu-button">Volver a Ver Tiendas</a>
</body>
</html>
<?php
$conn->close();
?>

