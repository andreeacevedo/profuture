<?php
include 'conexion.php';

$sql = "SELECT id, nombre FROM universidades ORDER BY nombre ASC";
$result = $conn->query($sql);

$universidades = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $universidades[] = $row;
    }
}

$conn->close();

echo json_encode($universidades);
?>
