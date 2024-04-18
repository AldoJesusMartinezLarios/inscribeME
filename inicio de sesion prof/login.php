<?php
include 'conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo_electronico = $_POST['correo_electronico'];
    $contraseña = md5($_POST['contraseña']);
    
    // Consultar en la tabla padres
    $sql_padres = "SELECT * FROM padres WHERE correo_electronico ='$correo_electronico' AND contraseña='$contraseña'";
    $resultado_padres = mysqli_query($conexion, $sql_padres);
    
    // Consultar en la tabla profesores
    $sql_profesores = "SELECT * FROM profesores WHERE correo_electronico ='$correo_electronico' AND contraseña='$contraseña'";
    $resultado_profesores = mysqli_query($conexion, $sql_profesores);

    if ($resultado_padres && mysqli_num_rows($resultado_padres) > 0) {
        session_start();
        $_SESSION['correo_electronico'] = $correo_electronico;
        header("Location: padres/menu.php");
        exit;
    } elseif ($resultado_profesores && mysqli_num_rows($resultado_profesores) > 0) {
        $fila = mysqli_fetch_assoc($resultado_profesores);
        $acceso = $fila['acceso'];
        
        if ($acceso == 1) {
            session_start();
            $_SESSION['correo_electronico'] = $correo_electronico;
            header("Location: profesores/menu.php");
            exit;
        } else {
            session_start();
            $_SESSION['error_message'] = "Por el momento no tienes acceso";
            header("Location: index.php");
            exit;
        }
    } else {
        session_start();
        $_SESSION['error_message'] = "Los datos ingresados no son válidos, verifícalos.";
        header("Location: index.php");
        exit;
    }
}
?>
