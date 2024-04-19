<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: ../../primera_parte/administracion/index.html");
    exit;
}

	include("../conexion/conexion.php");

	function sanitize($data) {
		return htmlspecialchars(stripslashes(trim($data)));
	}

	if (isset($_GET['id_profesor'])) {
		$id_profesor = sanitize($_GET['id_profesor']);
		$query = "SELECT * FROM profesores WHERE id_profesor = $id_profesor";
		$resultado = mysqli_query($conexion, $query);
		if (mysqli_num_rows($resultado) == 1) {
			$row = mysqli_fetch_array($resultado);
			$correo_electronico = $row['correo_electronico'];
			$nombre = $row['nombre'];
			$primer_apellido = $row['primer_apellido'];
			$segundo_apellido = $row['segundo_apellido'];
			$contraseña = $row['contraseña'];
			
		}else{
			header("Location: registro_profesor.php");
    		exit;
		}
	}

if (isset($_POST['modificar'])) {
    $id_profesor = sanitize($_GET['id_profesor']);
    $id_profesor1 = sanitize($_POST['id_profesor']);
    $nombre = sanitize($_POST['nombre']);
    $primer_apellido = sanitize($_POST['primer_apellido']);
    $segundo_apellido = sanitize($_POST['segundo_apellido']);
    $correo_electronico = sanitize($_POST['correo_electronico']);
    $contraseña = sanitize($_POST['contraseña']);

    $query = "SELECT * FROM profesores WHERE id_profesor = $id_profesor";
    $resultado = mysqli_query($conexion, $query);
    $original_values = mysqli_fetch_array($resultado);

    $query_update_profesores = "UPDATE profesores SET ";
    $values_to_update = array();

    if ($correo_electronico !== $original_values['correo_electronico']) {
		$correo_electronico = mysqli_real_escape_string($conexion, $correo_electronico);
		$query = "SELECT correo_electronico FROM profesores WHERE correo_electronico = '$correo_electronico'";
		$result = mysqli_query($conexion, $query);
	
		if ($result) {
			if (mysqli_num_rows($result) > 0) {
				session_start();
				$_SESSION['mensaje'] = "Ya existe un profesor con el correo proporcionado.";
				$_SESSION['message_type'] = 'danger';
				header("Location: registro_profesor.php");
				exit;
			} else {
				$values_to_update[] = "correo_electronico = '$correo_electronico'";
			}
		} else {
			echo "Error al verificar el nombre de usuario existente: " . mysqli_error($conexion);
		}
	}
    if ($nombre !== $original_values['nombre']) {
        $values_to_update[] = "nombre = '$nombre'";
    }
    if ($primer_apellido !== $original_values['primer_apellido']) {
        $values_to_update[] = "primer_apellido = '$primer_apellido'";
    }
    if ($segundo_apellido !== $original_values['segundo_apellido']) {
        $values_to_update[] = "segundo_apellido = '$segundo_apellido'";
    }
    if ($correo_electronico !== $original_values['correo_electronico']) {
        $values_to_update[] = "correo_electronico = '$correo_electronico'";
    }
    if ($contraseña !== $original_values['contraseña']) {
        $encrypted_password = md5($contraseña);
        $values_to_update[] = "contraseña = '$encrypted_password'";
    }
    if (empty($values_to_update)) {
		session_start();
        $_SESSION['mensaje'] = 'No se realizaron cambios.';
		$_SESSION['message_type'] = 'info';
        header("Location: registro_profesor.php");
        exit;
    }

    $query_update_profesores .= implode(", ", $values_to_update);
    $query_update_profesores .= " WHERE id_profesor = $id_profesor";

    mysqli_query($conexion, $query_update_profesores);
	
	session_start();
    $_SESSION['mensaje'] = 'Registro modificado correctamente.';
	$_SESSION['message_type'] = 'success';
    header("Location: registro_profesor.php");
    exit;
}

	
	
?>

<?php include("../includes/header.php"); ?>

<div class="container p-4">
	<div class="row">
		<div class="col-md-4 mx-auto">
			<div class="card card-body">
				<form action="modificar.php?id_profesor=<?php echo $id_profesor; ?>" method="POST">
					<div class="form-group">
						<label for="">Id Usuario:</label>
						<input name="id_profesor" value="<?php echo $id_profesor; ?>" class="form-control" readonly>
						<label for="">Nombre:</label>
						<input type="text" name="nombre" value="<?php echo $nombre; ?>" class="form-control">
						<label for="">Primer Apellido:</label>
						<input type="text" name="primer_apellido" value="<?php echo $primer_apellido; ?>" class="form-control">
						<label for="">Segundo Apellido:</label>
						<input type="text" name="segundo_apellido" value="<?php echo $segundo_apellido; ?>" class="form-control">
						<label for="">Correo Eléctronico</label>
						<input type="text" name="correo_electronico" value="<?php echo $correo_electronico; ?>" class="form-control">
						<label for="">Contraseña:</label>
						<input type="text" name="contraseña" value="<?php echo $contraseña; ?>" class="form-control">
						
					</div>
					<br>
					<button class="form-control btn btn-primary" name="modificar">
						<i class="fas fa-marker"></i>
						Modificar
					</button>
				</form>
				<br>
				<form action="registro_profesor.php">
					<button class="form-control btn-danger" name="regresar">
						<i class="fas fa-times"></i>
						Cancelar Modificación
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include("../includes/footer.php"); ?>
