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



?>

<div class="container p-4">
    <div class="row">
        <div class="col-md-4">
            <?php
            if (isset($_SESSION['mensaje'])) {
                $message_type = $_SESSION['message_type'];
                $mensaje = $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
                unset($_SESSION['message_type']);
            ?>
            <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show" role="alert">
                <?= $mensaje ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>

            <div class="card card-body">
                <form action="insertar.php" method="POST">
                    <h2>Agregar Materias</h2>
                    <div class="form-group">
                        <label for="">Nombre:</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                        <label for="">Profesor:</label>
                        <select name="id_profesor" class="form-control" required>
                        <option disabled selected="profesor">Asigna Profesor</option>
                            <?php
                            // Consulta para obtener los profesores
                            $query_profesores = "SELECT * FROM profesores";
                            $resultado_profesores = mysqli_query($conexion, $query_profesores);

                            while ($row_profesor = mysqli_fetch_array($resultado_profesores)) {
                                echo "<option value='" . $row_profesor['id_profesor'] . "'>" . $row_profesor['id_profesor']." (". $row_profesor['nombre']." ".$row_profesor['primer_apellido']." ". $row_profesor['segundo_apellido']. ")". "</option>";
                            }
                            ?>
                        </select>
                        <label for="">Grupo:</label>
                        <select name="id_grupo" class="form-control" required>
                        <option disabled selected="grupo">Asigna Grupo</option>
                            <?php
                            // Consulta para obtener los grupos
                            $query_grupos = "SELECT id_grupo, grupo FROM grupo";
                            $resultado_grupos = mysqli_query($conexion, $query_grupos);

                            while ($row_grupo = mysqli_fetch_array($resultado_grupos)) {
                                echo "<option value='" . $row_grupo['id_grupo'] . "'>" . $row_grupo['id_grupo']." (".$row_grupo['grupo'].")" . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <br>
                    <button class="form-control btn btn-success btn-block">
                        <span class="fas fa-save"></span>
                        Guardar Registro
                    </button>
                </form>
            </div>
            <hr>
        </div>
        <div class="col-md-8">
            <h1>Materias</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre de la materia</th>
                        <th>Profesor</th>
                        <th>Grupo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM materias";
                    $resultado_registro = mysqli_query($conexion, $query);
                    while ($row = mysqli_fetch_array($resultado_registro)) {
                        $id_materia = $row['id_materia'];
                        $nombre = $row['nombre'];
                        $id_profesor = $row['id_profesor'];
                        $id_grupo = $row['id_grupo'];
                        ?>
                        <tr>
                            <td><?php echo $row['id_materia']?></td>
                            <td><?php echo $row['nombre'] ?></td>
                            <td>
                                <?php
                                    // Consulta para obtener los profesores
                                    $query_profesores = "SELECT * FROM profesores WHERE id_profesor = $id_profesor";
                                    $resultado_profesores = mysqli_query($conexion, $query_profesores);

                                    while ($row_profesor = mysqli_fetch_array($resultado_profesores)) {
                                        echo $row_profesor['id_profesor']." (". $row_profesor['nombre']." ".$row_profesor['primer_apellido']." ". $row_profesor['segundo_apellido']. ")";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    // Consulta para obtener los grupos
                                    $query_grupos = "SELECT id_grupo, grupo FROM grupo WHERE id_grupo = $id_grupo";
                                    $resultado_grupos = mysqli_query($conexion, $query_grupos);

                                    while ($row_grupo = mysqli_fetch_array($resultado_grupos)) {
                                        echo "<option value='" . $row_grupo['id_grupo'] . "'>" . $row_grupo['id_grupo']." (".$row_grupo['grupo'].")" . "</option>";
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="modificar.php?id_materia=<?php echo $row['id_materia'] ?>" class="btn btn-primary">
                                    <i class="fas fa-marker"></i>
                                </a>
                                <a href="eliminar.php?id_materia=<?php echo $row['id_materia'] ?>" class="btn btn-danger">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <hr>
        </div>
    </div>
</div>

<?php include("includes/footer.php") ?>