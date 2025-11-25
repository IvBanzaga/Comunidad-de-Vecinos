<?php

    require 'conexion.php';
    require 'functions.php';

    /* TODO: Comprobación de autenticación y rol. Se usa session_regenerate_id(true) para evitar robo de sesión (fijación de sesión). Depuración: puedes poner breakpoint aquí para comprobar el estado de $_SESSION. */
    if (! isset($_SESSION['userId']) || $_SESSION['rol'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
    session_regenerate_id(true); // Justificación: previene ataques de fijación de sesión

    $mensaje = '';
    $accion  = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : 'listar');

    /* TODO: Procesar acciones del panel admin. Todas las acciones usan POST para mayor seguridad.
       Depuración: breakpoint útil para ver los datos recibidos por POST. */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['crear_vecino'])) {
            $nombre    = $_POST['nombre'];
            $apellidos = $_POST['apellidos'];
            $dni       = $_POST['dni'];
            $telefono  = $_POST['telefono'];
            $email     = $_POST['email'];
            $fechaAlta = $_POST['fechaAlta'];
            $piso      = $_POST['piso'];
            $bloque    = $_POST['bloque'];
            $letra     = $_POST['letra'];

            /* TODO: Llamada a función crear_vecino. Depuración: breakpoint útil para comprobar los datos enviados y el resultado. */
            if (crear_vecino($conexionPDO, $nombre, $apellidos, $dni, $telefono, $email, $fechaAlta)) {
                $idVecino = $conexionPDO->lastInsertId();
                asignar_vivienda($conexionPDO, $idVecino, $piso, $bloque, $letra); // Asignar vivienda al vecino
                crear_cuotas($conexionPDO, $idVecino, $idVecino, 0, 0, $fechaAlta);
                $mensaje = 'Vecino creado correctamente.';
            } else {
                $mensaje = 'Error al crear el vecino.';
            }
        }

        if (isset($_POST['editar_vecino'])) {
            $id        = $_POST['id'];
            $nombre    = $_POST['nombre'];
            $apellidos = $_POST['apellidos'];
            $dni       = $_POST['dni'];
            $telefono  = $_POST['telefono'];
            $email     = $_POST['email'];
            $piso      = $_POST['piso'];
            $bloque    = $_POST['bloque'];
            $letra     = $_POST['letra'];

            /* TODO: Llamada a función editar_vecino. Depuración: breakpoint útil para comprobar los datos enviados y el resultado. */
            if (editar_vecino($conexionPDO, $id, $nombre, $apellidos, $dni, $telefono, $email)) {
                editar_vivienda($conexionPDO, $id, $piso, $bloque, $letra);
                $mensaje = 'Vecino actualizado correctamente.';
            } else {
                $mensaje = 'Error al actualizar el vecino.';
            }
        }

        if (isset($_POST['cambiar_rol'])) {
            $idUsuario = $_POST['idUsuario'];
            $nuevoRol  = $_POST['nuevoRol'];

            /* TODO: Llamada a función cambiar_rol_usuario. Depuración: breakpoint útil para comprobar el cambio de rol. */
            if (cambiar_rol_usuario($conexionPDO, $idUsuario, $nuevoRol)) {
                $mensaje = 'Rol cambiado correctamente.';
            } else {
                $mensaje = 'Error al cambiar el rol.';
            }
        }
    }

    /* TODO: Eliminar vecino solo por POST. Depuración: breakpoint útil para comprobar el id recibido y el resultado. */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_vecino'])) {
        $id = $_POST['eliminar_vecino'];
        if (eliminar_vecino($conexionPDO, $id)) {
            $mensaje = 'Vecino eliminado correctamente.';
        } else {
            $mensaje = 'Error al eliminar el vecino.';
        }
    }

    /* TODO: Obtener datos para editar vecino. Depuración: breakpoint útil para comprobar el array $vecinoEditar. */
    $vecinoEditar = null;
    if ($accion === 'editar' && isset($_POST['id'])) {
        $stmt = $conexionPDO->prepare("SELECT v.*, vi.piso, vi.bloque, vi.letra FROM vecinos v LEFT JOIN vivienda vi ON v.id = vi.idVecino WHERE v.id = ?");
        $stmt->execute([$_POST['id']]);
        $vecinoEditar = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* TODO: Obtener todos los vecinos para mostrar en el panel. Depuración: breakpoint útil para comprobar el array $vecinos. */
    $vecinos = obtener_todos_vecinos_completos($conexionPDO);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Panel de Administración</h2>
      <div>
        <span class="me-3">Bienvenido <strong><?php echo htmlspecialchars(trim($_SESSION['user'])) ?></strong>
          (<?php echo ucfirst(htmlspecialchars(trim($_SESSION['rol']))) ?>)</span>
        <form action="logout.php" method="post" style="display:inline;">
          <button type="submit" class="btn btn-danger btn-sm">Cerrar sesión</button>
        </form>
      </div>
    </div>


    <?php if ($mensaje): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars(trim($mensaje)) ?></div>
    <?php endif; ?>

    <!-- Estadísticas de uso -->
    <div class="card mb-4">
      <div class="card-header bg-secondary text-white">
        <h5>Estadísticas de uso y comportamiento</h5>
      </div>
      <div class="card-body">
        <ul>
          <li>Número de visitas semanales por vecino:
            <ul>
              <?php
                  // Buscar cookies de visitas de vecinos
                  foreach ($_COOKIE as $k => $v) {
                      if (strpos($k, 'visitas_vecino_') === 0) {
                          $id = str_replace('visitas_vecino_', '', $k);
                          echo '<li>Vecino ID ' . htmlspecialchars($id) . ': ' . intval($v) . ' visitas</li>';
                      }
                  }
              ?>
            </ul>
          </li>
          <li>Número de ejecuciones SQL (insertar, modificar, altas, bajas) realizadas por el administrador este mes:
            <strong><?php echo isset($_COOKIE['admin_sql_mes']) ? intval($_COOKIE['admin_sql_mes']) : 0 ?></strong>
          </li>
          <li>Número de veces que el campo cuotas pagadas ha sido modificado en el último año:
            <strong><?php echo isset($_COOKIE['cuotas_modificadas_ano']) ? intval($_COOKIE['cuotas_modificadas_ano']) : 0 ?></strong>
          </li>
          <li>Fecha de la última visita de cada rol (con ID de usuario):
            <ul>
              <li>Administrador:
                <?php
                    $admin_id    = isset($_COOKIE['ultima_visita_admin_id']) ? htmlspecialchars($_COOKIE['ultima_visita_admin_id']) : 'N/A';
                    $admin_fecha = isset($_COOKIE['ultima_visita_admin']) ? htmlspecialchars($_COOKIE['ultima_visita_admin']) : 'Nunca';
                    echo $admin_fecha . ' (ID: ' . $admin_id . ')';
                ?>
              </li>
              <li>Presidente:
                <?php
                    $presidente_id    = isset($_COOKIE['ultima_visita_presidente_id']) ? htmlspecialchars($_COOKIE['ultima_visita_presidente_id']) : 'N/A';
                    $presidente_fecha = isset($_COOKIE['ultima_visita_presidente']) ? htmlspecialchars($_COOKIE['ultima_visita_presidente']) : 'Nunca';
                    echo $presidente_fecha . ' (ID: ' . $presidente_id . ')';
                ?>
              </li>
              <li>Vecino:
                <?php
                    $vecino_id    = isset($_COOKIE['ultima_visita_vecino_id']) ? htmlspecialchars($_COOKIE['ultima_visita_vecino_id']) : 'N/A';
                    $vecino_fecha = isset($_COOKIE['ultima_visita_vecino']) ? htmlspecialchars($_COOKIE['ultima_visita_vecino']) : 'Nunca';
                    echo $vecino_fecha . ' (ID: ' . $vecino_id . ')';
                ?>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>

    <div class="mb-4">
      <form method="post" style="display:inline;">
        <input type="hidden" name="accion" value="listar">
        <button type="submit" class="btn btn-primary">Ver Vecinos</button>
      </form>
      <form method="post" style="display:inline;">
        <input type="hidden" name="accion" value="crear">
        <button type="submit" class="btn btn-success">Crear Vecino</button>
      </form>
    </div>

    <?php if ($accion === 'crear' || $accion === 'editar'): ?>
    <div class="card mb-4">
      <div class="card-header">
        <h4><?php echo $accion === 'crear' ? 'Crear Nuevo Vecino' : 'Editar Vecino' ?></h4>
      </div>
      <div class="card-body">
        <form method="post">
          <?php if ($accion === 'editar'): ?>
          <input type="hidden" name="id" value="<?php echo $vecinoEditar['id'] ?>">
          <?php endif; ?>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo $vecinoEditar['nombre'] ?? '' ?>"
                  required>
              </div>
              <div class="mb-3">
                <label class="form-label">Apellidos:</label>
                <input type="text" name="apellidos" class="form-control" value="<?php echo $vecinoEditar['apellidos'] ?? '' ?>"
                  required>
              </div>
              <div class="mb-3">
                <label class="form-label">DNI:</label>
                <input type="text" name="dni" class="form-control" value="<?php echo $vecinoEditar['dni'] ?? '' ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="telefono" class="form-control" value="<?php echo $vecinoEditar['telefono'] ?? '' ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo $vecinoEditar['email'] ?? '' ?>">
              </div>
              <?php if ($accion === 'crear'): ?>
              <div class="mb-3">
                <label class="form-label">Fecha de Alta:</label>
                <input type="date" name="fechaAlta" class="form-control" value="<?php echo date('Y-m-d') ?>" required>
              </div>
              <?php endif; ?>
              <div class="mb-3">
                <label class="form-label">Piso:</label>
                <input type="text" name="piso" class="form-control" value="<?php echo $vecinoEditar['piso'] ?? '' ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Bloque:</label>
                <input type="text" name="bloque" class="form-control" value="<?php echo $vecinoEditar['bloque'] ?? '' ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Letra:</label>
                <input type="text" name="letra" class="form-control" value="<?php echo $vecinoEditar['letra'] ?? '' ?>"
                  maxlength="1">
              </div>
            </div>
          </div>

          <button type="submit" name="<?php echo $accion === 'crear' ? 'crear_vecino' : 'editar_vecino' ?>"
            class="btn btn-primary">
            <?php echo $accion === 'crear' ? 'Crear Vecino' : 'Actualizar Vecino' ?>
          </button>
          <a href="?accion=listar" class="btn btn-secondary">Cancelar</a>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($accion === 'listar'): ?>
    <div class="card">
      <div class="card-header">
        <h4>Lista de Vecinos</h4>
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>DNI</th>
              <th>Teléfono</th>
              <th>Email</th>
              <th>Vivienda</th>
              <th>Cuotas P/I</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vecinos as $vecino): ?>
            <tr>
              <td><?php echo htmlspecialchars($vecino['nombre'] . ' ' . $vecino['apellidos']) ?></td>
              <td><?php echo htmlspecialchars($vecino['dni']) ?></td>
              <td><?php echo htmlspecialchars($vecino['telefono']) ?></td>
              <td><?php echo htmlspecialchars($vecino['email']) ?></td>
              <td><?php echo $vecino['piso'] . $vecino['bloque'] . $vecino['letra'] ?></td>
              <td><?php echo $vecino['cuotasPagadas'] . '/' . $vecino['cuotasImpagadas'] ?></td>
              <td>
                <?php if ($vecino['usuarioId']): ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="idUsuario" value="<?php echo $vecino['usuarioId'] ?>">
                  <select name="nuevoRol" class="form-select form-select-sm" style="width:120px; display:inline;">
                    <option value="vecino"                                                                                                                               <?php echo $vecino['rol'] === 'vecino' ? 'selected' : '' ?>>Vecino
                    </option>
                    <option value="presidente"                                                                                                                                           <?php echo $vecino['rol'] === 'presidente' ? 'selected' : '' ?>>Presidente
                    </option>
                  </select>
                  <button type="submit" name="cambiar_rol" class="btn btn-sm btn-warning">Cambiar</button>
                </form>
                <?php else: ?>
                Sin usuario
                <?php endif; ?>
              </td>
              <td>
                <a href="?accion=editar&id=<?php echo $vecino['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                <a href="?eliminar=<?php echo $vecino['id'] ?>" class="btn btn-sm btn-danger"
                  onclick="return confirm('¿Estás seguro?')">Eliminar</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>
</body>

</html>
