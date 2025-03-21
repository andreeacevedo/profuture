<?php
session_start();
include 'conexion.php'; // Conectar a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: inicio-sesion.html"); // Redirigir si no ha iniciado sesión
    exit();
}

$usuario = $_SESSION["usuario"]; // Nombre del usuario
$usuario_id = $_SESSION["usuario_id"]; // ID del usuario

// Consultar las materias del usuario
$sql = "SELECT m.nombre, m.ciclo FROM usuario_materias um 
        JOIN materias m ON um.materia_id = m.id 
        WHERE um.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$materias = [];

while ($row = $result->fetch_assoc()) {
    $materias[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/images/book.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Montserrat:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <title>ProFuture</title>
</head>
<body>
    <!--Header - Menu-->
    <header>
        <div class="container">
            <p class="logo">ProFuture</p>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="agregar.php">Agregar</a>
                <a href="#">Pensum</a>
                <a href="#">Perfil</a>
                <span class="user-welcome">Hola, <strong><?php echo $usuario; ?></strong></span>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <!-- Mostrar Materias -->
    <main>
        <div class="container__cover">
            <h2>Mis Materias Seleccionadas</h2>

            <?php if (!empty($materias)): ?>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Ciclo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materias as $materia): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($materia["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($materia["ciclo"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No has seleccionado ninguna materia.</p>
            <?php endif; ?>
        </div>
        <button id="buscarCursos" class="btn__crearCuenta">Buscar Cursos Relacionados</button>
<div id="resultadoCursos"></div>

<!-- 🔹 Nuevo botón para buscar empleos -->
<button id="buscarEmpleos" class="btn__iniciarSesion">Buscar Oportunidades Laborales</button>
<div id="resultadoEmpleos"></div>

<script>
// 🔹 Buscar cursos
document.getElementById("buscarCursos").addEventListener("click", function () {
    fetch("buscar_cursos.php")
        .then(response => response.json())
        .then(data => {
            let resultados = document.getElementById("resultadoCursos");
            resultados.innerHTML = "<h3>Cursos Relacionados:</h3>";
            data.forEach(curso => {
                resultados.innerHTML += `
                    <div>
                        <h4>${curso.nombre}</h4>
                        <p>${curso.descripcion}</p>
                        <a href="${curso.url}" target="_blank">Ver Curso</a>
                    </div>
                `;
            });
        })
        .catch(error => console.error("Error obteniendo cursos:", error));
});

// 🔹 Buscar empleos
document.getElementById("buscarEmpleos").addEventListener("click", function () {
    fetch("buscar_empleos.php")
        .then(response => response.json())
        .then(data => {
            let resultados = document.getElementById("resultadoEmpleos");
            resultados.innerHTML = "<h3>Oportunidades Laborales Relacionadas:</h3>";
            data.forEach(empleo => {
                resultados.innerHTML += `
                    <div>
                        <h4>${empleo.titulo}</h4>
                        <p>${empleo.empresa}</p>
                        <a href="${empleo.url}" target="_blank">Ver Oferta</a>
                    </div>
                `;
            });
        })
        .catch(error => console.error("Error obteniendo empleos:", error));
});
</script>


    </main>
</body>
</html>
