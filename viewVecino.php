<?php

    require 'conexion.php';
    require 'functions.php';

    /* TODO: Comprobación de autenticación y rol. Depuración: puedes poner breakpoint aquí para comprobar el estado de
             $_SESSION. */

    // Comprobar autenticación y rol
    if (! isset($_SESSION['userId']) || $_SESSION['rol'] !== 'vecino') {
        header('Location: login.php');
        exit;
    }

    // Se guarda su id en la variable $_SESSION['userId'] , para recoger sus datos
    $userId = intval($_SESSION['userId']);

    /* TODO: Estadística de visitas semanales por vecino usando cookies.
       Depuración: breakpoint útil para comprobar el valor de $visitas. */
    $cookie_visitas = 'visitas_vecino_' . $userId;
    if (! isset($_COOKIE[$cookie_visitas])) {
        setcookie($cookie_visitas, 1, time() + 7 * 24 * 3600, '/');
        $visitas = 1;
    } else {
        $visitas = intval($_COOKIE[$cookie_visitas]) + 1;
        setcookie($cookie_visitas, $visitas, time() + 7 * 24 * 3600, '/');
    }

    /* TODO: Obtener datos del vecino, vivienda y cuotas usando funciones seguras.
       Depuración: breakpoint útil para comprobar los arrays $vecino, $vivienda y $cuotas. */
    $vecino   = obtener_vecino($conexionPDO, $userId);
    $vivienda = obtener_vivienda($conexionPDO, $userId);
    $cuotas   = obtener_cuotas($conexionPDO, $userId);

    /* TODO: Cambiar contraseña del vecino usando función segura.
      Depuración: breakpoint útil para comprobar el valor de $nueva_pass y el resultado. */
    $mensaje = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_pass'])) {
        $nueva_pass = $_POST['nueva_pass'];
        if (cambiar_contrasena($conexionPDO, $userId, $nueva_pass)) {
            $mensaje = 'Contraseña actualizada correctamente.';
        } else {
            $mensaje = 'Error al actualizar la contraseña.';
        }
    }
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Mi Perfil - Comunidad de Vecinos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
  .profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
  }

  .card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    transition: transform 0.2s;
  }

  .card:hover {
    transform: translateY(-5px);
  }

  .card-header {
    border-radius: 15px 15px 0 0 !important;
    font-weight: 600;
  }

  .info-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f8f9fa;
  }

  .info-item:last-child {
    border-bottom: none;
  }

  .info-icon {
    width: 40px;
    text-align: center;
    margin-right: 1rem;
    color: #6c757d;
  }

  .status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 500;
  }

  .status-paid {
    background-color: #d4edda;
    color: #155724;
  }

  .status-unpaid {
    background-color: #f8d7da;
    color: #721c24;
  }
  </style>
</head>

