<?php
include_once("MysqlConnector.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Descripcion = $_POST['Descripcion'];

    $db = new MysqlConnector();
    $conn = $db->connect();

    $sql = "INSERT INTO LineaArticulos (Descripcion) VALUES ('$Descripcion')";

    if ($conn->query($sql) === TRUE) {
        echo "Línea de artículo agregada exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $db->close();
} else {
?>
<form method="POST" action="FrmAddLinea.php">
    Descripción de la línea: <input type="text" name="descripcion" required><br>
    <input type="submit" value="Agregar Línea">
</form>
<?php
}
?>
