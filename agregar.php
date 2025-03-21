<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: inicio-sesion.html"); // Si no ha iniciado sesión, redirigirlo al login
    exit();
}

$usuario = $_SESSION["usuario"]; // Guardar el nombre del usuario en una variable
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/images/book.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Montserrat:wght@300&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <title>Seleccionar Materias</title>
</head>

<body>
    <!--Header - Menu-->
    <header>
        <div class="container">
            <p class="logo">ProFuture</p>
            <nav>
                <a href="Inicio.php">Inicio</a>
                <a href="agregar.php">Agregar</a>
                <a href="#">Pensum</a>
                <a href="#">Perfil</a>
                <span class="user-welcome">Hola, <strong><?php echo $usuario; ?></strong></span>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container__cover">
            <h2>Selecciona tus materias</h2>

            <!-- Seleccionar Universidad -->
            <select id="universidad" class="select-universidad" required>
                <option value="">Selecciona tu Universidad</option>
            </select>

            <!-- Seleccionar Carrera -->
            <select id="carrera" class="select-universidad" required>
                <option value="">Selecciona tu Carrera</option>
            </select>

            <!-- Seleccionar Materias -->
            <div id="materias-container">
                <h3>Materias Disponibles:</h3>
                <div id="materias-list"></div>
            </div>

            <button class="btn__crearCuenta" id="guardarMaterias">Guardar Selección</button>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const selectUniversidad = document.getElementById("universidad");
            const selectCarrera = document.getElementById("carrera");
            const materiasList = document.getElementById("materias-list");
            const btnGuardar = document.getElementById("guardarMaterias");

            // Cargar universidades
            fetch("obtener_universidades.php")
                .then(response => response.json())
                .then(data => {
                    data.forEach(universidad => {
                        let option = document.createElement("option");
                        option.value = universidad.id;
                        option.textContent = universidad.nombre;
                        selectUniversidad.appendChild(option);
                    });
                });

            // Cargar carreras cuando se seleccione una universidad
            selectUniversidad.addEventListener("change", function () {
                const universidadId = this.value;
                selectCarrera.innerHTML = '<option value="">Selecciona tu Carrera</option>';

                if (universidadId) {
                    fetch(`obtener_carreras.php?universidad_id=${universidadId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(carrera => {
                                let option = document.createElement("option");
                                option.value = carrera.id;
                                option.textContent = carrera.nombre;
                                selectCarrera.appendChild(option);
                            });
                        });
                }
            });

            // Cargar materias cuando se seleccione una carrera
            selectCarrera.addEventListener("change", function () {
                const carreraId = this.value;
                materiasList.innerHTML = ""; // Limpiar materias

                if (carreraId) {
                    fetch(`obtener_materias.php?carrera_id=${carreraId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(materia => {
                                let div = document.createElement("div");
                                div.innerHTML = `
                                    <input type="checkbox" name="materias" value="${materia.id}">
                                    ${materia.nombre} (Ciclo ${materia.ciclo})
                                `;
                                materiasList.appendChild(div);
                            });
                        });
                }
            });

            // Guardar materias seleccionadas
            btnGuardar.addEventListener("click", function () {
                const seleccionadas = [];
                document.querySelectorAll('input[name="materias"]:checked').forEach((checkbox) => {
                    seleccionadas.push(checkbox.value);
                });

                if (seleccionadas.length > 0) {
                    fetch("guardar_materias.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ materias: seleccionadas })
                    })
                        .then(response => response.text())
                        .then(data => alert(data));
                } else {
                    alert("Selecciona al menos una materia.");
                }
            });
        });
    </script>
</body>

</html>
