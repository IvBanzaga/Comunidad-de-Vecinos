<?php

/* TODO: Busca un usuario por nombre usando consulta preparada para evitar SQL Injection y sanitiza el dato para evitar XSS. Depuración: puedes poner un breakpoint aquí para ver el array $usuarios. */
function comprobar_username($conexion, $username)
{
    $username = trim($username);
    $stmt     = $conexion->prepare("SELECT * FROM Usuarios WHERE usuario = ?");
    $stmt->execute([$username]);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($usuarios)) {
        return false;
    }
    return $usuarios[0];
}

/* TODO: Devuelve los datos del vecino por id usando consulta preparada y sanitiza los datos para evitar XSS. Depuración: breakpoint útil para comprobar el array $vecinos. */
function obtener_vecino($conexion, $id)
{
    $id   = intval($id);
    $stmt = $conexion->prepare("SELECT * FROM Vecinos WHERE id = ?");
    $stmt->execute([$id]);
    $vecinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($vecinos)) {
        return false;
    }
    // Sanitizar datos para evitar XSS
    foreach ($vecinos[0] as $k => $v) {
        $vecinos[0][$k] = htmlspecialchars(trim($v));
    }
    return $vecinos[0];
}

/* TODO: Devuelve la vivienda del vecino por idVecino usando consulta preparada y sanitiza los datos para evitar XSS.
Depuración: breakpoint útil para comprobar el array $viviendas. */
function obtener_vivienda($conexion, $idVecino)
{
    $idVecino = intval($idVecino);
    $stmt     = $conexion->prepare("SELECT * FROM Vivienda WHERE idVecino = ?");
    $stmt->execute([$idVecino]);
    $viviendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($viviendas)) {
        return false;
    }
    foreach ($viviendas[0] as $k => $v) {
        $viviendas[0][$k] = htmlspecialchars(trim($v));
    }
    return $viviendas[0];
}

/* TODO: Devuelve las cuotas del vecino por idVecino usando consulta preparada y sanitiza los datos para evitar XSS.
Depuración: breakpoint útil para comprobar el array $cuotasArr. */
function obtener_cuotas($conexion, $idVecino)
{
    $idVecino = intval($idVecino);
    $stmt     = $conexion->prepare("SELECT * FROM Cuotas WHERE idVecino = ?");
    $stmt->execute([$idVecino]);
    $cuotasArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($cuotasArr)) {
        return false;
    }
    foreach ($cuotasArr[0] as $k => $v) {
        $cuotasArr[0][$k] = htmlspecialchars(trim($v));
    }
    return $cuotasArr[0];
}

/* TODO: Cambia la contraseña del usuario usando password_hash para cifrar y consulta preparada para evitar SQL Injection.
Depuración: breakpoint útil para comprobar el hash generado y el resultado de la consulta. */
function cambiar_contrasena($conexion, $id, $nueva_pass)
{
    $id         = intval($id);
    $nueva_pass = trim($nueva_pass);
    $hash       = password_hash($nueva_pass, PASSWORD_DEFAULT);
    $stmt       = $conexion->prepare("UPDATE Usuarios SET pass = ? WHERE id = ?");
    return $stmt->execute([$hash, $id]);
}

/* TODO:FUNCIONES PARA ADMINISTRADOR */

/* TODO: Crear un nuevo vecino */
function crear_vecino($conexionPDO, $nombre, $apellidos, $dni, $telefono, $email, $fechaAlta)
{
    /* TODO:Estadística: contar ejecuciones SQL del admin este mes**/
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
        $mes        = date('Ym');
        $cookie_sql = 'admin_sql_mes';
        $count      = isset($_COOKIE[$cookie_sql]) ? intval($_COOKIE[$cookie_sql]) + 1 : 1;
        setcookie($cookie_sql, $count, time() + 31 * 24 * 3600, '/');
    }
    $stmt = $conexionPDO->prepare("INSERT INTO Vecinos (nombre, apellidos, dni, telefono, email, fechaAlta) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$nombre, $apellidos, $dni, $telefono, $email, $fechaAlta]);
}

/* TODO: Actualizar datos del vecino */
function editar_vecino($conexionPDO, $id, $nombre, $apellidos, $dni, $telefono, $email)
{
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
        $cookie_sql = 'admin_sql_mes';
        $count      = isset($_COOKIE[$cookie_sql]) ? intval($_COOKIE[$cookie_sql]) + 1 : 1;
        setcookie($cookie_sql, $count, time() + 31 * 24 * 3600, '/');
    }
    $stmt = $conexionPDO->prepare("UPDATE Vecinos SET nombre = ?, apellidos = ?, dni = ?, telefono = ?, email = ? WHERE id = ?");
    return $stmt->execute([$nombre, $apellidos, $dni, $telefono, $email, $id]);
}

