<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


    /*
    Solicita un usuario y contraseña y comprueba que esta es correcta, si lo es, redirije la aplicación a usuarios.php
*/

    require 'conexion.php';
    require 'functions.php';

    /* TODO: Procesamiento de login. Se usa password_verify para comprobar la contraseña cifrada y session_regenerate_id(true) para evitar robo de sesión. Depuración: puedes poner breakpoint aquí para comprobar los datos recibidos y el resultado de la autenticación. */

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['user']);
        $password = trim($_POST['password']);

        $user = comprobar_username($conexionPDO, $username);

        /* TODO: Verificación segura de contraseña y gestión de sesión. Depuración: breakpoint útil para comprobar el array $user y el resultado de password_verify. */
        if ($user && password_verify($password, $user['pass'])) {
            session_regenerate_id(true); // Justificación: previene ataques de fijación de sesión
            $_SESSION['userId'] = $user['id'];
            $_SESSION['user']   = htmlspecialchars($username);
            $_SESSION['rol']    = $user['rol'];

            // Guardar fecha de última visita por rol en cookie y el id de usuario
            setcookie('ultima_visita_' . $user['rol'], date('Y-m-d H:i:s'), time() + 365 * 24 * 3600, '/');
            setcookie('ultima_visita_' . $user['rol'] . '_id', $user['id'], time() + 365 * 24 * 3600, '/');

            /* TODO: Redirección según rol. Depuración: breakpoint útil para comprobar el valor de $user['rol']. */
            if ($user['rol'] === 'vecino') {
                header('Location: viewVecino.php');
            } else if ($user['rol'] === 'admin') {
                header('Location: viewAdmin.php');
            } else {
                header('Location: viewPresidente.php');
            }
            exit;
        } else {
            $error = 'Credenciales inválidas';
        }
    }
?>



<!DOCTYPE html>
<html lang="es">


<head>
  <meta charset="utf-8">
  <title>Acceso a la aplicación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/svg+xml" href="public/icon.svg">
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow">
          <div class="card-header bg-primary text-white text-center">
            <h2>Acceso a la aplicación</h2>
          </div>
          <div class="card-body">
            <?php if (! empty($error)) {
                    echo "<div class='alert alert-danger text-center'>$error</div>";
                }
            ?>
            <form method="post">
              <div class="mb-3">
                <label for="user" class="form-label">Usuario:</label>
                <input type="text" class="form-control" id="user" name="user" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Acceder</button>
              </div>
            </form>

            <!-- Información de usuarios de prueba para acceso rápido -->
            <div class="mt-4 p-3 border rounded bg-light">
              <h3 class="h6 mb-3 text-primary">Usuarios de prueba</h3>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Rol</th>
                      <th>Usuario</th>
                      <th>Contraseña</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Admin</td>
                      <td><code>admin</code></td>
                      <td><code>admin</code></td>
                    </tr>
                    <tr>
                      <td>Presidente</td>
                      <td><code>presidente</code></td>
                      <td><code>presidente</code></td>
                    </tr>
                    <tr>
                      <td>Vecino</td>
                      <td><code>vecino</code></td>
                      <td><code>vecino</code></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <small class="text-muted d-block mt-2">Puedes usar estos datos para probar la aplicación.</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
