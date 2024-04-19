<?php
session_start();

include("../conexion/conexion.php");

if (!isset($_SESSION['curp'])) {
    header("Location: index.php");
    exit;
}

$curp = $_SESSION['curp'];

if (isset($_POST["id_aspirante"]) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo"])) {
    $id_aspirante = $_POST["id_aspirante"];
    $validacion = $_POST["validacion"];
    $id_pago = $_POST["id_pago"];
    $archivo = $_FILES["archivo"];
    $nombre_archivo = $archivo["name"];
    $ruta_temporal = $archivo["tmp_name"];

    // Verificar que el archivo sea un PDF (puedes agregar más verificaciones si es necesario).
    $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
    if (strtolower($extension) !== "pdf") {
        $_SESSION['mensaje_error'] = "<div style='color: red;'>Error: Solo se permiten archivos PDF.</div>";
        // Redireccionar a reinscripcion.php
        header("Location: reinscripcion.php");
        exit;
    } else {
        // Verificar el tamaño del archivo (2MB = 2097152 bytes).
        $tamano_limite = 2097152; // 2MB en bytes
        if ($archivo["size"] > $tamano_limite) {
            $_SESSION['mensaje_error'] = "<div style='color: red;'>Error: El tamaño del archivo debe ser menor a 2MB.</div>";
            // Redireccionar a reinscripcion.php
            header("Location: reinscripcion.php");
            exit;
        }

        if ($validacion == '0') {
            // Leer el contenido del archivo para guardarlo en la base de datos.
            $contenido = file_get_contents($ruta_temporal);
            $id_financiero = "0";
            $monto_pago = "2300";
            $fecha_pago = date("Y-m-d");
            $referencia_pago = "555555";

            // Preparar la consulta SQL para insertar el archivo en la tabla "pagos".
            $query = "INSERT INTO pagos (curp, tipo_pago, validacion, pago, id_usuario, monto_pago, fecha_pago, referencia_pago, comentarios) VALUES (?, 'Reinscripción', 0, ?, ?, ?, ?, ?,'Falta Validar')";
            $stmt = mysqli_prepare($conexion, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", $curp, $contenido, $id_financiero, $monto_pago, $fecha_pago, $referencia_pago);

            // Ejecutar la consulta y guardar el archivo en la base de datos.
            if (mysqli_stmt_execute($stmt)) {
                // Almacenar el mensaje en una variable de sesión.
                $_SESSION['mensaje_subida'] = "<div style='color: #25be43;'>Archivo subido correctamente.</div>";
                // Redireccionar a reinscripcion.php
                header("Location: reinscripcion.php");
                exit;
            } else {
                echo "Error al subir el archivo: " . mysqli_error($conexion);
            }
        }
        // Asegurarse de que $validacion contenga el valor correcto.
        if ($validacion == '3') {
            // Verificar que las variables necesarias estén definidas.
            if (isset($ruta_temporal, $id_pago)) {
                // Leer el contenido del archivo para guardarlo en la base de datos.
                $contenido = file_get_contents($ruta_temporal);
                $fecha_pago = date("Y-m-d");
                $comentarios = "Corregido";

                // Preparar la consulta SQL para insertar el archivo en la tabla "pagos".
                $query = "UPDATE pagos SET validacion = '2', pago = ?, comentarios = ?, fecha_pago = ? WHERE id_pago = ?";
                $stmt = mysqli_prepare($conexion, $query);

                // Verificar que la preparación de la consulta sea exitosa.
                if ($stmt) {
                    // Asociar los parámetros a la consulta preparada.
                    // Asegurarse de que los tipos de datos coincidan con las columnas en la tabla "pagos".
                    mysqli_stmt_bind_param($stmt, "sssi", $contenido, $comentarios, $fecha_pago, $id_pago);

                    // Ejecutar la consulta y guardar el archivo en la base de datos.
                    if (mysqli_stmt_execute($stmt)) {
                        // Almacenar el mensaje en una variable de sesión.
                        $_SESSION['mensaje_subida'] = "<div style='color: #25be43;'>Archivo corregido correctamente.</div>";
                        // Redireccionar a reinscripcion.php
                        header("Location: reinscripcion.php");
                        exit;
                    } else {
                        echo "Error al subir el archivo: " . mysqli_error($conexion);
                    }
                } else {
                    echo "Error al preparar la consulta: " . mysqli_error($conexion);
                }
            } else {
                echo "Variables requeridas no definidas.";
            }
        }
    }
}
?>