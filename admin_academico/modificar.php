<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: ../../primera_parte/administracion/index.html");
    exit;
}

include("../conexion/conexion.php");

$nombre_usuario = $_SESSION['nombre_usuario'];

include("includes/header.php");

function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_GET['id_materia'])) {
    $id_materia = $_GET['id_materia'];
    $query = "SELECT * FROM materias WHERE id_materia = $id_materia";
    $resultado = mysqli_query($conexion, $query);
    if (mysqli_num_rows($resultado) == 1) {
        $row = mysqli_fetch_array($resultado);
        $id_materia = $row['id_materia'];
        $nombre = $row['nombre'];
        $id_profesor = $row['id_profesor'];
        $id_grupo = $row['id_grupo'];
    } else {
        header("Location: menu.php");
        exit;
    }
}

if (isset($_POST['modificar'])) {
    $id_materia = $_POST['id_materia'];
    $nombre = $_POST['nombre'];
    $id_profesor = $_POST['id_profesor'];
    $id_grupo = $_POST['id_grupo'];

    $query = "UPDATE materias SET nombre = '$nombre', id_profesor = '$id_profesor', id_grupo = '$id_grupo' WHERE id_materia = $id_materia";
    mysqli_query($conexion, $query);

    session_start();
    $_SESSION['mensaje'] = 'Datos de la materia modificados correctamente.';
    $_SESSION['message_type'] = 'success';
    header("Location: menu.php");
    exit;
}
?>

<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <h4>Modificar datos de la materia</h4>
                <form action="modificar.php" method="POST">
                    <input type="hidden" name="id_materia" value="<?php echo $id_materia; ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" required>
                        <label for="id_profesor">Profesor Asignado:</label>
                        <select name="id_profesor" class="form-control" required>
                            <?php
                            // Consulta para obtener todos los profesores
                            $query_profesores = "SELECT * FROM profesores";
                            $resultado_profesores = mysqli_query($conexion, $query_profesores);

                            $profesor_actual_mostrado = false; // Flag para verificar si el profesor actual ya se ha mostrado

                            while ($row_profesor = mysqli_fetch_array($resultado_profesores)) {
                                if ($row_profesor['id_profesor'] == $id_profesor) {
                                    // Mostrar el profesor actualmente asignado primero en la lista
                                    echo "<option value='" . $row_profesor['id_profesor'] . "' selected>" . $row_profesor['nombre']." ".$row_profesor['primer_apellido']." ". $row_profesor['segundo_apellido']. "</option>";
                                    $profesor_actual_mostrado = true;
                                } else {
                                    // Mostrar los demás profesores
                                    echo "<option value='" . $row_profesor['id_profesor'] . "'>" . $row_profesor['nombre']." ".$row_profesor['primer_apellido']." ". $row_profesor['segundo_apellido']. "</option>";
                                }
                            }

                            // Si el profesor actual no se ha mostrado en la lista, añadirlo al final
                            if (!$profesor_actual_mostrado && isset($id_profesor)) {
                                $query_profesor_actual = "SELECT * FROM profesores WHERE id_profesor = $id_profesor";
                                $resultado_profesor_actual = mysqli_query($conexion, $query_profesor_actual);
                                $row_profesor_actual = mysqli_fetch_array($resultado_profesor_actual);
                                echo "<option value='" . $row_profesor_actual['id_profesor'] . "' selected>" . $row_profesor_actual['nombre']." ".$row_profesor_actual['primer_apellido']." ". $row_profesor_actual['segundo_apellido']. "</option>";
                            }
                            ?>
                        </select>

                        <label for="id_grupo">Grupo Asignado:</label>
                        <select name="id_grupo" class="form-control" required>
                            <?php
                            // Consulta para obtener todos los grupos
                            $query_grupos = "SELECT id_grupo, grupo FROM grupo";
                            $resultado_grupos = mysqli_query($conexion, $query_grupos);

                            $grupo_actual_mostrado = false; // Flag para verificar si el grupo actual ya se ha mostrado

                            while ($row_grupo = mysqli_fetch_array($resultado_grupos)) {
                                if ($row_grupo['id_grupo'] == $id_grupo) {
                                    // Mostrar el grupo actualmente asignado primero en la lista
                                    echo "<option value='" . $row_grupo['id_grupo'] . "' selected>" . $row_grupo['id_grupo']." (".$row_grupo['grupo'].")" . "</option>";
                                    $grupo_actual_mostrado = true;
                                } else {
                                    // Mostrar los demás grupos
                                    echo "<option value='" . $row_grupo['id_grupo'] . "'>" . $row_grupo['id_grupo']." (".$row_grupo['grupo'].")" . "</option>";
                                }
                            }

                            // Si el grupo actual no se ha mostrado en la lista, añadirlo al final
                            if (!$grupo_actual_mostrado && isset($id_grupo)) {
                                $query_grupo_actual = "SELECT id_grupo, grupo FROM grupo WHERE id_grupo = $id_grupo";
                                $resultado_grupo_actual = mysqli_query($conexion, $query_grupo_actual);
                                $row_grupo_actual = mysqli_fetch_array($resultado_grupo_actual);
                                echo "<option value='" . $row_grupo_actual['id_grupo'] . "' selected>" . $row_grupo_actual['id_grupo']." (".$row_grupo_actual['grupo'].")" . "</option>";
                            }
                            ?>
                        </select>

                    </div><br>
                    <button class="form-control btn btn-primary" name="modificar">
                        <i class="fas fa-edit"></i>
                        Modificar
                    </button>
                </form><br>
                <form action="menu.php">
                    <button class="form-control btn btn-secondary" name="cancelar">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
