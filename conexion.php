<?php
$host = "localhost";  
$user = "root";       
$pass = "1234";           
$dbname = "profuture";

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>