/* TODO:Eliminar un vecino */
function eliminar_vecino($conexionPDO, $id)
{
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
        $cookie_sql = 'admin_sql_mes';
        $count      = isset($_COOKIE[$cookie_sql]) ? intval($_COOKIE[$cookie_sql]) + 1 : 1;
        setcookie($cookie_sql, $count, time() + 31 * 24 * 3600, '/');
    }
    // Eliminar registros relacionados primero
    $conexionPDO->prepare("DELETE FROM Cuotas WHERE idVecino = ?")->execute([$id]);
    $conexionPDO->prepare("DELETE FROM Vivienda WHERE idVecino = ?")->execute([$id]);
    $conexionPDO->prepare("DELETE FROM Usuarios WHERE idVecino = ?")->execute([$id]);
    $stmt = $conexionPDO->prepare("DELETE FROM Vecinos WHERE id = ?");
    return $stmt->execute([$id]);
}

/* TODO: Actualiza cuotas pagadas y fecha de última cuota para un vecino */
function actualizar_cuotas($conexionPDO, $idCuota, $cuotasPagadas, $fechaUltimaCuota)
{
    // Estadística: contar modificaciones de cuotas pagadas en el año
    $cookie_cuotas = 'cuotas_modificadas_ano';
    $count         = isset($_COOKIE[$cookie_cuotas]) ? intval($_COOKIE[$cookie_cuotas]) + 1 : 1;
    setcookie($cookie_cuotas, $count, time() + 365 * 24 * 3600, '/');
    $stmt = $conexionPDO->prepare("UPDATE Cuotas SET cuotasPagadas = ?, fechaUltimaCuota = ? WHERE id = ?");
    return $stmt->execute([$cuotasPagadas, $fechaUltimaCuota, $idCuota]);
}


/* TODO: Editar vivienda */
function editar_vivienda($conexionPDO, $idVecino, $piso, $bloque, $letra)
{
    $stmt = $conexionPDO->prepare("UPDATE Vivienda SET piso = ?, bloque = ?, letra = ? WHERE idVecino = ?");
    return $stmt->execute([$piso, $bloque, $letra, $idVecino]);
}

/* Asignar vivienda a un vecino (si no tiene) */
function asignar_vivienda($conexionPDO, $idVecino, $piso, $bloque, $letra)
{
    // Verificar si el vecino ya tiene una vivienda asignada
    $stmtCheck = $conexionPDO->prepare("SELECT COUNT(*) FROM Vivienda WHERE idVecino = ?");
    $stmtCheck->execute([$idVecino]);
    $count = $stmtCheck->fetchColumn();
    if ($count == 0) {
        // Si no tiene vivienda, asignarla
        $stmt = $conexionPDO->prepare("INSERT INTO Vivienda (idVecino, piso, bloque, letra) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$idVecino, $piso, $bloque, $letra]);
    }
    return false; // Ya tiene vivienda asignada
}

/* TODO: Crear cuotas para un vecino */
function crear_cuotas($conexionPDO, $idVivienda, $idVecino, $cuotasPagadas, $cuotasImpagadas, $fechaUltimaCuota)
{
    $stmt = $conexionPDO->prepare("INSERT INTO Cuotas (idVivienda, idVecino, cuotasPagadas, cuotasImpagadas, fechaUltimaCuota) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$idVivienda, $idVecino, $cuotasPagadas, $cuotasImpagadas, $fechaUltimaCuota]);
}

/* TODO: Cambiar rol de usuario */
function cambiar_rol_usuario($conexionPDO, $idUsuario, $nuevoRol)
{
    // Si se asigna rol presidente, quitar presidente a otros
    if ($nuevoRol === 'presidente') {
        $conexionPDO->prepare("UPDATE Usuarios SET rol = 'vecino' WHERE rol = 'presidente'")->execute();
    }
    $stmt = $conexionPDO->prepare("UPDATE Usuarios SET rol = ? WHERE id = ?");
    return $stmt->execute([$nuevoRol, $idUsuario]);
}

/* TODO: Obtener todos los vecinos con sus datos completos */
function obtener_todos_vecinos_completos($conexionPDO)
{
    $sql = "SELECT v.*, vi.piso, vi.bloque, vi.letra,
                   c.cuotasPagadas, c.cuotasImpagadas, c.fechaUltimaCuota,
                   u.id as usuarioId, u.usuario, u.rol
            FROM Vecinos v
            LEFT JOIN Vivienda vi ON v.id = vi.idVecino
            LEFT JOIN Cuotas c ON v.id = c.idVecino
            LEFT JOIN Usuarios u ON v.id = u.idVecino
            ORDER BY v.id";
    return $conexionPDO->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}