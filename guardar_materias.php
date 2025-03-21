<?php
session_start();
include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    echo "Error: Debes iniciar sesión.";
    exit;
}

$usuario_id = $_SESSION["usuario_id"]; // ID del usuario en sesión

if (!empty($data["materias"])) {
    foreach ($data["materias"] as $materia_id) {
        // Verificar si la materia ya fue seleccionada para evitar duplicados
        $sql_check = "SELECT * FROM usuario_materias WHERE usuario_id = ? AND materia_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $usuario_id, $materia_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 0) { // Solo insertar si no existe
            $sql_insert = "INSERT INTO usuario_materias (usuario_id, materia_id) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ii", $usuario_id, $materia_id);
            $stmt_insert->execute();
        }
    }
    echo "Materias guardadas con éxito.";
} else {
    echo "Error: No se seleccionaron materias.";
}

$stmt_check->close();
$conn->close();
?>