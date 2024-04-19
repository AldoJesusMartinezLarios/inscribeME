<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>InscribeME</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="icon" href="../estilos/imagenes/icono.png" type="image/x-icon">
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Montserrat', sans-serif;
    }
    .card {
      border: none;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .card-body {
      padding: 40px;
    }
    .form-control:focus {
      border-color: #6c757d;
      box-shadow: none;
    }
    .btn-primary {
      background-color: #6c757d;
      border-color: #6c757d;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #5a6268;
      border-color: #545b62;
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <?php session_start(); if (isset($_SESSION['error_message'])) { echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>'; unset($_SESSION['error_message']); } ?>
            <h2 class="text-center mb-4">Inicia sesión</h2>
            <hr>
            <form action="iniciosesion.php" method="post" onsubmit="return verificarForm()">
              <div class="form-group">
                <label for="curp">CURP</label>
                <input type="text" class="form-control" id="curp" name="curp" required placeholder="GALJ900315HJALP01" maxlength="18" oninput="this.value = this.value.replace(/\[^A-Z^0-9\]/g, '')" onblur="verificarForm()">
                <span id="curp-error-message" style="color: red; display: none;">La CURP debe tener exactamente 18 caracteres. Por favor, verifícalo</span>
              </div>
              <div class="form-group">
                <label for="contraseña">Contraseña</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="showPasswordBtn">Mostrar</button>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <input type="submit" value="Entrar" class="btn btn-primary btn-block">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>