<?php
session_start();

// Si no ha iniciado sesión, redirige a loginCliente.php
if (!isset($_SESSION['idCliente'])) {
    header("Location: loginCliente.php");
    exit;
}

include_once("MysqlConnector.php");
$db   = new MysqlConnector();
$conn = $db->connect();

// 1) Cargar tiendas con productos en inventario y líneas
$tiendas = $conn->query("
    SELECT DISTINCT t.idTienda, t.Descripcion
    FROM Tiendas t
    JOIN Inventario i ON t.idTienda = i.idTienda
    WHERE i.cantidad > 0
    ORDER BY t.Descripcion
");

$lineas = $conn->query("SELECT * FROM LineaArticulos ORDER BY Descripcion");

// 2) Leer filtros desde GET
$idTienda = isset($_GET['idTienda']) && is_numeric($_GET['idTienda']) ? (int)$_GET['idTienda'] : 0;
$idLinea  = isset($_GET['idLinea']) && is_numeric($_GET['idLinea']) ? (int)$_GET['idLinea'] : 0;

// 3) Consulta de productos
if ($idTienda > 0) {
    $sql = "
        SELECT a.*, e.cantidad
        FROM Articulos a
        JOIN Inventario e ON a.idArticulo = e.idArticulo
        WHERE e.idTienda = ? AND e.cantidad > 0
    ";
    if ($idLinea > 0) {
        $sql .= " AND a.idLinea = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idTienda, $idLinea);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idTienda);
    }
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
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Catálogo de Productos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background: #e0f2f1;
      color: #333;
      margin: 0;
    }
    header, footer {
      background: #004d40;
      color: #fff;
      text-align: center;
      padding: 20px;
      position: relative;
    }
    .top-links {
      position: absolute;
      top: 20px;
      left: 20px;
    }
    .top-links a {
      margin-right: 20px;
      color: #fff;
      text-decoration: none;
      font-weight: bold;
    }
    nav {
      margin: 10px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }
    nav select {
      padding: 5px;
      font-size: 1rem;
      background: #b2dfdb;
      border: 1px solid #004d40;
      border-radius: 5px;
    }
    .back-button {
      background: #004d40;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      text-decoration: none;
      display: inline-block;
      font-weight: bold;
      transition: background-color 0.3s, transform 0.2s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .back-button:hover {
      background: #00796b;
      transform: translateY(-2px);
    }
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 40px auto;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }
    .card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s;
    }
    .card:hover {
      transform: scale(1.02);
    }
    .card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .card-body {
      padding: 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .card-body h3 {
      margin: 0 0 10px;
      color: #004d40;
    }
    .card-body p {
      margin: 5px 0;
      flex: 1;
      font-size: .95rem;
    }
    .card-body .price {
      font-weight: bold;
      margin-bottom: 10px;
      color: #00796b;
    }
    .card-body form {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    .card-body input[type=number] {
      width: 60px;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .card-body input[type=submit] {
      background: #004d40;
      color: #fff;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
    }
    .card-body input[type=submit]:hover {
      background: #00796b;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
<header>
  <div class="top-links">
    <a href="perfil.php"><i class="fas fa-user"></i> Perfil</a>
    <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
  </div>
  <h1></h1>
  <?php if (isset($_GET['bienvenida']) && isset($_SESSION['Nombre'])): ?>
    <p style="margin: 10px 0; font-size: 1.1rem;">
      ¡Bienvenido/a <strong><?= htmlspecialchars($_SESSION['Nombre']) ?></strong>!
    </p>
  <?php endif; ?>
  <nav>
    <form method="GET" action="productos.php" id="formTienda">
      <select name="idTienda" onchange="document.getElementById('formTienda').submit()">
        <option value="">— Selecciona La Sucursal —</option>
        <?php while($t = $tiendas->fetch_assoc()): ?>
          <option value="<?= $t['idTienda'] ?>" <?= $t['idTienda']==$idTienda?'selected':''?>>
            <?= htmlspecialchars($t['Descripcion']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>

    <a href="index.html" class="back-button"><i class="fas fa-arrow-left"></i> Menú Principal</a>
  </nav>
</header>

<div class="container">
  <?php if ($idTienda === 0): ?>
    <p style="text-align:center;">.</p>
  <?php elseif ($productos && $productos->num_rows): ?>
    <div class="grid">
      <?php while($p = $productos->fetch_assoc()): ?>
        <div class="card">
          <img src="uploads/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['Descripcion']) ?>">
          <div class="card-body">
            <h3><?= htmlspecialchars($p['Descripcion']) ?></h3>
            <p>Características: <?= htmlspecialchars($p['Caracteristicas']) ?></p>
            <p class="price">$<?= number_format($p['Precio'], 2) ?></p>
            <p>Disponibles: <?= $p['cantidad'] ?></p>
            <form method="POST" action="agregar_al_carrito.php">
              <input type="hidden" name="idArticulo" value="<?= $p['idArticulo'] ?>">
              <input type="hidden" name="idTienda" value="<?= $idTienda ?>">
              <input type="number" name="Cantidad" value="1" min="1" max="<?= $p['cantidad'] ?>" required>
              <input type="submit" value="Agregar">
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center;">No hay productos disponibles en esta tienda.</p>
  <?php endif; ?>
</div>

<footer>
  <p>&copy; <?= date('Y') ?> Joyería Suárez</p>
</footer>
</body>
</html>
<?php
if (isset($stmt)) $stmt->close();
$conn->close();
?>

