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
                        <a class="nav-link text-white" href="menu.php">Regresar</a>
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
                <h1 class="mb-4">Archivos</h1>
                <div class="opciones-formales">
            <?php
            function limpiar($dato)
            {
                return htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
            }

            $sql = "SELECT * FROM archivos WHERE curp = '$curp'";
            $resultado = mysqli_query($conexion, $sql);

            ?>
            <div class="contenido">
                <div class="contenedorTabla">
                    <table>
                        <tr>
                            <th>Tipo de Documento</th>
                            <th>Fecha de Entrega</th>
                            <th>Ver PDF</th>
                        </tr>
                        <?php
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            ?>
                            <tr>
                                <td><?php echo limpiar($fila['tipo_documento']); ?></td>
                                <td><?php echo limpiar($fila['fecha_entrega']); ?></td>
                                <td>
                                    <?php
                                    if (file_exists("../../primera_parte/aspirantes/archivos/documentos/" . strtolower(str_replace(' ', '', $fila['tipo_documento'])) . "/" . $fila['nombre_documento'])) {
                                        ?>
                                        <button class="lins" data-ruta="<?php echo strtolower(str_replace(' ', '', $fila['tipo_documento'])); ?>/<?php echo $fila['nombre_documento']; ?>" onclick="mostrarOcultarPDF(this)">Ver PDF</button>
                                        <?php
                                    } else {
                                        echo "No disponible";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
                <div id="pdf-container" style="display: none;">
                    <iframe id="pdf-viewer" width="600" height="400" style="border: 1px solid white;"></iframe>
                </div>
            </div>
            <script>
                let botonActual = null;

                function mostrarOcultarPDF(boton) {
                    const pdfViewer = document.getElementById("pdf-viewer");
                    const pdfContainer = document.getElementById("pdf-container");
                    const ruta = boton.dataset.ruta;

                    if (botonActual === boton) {
                        pdfViewer.setAttribute("src", "");
                        pdfContainer.style.display = "none";
                        botonActual.textContent = "Ver PDF";
                        botonActual = null;
                    } else {
                        if (botonActual !== null) {
                            pdfViewer.setAttribute("src", "");
                            pdfContainer.style.display = "none";
                            botonActual.textContent = "Ver PDF";
                        }
                        const pdfPath = "../../primera_parte/aspirantes/archivos/documentos/" + ruta + "?timestamp=" + Date.now();
                        pdfViewer.setAttribute("src", pdfPath);
                        pdfContainer.style.display = "block";
                        boton.textContent = "Ocultar PDF";
                        botonActual = boton;
                    }
                }
            </script>
    </div>
        </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
