<?php
include_once("MysqlConnector.php");
$db   = new MysqlConnector();
$conn = $db->connect();

// Cargar tiendas
$tiendas = $conn->query("SELECT * FROM Tiendas ORDER BY Descripcion");

// Filtros
$idTienda = isset($_GET['idTienda']) && is_numeric($_GET['idTienda']) ? (int)$_GET['idTienda'] : 0;

// Consulta
if ($idTienda > 0) {
    $sql = "
        SELECT a.*, e.cantidad
        FROM Articulos a
        JOIN Inventario e ON a.idArticulo = e.idArticulo
        WHERE e.idTienda = ? AND e.cantidad > 0
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idTienda);
    $stmt->execute();
    $productos = $stmt->get_result();
} else {
    $productos = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Catálogo de Productos</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #e0f2f1;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    header, footer {
      background-color: #004d40;
      color: white;
      text-align: center;
      padding: 20px;
    }

    .main-content {
      display: flex;
      flex-direction: row;
      flex: 1;
    }

    aside {
      width: 240px;
      background-color: #00796b;
      padding: 20px;
      color: white;
      min-height: 100vh;
      box-shadow: 2px 0 6px rgba(0,0,0,0.1);
    }

    aside h2 {
      margin-top: 0;
      font-size: 18px;
    }

    aside form {
      display: flex;
      flex-direction: column;
    }

    aside select {
      padding: 8px;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      margin-top: 10px;
    }

    .content {
      flex: 1;
      padding: 20px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }

    .card {
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    .card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .card-body {
      padding: 15px;
    }

    .card-body h3 {
      margin: 0 0 10px;
      color: #00796b;
    }

    .card-body p {
      margin: 5px 0;
      font-size: 14px;
    }

    .card-body .price {
      font-weight: bold;
    }

    .decorative-img {
      max-width: 100%;
      height: auto;
      margin: 30px auto;
      display: block;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .btn-regresar {
      display: block;
      width: 220px;
      margin: 40px auto;
      padding: 12px;
      background-color: #004d40;
      color: white;
      text-align: center;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .btn-regresar:hover {
      background-color: #00695c;
    }
  </style>
</head>
<body>

<header>
  <h1>Catálogo de Productos</h1>
</header>

<div class="main-content">

  <!-- Menú lateral izquierdo -->
  <aside>
    <h2>Selecciona una tienda:</h2>
    <form method="GET" id="formTienda">
      <select name="idTienda" onchange="document.getElementById('formTienda').submit()">
        <option value="">— Selecciona —</option>
        <?php while($t = $tiendas->fetch_assoc()): ?>
          <option value="<?= $t['idTienda'] ?>" <?= $t['idTienda']==$idTienda?'selected':'' ?>>
            <?= htmlspecialchars($t['Descripcion']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>
  </aside>

  <!-- Contenido principal -->
  <div class="content">
    <?php if ($idTienda === 0): ?>
      <p style="text-align:center; font-size:18px;"></p>
    <?php elseif ($productos && $productos->num_rows): ?>
      <div class="grid">
        <?php while($p = $productos->fetch_assoc()): ?>
          <div class="card">
            <img src="uploads/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['Descripcion']) ?>">
            <div class="card-body">
              <h3><?= htmlspecialchars($p['Descripcion']) ?></h3>
              <p><?= htmlspecialchars($p['Caracteristicas']) ?></p>
              <p class="price">Precio: $<?= number_format($p['Precio'], 2) ?></p>
              <p>Disponibles: <?= $p['cantidad'] ?></p>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p style="text-align:center; font-size:18px;">No hay productos disponibles en esta tienda.</p>
    <?php endif; ?>

    <style>
  .decorative-img {
    display: block;
    max-width: 800px;
    width: 100%;
    margin: 40px auto 20px auto;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 100, 0, 0.3);
  }
</style>

<?php if ($idTienda === 0): ?>
  <!-- Imagen decorativa solo si no se ha seleccionado tienda -->
  <img class="decorative-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT2I2x-5aqKaW5NGC8AVziosxvGeY_CBVkPGw&s" alt="Imagen decorativa">
<?php endif; ?>


    <!-- Botón de regreso -->
    <a href="index.php" class="btn-regresar">Regresar al Menú</a>
  </div>
</div>

<footer>
  <p>&copy; <?= date('Y') ?> Joyería SUAREZ</p>
</footer>

<?php
if (isset($stmt)) $stmt->close();
$conn->close();
?>

