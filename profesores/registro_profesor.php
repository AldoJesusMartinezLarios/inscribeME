<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: ../../primera_parte/administracion/index.html");
    exit;
}

include("../conexion/conexion.php");

$nombre_usuario = $_SESSION['nombre_usuario'];

include("../includes/header.php");

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
			<h2>Agregar Profesor</h2>
			<br>
					<div class="form-group">
						<label for="">Correo electrónico (no se puede repetir):</label>
						<input type="text" name="correo_electronico" class="form-control" placeholder="Correo electrónico" required>
						<label for="">Nombre:</label>
						<input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
						<label for="">Primer Apellido:</label>
						<input type="text" name="primer_apellido" class="form-control" placeholder="Primer Apellido" required>
						<label for="">Segundo Apellido:</label>
						<input type="text" name="segundo_apellido" class="form-control" placeholder="Segundo Apellido"  required>
						<label for="">Contraseña:</label>
						<input type="password" name="contraseña" class="form-control" placeholder="Contraseña" required>
						<label for="">Acceso: </label>
						<select required id="acceso" name="acceso" class="form-control"  required>
                        <option disabled selected="Acceso">Acceso</option>
                        <option value="1">Con acceso</option>
                        <option value="0">Sin acceso</option>
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
		<h1>Profesores</h1>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Id</th>
							<th>Nombre</th>
							<th>Correo Electrónico</th>
							<th>Acceso</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = "SELECT * FROM profesores";
						$resultado_registro = mysqli_query($conexion, $query);

						while ($row = mysqli_fetch_array($resultado_registro)) {
							?>
							<tr>
								<td><?php echo $row['id_profesor'] ?></td>
								<td><?php echo $row['nombre']." ". $row['primer_apellido']." ". $row['segundo_apellido']?></td>
								<td><?php echo $row['correo_electronico'] ?></td>
								<td><?php
								 $acceso = $row['acceso'];
								 if($acceso == 1){
									?>
									<a href='acceso.php?id_profesor=<?php echo $row['id_profesor'] ?>' class='btn btn-danger'>Quitar Acceso</a>
									<?php
								 }else{
									?>
									<a href='acceso.php?id_profesor=<?php echo $row['id_profesor'] ?>' class='btn btn-success'>Dar acceso</a>
									<?php
								 }
								 ?></td>
								<td>
									<a href="modificar.php?id_profesor=<?php echo $row['id_profesor'] ?>" class="btn btn-primary">
										<i class="fas fa-marker"></i>
									</a>
									<a href="eliminar.php?id_profesor=<?php echo $row['id_profesor'] ?>" class="btn btn-danger">
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
<?php include("../includes/footer.php") ?>