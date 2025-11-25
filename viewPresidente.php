<?php
    require 'conexion.php';
    require 'functions.php';

    // Comprobar autenticación y rol
    if (! isset($_SESSION['userId']) || $_SESSION['rol'] !== 'presidente') {
        header('Location: login.php');
        exit;
    }

    // Actualizar cuotas si se envía el formulario
    $mensaje = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCuota'])) {
        $idCuota          = $_POST['idCuota'];
        $cuotasPagadas    = $_POST['cuotasPagadas'];
        $cuotasImpagadas  = $_POST['cuotasImpagadas'];
        $fechaUltimaCuota = $_POST['fechaUltimaCuota'];

        $stmt = $conexionPDO->prepare("UPDATE cuotas SET cuotasPagadas = ?, cuotasImpagadas = ?, fechaUltimaCuota = ? WHERE id = ?");
        if ($stmt->execute([$cuotasPagadas, $cuotasImpagadas, $fechaUltimaCuota, $idCuota])) {
            $mensaje = 'Cuotas actualizadas correctamente.';
        } else {
            $mensaje = 'Error al actualizar las cuotas.';
        }
    }

    // Obtener todos los vecinos
    $vecinos = $conexionPDO->query("SELECT * FROM vecinos")->fetchAll(PDO::FETCH_ASSOC);
    // Obtener todas las viviendas
    $viviendas = $conexionPDO->query("SELECT * FROM vivienda")->fetchAll(PDO::FETCH_ASSOC);
    // Obtener todas las cuotas
    $cuotas = $conexionPDO->query("SELECT * FROM cuotas")->fetchAll(PDO::FETCH_ASSOC);

    // Indexar viviendas y cuotas por idVecino para acceso rápido
    $viviendasPorVecino = [];
    foreach ($viviendas as $v) {
        $viviendasPorVecino[$v['idVecino']] = $v;
    }
    $cuotasPorVecino = [];
    foreach ($cuotas as $c) {
        $cuotasPorVecino[$c['idVecino']] = $c;
    }
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel Presidente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-4">Gestión de Vecinos, Viviendas y Cuotas</h2>
      <div>
        <span class="me-3">Bienvenido <strong><?php echo $_SESSION['user']?></strong>
          (<?php echo ucfirst($_SESSION['rol'])?>)</span>
        <form action="logout.php" method="post" style="display:inline;">
          <button type="submit" class="btn btn-danger btn-sm">Cerrar sesión</button>
        </form>
      </div>
    </div>
    <?php if ($mensaje): ?>
    <div id="mensajeCuota" class="alert alert-info"><?php echo $mensaje?></div>
    <script>
    document.getElementById('logoutForm').style.display = 'none';
    setTimeout(function() {
      var msg = document.getElementById('mensajeCuota');
      if (msg) msg.style.display = 'none';
      document.getElementById('logoutForm').style.display = 'inline';
    }, 3000);
    </script>
    <?php endif; ?>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Apellidos</th>
          <th>DNI</th>
          <th>Teléfono</th>
          <th>Email</th>
          <th>Piso</th>
          <th>Bloque</th>
          <th>Letra</th>
          <th>Cuotas Pagadas</th>
          <th>Cuotas Impagadas</th>
          <th>Fecha Última Cuota</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vecinos as $vecino):
                $vivienda = $viviendasPorVecino[$vecino['id']] ?? null;
                $cuota    = $cuotasPorVecino[$vecino['id']] ?? null;
            ?>
	        <tr>
	          <td><?php echo htmlspecialchars($vecino['nombre'])?></td>
	          <td><?php echo htmlspecialchars($vecino['apellidos'])?></td>
	          <td><?php echo htmlspecialchars($vecino['dni'])?></td>
	          <td><?php echo htmlspecialchars($vecino['telefono'])?></td>
	          <td><?php echo htmlspecialchars($vecino['email'])?></td>
	          <td><?php echo $vivienda ? htmlspecialchars($vivienda['piso']) : '-'?></td>
	          <td><?php echo $vivienda ? htmlspecialchars($vivienda['bloque']) : '-'?></td>
	          <td><?php echo $vivienda ? htmlspecialchars($vivienda['letra']) : '-'?></td>
	          <td>
	            <?php if ($cuota): ?>
	            <form method="post" class="d-flex gap-2 align-items-center">
	              <input type="hidden" name="idCuota" value="<?php echo $cuota['id']?>">
	              <input type="number" name="cuotasPagadas" value="<?php echo $cuota['cuotasPagadas']?>" min="0"
	                class="form-control form-control-sm" style="width:80px;">
	          </td>
	          <td>
	            <input type="number" name="cuotasImpagadas" value="<?php echo $cuota['cuotasImpagadas']?>" min="0"
	              class="form-control form-control-sm" style="width:80px;">
	          </td>
	          <td>
	            <input type="date" name="fechaUltimaCuota" value="<?php echo $cuota['fechaUltimaCuota']?>"
	              class="form-control form-control-sm" style="width:150px;">
	          </td>
	          <td>
	            <button type="submit" class="btn btn-success btn-sm">Actualizar</button>
	            </form>
	            <?php else: ?>
            -
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>
