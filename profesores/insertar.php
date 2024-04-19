<?php

include("../conexion/conexion.php");
$correo_electronico = $_POST['correo_electronico'];
$nombre = $_POST['nombre'];
$primer_apellido = $_POST['primer_apellido'];
$segundo_apellido = $_POST['segundo_apellido'];
$contraseña = md5($_POST['contraseña']);
$acceso = $_POST['acceso'];

$consulta = "SELECT * FROM profesores WHERE correo_electronico = '$correo_electronico'";
$resultado_consulta = mysqli_query($conexion, $consulta);

if (mysqli_num_rows($resultado_consulta) > 0) {
    session_start();
    $_SESSION['mensaje'] = 'El correo del usuario ya está en uso.';
    $_SESSION['message_type'] = 'danger';
    header("Location: registro_profesor.php");
    exit;
} else {
    $query = "INSERT INTO profesores (nombre, primer_apellido, segundo_apellido, correo_electronico, contraseña, acceso)
              VALUES ('$nombre', '$primer_apellido', '$segundo_apellido', '$correo_electronico', '$contraseña', '$acceso')";
    $resultado = mysqli_query($conexion, $query);

    session_start();
    $_SESSION['mensaje'] = 'Profesor guardado correctamente.';
    $_SESSION['message_type'] = 'success';
    header("Location: registro_profesor.php");
    exit;
}

?>
