<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: ../../primera_parte/administracion/index.html");
    exit;
}
	include("../conexion/conexion.php");

	if(isset($_GET['id_profesor'])) {
		$id_profesor = $_GET['id_profesor'];
		$query = "SELECT * FROM profesores WHERE id_profesor = $id_profesor";
		$resultado = mysqli_query($conexion, $query);
		if (mysqli_num_rows($resultado) == 1) {
			$row = mysqli_fetch_array($resultado);
			$id_profesor = $row['id_profesor'];
			$nombre = $row['nombre'];
			$primer_apellido = $row['primer_apellido'];
			$segundo_apellido = $row['segundo_apellido'];
			$correo_electronico	 = $row['correo_electronico'];

		}else{
			header("Location: registro_profesor.php");
    		exit;
		}
	}


	if (isset($_POST['eliminar'])) {
        $id_profesor = $row['id_profesor'];

		$query = "DELETE FROM profesores WHERE id_profesor = $id_profesor";
		mysqli_query($conexion, $query);

		session_start();
		$_SESSION['mensaje'] = 'Registro eliminado correctamente.';
		$_SESSION['message_type'] = 'danger';
        header("Location: registro_profesor.php");
		exit;
	}
?>



<?php include("../includes/header.php"); ?>

	<div class="container p-4">
		<div class="row">
			<div class="col-md-4 mx-auto">
				<div class="card card-body">
					<h4>¿Seguro que desea eliminar este profesor?</h4>
					<form action="eliminar.php?id_profesor=<?php echo $_GET['id_profesor']; ?>" method="POST">
						<div class="form-group">
							<input name="id_profesor" value="<?php echo $id_profesor; ?>" class="form-control" readonly>
							<input name="nombre" value="<?php echo $nombre; ?>" class="form-control" readonly>
							<input name="primer_apellido" value="<?php echo $primer_apellido; ?>" class="form-control" readonly>
							<input name="segundo_apellido" value="<?php echo $segundo_apellido; ?>" class="form-control" readonly>
							<input name="correo_electronico	" value="<?php echo $correo_electronico	; ?>" class="form-control" readonly>
						</div><br>
						<button class="form-control btn btn-danger" name="eliminar">
						<i class="far fa-trash-alt"></i>
							Eliminar
						</button>
					</form><br>
					<form action="registro_profesor.php">
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
