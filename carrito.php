<?php
session_start();
$carrito = $_SESSION['carrito'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tu Carrito</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom right, #e0f2f1, #ffffff);
      margin: 0;
      padding: 0;
      color: #004d40;
    }

    header {
      background-color: #004d40;
      color: white;
      padding: 25px;
      text-align: center;
      font-size: 26px;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
    }

    .producto {
      display: flex;
      gap: 20px;
      background-color: #ffffff;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      border: 1px solid #b2dfdb;
    }

    img {
      width: 120px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #b2dfdb;
    }

    .info {
      flex: 1;
    }

    .info h3 {
      margin: 0 0 10px;
      color: #00796b;
    }

    .info p {
      margin: 4px 0;
      color: #00695c;
    }

    .total {
      text-align: right;
      font-size: 1.3rem;
      font-weight: bold;
      color: #004d40;
      margin-top: 20px;
    }

    .botones {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: flex-start;
      margin-top: 30px;
    }

    .botones a,
    .botones button {
      background-color: #00796b;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      text-decoration: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: background-color 0.3s ease;
    }

    .botones a:hover,
    .botones button:hover {
      background-color: #004d40;
    }

    form {
      margin: 0;
    }

    .mensaje {
      font-size: 20px;
      color: #00796b;
      text-align: center;
      margin-top: 60px;
    }
  </style>
</head>
<body>

<header>
  <i class="fas fa-shopping-cart"></i> Carrito de Compras
</header>

<div class="container">
  <?php if (empty($carrito)): ?>
    <div class="mensaje">Tu carrito está vacío.</div>
  <?php else: ?>
    <?php $total = 0; ?>
    <?php foreach ($carrito as $item): ?>
      <div class="producto">
        <img src="uploads/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['Descripcion']) ?>">
        <div class="info">
          <h3><?= htmlspecialchars($item['Descripcion']) ?></h3>
          <p>Precio: $<?= number_format($item['Precio'], 2) ?></p>
          <p>Cantidad: <?= $item['Cantidad'] ?></p>
          <p>Subtotal: $<?= number_format($item['Cantidad'] * $item['Precio'], 2) ?></p>
        </div>
      </div>
      <?php $total += $item['Cantidad'] * $item['Precio']; ?>
    <?php endforeach; ?>

    <div class="total">Total: $<?= number_format($total, 2) ?></div>

    <div class="botones">
      <a href="productos.php"><i class="fas fa-arrow-left"></i> Seguir comprando</a>

      <form action="finalizar_compra.php" method="POST">
        <button type="submit"><i class="fas fa-check"></i> Finalizar compra</button>
      </form>

      <form action="vaciar_carrito.php" method="POST">
        <button type="submit"><i class="fas fa-trash-alt"></i> Vaciar carrito</button>
      </form>
    </div>
  <?php endif; ?>

  <!-- Botón "Volver al menú" SIEMPRE visible -->
  <div class="botones" style="margin-top: 40px;">
    <a href="productos.php"><i class="fas fa-home"></i> Volver al menú</a>
  </div>
</div>

</body>
</html>





