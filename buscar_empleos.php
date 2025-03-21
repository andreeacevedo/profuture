<?php
session_start();
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "Debes iniciar sesión"]);
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

// Obtener materias seleccionadas por el usuario
$sql = "SELECT m.nombre FROM usuario_materias um 
        JOIN materias m ON um.materia_id = m.id 
        WHERE um.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$materias = [];
while ($row = $result->fetch_assoc()) {
    $materias[] = $row["nombre"];
}
$stmt->close();
$conn->close();

// Validar si el usuario tiene materias registradas
if (empty($materias)) {
    echo json_encode(["error" => "No tienes materias registradas."]);
    exit();
}

$empleos = [];

//Integración con Remotive.io
foreach ($materias as $materia) {
    $materia_encoded = urlencode($materia);
    $url = "https://remotive.io/api/remote-jobs?search=$materia_encoded";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $count = 0;

    if (isset($data["jobs"])) {
        foreach ($data["jobs"] as $job) {
            if ($count >= 3) break;
            $empleos[] = [
                "titulo" => $job["title"],
                "empresa" => $job["company_name"],
                "url" => $job["url"],
                "plataforma" => "Remotive.io"
            ];
            $count++;
        }
    }
}

// Devolver JSON con los empleos
echo json_encode($empleos);
?>
