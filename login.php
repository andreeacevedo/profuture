<?php
session_start();
include 'conexion.php'; // Conexión con MySQL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nombre, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // 🔹 Guardamos el ID y el nombre en la sesión con nombres consistentes
            $_SESSION["usuario_id"] = $id; // ID del usuario
            $_SESSION["usuario"] = $nombre; // Nombre del usuario

            header("Location: inicio.php"); // Redirigir al dashboard después de iniciar sesión
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>
