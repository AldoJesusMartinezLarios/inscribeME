<?php
session_start();

include("../conexion/conexion.php");

if (!isset($_SESSION['correo_electronico'])) {
    header("Location: ../index.php");
    exit;
}

$correo_electronico = $_SESSION['correo_electronico'];

include("includes/header.php");

if(isset($_GET['id_materia'])) {
    $id_materia = $_GET['id_materia'];

    // Obtener el id_grupo de la materia seleccionada
    $query_grupo_materia = "SELECT id_grupo, nombre FROM materias WHERE id_materia = $id_materia";
    $resultado_grupo_materia = mysqli_query($conexion, $query_grupo_materia);

    if (!$resultado_grupo_materia) {
        die("Error al ejecutar la consulta: " . mysqli_error($conexion));
    }

    $row_grupo_materia = mysqli_fetch_assoc($resultado_grupo_materia);
    $id_grupo = $row_grupo_materia['id_grupo'];
    $nombre_materia = $row_grupo_materia['nombre'];

    // Consultar el nombre del grupo
    $query_nombre_grupo = "SELECT grupo FROM grupo WHERE id_grupo = $id_grupo";
    $resultado_nombre_grupo = mysqli_query($conexion, $query_nombre_grupo);

    if (!$resultado_nombre_grupo) {
        die("Error al ejecutar la consulta: " . mysqli_error($conexion));
    }

    $row_nombre_grupo = mysqli_fetch_assoc($resultado_nombre_grupo);
    $nombre_grupo = $row_nombre_grupo['grupo'];

    // Consultar los alumnos que pertenecen al grupo asignado a la materia
    $query_alumnos_grupo = "SELECT * FROM alumnos WHERE grupo = '$nombre_grupo'";
    $resultado_alumnos_grupo = mysqli_query($conexion, $query_alumnos_grupo);

    if (!$resultado_alumnos_grupo) {
        die("Error al ejecutar la consulta: " . mysqli_error($conexion));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $calificaciones = $_POST['calificaciones'];

    foreach ($calificaciones as $id_aspirante => $calificacion) {
        // Verificar si la calificación es válida y no está vacía
        if (!empty($calificacion) && is_numeric($calificacion)) {
            // Obtener las calificaciones anteriores del alumno
            $query_calificaciones_anteriores = "SELECT primer_parcial, segundo_parcial FROM calificaciones WHERE id_materia = $id_materia AND id_aspirante = $id_aspirante";
            $resultado_calificaciones_anteriores = mysqli_query($conexion, $query_calificaciones_anteriores);
            
            if (!$resultado_calificaciones_anteriores) {
                die("Error al obtener calificaciones anteriores: " . mysqli_error($conexion));
            }
            
            $row_calificaciones_anteriores = mysqli_fetch_assoc($resultado_calificaciones_anteriores);
            $primer_parcial = $row_calificaciones_anteriores['primer_parcial'];
            $segundo_parcial = $row_calificaciones_anteriores['segundo_parcial'];
            
            // Calcular el promedio y el estado
            $promedio = ($primer_parcial + $segundo_parcial + $calificacion) / 3;
            $estado = ($promedio >= 7) ? 'Aprobado' : 'Reprobado';
            
            // Actualizar la calificación, el promedio y el estado en la base de datos
            $query_update_calificacion = "UPDATE calificaciones 
                                          SET tercer_parcial = '$calificacion', 
                                              promedio = '$promedio',
                                              estado_parcial = '$estado' 
                                          WHERE id_materia = '$id_materia' AND id_aspirante = '$id_aspirante'";
            $resultado_update_calificacion = mysqli_query($conexion, $query_update_calificacion);
    
            if (!$resultado_update_calificacion) {
                die("Error al actualizar calificación: " . mysqli_error($conexion));
            }
        } else {
            echo "Error: Calificación no válida para el aspirante con ID: $id_aspirante. Se ha omitido la calificación.";
        }
    }
    

    // Redirigir a calificaciones.php después de procesar las calificaciones
    header("Location: calificaciones.php?id_materia=$id_materia");
    exit;
}

// Crear un array para almacenar los IDs de los alumnos que ya tienen una calificación asignada
$alumnos_con_calificacion = array();

// Obtener los IDs de los alumnos con calificaciones asignadas en la materia
$query_calificaciones_asignadas = "SELECT id_aspirante, primer_parcial, segundo_parcial, tercer_parcial FROM calificaciones WHERE id_materia = $id_materia";
$resultado_calificaciones_asignadas = mysqli_query($conexion, $query_calificaciones_asignadas);

if ($resultado_calificaciones_asignadas) {
    while ($row_calificacion = mysqli_fetch_assoc($resultado_calificaciones_asignadas)) {
        $alumnos_con_calificacion[$row_calificacion['id_aspirante']] = array(
            'primer_parcial' => $row_calificacion['primer_parcial'],
            'segundo_parcial' => $row_calificacion['segundo_parcial'],
            'tercer_parcial' => $row_calificacion['tercer_parcial']
        );
    }
}

$query_alumnos_con_calificacion = "SELECT alumnos.*, calificaciones.* 
                                    FROM alumnos 
                                    LEFT JOIN calificaciones 
                                    ON alumnos.id_aspirante = calificaciones.id_aspirante 
                                    WHERE calificaciones.id_materia = $id_materia";
$resultado_alumnos_con_calificacion = mysqli_query($conexion, $query_alumnos_con_calificacion);

if (!$resultado_alumnos_con_calificacion) {
    die("Error al ejecutar la consulta: " . mysqli_error($conexion));
}
?>

<div class="container p-4 d-flex justify-content-end">
    <a href="calificaciones.php?id_materia=<?php echo $id_materia ?>" class=" btn btn-primary">Regresar</a>
</div>

<?php
if(mysqli_num_rows($resultado_alumnos_con_calificacion) > 0){
    ?>
    <div class="container p-4">
        <h1 class="mb-4">Calificaciones de la materia:  <?php echo $nombre_materia; ?></h1>

        <div class="card card-body">
            <form method="POST" action="">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Id Alumno</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Calificación del Primer Parcial</th>
                            <th>Calificación del Segundo Parcial</th>
                            <th>Calificación del Tercer Parcial</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row_alumno_calificado = mysqli_fetch_assoc($resultado_alumnos_con_calificacion)) {
                            echo "<tr>";
                            echo "<td>" . $row_alumno_calificado['id_aspirante'] . "</td>";
                            echo "<td>" . $row_alumno_calificado['nombre'] . "</td>";
                            echo "<td>" . $row_alumno_calificado['primer_apellido'] . " " . $row_alumno_calificado['segundo_apellido'] . "</td>";
                            echo "<td>" . $row_alumno_calificado['correo_electronico'] . "</td>";
                            echo "<td>" . $row_alumno_calificado['telefono'] . "</td>";
                            echo "<td>" . $row_alumno_calificado['primer_parcial'] . "</td>";
                            echo "<td>" . $row_alumno_calificado['segundo_parcial'] . "</td>";
                            echo "<td>";
                            // Verificar si ya hay una calificación del tercer parcial
                            if (isset($alumnos_con_calificacion[$row_alumno_calificado['id_aspirante']]['tercer_parcial'])) {
                                echo "<input type='number' name='calificaciones[" . $row_alumno_calificado['id_aspirante'] . "]' class='form-control' value='" . $alumnos_con_calificacion[$row_alumno_calificado['id_aspirante']]['tercer_parcial'] . "' required>";
                            } else {
                                echo "<input type='number' name='calificaciones[" . $row_alumno_calificado['id_aspirante'] . "]' class='form-control' required>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <hr>
                <div class="container p-4 d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary form-control">Guardar Calificaciones del Tercer Parcial</button>
                </div>
            </form>
        </div>
    </div>
    <hr>
    <?php
}else{
    ?>
    <div class="container p-4">
        <h1 class="mb-4">Ingresar Calificaciones del Tercer Parcial - Materia: <?php echo $nombre_materia; ?></h1>

        <div class="card card-body">
            <form method="POST" action="">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Id Alumno</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Calificación del Tercer Parcial</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Mostrar los alumnos en el formulario para ingresar nuevas calificaciones del tercer parcial
                        while ($row_alumno = mysqli_fetch_assoc($resultado_alumnos_grupo)) {
                            echo "<tr>";
                            echo "<td>" . $row_alumno['id_aspirante'] . "</td>";
                            echo "<td>" . $row_alumno['nombre'] . "</td>";
                            echo "<td>" . $row_alumno['primer_apellido'] . " " . $row_alumno['segundo_apellido'] . "</td>";
                            echo "<td>" . $row_alumno['correo_electronico'] . "</td>";
                            echo "<td>" . $row_alumno['telefono'] . "</td>";
                            ?>
                            <td><input type="number" name="calificaciones[<?php echo $row_alumno['id_aspirante']; ?>]" class="form-control" required></td>
                            <?php
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <hr>
                <div class="container p-4 d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary form-control">Guardar Calificaciones del Tercer Parcial</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>

<?php include("includes/footer.php"); ?>
