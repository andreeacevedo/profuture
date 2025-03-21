<?php
include 'conexion.php';

$carrera_id = isset($_GET["carrera_id"]) ? $_GET["carrera_id"] : 0;

$sql = "SELECT descripcion FROM universidad_carrera WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $carrera_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["descripcion" => $row["descripcion"]]);
} else {
    echo json_encode(["descripcion" => "No hay información disponible."]);
}

$stmt->close();
$conn->close();
?>
