<?php
include 'conexion.php';

if (isset($_GET["universidad_id"])) {
    $universidad_id = intval($_GET["universidad_id"]); // Asegurar que sea un número entero

    $sql = "SELECT uc.id, cg.nombre 
            FROM universidad_carrera uc
            INNER JOIN carreras_generales cg ON uc.carrera_general_id = cg.id
            WHERE uc.universidad_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $universidad_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $carreras = [];

    while ($row = $result->fetch_assoc()) {
        $carreras[] = $row;
    }

    echo json_encode($carreras);
}

$stmt->close();
$conn->close();
?>
