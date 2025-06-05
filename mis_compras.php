<?php
session_start();
include_once("MysqlConnector.php");

if (!isset($_SESSION['idCliente'])) {
    header("Location: loginCliente.php");
    exit;
}

$idCliente = $_SESSION['idCliente'];

$db = new MysqlConnector();
$conn = $db->connect();

// Consulta todas las ventas del cliente
$sql = "SELECT v.folio, v.fecha, a.Descripcion, d.cantidad, d.precio_unitario
        FROM Ventas v
        JOIN VentasDetalles d ON v.folio = d.folio
        JOIN Articulos a ON d.idArticulo = a.idArticulo
        WHERE v.idCliente = ?
        ORDER BY v.fecha DESC, v.folio DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$result = $stmt->get_result();

// Agrupar ventas por folio
$ventas = [];
while ($row = $result->fetch_assoc()) {
    $folio = $row['folio'];
    if (!isset($ventas[$folio])) {
        $ventas[$folio] = [
            'fecha' => $row['fecha'],
            'detalles' => []
        ];
    }

    $ventas[$folio]['detalles'][] = [
        'descripcion' => $row['Descripcion'],
        'cantidad' => $row['cantidad'],
        'precio' => $row['precio_unitario']
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Compras</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f8f8f8;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #e91e63;
        }
        .compra {
            background: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .compra h3 {
            margin-top: 0;
            color: #e91e63;
        }
        .item {
            margin-left: 20px;
            margin-bottom: 8px;
        }
        .total {
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        .btn-imprimir {
            background: #e91e63;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-imprimir:hover {
            background: #c2185b;
        }
    </style>
    <script>
        function imprimirTicket(folio) {
            const contenido = document.getElementById('ticket-' + folio).innerHTML;
            const ventana = window.open('', '', 'width=600,height=800');
            ventana.document.write('<html><head><title>Imprimir Compra</title></head><body>');
            ventana.document.write('<h2>Joyería Suárez</h2>');
            ventana.document.write(contenido);
            ventana.document.write('</body></html>');
            ventana.document.close();
            ventana.print();
        }
    </script>
</head>
<body>
    <h2>Mis Compras</h2>

    <?php if (empty($ventas)): ?>
        <p style="text-align:center;">No tienes compras registradas.</p>
    <?php else: ?>
        <?php foreach ($ventas as $folio => $venta): ?>
            <div class="compra">
                <div id="ticket-<?= $folio ?>">
                    <h3>Folio #<?= $folio ?> — <?= $venta['fecha'] ?></h3>
                    <?php
                        $total = 0;
                        foreach ($venta['detalles'] as $d):
                            $subtotal = $d['cantidad'] * $d['precio'];
                            $total += $subtotal;
                    ?>
                        <div class="item">
                            <?= htmlspecialchars($d['Descripcion']) ?> — <?= $d['cantidad'] ?> x $<?= number_format($d['precio'], 2) ?> = $<?= number_format($subtotal, 2) ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="total">Total: $<?= number_format($total, 2) ?></div>
                </div>
                <button class="btn-imprimir" onclick="imprimirTicket(<?= $folio ?>)">Imprimir</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
