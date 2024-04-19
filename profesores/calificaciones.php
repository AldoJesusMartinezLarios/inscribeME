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
    $id_materia = (int)$_GET['id_materia'];

    // Obtener información de la materia
    $query_materia = "SELECT materias.nombre AS nombre_materia, grupo.grupo AS nombre_grupo
                      FROM materias
                      INNER JOIN grupo ON materias.id_grupo = grupo.id_grupo
                      WHERE materias.id_materia = $id_materia";
    $resultado_materia = mysqli_query($conexion, $query_materia);

    if (!$resultado_materia) {
        die("Error al ejecutar la consulta: " . mysqli_error($conexion));
    }

    $row_materia = mysqli_fetch_assoc($resultado_materia);
    $nombre_materia = $row_materia['nombre_materia'];
    $nombre_grupo = $row_materia['nombre_grupo'];

    // Obtener calificaciones de los alumnos para la materia
    $query_calificaciones = "SELECT calificaciones.*, alumnos.nombre, alumnos.primer_apellido, alumnos.segundo_apellido
                             FROM calificaciones
                             INNER JOIN alumnos ON calificaciones.id_aspirante = alumnos.id_aspirante
                             WHERE calificaciones.id_materia = $id_materia";
    $resultado_calificaciones = mysqli_query($conexion, $query_calificaciones);

    if (!$resultado_calificaciones) {
        die("Error al ejecutar la consulta: " . mysqli_error($conexion));
    }
}
?>

<div class="container p-4 d-flex justify-content-end">
    <a href="menu.php" class="btn btn-primary">Regresar</a>
</div>

<div class="container p-4">
    <h1 class="mb-4">Lista de Alumnos del Grupo <?php echo $nombre_grupo . " Materia: ".$nombre_materia; ?></h1><?php
// Suponiendo que tengas una conexión a tu base de datos establecida

// Consulta para verificar si existen calificaciones para el primer parcial
$query_primer_parcial = "SELECT COUNT(*) AS count FROM calificaciones WHERE id_materia = $id_materia AND primer_parcial IS NOT NULL";
$result_primer_parcial = mysqli_query($conexion, $query_primer_parcial);
$row_primer_parcial = mysqli_fetch_assoc($result_primer_parcial);
$count_primer_parcial = $row_primer_parcial['count'];

// Consulta para verificar si existen calificaciones para el segundo parcial
$query_segundo_parcial = "SELECT COUNT(*) AS count FROM calificaciones WHERE id_materia = $id_materia AND segundo_parcial IS NOT NULL";
$result_segundo_parcial = mysqli_query($conexion, $query_segundo_parcial);
$row_segundo_parcial = mysqli_fetch_assoc($result_segundo_parcial);
$count_segundo_parcial = $row_segundo_parcial['count'];

// Consulta para verificar si existen calificaciones para el tercer parcial
$query_tercer_parcial = "SELECT COUNT(*) AS count FROM calificaciones WHERE id_materia = $id_materia AND tercer_parcial IS NOT NULL";
$result_tercer_parcial = mysqli_query($conexion, $query_tercer_parcial);
$row_tercer_parcial = mysqli_fetch_assoc($result_tercer_parcial);
$count_tercer_parcial = $row_tercer_parcial['count'];

// Mostrar los botones en función de si existen calificaciones para cada parcial
if ($count_primer_parcial == 0) {
    echo '<a href="primer_parcial.php?id_materia=' . $id_materia . '" class=" btn btn-primary"> Primer parcial</a>';
}

if ($count_primer_parcial > 0 && $count_segundo_parcial == 0) {
    echo '<a href="segundo_parcial.php?id_materia=' . $id_materia . '" class=" btn btn-primary"> Segundo parcial</a>';
}

if ($count_segundo_parcial > 0 && $count_tercer_parcial == 0) {
    echo '<a href="tercer_parcial.php?id_materia=' . $id_materia . '" class=" btn btn-primary"> Tercer parcial</a>';
}
?>

    <div class="card card-body">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Alumno</th>
                    <th>Primer Parcial</th>
                    <th>Segundo Parcial</th>
                    <th>Tercer Parcial</th>
                    <th>Estado Parcial</th>
                    <th>Promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row_calificacion = mysqli_fetch_assoc($resultado_calificaciones)) {
                    echo "<tr";
                    // Aplica la clase de acuerdo al estado de parcial
                    if ($row_calificacion['estado_parcial'] == 0) {
                        echo " class='reprobado'";
                    } else {
                        echo " class='aprobado'";
                    }
                    echo ">";
                    echo "<td>" . $row_calificacion['id_calificacion'] . "</td>";
                    echo "<td>" . $row_calificacion['nombre'] . " " . $row_calificacion['primer_apellido'] . " " . $row_calificacion['segundo_apellido'] . "</td>";
                    echo "<td>" . $row_calificacion['primer_parcial'] . "</td>";
                    echo "<td>" . $row_calificacion['segundo_parcial'] . "</td>";
                    echo "<td>" . $row_calificacion['tercer_parcial'] . "</td>";
                    echo "<td>" . $row_calificacion['estado_parcial'] . "</td>";
                    echo "<td>" . $row_calificacion['promedio'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
