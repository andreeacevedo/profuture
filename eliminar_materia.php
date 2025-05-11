<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: inicio-sesion.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["materia_id"])) {
    $usuario_id = $_SESSION["usuario_id"];
    $materia_id = $_POST["materia_id"];

    $sql = "DELETE FROM usuario_materias WHERE usuario_id = ? AND materia_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $materia_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: inicio.php");
exit();
