<?php 
session_start();
include 'conexion.php';

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "Debes iniciar sesión"]);
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

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

if (empty($materias)) {
    echo json_encode(["error" => "No tienes materias registradas."]);
    exit();
}

$empleos = [];

$api_url = "https://jsearch.p.rapidapi.com/search";
$api_key = "bae94c5651msh7810d674e074304p164dabjsn3410759e2cbc"; 
$headers = [
    "X-RapidAPI-Key: $api_key",
    "X-RapidAPI-Host: jsearch.p.rapidapi.com"
];

foreach ($materias as $materia) {
    $query = urlencode($materia);
    $url = "$api_url?query=$query&num_pages=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data["data"])) {
        $count = 0;
        foreach ($data["data"] as $job) {
            if ($count >= 3) break;
            $empleos[] = [
                "titulo" => $job["job_title"] ?? "Sin título",
                "empresa" => $job["employer_name"] ?? "Empresa no especificada",
                "url" => $job["job_apply_link"] ?? "#",
                "plataforma" => "JSearch API"
            ];
            $count++;
        }
    }
}

echo json_encode($empleos);