<body class="bg-light">
  <!-- Header con bienvenida -->
  <div class="profile-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h1><i class="fas fa-user-circle me-3"></i>Mi Perfil</h1>
          <p class="mb-0">Bienvenido <strong><?php echo htmlspecialchars(trim($_SESSION['user'])) ?></strong> -
            <?php echo ucfirst(htmlspecialchars(trim($_SESSION['rol']))) ?></p>
        </div>
        <div class="col-md-4 text-end">
          <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="btn btn-outline-light">
              <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container pb-5">
    <div class="row">
      <!-- Datos Personales -->
      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header bg-primary text-white">
            <i class="fas fa-user me-2"></i>Datos Personales
          </div>
          <div class="card-body p-0">
            <?php if ($vecino): ?>
            <div class="info-item">
              <div class="info-icon"><i class="fas fa-signature"></i></div>
              <div>
                <small class="text-muted">Nombre completo</small>
                <div>
                  <strong><?php echo htmlspecialchars($vecino['nombre'] . ' ' . $vecino['apellidos']) ?></strong>
                </div>
              </div>
            </div>
            <div class="info-item">
              <div class="info-icon"><i class="fas fa-id-card"></i></div>
              <div>
                <small class="text-muted">DNI</small>
                <div><strong><?php echo htmlspecialchars($vecino['dni']) ?></strong></div>
              </div>
            </div>
            <div class="info-item">
              <div class="info-icon"><i class="fas fa-phone"></i></div>
              <div>
                <small class="text-muted">Teléfono</small>
                <div><strong><?php echo htmlspecialchars($vecino['telefono']) ?></strong></div>
              </div>
            </div>
            <div class="info-item">
              <div class="info-icon"><i class="fas fa-envelope"></i></div>
              <div>
                <small class="text-muted">Email</small>
                <div><strong><?php echo htmlspecialchars($vecino['email']) ?></strong></div>
              </div>
            </div>
            <div class="info-item">
              <div class="info-icon"><i class="fas fa-calendar-plus"></i></div>
              <div>
                <small class="text-muted">Fecha de alta</small>
                <div><strong><?php echo date('d/m/Y', strtotime($vecino['fechaAlta'])) ?></strong></div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Vivienda -->
      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header bg-success text-white">
            <i class="fas fa-home me-2"></i>Mi Vivienda
          </div>
          <div class="card-body p-0">
            <?php if ($vecino): ?>
            <div class="info-item">
              <span class="info-icon"><i class="fas fa-user"></i></span>Nombre:                                                                                <?php echo $vecino['nombre'] ?>
            </div>
            <div class="info-item">
              <span class="info-icon"><i class="fas fa-id-card"></i></span>DNI:                                                                                <?php echo $vecino['dni'] ?>
            </div>
            <div class="info-item">
              <span class="info-icon"><i class="fas fa-phone"></i></span>Teléfono:                                                                                    <?php echo $vecino['telefono'] ?>
            </div>
            <div class="info-item">
              <span class="info-icon"><i class="fas fa-envelope"></i></span>Email:                                                                                   <?php echo $vecino['email'] ?>
            </div>
            <div class="info-item">
              <span class="info-icon"><i class="fas fa-calendar-alt"></i></span>Fecha Alta:                                                                                            <?php echo $vecino['fechaAlta'] ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Estado de Cuotas -->
      <div class="col-lg-6 mb-4">
        <div class="card">
          <div class="card-header bg-info text-white">
            <i class="fas fa-credit-card me-2"></i>Estado de Cuotas
          </div>
          <div class="card-body">
            <?php if ($cuotas): ?>
            <div class="row text-center mb-3">
              <div class="col-6">
                <div class="p-3 bg-light rounded">
                  <h3 class="text-success mb-1"><?php echo htmlspecialchars($cuotas['cuotasPagadas']) ?></h3>
                  <small class="text-muted">Cuotas Pagadas</small>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 bg-light rounded">
                  <h3 class="text-danger mb-1"><?php echo htmlspecialchars($cuotas['cuotasImpagadas']) ?>
                  </h3>
                  <small class="text-muted">Cuotas Pendientes</small>
                </div>
              </div>
            </div>
            <div class="text-center">
              <i class="fas fa-calendar-check me-2 text-muted"></i>
              <small class="text-muted">Último pago: </small>
              <strong><?php echo date('d/m/Y', strtotime($cuotas['fechaUltimaCuota'])) ?></strong>
            </div>
            <div class="mt-3 text-center">
              <?php if ($cuotas['cuotasImpagadas'] == 0): ?>
              <span class="status-badge status-paid">
                <i class="fas fa-check-circle me-1"></i>Al día
              </span>
              <?php else: ?>
              <span class="status-badge status-unpaid">
                <i class="fas fa-exclamation-circle me-1"></i>Pendiente de pago
              </span>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Cambiar Contraseña -->
      <div class="col-lg-6 mb-4">
        <div class="card">
          <div class="card-header bg-warning text-dark">
            <i class="fas fa-key me-2"></i>Cambiar Contraseña
          </div>
          <div class="card-body">
            <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="fas fa-check-circle me-2"></i><?php echo $mensaje ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <form method="post">
              <div class="mb-3">
                <label for="nueva_pass" class="form-label">Nueva contraseña</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                  <input type="password" class="form-control" id="nueva_pass" name="nueva_pass" required>
                </div>
              </div>
              <button type="submit" class="btn btn-warning w-100">
                <i class="fas fa-save me-2"></i>Actualizar Contraseña
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
