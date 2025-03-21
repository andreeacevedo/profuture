<?php
session_start();
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["error" => "Debes iniciar sesión"]);
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

// Obtener materias del usuario desde la base de datos
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

$cursos = [];

//coursera//
foreach ($materias as $materia) {
    $materia_encoded = urlencode($materia);
    $url = "https://api.coursera.org/api/courses.v1?q=search&query=$materia_encoded";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $count = 0;

    if (isset($data["elements"])) {
        foreach ($data["elements"] as $curso) {
            if ($count >= 3) break;
            $cursos[] = [
                "nombre" => $curso["name"],
                "descripcion" => "Curso de $materia en Coursera",
                "url" => "https://www.coursera.org/learn/" . $curso["slug"],
                "plataforma" => "Coursera"
            ];
            $count++;
        }
    }
}

//freeCode//

$mapaFreeCodeCamp = [
    "javascript" => "https://www.freecodecamp.org/learn/javascript-algorithms-and-data-structures/",
    "python" => "https://www.freecodecamp.org/learn/scientific-computing-with-python/",
    "html" => "https://www.freecodecamp.org/learn/responsive-web-design/",
    "css" => "https://www.freecodecamp.org/learn/responsive-web-design/"
];

$agregadosFCC = [];

foreach ($materias as $materia) {
    foreach ($mapaFreeCodeCamp as $clave => $url) {
        if (stripos($materia, $clave) !== false && !in_array($clave, $agregadosFCC)) {
            $cursos[] = [
                "nombre" => "Certificación en " . ucfirst($clave),
                "descripcion" => "Curso gratuito en freeCodeCamp",
                "url" => $url,
                "plataforma" => "freeCodeCamp"
            ];
            $agregadosFCC[] = $clave;
            break;
        }
    }
}

//Khan Academy//
$mapaKhan = [
    "matemáticas" => "https://es.khanacademy.org/math",
    "ciencias" => "https://es.khanacademy.org/science",
    "programación" => "https://es.khanacademy.org/computing/computer-programming",
    "historia" => "https://es.khanacademy.org/humanities"
];

$agregadosKhan = [];

foreach ($materias as $materia) {
    foreach ($mapaKhan as $clave => $url) {
        if (stripos($materia, $clave) !== false && !in_array($clave, $agregadosKhan)) {
            $cursos[] = [
                "nombre" => "Curso de " . ucfirst($clave),
                "descripcion" => "Contenido gratuito en Khan Academy",
                "url" => $url,
                "plataforma" => "Khan Academy"
            ];
            $agregadosKhan[] = $clave;
            break;
        }
    }
}

// ===================================
// 🔹 4. Codecademy (sin duplicados)
// ===================================
$mapaCodecademy = [
    "javascript" => "https://www.codecademy.com/learn/introduction-to-javascript",
    "python" => "https://www.codecademy.com/learn/learn-python-3",
    "html" => "https://www.codecademy.com/learn/learn-html",
    "css" => "https://www.codecademy.com/learn/learn-css"
];

$agregadosCodecademy = [];

foreach ($materias as $materia) {
    foreach ($mapaCodecademy as $clave => $url) {
        if (stripos($materia, $clave) !== false && !in_array($clave, $agregadosCodecademy)) {
            $cursos[] = [
                "nombre" => "Curso de " . ucfirst($clave),
                "descripcion" => "Curso interactivo gratuito en Codecademy",
                "url" => $url,
                "plataforma" => "Codecademy"
            ];
            $agregadosCodecademy[] = $clave;
            break;
        }
    }
}

// ===================================
// 🔚 Retornar los cursos en JSON
// ===================================
echo json_encode($cursos);
?>
