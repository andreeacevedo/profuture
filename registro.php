<?php
session_start();
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $universidad_id = $_POST["universidad"]; // Capturar la universidad seleccionada

    $sql = "INSERT INTO usuarios (nombre, email, password, universidad_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssi", $nombre, $email, $password, $universidad_id);

        if ($stmt->execute()) {
            $_SESSION["usuario"] = $nombre; // Guarda el usuario en la sesión
            $_SESSION["id_usuario"] = $stmt->insert_id; // Guarda el ID del nuevo usuario en la sesión
            header("Location: inicio.php"); // 🔹 Redirigir automáticamente a inicio.php
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
