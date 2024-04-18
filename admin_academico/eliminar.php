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

if (isset($_POST['eliminar'])) {
    $id_materia = $_POST['id_materia']; // Corregido aquí

    $query = "DELETE FROM materias WHERE id_materia = $id_materia";
    mysqli_query($conexion, $query);

    session_start();
    $_SESSION['mensaje'] = 'Materia eliminada correctamente.';
    $_SESSION['message_type'] = 'danger';
    header("Location: menu.php");
    exit;
}

?>

<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <h4>¿Seguro que desea eliminar esta materia?</h4>
                <form action="eliminar.php?id_materia=<?php echo $_GET['id_materia']; ?>" method="POST">
                    <div class="form-group">
                        <label for="">Id materia:</label>
                        <input name="id_materia" value="<?php echo $id_materia; ?>" class="form-control" readonly>
                        <label for="">Nombre:</label>
                        <input name="nombre" value="<?php echo $nombre ?>" class="form-control" readonly>
                        <label for="">Profesor Asignado:</label>
                        <input type="text" value="<?php
                        // Consulta para obtener los profesores
                        $query_profesores = "SELECT * FROM profesores WHERE id_profesor = $id_profesor";
                        $resultado_profesores = mysqli_query($conexion, $query_profesores);

                        while ($row_profesor = mysqli_fetch_array($resultado_profesores)) {
                            echo $row_profesor['id_profesor'] . " (" . $row_profesor['nombre'] . " " . $row_profesor['primer_apellido'] . " " . $row_profesor['segundo_apellido'] . ")";
                        }
                        ?>" class="form-control" readonly>
                        <label for="">Grupo Asignado:</label>
                        <input value="<?php
                        // Consulta para obtener los grupos
                        $query_grupos = "SELECT id_grupo, grupo FROM grupo WHERE id_grupo = $id_grupo";
                        $resultado_grupos = mysqli_query($conexion, $query_grupos);

                        while ($row_grupo = mysqli_fetch_array($resultado_grupos)) {
                            echo $row_grupo['id_grupo'] . " (" . $row_grupo['grupo'] . ")";
                        }
                        ?>" class="form-control" readonly>
                    </div><br>
                    <button class="form-control btn btn-danger" name="eliminar">
                        <i class="far fa-trash-alt"></i>
                        Eliminar
                    </button>
                </form><br>
                <form action="menu.php">
                    <button class="form-control btn btn-success" name="cancelar">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<?php include("../includes/footer.php"); ?>
