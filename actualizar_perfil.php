<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: inicio-sesion.html");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $universidad = $_POST["universidad"];
    $carrera = $_POST["carrera"];

    $sql = "UPDATE usuarios SET nombre = ?, email = ?, universidad_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $nombre, $email, $universidad, $usuario_id);
    $stmt->execute();
    $stmt->close();

    header("Location: perfil.php");
    exit();
}
?>
