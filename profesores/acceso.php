<?php
session_start(); // Iniciar la sesión

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
			$nombre = $row['nombre'];
			$primer_apellido = $row['primer_apellido'];
			$segundo_apellido = $row['segundo_apellido'];
			$correo_electronico	 = $row['correo_electronico'];
			$acceso = $row['acceso'];
		}else{
			header("Location: registro_profesor.php");
    		exit;
		}
	}

	if (isset($_POST['modificar'])) {
		$nuevo_acceso = $acceso === '1' ? '0' : '1';
		$update_query = "UPDATE profesores SET acceso = $nuevo_acceso WHERE id_profesor = $id_profesor";
		mysqli_query($conexion, $update_query);

        session_start();
        if ($nuevo_acceso === '0') {
            $_SESSION['mensaje'] = 'Se quitó el acceso.';
            $_SESSION['message_type'] = 'warning';
        } else {
            $_SESSION['mensaje'] = 'Se concedió el acceso.';
            $_SESSION['message_type'] = 'success';
        }

        header("Location: registro_profesor.php");
        exit;
	}

	// Definir el título del formulario según el valor actual de "acceso"
	$form_titulo = $acceso === '1' ? '¿Quieres quitarle el acceso?' : '¿Quieres darle acceso?';
	$btn_texto = $acceso === '1' ? 'Quitar Acceso' : 'Dar Acceso';
?>

<?php include("../includes/header.php"); ?>

<div class="container p-4">
	<div class="row">
		<div class="col-md-4 mx-auto">
			<div class="card card-body">
            <h3> <?php echo $form_titulo; ?></h3>
            <hr>
				<form action="acceso.php?id_profesor=<?php echo $id_profesor; ?>" method="POST">
					<div class="form-group">
						<label for="">Id Profesor:</label>
						<input name="id_profesor" value="<?php echo $id_profesor; ?>" class="form-control" readonly>
						<label for="">Profesor:</label>
						<input name="nombre" value="<?php echo $nombre ." ". $primer_apellido ." ". $segundo_apellido ?>" class="form-control" readonly>
						<label for="">Correo:</label>
						<input name="correo_electronico	" value="<?php echo $correo_electronico	; ?>" class="form-control" readonly>
					</div>
					<br>
					<button class="form-control btn btn-primary" name="modificar">
						<?php echo $btn_texto; ?>
					</button>
				</form>
				<br>
				<form action="registro_profesor.php">
					<button class="form-control btn-danger" name="regresar">
						Cancelar
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include("../includes/footer.php"); ?>
