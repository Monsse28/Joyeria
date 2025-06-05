<?php
session_start();
if (!isset($_SESSION['Admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menú de Administrador</title>
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

    .main-container {
      display: flex;
      flex-direction: row;
      padding: 30px;
      justify-content: center;
      align-items: flex-start;
      gap: 40px;
      flex-wrap: wrap;
    }

    .image-container {
      flex: 1 1 300px;
      text-align: center;
    }

    .image-container img {
      max-width: 100%;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    .menu-container {
      flex: 1 1 300px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .menu-button {
      background-color: #004d40;
      color: white;
      padding: 15px;
      border: none;
      border-radius: 12px;
      text-align: center;
      font-size: 18px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s, transform 0.2s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .menu-button:hover {
      background-color: #00796b;
      transform: translateY(-2px);
    }

    footer {
      background-color: #004d40;
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
    }

    @media (max-width: 768px) {
      .main-container {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>Menú</h1>
  </header>

  <div class="main-container">
    <div class="image-container">
      <img src="https://www.joyeriasuarez.com/on/demandware.static/-/Library-Sites-Suarez_SFRA/default/dw1afb967f/2025/HojasdeArce/hojasdearceH7.jpg" alt="Joyería">
    </div>

    <div class="menu-container">
      <a class="menu-button" href="ver_clientes.php">Clientes</a>
      <a class="menu-button" href="ordenes_admin.php">Órdenes</a>
      <a class="menu-button" href="ventas.php">Ventas</a>
      <a class="menu-button" href="ver_inventario.php">Ver Inventario</a>
      <a class="menu-button" href="ShowExistencias.php">Actualizar Inventario</a>
      <a class="menu-button" href="Showproductos.php">Ver Productos</a>
      <a class="menu-button" href="FrmAddArticulos.php">Agregar Producto</a>
      <a class="menu-button" href="FrmAddTienda.php">Agregar Sucursal</a>
      <a class="menu-button" href="ver_tiendas.php">Actualizar Sucursal</a>
      <a class="menu-button" href="index.html">Regresar a la Página Principal</a>
    </div>
  </div>

  <footer>
    &copy; <?= date("Y") ?> Joyería SUAREZ
  </footer>

</body>
</html>

