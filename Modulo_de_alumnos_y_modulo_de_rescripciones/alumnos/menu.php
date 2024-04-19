<?php
session_start();

include("../conexion/conexion.php");

if (!isset($_SESSION['curp'])) {
    header("Location: index.php");
    exit;
}

$curp = $_SESSION['curp'];

// Consulta para obtener la información del alumno
$query_alumno = "SELECT * FROM alumnos WHERE curp = '$curp'";
$resultado_alumno = mysqli_query($conexion, $query_alumno);
$alumno = mysqli_fetch_assoc($resultado_alumno);

// Consulta para obtener la información de las calificaciones del alumno
$query_calificaciones = "SELECT calificaciones.*, materias.nombre AS materia, CONCAT(profesores.nombre, ' ', profesores.primer_apellido, ' ', profesores.segundo_apellido) AS profesor
                         FROM calificaciones
                         INNER JOIN materias ON calificaciones.id_materia = materias.id_materia
                         INNER JOIN profesores ON materias.id_profesor = profesores.id_profesor
                         WHERE calificaciones.id_aspirante = " . $alumno['id_aspirante'];

$resultado_calificaciones = mysqli_query($conexion, $query_calificaciones);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Alumno - InscribeME</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">InscribeME</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto ">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="archivos.php">Archivos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="reinscripcion.php">Reinscripción</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="historial/pdf.php">Generar Historial</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="salir.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
            <div class="card border-dark">
                <div class="bg-light text-center p-3">
                    <img src="../../primera_parte/estilos/imagenes/logooo.png" alt="Logo de la Institución" class="img-fluid" style="max-height: 100px;">
                </div>
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="card-title mb-0">Información del alumno</h4>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Nombre:</strong> <?php echo $alumno['nombre'] . ' ' . $alumno['primer_apellido'] . ' ' . $alumno['segundo_apellido']; ?></p>
                    <p class="card-text"><strong>Correo Electrónico:</strong> <?php echo $alumno['correo_electronico']; ?></p>
                    <p class="card-text"><strong>Grupo:</strong> <?php echo $alumno['grupo']; ?></p>
                    <p class="card-text"><strong>Matrícula:</strong> <?php echo $alumno['id_aspirante']; ?></p>
                </div>
            </div>
            </div>
            <div class="col-md-8">
                <h1 class="mb-4">Calificaciones</h1>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Materia</th>
                            <th scope="col">Profesor</th>
                            <th scope="col">Primer Parcial</th>
                            <th scope="col">Segundo Parcial</th>
                            <th scope="col">Tercer Parcial</th>
                            <th scope="col">Promedio</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = mysqli_fetch_assoc($resultado_calificaciones)) { ?>
                            <tr>
                                <td><?php echo $fila['materia']; ?></td>
                                <td><?php echo $fila['profesor']; ?></td>
                                <td><?php echo $fila['primer_parcial']; ?></td>
                                <td><?php echo $fila['segundo_parcial']; ?></td>
                                <td><?php echo $fila['tercer_parcial']; ?></td>
                                <td><?php echo $fila['promedio']; ?></td>
                                <td><?php echo $fila['estado_parcial']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
