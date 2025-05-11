<?php
session_start();
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $universidad_id = $_POST["universidad"];
    $carrera_id = $_POST["carrera"]; // ✅ nuevo campo obligatorio

    $sql = "INSERT INTO usuarios (nombre, email, password, universidad_id, carrera_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssii", $nombre, $email, $password, $universidad_id, $carrera_id);

        if ($stmt->execute()) {
            $_SESSION["usuario"] = $nombre; 
            $_SESSION["usuario_id"] = $stmt->insert_id;
            header("Location: Inicio.php"); 
            exit();
        } else {
            echo "Error al registrar usuario: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error en la consulta SQL.";
    }

    $conn->close();
}
?>
