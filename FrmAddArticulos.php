<?php
include_once("MysqlConnector.php");
$db   = new MysqlConnector();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty(trim($_POST['descripcion_nueva_linea']))) {
        $descLinea = trim($_POST['descripcion_nueva_linea']);
        $stmtCheck = $conn->prepare("SELECT idLinea FROM LineaArticulos WHERE Descripcion = ?");
        $stmtCheck->bind_param("s", $descLinea);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->bind_result($idLinea);
            $stmtCheck->fetch();
        } else {
            $stmtLinea = $conn->prepare("INSERT INTO LineaArticulos (Descripcion) VALUES (?)");
            $stmtLinea->bind_param("s", $descLinea);
            $stmtLinea->execute();
            $idLinea = $stmtLinea->insert_id;
            $stmtLinea->close();
        }
        $stmtCheck->close();
    } else {
        $idLinea = isset($_POST['idLinea_existente']) ? (int)$_POST['idLinea_existente'] : 0;
    }

    $Descripcion     = trim($_POST['Descripcion']);
    $Caracteristicas = trim($_POST['Caracteristicas']);
    $Precio          = (float) $_POST['Precio'];

    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        die("Error al subir la imagen.");
    }
    $info = getimagesize($_FILES['imagen']['tmp_name']);
    if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
        die("Solo se permiten imágenes JPG o PNG.");
    }
    if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) {
        die("La imagen no debe superar los 2 MB.");
    }

    $ext    = $info[2] === IMAGETYPE_JPEG ? '.jpg' : '.png';
    $nombre = uniqid('art_') . $ext;
    $dest   = __DIR__ . "/uploads/{$nombre}";
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
        die("No se pudo guardar la imagen.");
    }

    $stmt = $conn->prepare(
        "INSERT INTO Articulos (idLinea, Descripcion, Caracteristicas, Precio, imagen) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issds", $idLinea, $Descripcion, $Caracteristicas, $Precio, $nombre);
    if (!$stmt->execute()) {
        die("Error al guardar artículo: " . htmlspecialchars($stmt->error));
    }
    $idArticulo = $stmt->insert_id;
    $stmt->close();
    $conn->close();

    header("Location: FrmAsignarExistencias.php?idArticulo={$idArticulo}");
    exit();
}

$lineas = $conn->query("SELECT * FROM LineaArticulos ORDER BY Descripcion");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Artículo</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #e0f2f1;
      margin: 0;
      padding: 20px;
    }

    h1 {
      color: #004d40;
      text-align: center;
    }

    .container {
      display: flex;
      gap: 40px;
      align-items: flex-start;
      justify-content: center;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    form {
      background: #ffffff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 77, 64, 0.2);
      width: 420px;
    }

    label {
      font-weight: bold;
      color: #00695c;
    }

    input, select, button {
      padding: 8px;
      font-size: 1rem;
      border: 1px solid #26a69a;
      border-radius: 5px;
      width: 100%;
      box-sizing: border-box;
    }

    input[type="file"] {
      padding: 5px;
    }

    .grupo-linea {
      display: flex;
      gap: 10px;
    }

    .grupo-linea > div {
      flex: 1;
    }

    button,
    .boton-regresar {
      background: #004d40;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
      margin-top: 10px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      padding: 10px;
      border-radius: 8px;
      width: 100%;
      box-sizing: border-box;
      transition: background 0.3s, transform 0.2s;
    }

    button:hover,
    .boton-regresar:hover {
      background: #00796b;
      transform: translateY(-2px);
    }

    .imagen-decorativa {
      max-width: 320px;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0, 77, 64, 0.3);
    }
  </style>
</head>
<body>
  <h1>Agregar Producto</h1>

  <div class="container">
    <form method="POST" action="FrmAddArticulos.php" enctype="multipart/form-data">
      <div class="grupo-linea">
        <div>
          <label for="idLinea_existente">Línea existente:</label>
          <select name="idLinea_existente" id="idLinea_existente">
            <option value="">— Selecciona una línea —</option>
            <?php while($l = $lineas->fetch_assoc()): ?>
              <option value="<?= $l['idLinea'] ?>"><?= htmlspecialchars($l['Descripcion']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div>
          <label for="descripcion_nueva_linea">O nueva línea:</label>
          <input type="text" name="descripcion_nueva_linea" id="descripcion_nueva_linea" placeholder="Ej. Anillos">
        </div>
      </div>

      <label for="Descripcion">Nombre:</label>
      <input type="text" name="Descripcion" id="Descripcion" required>

      <label for="Caracteristicas">Características:</label>
      <input type="text" name="Caracteristicas" id="Caracteristicas" required>

      <label for="Precio">Precio:</label>
      <input type="number" step="0.01" name="Precio" id="Precio" required>

      <label for="imagen">Imagen (JPG/PNG):</label>
      <input type="file" name="imagen" id="imagen" accept="image/png, image/jpeg" required>

      <button type="submit">Agregar Artículo</button>
      <a href="index.php" class="boton-regresar">← Regresar al Menú</a>
    </form>

    <img class="imagen-decorativa" src="https://www.joyeriasuarez.com/dw/image/v2/BCBK_PRD/on/demandware.static/-/Sites-Suarez-master-catalog/default/dwc2f42469/images/hi-res/PU22011-OAE-A001-2.jpg?sw=800&sh=1066" alt="Imagen decorativa">
  </div>
</body>
</html>

