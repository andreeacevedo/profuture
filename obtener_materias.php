<?php
include 'conexion.php';

if (isset($_GET["carrera_id"])) {
    $carrera_id = intval($_GET["carrera_id"]); // Convertir a entero para evitar inyecciones SQL

    $sql = "SELECT id, nombre, ciclo FROM materias WHERE carrera_id = ? ORDER BY ciclo";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carrera_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $materias = [];

    while ($row = $result->fetch_assoc()) {
        $materias[] = $row;
    }

    echo json_encode($materias);
}

$stmt->close();
$conn->close();
?>