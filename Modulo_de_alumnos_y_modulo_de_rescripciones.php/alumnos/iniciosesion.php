<?php
include '../conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $curp = $_POST['curp'];
    $contraseña = md5($_POST['contraseña']);
    $sql = "SELECT * FROM alumnos WHERE curp = ? AND contraseña = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $curp, $contraseña);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filas = mysqli_num_rows($resultado);
    
    if ($filas > 0) {
        session_start();
        $_SESSION['curp'] = $curp;
        header("Location: menu.php");
        exit;
    } else {
        session_start();
        $_SESSION['error_message'] = "Los datos ingresados no son válidos, verifícalos.";
        header("Location: index.php");
        exit;
    }
}
?>
