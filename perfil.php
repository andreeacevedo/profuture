<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: inicio-sesion.html");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

$sql = "SELECT u.nombre, u.email, u.universidad_id, u.carrera_id,
               un.nombre AS universidad_nombre,
               cg.nombre AS carrera_nombre
        FROM usuarios u
        LEFT JOIN universidades un ON u.universidad_id = un.id
        LEFT JOIN universidad_carrera uc ON u.carrera_id = uc.id
        LEFT JOIN carreras_generales cg ON uc.carrera_general_id = cg.id
        WHERE u.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Listado para selects del formulario
$universidades = $conn->query("SELECT id, nombre FROM universidades");

$carreras = [];
if ($usuario['universidad_id']) {
    $carrera_sql = "SELECT uc.id, cg.nombre 
                    FROM universidad_carrera uc
                    INNER JOIN carreras_generales cg ON uc.carrera_general_id = cg.id
                    WHERE uc.universidad_id = " . intval($usuario['universidad_id']);
    $carreras = $conn->query($carrera_sql);
}
?>

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/images/book.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Montserrat:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <title>Perfil</title>
</head>
<body>
<header>
    <div class="container">
        <p class="logo">ProFuture</p>
        <nav>
            <a href="Inicio.php">Inicio</a>
            <a href="agregar.php">Agregar</a>
            <a href="perfil.php">Perfil</a>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        </nav>
    </div>
</header>

<main class="container__cover">
    <h2>Mi Perfil</h2>

    <div id="vistaPerfil">
        <p><strong>Nombre:</strong> <?php echo $usuario['nombre']; ?></p>
        <p><strong>Email:</strong> <?php echo $usuario['email']; ?></p>
        <p><strong>Universidad:</strong> <?php echo $usuario['universidad_nombre']; ?></p>
        <p><strong>Carrera:</strong> <?php echo $usuario['carrera_nombre']; ?></p>
        <button id="editarPerfilBtn" class="btn__crearCuenta">Editar Información</button>
    </div>

    <form id="formEditarPerfil" action="actualizar_perfil.php" method="POST" style="display:none;">
        <label>Nombre:
            <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
        </label>
        <label>Email:
            <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
        </label>
        <label>Universidad:
            <select name="universidad" required>
                <option value="">Selecciona Universidad</option>
                <?php while($uni = $universidades->fetch_assoc()): ?>
                    <option value="<?php echo $uni['id']; ?>" <?php if($uni['id'] == $usuario['universidad_id']) echo 'selected'; ?>>
                        <?php echo $uni['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label>
        <label>Carrera:
            <select name="carrera" required>
                <option value="">Selecciona Carrera</option>
                <?php if ($carreras): while($c = $carreras->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>" <?php if($c['id'] == $usuario['carrera_id']) echo 'selected'; ?>>
                        <?php echo $c['nombre']; ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>
        </label>
        <button type="submit" class="btn__crearCuenta">Guardar Cambios</button>
        <button type="button" class="btn__cancelar" id="cancelarEdicion">Cancelar</button>
    </form>
</main>

<script>
document.getElementById("editarPerfilBtn").addEventListener("click", function() {
    document.getElementById("vistaPerfil").style.display = "none";
    document.getElementById("formEditarPerfil").style.display = "block";
});

document.getElementById("cancelarEdicion").addEventListener("click", function() {
    document.getElementById("formEditarPerfil").style.display = "none";
    document.getElementById("vistaPerfil").style.display = "block";
});
</script>
</body>
</html>