<?php

include("../conexion/conexion.php");
$nombre = $_POST['nombre'];
$id_profesor = $_POST['id_profesor'];
$id_grupo = $_POST['id_grupo'];

// Verificar si ya existe una materia con el mismo nombre en el mismo grupo
$consulta_materia = "SELECT * FROM materias WHERE nombre = '$nombre' AND id_grupo = '$id_grupo'";
$resultado_consulta_materia = mysqli_query($conexion, $consulta_materia);

if (mysqli_num_rows($resultado_consulta_materia) > 0) {
    session_start();
    $_SESSION['mensaje'] = 'Ya existe una materia con el mismo nombre en este grupo.';
    $_SESSION['message_type'] = 'danger';
    header("Location: menu.php");
    exit;
} else {
    $query = "INSERT INTO materias (nombre, id_grupo, id_profesor)
              VALUES ('$nombre', '$id_grupo','$id_profesor')";
    $resultado = mysqli_query($conexion, $query);

    session_start();
    $_SESSION['mensaje'] = 'Materia guardada correctamente.';
    $_SESSION['message_type'] = 'success';
    header("Location: menu.php");
    exit;
}
?>
