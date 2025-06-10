<?php
// Mostrar errores en pantalla para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once("MysqlConnector.php");

// Verifica si el administrador ha iniciado sesión
if (!isset($_SESSION['Admin_id'])) {
    header("Location: index.php");
    exit;
}

$db = new MysqlConnector();
$conn = $db->connect();

// Cambiar estado de habilitado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_id'], $_POST['current_status'])) {
    $id = (int)$_POST['toggle_id'];
    $new_status = ($_POST['current_status'] == 1) ? 0 : 1;

    $stmtToggle = $conn->prepare("UPDATE Articulos SET habilitado = ? WHERE idArticulo = ?");
    if ($stmtToggle) {
        $stmtToggle->bind_param("ii", $new_status, $id);
        $stmtToggle->execute();
        $stmtToggle->close();
        header("Location: ver_inventario.php");
        exit;
    } else {
        echo "Error al preparar toggle: " . $conn->error;
    }
}

// Eliminar artículo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];

    $stmtInv = $conn->prepare("DELETE FROM Inventario WHERE idArticulo = ?");
    if ($stmtInv) {
        $stmtInv->bind_param("i", $delete_id);
        $stmtInv->execute();
        $stmtInv->close();
    }

    $stmtArt = $conn->prepare("DELETE FROM Articulos WHERE idArticulo = ?");
    if ($stmtArt) {
        $stmtArt->bind_param("i", $delete_id);
        $stmtArt->execute();
        $stmtArt->close();
        header("Location: ver_inventario.php");
        exit;
    } else {
        echo "Error al preparar eliminación: " . $conn->error;
    }
}

// Obtener artículos e inventario
$stmt = $conn->prepare("
    SELECT a.idArticulo, a.descripcion, a.caracteristicas, a.precio, a.imagen, a.habilitado,
           COALESCE(i.cantidad, 0) as cantidad, i.idTienda
    FROM Articulos a
    LEFT JOIN Inventario i ON a.idArticulo = i.idArticulo
");

if (!$stmt) {
    die("Error al preparar consulta: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario</title>
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
    table {
      width: 100%;
      margin: 20px auto;
      border-collapse: collapse;
      background: white;
    }
    th, td {
      padding: 12px;
      border: 1px solid #b2dfdb;
      text-align: center;
    }
    th {
      background-color: #004d40;
      color: white;
    }
    tr.disabled {
      background-color: #ffe0b2;
    }
    img {
      width: 80px;
    }
    .btn {
      padding: 8px 12px;
      margin: 2px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }
    .edit-btn {
      background-color: #004d40;
      color: white;
    }
    .delete-btn {
      background-color: #c62828;
      color: white;
    }
    .toggle-btn {
      background-color: #00796b;
      color: white;
    }
    .edit-btn:hover { background: #00695c; }
    .delete-btn:hover { background: #b71c1c; }
    .toggle-btn:hover { background: #004d40; }
    .back-link {
      display: block;
      margin: 20px;
      text-align: center;
    }
    .back-link a {
      background-color: #004d40;
      color: white;
      padding: 10px 20px;
      border-radius: 10px;
      text-decoration: none;
    }
    .back-link a:hover {
      background-color: #00796b;
    }
  </style>
</head>
<body>

<header>
  <h1>Inventario</h1>
</header>

<table>
  <thead>
    <tr>
      <th>Descripción</th>
      <th>Características</th>
      <th>Precio</th>
      <th>Cantidad</th>
      <th>Imagen</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr class="<?= $row['habilitado'] ? '' : 'disabled' ?>">
        <td><?= htmlspecialchars($row['descripcion']) ?></td>
        <td><?= htmlspecialchars($row['caracteristicas']) ?></td>
        <td>$<?= number_format($row['precio'], 2) ?></td>
        <td><?= $row['cantidad'] ?></td>
        <td>
          <?php if (!empty($row['imagen']) && file_exists("uploads/" . $row['imagen'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['imagen']) ?>" alt="Imagen">
          <?php else: ?>
            Sin imagen
          <?php endif; ?>
        </td>
        <td><?= $row['habilitado'] ? 'Activo' : 'Deshabilitado' ?></td>
        <td>
          <a class="btn edit-btn" href="editar_inventario.php?id=<?= $row['idArticulo'] ?>&tienda=<?= $row['idTienda'] ?? 0 ?>">Editar</a>

          <form method="POST" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $row['idArticulo'] ?>">
            <button class="btn delete-btn" onclick="return confirm('¿Eliminar este artículo?')">Eliminar</button>
          </form>

          <form method="POST" style="display:inline;">
            <input type="hidden" name="toggle_id" value="<?= $row['idArticulo'] ?>">
            <input type="hidden" name="current_status" value="<?= $row['habilitado'] ?>">
            <button class="btn toggle-btn"><?= $row['habilitado'] ? 'Deshabilitar' : 'Habilitar' ?></button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<div class="back-link">
  <a href="index.php">← Regresar al Menú</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

