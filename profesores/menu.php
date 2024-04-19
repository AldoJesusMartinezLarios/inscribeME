<?php
session_start();

if (!isset($_SESSION['correo_electronico'])) {
    header("Location: ../index.php");
    exit;
}

include("../conexion/conexion.php");

$correo_electronico = $_SESSION['correo_electronico'];

include("includes/header.php");
?>

<div class="container p-4">
    <?php
    // Consulta para obtener los datos del profesor
    $query_profesor = "SELECT * FROM profesores WHERE correo_electronico = '$correo_electronico'";
    $resultado_profesor = mysqli_query($conexion, $query_profesor);
    $row_profesor = mysqli_fetch_assoc($resultado_profesor);
    ?>

    <h1 class="mb-4 text-right">Profesor: <?php echo $row_profesor['nombre'] . ' ' . $row_profesor['primer_apellido'] . ' ' . $row_profesor['segundo_apellido']; ?></h1>

    <div class="card card-body">
        <h2 class="mb-4">Materias Asignadas</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Id Materia</th>
                        <th>Nombre Materia</th>
                        <th>Grupo</th>
                        <th>Calificaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener id_profesor basado en el correo electrónico
                    $id_profesor = $row_profesor['id_profesor'];

                    // Consultar las materias asignadas al profesor
                    $query_materias = "SELECT * FROM materias WHERE id_profesor = $id_profesor";
                    $resultado_materias = mysqli_query($conexion, $query_materias);

                    // Mostrar las materias en la tabla
                    while ($row_materia = mysqli_fetch_assoc($resultado_materias)) {
                        echo "<tr>";
                        echo "<td>" . $row_materia['id_materia'] . "</td>";
                        echo "<td>" . $row_materia['nombre'] . "</td>";
                        
                        $id_grupo = $row_materia['id_grupo'];
                        // Consulta para obtener los grupos
                        $query_grupos = "SELECT id_grupo, grupo FROM grupo WHERE id_grupo = $id_grupo";
                        $resultado_grupos = mysqli_query($conexion, $query_grupos);

                        while ($row_grupo = mysqli_fetch_array($resultado_grupos)) {
                            echo "<td>" . $row_grupo['id_grupo'] . " (" . $row_grupo['grupo'] . ")" . "</td>";
                        }
                        ?>
                        <td>
                            <a href="calificaciones.php?id_materia=<?php echo $row_materia['id_materia'] ?>" class=" btn btn-primary">
                            Lista de calificaciones
                            </a>
                        </td>
                        <?php
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("includes/footer.php") ?>
