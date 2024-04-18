<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .login-container {
      margin-top: 10%;
    }
  </style>
</head>
<body>

<div class="container login-container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary text-white text-center">
          <h4>Iniciar Sesión</h4>
        </div>
        <div class="card-body">
            <?php session_start(); if (isset($_SESSION['error_message'])) { echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>'; unset($_SESSION['error_message']); } ?>
          <form action="login.php" method="POST">
            <div class="form-group">
              <label for="correo_electronico">Correo Electrónico:</label>
              <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" placeholder="Ingrese su correo" required>
            </div>
            <div class="form-group">
              <label for="contraseña">Contraseña:</label>
              <div class="input-group">
                <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Ingrese su contraseña" required>
                <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="button" id="mostrar_contrasena" onclick="mostrarContrasena()">Mostrar</button>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    function mostrarContrasena() {
        var inputContrasena = document.getElementById("contraseña");
        var botonMostrar = document.getElementById("mostrar_contrasena");

        if (inputContrasena.type === "password") {
            inputContrasena.type = "text";
            botonMostrar.textContent = "Ocultar";
        } else {
            inputContrasena.type = "password";
            botonMostrar.textContent = "Mostrar";
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
