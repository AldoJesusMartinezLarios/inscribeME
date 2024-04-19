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

$id_aspirante = $alumno['id_aspirante']; 

$grupo_actual = $alumno['grupo'];
$nuevo_grupo = substr($grupo_actual, 0, 1) + 1 . substr($grupo_actual, 1);

if (isset($_POST['nuevo_grupo'])) {
    // Consultar si el nuevo grupo existe en la tabla "grupo"
    $query_verificar_grupo = "SELECT * FROM grupo WHERE grupo = '$nuevo_grupo'";
    $resultado_verificar_grupo = mysqli_query($conexion, $query_verificar_grupo);

    if (mysqli_num_rows($resultado_verificar_grupo) > 0) {
        // Actualizar el grupo del alumno en la base de datos
        $query_actualizar_grupo = "UPDATE alumnos SET grupo = '$nuevo_grupo' WHERE id_aspirante = $id_aspirante";
        $resultado_actualizar_grupo = mysqli_query($conexion, $query_actualizar_grupo);

        if ($resultado_actualizar_grupo) {
            // Insertar registro en la tabla "reinscripcion"
            $query_insertar_reinscripcion = "INSERT INTO reinscripcion (id_aspirante, status) VALUES ($id_aspirante, 1)";
            $resultado_insertar_reinscripcion = mysqli_query($conexion, $query_insertar_reinscripcion);

            if ($resultado_insertar_reinscripcion) {
                // Mensaje de éxito
                $_SESSION['mensaje_exito'] = "El grupo del alumno ha sido actualizado correctamente y se ha registrado la reinscripción.";
            } else {
                // Mensaje de error
                $_SESSION['mensaje_error'] = "Hubo un error al registrar la reinscripción.";
            }
        } else {
            // Mensaje de error
            $_SESSION['mensaje_error'] = "Hubo un error al actualizar el grupo del alumno.";
        }
    } else {
        // Mensaje de error
        $_SESSION['mensaje_error'] = "El nuevo grupo no existe en la base de datos.";
    }

    // Redirigir para evitar reenvío de formularios
    header("Location: reinscripcion.php");
    exit;
}
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
                <h1 class="mb-4">Proseso de Reinscripción</h1>
                
                <?php
                if (isset($_SESSION['mensaje_error'])) {
                    ?>
                    <div class="alert alert-info" role="alert">
                        <?php
                        echo $_SESSION['mensaje_error'];
                        ?>
                    </div>
                <?php
                    unset($_SESSION['mensaje_error']);
                }
                            $tipo_pago = "Reinscripción"; 
                            $query = "SELECT id_pago, validacion, comentarios FROM pagos WHERE curp = '$curp' AND tipo_pago = '$tipo_pago'";
                            $result = mysqli_query($conexion, $query);

                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $validacion = $row['validacion'];
                                $comentarios = $row['comentarios'];
                                $id_pago = $row['id_pago'];

                                if ($validacion == 1) {
                                    $query_verificar_reinscripcion = "SELECT * FROM reinscripcion WHERE id_aspirante = $id_aspirante";
                                    $resultado_verificar_reinscripcion = mysqli_query($conexion, $query_verificar_reinscripcion);

                                    if (mysqli_num_rows($resultado_verificar_reinscripcion) > 0) {
                                        // Ya tiene una reinscripción, mostrar solo el mensaje de éxito y el grupo actual
                                        echo '<div class="alert alert-success" role="alert">Reinscrito con éxito. Grupo actual: ' . $alumno['grupo'] . '</div>';
                                    } else {
                                        // No tiene una reinscripción, mostrar el formulario
                                ?>
                                        <div class="alert alert-success" role="alert">
                                            Pago de reinscripción realizado y verificado.
                                        </div>
                                        <div class="card shadow">
                                            <div class="card-body">
                                                <p class="text-muted">Actual grupo: <?php echo $alumno['grupo']; ?> </p>
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="id_aspirante" value="<?php echo $id_aspirante; ?>">
                                                    <label for="archivo" class="font-weight-bold">Grupo al que se te va a reinscribir.</label>
                                                    <input type="text" class="form-group" value="<?php echo $nuevo_grupo; ?>" disabled>
                                                    <input type="hidden" name="nuevo_grupo" value="<?php echo $nuevo_grupo; ?>">
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-primary form-control">Aceptar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                <?php
                                    }
                                ?>


                                <?php
                                }elseif($validacion == 0){
                                    echo '<div class="alert alert-info" role="alert">
                                            <strong>Pago de reinscripción realizado.</strong> El pago está siendo verificado.
                                        </div>';
                                }elseif($validacion == 3){
                                    ?>                 
                                    <div class="card shadow">
                                        <div class="card-body">
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Tu pago no es correcto - Motivo: </strong><?php echo $comentarios;?>
                                        </div>
                                        <form id="myForm" action="pagos.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id_pago" value="<?php echo $id_pago; ?>">
                                        <input type="hidden" name="id_aspirante" value="<?php echo $id_aspirante; ?>">
                                        <input type="hidden" name="validacion" value="<?php echo $validacion = 3; ?>">
                                            <div class="form-group">
                                            <label for="archivo" class="font-weight-bold">Seleccione un archivo PDF:</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="archivo" name="archivo" accept=".pdf">
                                                <label class="custom-file-label" for="archivo">Seleccionar archivo</label>
                                            </div>
                                            </div>
                                            <div class="text-center">
                                            <button type="submit" class="btn btn-primary form-control">Subir</button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                <?php
                                }elseif($validacion == 2){
                                    echo '<div class="alert alert-info" role="alert">
                                            <strong>Pago de reinscripción corregido.</strong> El pago esta siendo verificado.
                                        </div>';
                                }
                            }else{
                                ?> 
                                <div class="card shadow">
                                    <div class="card-body">
                                    <p class="text-muted">En la referencia: 958345091364509</p>
                                    <form id="myForm" action="pagos.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id_aspirante" value="<?php echo $id_aspirante; ?>">
                                        <input type="hidden" name="validacion" value="<?php echo $validacion = 0; ?>">
                                        <div class="form-group">
                                        <label for="archivo" class="font-weight-bold">Seleccione un archivo PDF:</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="archivo" name="archivo" accept=".pdf">
                                            <label class="custom-file-label" for="archivo">Seleccionar archivo</label>
                                        </div>
                                        </div>
                                        <div class="text-center">
                                        <button type="submit" class="btn btn-primary form-control">Subir</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                                <?php
                            }
                ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
