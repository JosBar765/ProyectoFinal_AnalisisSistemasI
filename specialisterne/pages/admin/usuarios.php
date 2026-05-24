<?php
session_start();

require_once '../../php/path.php';
require_once '../../php/db.php';

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ((int)$_SESSION["user"]["id_rol"] !== 1) {
    header("Location: {$path}/403.php");
    exit;
}

/* =========================
   FUNCIONES GENERALES
========================= */

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirectUsuarios($msg = null, $type = 'success')
{
    $query = $msg ? ('?' . http_build_query([
        'msg' => $msg,
        'type' => $type
    ])) : '';

    header('Location: usuarios.php' . $query);
    exit;
}

function badgeRol($rol)
{
    $rol = strtolower(trim((string)$rol));

    if ($rol === 'admin') {
        return '<span class="badge badge-neutral">Admin</span>';
    }

    if ($rol === 'supervisor') {
        return '<span class="badge badge-warning">Supervisor</span>';
    }

    return '<span class="badge badge-primary">Consultor</span>';
}

function badgeEstado($estado)
{
    return ((int)$estado === 1)
        ? '<span class="badge badge-success">Activo</span>'
        : '<span class="badge badge-error">Inactivo</span>';
}

function formatoFecha($fecha)
{
    if (!$fecha) {
        return 'Sin fecha';
    }

    return date('d/m/Y', strtotime($fecha));
}

function inicialUsuario($nombre, $apellido = '')
{
    $nombre = trim((string)$nombre);
    $apellido = trim((string)$apellido);

    $inicialNombre = $nombre !== '' ? strtoupper(substr($nombre, 0, 1)) : '';
    $inicialApellido = $apellido !== '' ? strtoupper(substr($apellido, 0, 1)) : '';

    $iniciales = $inicialNombre . $inicialApellido;

    return $iniciales !== '' ? $iniciales : 'U';
}

/* =========================
   VALIDACIONES DE DEPENDENCIAS
========================= */

function tableColumnExists($pdo, $table, $column)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");

    $stmt->execute([$table, $column]);

    return (int)$stmt->fetchColumn() > 0;
}

function tableExists($pdo, $table)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
    ");

    $stmt->execute([$table]);

    return (int)$stmt->fetchColumn() > 0;
}

function countTableDependency($pdo, $table, $column, $value)
{
    if (!tableExists($pdo, $table)) {
        return 0;
    }

    if (!tableColumnExists($pdo, $table, $column)) {
        return 0;
    }

    $sql = "SELECT COUNT(*) FROM `$table` WHERE `$column` = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$value]);

    return (int)$stmt->fetchColumn();
}

function validarTrabajoAsignadoConsultor($pdo, $idConsultor, $idUsuario)
{
    $dependencias = [];

    /*
        Dependencias directas por id_consultor.
        Si alguna tabla no existe o no tiene la columna, se ignora automáticamente.
    */
    $validacionesConsultor = [
        [
            'tabla' => 'proyecto_consultor',
            'columna' => 'id_consultor',
            'nombre' => 'proyecto(s) asignado(s)'
        ],
        [
            'tabla' => 'ejecucionprueba',
            'columna' => 'id_consultor',
            'nombre' => 'ejecución(es) de prueba'
        ],
        [
            'tabla' => 'casoprueba',
            'columna' => 'id_consultor',
            'nombre' => 'caso(s) de prueba'
        ],
        [
            'tabla' => 'manualprueba',
            'columna' => 'id_consultor',
            'nombre' => 'manual(es) de prueba'
        ],
        [
            'tabla' => 'errorreporte',
            'columna' => 'id_consultor',
            'nombre' => 'reporte(s) de error'
        ],
        [
            'tabla' => 'imagenerrorreporte',
            'columna' => 'id_consultor',
            'nombre' => 'imagen(es) o evidencia(s) de error'
        ]
    ];

    foreach ($validacionesConsultor as $v) {
        $cantidad = countTableDependency($pdo, $v['tabla'], $v['columna'], $idConsultor);

        if ($cantidad > 0) {
            $dependencias[] = $cantidad . ' ' . $v['nombre'];
        }
    }

    /*
        Dependencias por id_usuario.
        Esto cubre tablas que guarden directamente el usuario responsable.
    */
    $validacionesUsuario = [
        [
            'tabla' => 'proyecto_consultor',
            'columna' => 'id_usuario',
            'nombre' => 'proyecto(s) asignado(s) al usuario'
        ],
        [
            'tabla' => 'ejecucionprueba',
            'columna' => 'id_usuario',
            'nombre' => 'ejecución(es) asignada(s) al usuario'
        ],
        [
            'tabla' => 'casoprueba',
            'columna' => 'id_usuario',
            'nombre' => 'caso(s) asignado(s) al usuario'
        ],
        [
            'tabla' => 'manualprueba',
            'columna' => 'id_usuario',
            'nombre' => 'manual(es) asignado(s) al usuario'
        ],
        [
            'tabla' => 'errorreporte',
            'columna' => 'id_usuario',
            'nombre' => 'reporte(s) asignado(s) al usuario'
        ],
        [
            'tabla' => 'imagenerrorreporte',
            'columna' => 'id_usuario',
            'nombre' => 'imagen(es) o evidencia(s) asignada(s) al usuario'
        ]
    ];

    foreach ($validacionesUsuario as $v) {
        $cantidad = countTableDependency($pdo, $v['tabla'], $v['columna'], $idUsuario);

        if ($cantidad > 0) {
            $dependencias[] = $cantidad . ' ' . $v['nombre'];
        }
    }

    return $dependencias;
}

function obtenerConsultorPorUsuario($pdo, $idUsuario)
{
    $stmt = $pdo->prepare("
        SELECT 
            c.id AS id_consultor,
            c.id_usuario,
            c.id_perfil_trabajo,
            u.nombre,
            u.apellido,
            u.correo
        FROM consultor c
        INNER JOIN usuario u
            ON u.id = c.id_usuario
        WHERE c.id_usuario = ?
        LIMIT 1
    ");

    $stmt->execute([$idUsuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function eliminarRelacionConsultorSiExiste($pdo, $idUsuario)
{
    $consultor = obtenerConsultorPorUsuario($pdo, $idUsuario);

    if (!$consultor) {
        return;
    }

    $idConsultor = (int)$consultor['id_consultor'];
    $idPerfilTrabajo = (int)$consultor['id_perfil_trabajo'];

    $stmtDeleteConsultor = $pdo->prepare("
        DELETE FROM consultor
        WHERE id = ?
    ");

    $stmtDeleteConsultor->execute([$idConsultor]);

    if ($idPerfilTrabajo > 0) {
        $stmtUsoPerfil = $pdo->prepare("
            SELECT COUNT(*)
            FROM consultor
            WHERE id_perfil_trabajo = ?
        ");

        $stmtUsoPerfil->execute([$idPerfilTrabajo]);
        $perfilEnUso = (int)$stmtUsoPerfil->fetchColumn();

        if ($perfilEnUso === 0) {
            $stmtDeletePerfil = $pdo->prepare("
                DELETE FROM perfiltrabajo
                WHERE id = ?
            ");

            $stmtDeletePerfil->execute([$idPerfilTrabajo]);
        }
    }
}

/* =========================
   USUARIO CONECTADO
========================= */

$usuarioConectado = null;

if (isset($_SESSION["user"]["id"])) {
    $stmtUsuarioActual = $pdo->prepare("
        SELECT 
            u.id,
            u.nombre,
            u.apellido,
            u.correo,
            r.nombre AS rol
        FROM usuario u
        INNER JOIN rol r ON r.id = u.id_rol
        WHERE u.id = ?
        LIMIT 1
    ");

    $stmtUsuarioActual->execute([$_SESSION["user"]["id"]]);
    $usuarioConectado = $stmtUsuarioActual->fetch(PDO::FETCH_ASSOC);
}

$nombreConectado = $usuarioConectado["nombre"] ?? ($_SESSION["user"]["nombre"] ?? "Admin");
$apellidoConectado = $usuarioConectado["apellido"] ?? ($_SESSION["user"]["apellido"] ?? "");
$rolConectado = $usuarioConectado["rol"] ?? "admin";
$nombreCompletoConectado = trim($nombreConectado . " " . $apellidoConectado);
$inicialesConectado = inicialUsuario($nombreConectado, $apellidoConectado);

/* =========================
   ACCIONES CRUD
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasenia = trim($_POST['contrasenia'] ?? '');
            $idRol = (int)($_POST['id_rol'] ?? 0);
            $estado = (int)($_POST['estado'] ?? 1);

            if ($nombre === '' || $apellido === '' || $correo === '' || $contrasenia === '' || $idRol <= 0) {
                redirectUsuarios('Complete todos los campos obligatorios.', 'error');
            }

            $stmtExiste = $pdo->prepare("
                SELECT COUNT(*) 
                FROM usuario 
                WHERE correo = ?
            ");
            $stmtExiste->execute([$correo]);

            if ((int)$stmtExiste->fetchColumn() > 0) {
                redirectUsuarios('Ya existe un usuario registrado con ese correo.', 'error');
            }

            $stmt = $pdo->prepare("
                INSERT INTO usuario 
                    (nombre, apellido, correo, contrasenia, id_rol, estado, fecha_creacion)
                VALUES 
                    (?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $nombre,
                $apellido,
                $correo,
                $contrasenia,
                $idRol,
                $estado
            ]);

            redirectUsuarios('Usuario creado correctamente.');
        }

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasenia = trim($_POST['contrasenia'] ?? '');
            $idRol = (int)($_POST['id_rol'] ?? 0);
            $estado = (int)($_POST['estado'] ?? 1);

            if ($id <= 0 || $nombre === '' || $apellido === '' || $correo === '' || $idRol <= 0) {
                redirectUsuarios('Datos inválidos para actualizar el usuario.', 'error');
            }

            $stmtUsuarioActual = $pdo->prepare("
                SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.correo,
                    u.id_rol,
                    r.nombre AS rol_actual
                FROM usuario u
                INNER JOIN rol r
                    ON r.id = u.id_rol
                WHERE u.id = ?
                LIMIT 1
            ");

            $stmtUsuarioActual->execute([$id]);
            $usuarioActual = $stmtUsuarioActual->fetch(PDO::FETCH_ASSOC);

            if (!$usuarioActual) {
                redirectUsuarios('El usuario seleccionado no existe.', 'error');
            }

            $stmtExiste = $pdo->prepare("
                SELECT COUNT(*) 
                FROM usuario 
                WHERE correo = ? 
                  AND id <> ?
            ");
            $stmtExiste->execute([$correo, $id]);

            if ((int)$stmtExiste->fetchColumn() > 0) {
                redirectUsuarios('Ya existe otro usuario registrado con ese correo.', 'error');
            }

            if ($id === (int)$_SESSION["user"]["id"] && $estado === 0) {
                redirectUsuarios('No puedes inactivar tu propio usuario mientras estás conectado.', 'error');
            }

            /*
                Si el usuario actualmente tiene perfil de consultor y se cambia a otro rol,
                primero validamos que no tenga trabajo asignado.
            */
            $consultorActual = obtenerConsultorPorUsuario($pdo, $id);
            $nuevoRolEsConsultor = ($idRol === 3);

            if ($consultorActual && !$nuevoRolEsConsultor) {
                $idConsultor = (int)$consultorActual['id_consultor'];
                $nombreUsuario = trim($usuarioActual['nombre'] . ' ' . $usuarioActual['apellido']);

                $dependencias = validarTrabajoAsignadoConsultor($pdo, $idConsultor, $id);

                if (!empty($dependencias)) {
                    redirectUsuarios(
                        'No se puede cambiar el rol de ' . $nombreUsuario . ' porque tiene trabajo asignado: ' . implode(', ', $dependencias) . '. Primero reasigna o libera esas actividades.',
                        'error'
                    );
                }
            }

            $pdo->beginTransaction();

            /*
                Si deja de ser consultor y no tiene trabajo, quitamos relación consultor/perfil.
            */
            if ($consultorActual && !$nuevoRolEsConsultor) {
                eliminarRelacionConsultorSiExiste($pdo, $id);
            }

            if ($contrasenia !== '') {
                $stmt = $pdo->prepare("
                    UPDATE usuario 
                    SET 
                        nombre = ?,
                        apellido = ?,
                        correo = ?,
                        contrasenia = ?,
                        id_rol = ?,
                        estado = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $nombre,
                    $apellido,
                    $correo,
                    $contrasenia,
                    $idRol,
                    $estado,
                    $id
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE usuario 
                    SET 
                        nombre = ?,
                        apellido = ?,
                        correo = ?,
                        id_rol = ?,
                        estado = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $nombre,
                    $apellido,
                    $correo,
                    $idRol,
                    $estado,
                    $id
                ]);
            }

            $pdo->commit();

            redirectUsuarios('Usuario actualizado correctamente.');
        }

        if ($action === 'toggle') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                redirectUsuarios('Usuario inválido.', 'error');
            }

            if ($id === (int)$_SESSION["user"]["id"]) {
                redirectUsuarios('No puedes cambiar el estado de tu propio usuario.', 'error');
            }

            $stmt = $pdo->prepare("
                UPDATE usuario 
                SET estado = CASE WHEN estado = 1 THEN 0 ELSE 1 END
                WHERE id = ?
            ");

            $stmt->execute([$id]);

            redirectUsuarios('Estado del usuario actualizado correctamente.');
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                redirectUsuarios('Usuario inválido.', 'error');
            }

            if ($id === (int)$_SESSION["user"]["id"]) {
                redirectUsuarios('No puedes eliminar tu propio usuario.', 'error');
            }

            /*
                1. Validar que el usuario exista.
            */
            $stmtUsuario = $pdo->prepare("
                SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.correo,
                    u.id_rol,
                    r.nombre AS rol
                FROM usuario u
                INNER JOIN rol r
                    ON r.id = u.id_rol
                WHERE u.id = ?
                LIMIT 1
            ");

            $stmtUsuario->execute([$id]);
            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                redirectUsuarios('El usuario seleccionado no existe o ya fue eliminado.', 'error');
            }

            $nombreUsuario = trim($usuario['nombre'] . ' ' . $usuario['apellido']);

            /*
                2. Validar si tiene relación como consultor.
            */
            $consultor = obtenerConsultorPorUsuario($pdo, $id);

            /*
                3. Si es consultor, validar si tiene trabajo/proyecto asignado.
                   Si tiene dependencias, NO elimina y muestra mensaje amigable.
            */
            if ($consultor) {
                $idConsultor = (int)$consultor['id_consultor'];
                $dependencias = validarTrabajoAsignadoConsultor($pdo, $idConsultor, $id);

                if (!empty($dependencias)) {
                    redirectUsuarios(
                        'No se puede eliminar a ' . $nombreUsuario . ' porque tiene trabajo asignado: ' . implode(', ', $dependencias) . '. Primero reasigna o libera esas actividades.',
                        'error'
                    );
                }
            }

            /*
                4. Si no tiene trabajo asignado, eliminar en orden correcto:
                   consultor -> perfiltrabajo -> usuario
            */
            $pdo->beginTransaction();

            if ($consultor) {
                eliminarRelacionConsultorSiExiste($pdo, $id);
            }

            $stmtDeleteUsuario = $pdo->prepare("
                DELETE FROM usuario 
                WHERE id = ?
            ");

            $stmtDeleteUsuario->execute([$id]);

            $pdo->commit();

            redirectUsuarios('Usuario eliminado correctamente.');
        }

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1451) {
            redirectUsuarios(
                'No se puede eliminar este usuario porque tiene registros relacionados en el sistema. Primero reasigna o libera sus proyectos, actividades o trabajo asignado.',
                'error'
            );
        }

        redirectUsuarios('No se pudo completar la operación por una restricción de la base de datos.', 'error');
    }
}

/* =========================
   DATOS PARA LA VISTA
========================= */

$roles = $pdo->query("
    SELECT id, nombre 
    FROM rol 
    ORDER BY id
")->fetchAll(PDO::FETCH_ASSOC);

$usuarios = $pdo->query("
    SELECT 
        u.id,
        u.nombre,
        u.apellido,
        u.correo,
        u.id_rol,
        u.estado,
        u.fecha_creacion,
        r.nombre AS rol
    FROM usuario u
    INNER JOIN rol r 
        ON r.id = u.id_rol
    ORDER BY u.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$totalUsuarios = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
")->fetchColumn();

$totalActivos = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE estado = 1
")->fetchColumn();

$totalInactivos = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE estado = 0
")->fetchColumn();

$totalAdministradores = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE id_rol = 1
")->fetchColumn();

$totalConsultores = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE id_rol = 3
")->fetchColumn();

$totalSupervisores = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE id_rol = 2
")->fetchColumn();

$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? 'success';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Specialisterne</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .top-header {
            min-height: 56px;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .content-area {
            padding-top: 22px;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .admin-logout-link {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 6px;
            border-radius: 8px;
        }

        .admin-logout-link:hover {
            background-color: var(--bg-color);
            color: var(--primary-color);
        }

        .usuarios-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f4f7fb 100%);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 22px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
        }

        .usuarios-hero h2 {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .usuarios-hero p {
            color: var(--text-muted);
            margin: 0;
            max-width: 780px;
            font-size: 13px;
        }

        .usuarios-stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .usuarios-stat-card {
            background-color: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 100px;
        }

        .usuarios-stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .usuarios-stat-icon.total {
            background-color: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .usuarios-stat-icon.active {
            background-color: rgba(91, 173, 145, 0.15);
            color: var(--success-color);
        }

        .usuarios-stat-icon.inactive {
            background-color: rgba(214, 64, 69, 0.12);
            color: var(--error-color);
        }

        .usuarios-stat-icon.admins {
            background-color: rgba(44, 62, 80, 0.10);
            color: var(--text-main);
        }

        .usuarios-stat-icon.consultores {
            background-color: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .usuarios-stat-icon.supervisores {
            background-color: rgba(232, 168, 56, 0.15);
            color: var(--warning-color);
        }

        .usuarios-stat-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .usuarios-stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.05;
        }

        .usuarios-stat-info p {
            margin: 6px 0 0 0;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.2;
        }

        .toast-container {
            position: fixed;
            top: 74px;
            right: 24px;
            z-index: 12000;
            width: min(420px, calc(100vw - 32px));
            pointer-events: none;
        }

        .toast-message {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 15px 16px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
            pointer-events: auto;
            opacity: 0;
            transform: translateX(18px) translateY(-6px);
            animation: toastIn 0.28s ease forwards;
        }

        .toast-success {
            border-left: 5px solid var(--success-color);
        }

        .toast-error {
            border-left: 5px solid var(--error-color);
        }

        .toast-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-success .toast-icon {
            background: rgba(91, 173, 145, 0.14);
            color: var(--success-color);
        }

        .toast-error .toast-icon {
            background: rgba(214, 64, 69, 0.12);
            color: var(--error-color);
        }

        .toast-content {
            flex: 1;
            min-width: 0;
            padding-top: 1px;
        }

        .toast-content strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 3px;
        }

        .toast-content span {
            display: block;
            font-size: 13px;
            line-height: 1.4;
            color: var(--text-muted);
        }

        .toast-close {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .toast-close:hover {
            background: var(--bg-color);
            color: var(--text-main);
            border-color: var(--border-color);
        }

        .toast-progress {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            animation: toastProgress 3.4s linear forwards;
        }

        .toast-success .toast-progress {
            background: var(--success-color);
        }

        .toast-error .toast-progress {
            background: var(--error-color);
        }

        .toast-message.toast-hide {
            animation: toastOut 0.24s ease forwards;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(18px) translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateX(0) translateY(0);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0) translateY(0);
            }

            to {
                opacity: 0;
                transform: translateX(18px) translateY(-6px);
            }
        }

        @keyframes toastProgress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        .usuarios-toolbar {
            display: grid;
            grid-template-columns: 1fr 210px 190px auto;
            gap: 12px;
            align-items: center;
            margin-bottom: 18px;
        }

        .usuarios-toolbar-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .result-counter {
            color: var(--text-muted);
            font-size: 12px;
            margin-bottom: 12px;
        }

        .users-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .users-table col.col-user {
            width: 43%;
        }

        .users-table col.col-role {
            width: 14%;
        }

        .users-table col.col-status {
            width: 13%;
        }

        .users-table col.col-date {
            width: 15%;
        }

        .users-table col.col-actions {
            width: 15%;
        }

        .users-table th,
        .users-table td {
            vertical-align: middle;
            padding: 14px 16px;
        }

        .users-table th {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            background-color: #f4f6fa;
        }

        .users-table th:first-child {
            text-align: left;
            padding-left: 58px;
        }

        .users-table td:first-child {
            text-align: left;
            padding-left: 22px;
        }

        .users-table th:nth-child(2),
        .users-table th:nth-child(3),
        .users-table th:nth-child(4),
        .users-table th:nth-child(5),
        .users-table td:nth-child(2),
        .users-table td:nth-child(3),
        .users-table td:nth-child(4),
        .users-table td:nth-child(5) {
            text-align: center;
        }

        .usuario-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .usuario-avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            flex-shrink: 0;
        }

        .usuario-cell strong {
            display: block;
            margin-bottom: 2px;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .usuario-cell small {
            color: var(--text-muted);
            font-size: 12px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .actions-header {
            text-align: center !important;
        }

        .actions-cell {
            text-align: center !important;
            white-space: nowrap;
            vertical-align: middle;
        }

        .action-buttons {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
        }

        .action-btn svg {
            width: 16px;
            height: 16px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.50);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.24s ease, visibility 0.24s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .modal-card {
            width: 100%;
            max-width: 760px;
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.30);
            overflow: hidden;
            opacity: 0;
            transform: translateY(18px) scale(0.96);
            transition: opacity 0.24s ease, transform 0.24s ease;
        }

        .modal-overlay.show .modal-card {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .modal-header {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, #ffffff 0%, #f4f7fb 100%);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .modal-title-area {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .modal-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .modal-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
        }

        .modal-subtitle {
            margin: 5px 0 0 0;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.4;
        }

        .modal-close {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-main);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .modal-close:hover {
            background: var(--bg-color);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .modal-grid .form-group.full {
            grid-column: 1 / -1;
        }

        .modal-footer {
            padding: 18px 24px;
            border-top: 1px solid var(--border-color);
            background: #ffffff;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-help {
            display: block;
            margin-top: 6px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .delete-card,
        .status-card {
            max-width: 560px;
        }

        .delete-header {
            background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%);
        }

        .delete-icon {
            background: rgba(214, 64, 69, 0.12);
            color: var(--error-color);
        }

        .delete-warning-box,
        .status-warning-box {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 18px;
            border-radius: 16px;
        }

        .delete-warning-box {
            border: 1px solid rgba(214, 64, 69, 0.18);
            background: rgba(214, 64, 69, 0.06);
        }

        .status-warning-box {
            border: 1px solid rgba(74, 111, 165, 0.18);
            background: rgba(74, 111, 165, 0.06);
        }

        .delete-warning-icon,
        .status-warning-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .delete-warning-icon {
            background: rgba(214, 64, 69, 0.12);
            color: var(--error-color);
        }

        .status-warning-icon {
            background: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .delete-warning-box h3,
        .status-warning-box h3 {
            margin: 0 0 6px 0;
            font-size: 17px;
            color: var(--text-main);
        }

        .delete-warning-box p,
        .status-warning-box p {
            margin: 0;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .delete-warning-box small,
        .status-warning-box small {
            display: block;
            margin-top: 8px;
            color: var(--text-muted);
            font-size: 12px;
        }

        .status-preview {
            margin-top: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            font-size: 13px;
        }

        .status-preview span {
            color: var(--text-muted);
        }

        .status-preview strong {
            color: var(--text-main);
        }

        .btn-danger {
            background: #ef4444;
            color: #ffffff;
            border: 1px solid #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        .empty-row {
            display: none;
        }

        @media (max-width: 1550px) {
            .usuarios-stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1200px) {
            .usuarios-toolbar {
                grid-template-columns: 1fr;
            }

            .usuarios-toolbar-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 800px) {
            .usuarios-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .usuarios-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .modal-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .toast-container {
                top: 70px;
                right: 16px;
                left: 16px;
                width: auto;
            }
        }

        @media (max-width: 650px) {
            .usuarios-stats-grid {
                grid-template-columns: 1fr;
            }

            .modal-card {
                max-height: 92vh;
                overflow-y: auto;
            }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Specialisterne</h2>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php">
                <i data-lucide="layout-dashboard"></i>
                Dashboard
            </a>

            <a href="usuarios.php" class="active">
                <i data-lucide="users"></i>
                Usuarios
            </a>

            <a href="consultores.php">
                <i data-lucide="user-check"></i>
                Consultores
            </a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="top-header">
            <div></div>

            <div class="user-info">
                <span class="role-badge">
                    <?= e(ucfirst($rolConectado)) ?>
                </span>

                <span>
                    <?= e($nombreCompletoConectado) ?>
                </span>

                <div class="avatar">
                    <?= e($inicialesConectado) ?>
                </div>

                <a 
                    id="logoutBtn"
                    href="../../index.php"
                    title="Cerrar sesión"
                    class="admin-logout-link"
                >
                    <i data-lucide="log-out" size="18"></i>
                </a>
            </div>
        </header>

        <div class="content-area">

            <?php if ($msg): ?>
                <div class="toast-container" id="toastContainer">
                    <div class="toast-message <?= $type === 'error' ? 'toast-error' : 'toast-success' ?>">
                        <div class="toast-icon">
                            <i data-lucide="<?= $type === 'error' ? 'alert-circle' : 'check-circle' ?>" size="20"></i>
                        </div>

                        <div class="toast-content">
                            <strong>
                                <?= $type === 'error' ? 'Acción no realizada' : 'Operación completada' ?>
                            </strong>

                            <span>
                                <?= e($msg) ?>
                            </span>
                        </div>

                        <button type="button" class="toast-close" onclick="closeToast(this)">
                            <i data-lucide="x" size="16"></i>
                        </button>

                        <div class="toast-progress"></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <div class="page-title">
                    <div class="breadcrumb">Admin / Usuarios</div>
                    <h1>Gestión de Usuarios</h1>
                </div>

                <button class="btn btn-primary" onclick="openUserCreate()" type="button">
                    <i data-lucide="user-plus" size="16"></i>
                    Nuevo Usuario
                </button>
            </div>

            <div class="usuarios-hero">
                <div>
                    <h2>Administración de usuarios del sistema</h2>
                    <p>
                        Desde esta sección puedes crear usuarios, actualizar sus datos, cambiar roles, activar o inactivar accesos y consultar el estado general de cuentas.
                    </p>
                </div>

                <div class="usuarios-toolbar-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i data-lucide="layout-dashboard" size="16"></i>
                        Dashboard
                    </a>
                </div>
            </div>

            <div class="usuarios-stats-grid">
                <div class="usuarios-stat-card">
                    <div class="usuarios-stat-icon total">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="usuarios-stat-info">
                        <h3><?= e($totalUsuarios) ?></h3>
                        <p>Total Usuarios</p>
                    </div>
                </div>

                <div class="usuarios-stat-card">
                    <div class="usuarios-stat-icon active">
                        <i data-lucide="user-check"></i>
                    </div>
                    <div class="usuarios-stat-info">
                        <h3><?= e($totalActivos) ?></h3>
                        <p>Usuarios Activos</p>
                    </div>
                </div>

                <div class="usuarios-stat-card">
                    <div class="usuarios-stat-icon inactive">
                        <i data-lucide="user-x"></i>
                    </div>
                    <div class="usuarios-stat-info">
                        <h3><?= e($totalInactivos) ?></h3>
                        <p>Usuarios Inactivos</p>
                    </div>
                </div>

                <div class="usuarios-stat-card">
                    <div class="usuarios-stat-icon admins">
                        <i data-lucide="shield"></i>
                    </div>
                    <div class="usuarios-stat-info">
                        <h3><?= e($totalAdministradores) ?></h3>
                        <p>Usuarios Administradores</p>
                    </div>
                </div>

                <div class="usuarios-stat-card">
                    <div class="usuarios-stat-icon consultores">
                        <i data-lucide="user-round-check"></i>
                    </div>
                    <div class="usuarios-stat-info">
                        <h3><?= e($totalConsultores) ?></h3>
                        <p>Usuarios Consultores</p>
                    </div>
                </div>

                <div class="usuarios-stat-card">
                    <div class="usuarios-stat-icon supervisores">
                        <i data-lucide="user-cog"></i>
                    </div>
                    <div class="usuarios-stat-info">
                        <h3><?= e($totalSupervisores) ?></h3>
                        <p>Usuarios Supervisores</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Lista de Usuarios</h2>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                            Consulta, filtra y administra los usuarios registrados.
                        </p>
                    </div>
                </div>

                <div class="usuarios-toolbar">
                    <input 
                        type="text" 
                        id="searchInput"
                        class="form-control" 
                        placeholder="Buscar por nombre, apellido o correo..."
                        autocomplete="off"
                    >

                    <select id="roleFilter" class="form-control">
                        <option value="">Todos los roles</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= e(strtolower($r['nombre'])) ?>">
                                <?= e(ucfirst($r['nombre'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="statusFilter" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>

                    <div class="usuarios-toolbar-actions">
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                            Limpiar
                        </button>
                    </div>
                </div>

                <div class="result-counter">
                    Mostrando <strong id="visibleCount">0</strong> de <strong id="totalCount"><?= count($usuarios) ?></strong> usuario(s)
                </div>

                <div class="table-container">
                    <table class="users-table">
                        <colgroup>
                            <col class="col-user">
                            <col class="col-role">
                            <col class="col-status">
                            <col class="col-date">
                            <col class="col-actions">
                        </colgroup>

                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="actions-header">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="usersTableBody">
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($usuarios as $u): ?>
                            <?php
                                $fullName = trim($u['nombre'] . ' ' . $u['apellido']);
                                $searchData = strtolower($fullName . ' ' . $u['correo'] . ' ' . $u['rol']);
                            ?>

                            <tr 
                                class="user-row"
                                data-search="<?= e($searchData) ?>"
                                data-role="<?= e(strtolower($u['rol'])) ?>"
                                data-status="<?= e($u['estado']) ?>"
                            >
                                <td>
                                    <div class="usuario-cell">
                                        <div class="usuario-avatar-small">
                                            <?= e(inicialUsuario($u['nombre'], $u['apellido'])) ?>
                                        </div>

                                        <div>
                                            <strong>
                                                <?= e($fullName) ?>
                                            </strong>

                                            <small>
                                                <?= e($u['correo']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?= badgeRol($u['rol']) ?>
                                </td>

                                <td>
                                    <?= badgeEstado($u['estado']) ?>
                                </td>

                                <td>
                                    <?= e(formatoFecha($u['fecha_creacion'])) ?>
                                </td>

                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <button 
                                            class="btn btn-secondary action-btn" 
                                            type="button" 
                                            title="Editar usuario"
                                            onclick="openUserEdit(this)"
                                            data-id="<?= e($u['id']) ?>"
                                            data-nombre="<?= e($u['nombre']) ?>"
                                            data-apellido="<?= e($u['apellido']) ?>"
                                            data-correo="<?= e($u['correo']) ?>"
                                            data-rol="<?= e($u['id_rol']) ?>"
                                            data-estado="<?= e($u['estado']) ?>"
                                        >
                                            <i data-lucide="edit" size="14"></i>
                                        </button>

                                        <button 
                                            class="btn btn-secondary action-btn" 
                                            type="button"
                                            title="Activar/Inactivar usuario"
                                            onclick="openStatusModal(this)"
                                            data-id="<?= e($u['id']) ?>"
                                            data-nombre="<?= e($fullName) ?>"
                                            data-correo="<?= e($u['correo']) ?>"
                                            data-estado="<?= e($u['estado']) ?>"
                                        >
                                            <i data-lucide="refresh-cw" size="14"></i>
                                        </button>

                                        <button 
                                            class="btn btn-danger action-btn" 
                                            type="button"
                                            title="Eliminar usuario"
                                            onclick="openDeleteModal(this)"
                                            data-id="<?= e($u['id']) ?>"
                                            data-nombre="<?= e($fullName) ?>"
                                            data-correo="<?= e($u['correo']) ?>"
                                        >
                                            <i data-lucide="trash-2" size="14"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr id="emptyFilterRow" class="empty-row">
                            <td colspan="5" class="text-center">
                                No se encontraron usuarios con los filtros aplicados.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- MODAL CREAR / EDITAR USUARIO -->
<div class="modal-overlay" id="userModal">
    <div class="modal-card">
        <form method="POST" id="userModalForm">
            <input type="hidden" name="action" id="modalAction" value="create">
            <input type="hidden" name="id" id="modalUserId">

            <div class="modal-header">
                <div class="modal-title-area">
                    <div class="modal-icon">
                        <i data-lucide="user-plus"></i>
                    </div>

                    <div>
                        <h2 class="modal-title" id="modalTitle">Crear Nuevo Usuario</h2>
                        <p class="modal-subtitle" id="modalSubtitle">
                            Registra un nuevo acceso para administrador, supervisor o consultor.
                        </p>
                    </div>
                </div>

                <button type="button" class="modal-close" onclick="closeUserModal()">
                    <i data-lucide="x" size="18"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="modal-grid">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input name="nombre" type="text" class="form-control" id="modalNombre" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <input name="apellido" type="text" class="form-control" id="modalApellido" required>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Correo Electrónico</label>
                        <input name="correo" type="email" class="form-control" id="modalCorreo" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" id="modalPasswordLabel">Contraseña</label>
                        <input name="contrasenia" type="password" class="form-control" id="modalContrasenia">
                        <small class="form-help" id="modalPasswordHelp">
                            Este valor será utilizado para iniciar sesión.
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rol</label>
                        <select name="id_rol" class="form-control" id="modalRol" required>
                            <option value="">Seleccionar rol...</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= e($r['id']) ?>">
                                    <?= e(ucfirst($r['nombre'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-control" id="modalEstado">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeUserModal()">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CAMBIAR ESTADO -->
<div class="modal-overlay status-overlay" id="statusModal">
    <div class="modal-card status-card">
        <form method="POST" id="statusUserForm">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" id="statusUserId">

            <div class="modal-header status-header">
                <div class="modal-title-area">
                    <div class="modal-icon status-icon">
                        <i data-lucide="refresh-cw"></i>
                    </div>

                    <div>
                        <h2 class="modal-title">Cambiar Estado</h2>
                        <p class="modal-subtitle">
                            Esta acción cambiará el estado actual del usuario seleccionado.
                        </p>
                    </div>
                </div>

                <button type="button" class="modal-close" onclick="closeStatusModal()">
                    <i data-lucide="x" size="18"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="status-warning-box">
                    <div class="status-warning-icon">
                        <i data-lucide="refresh-cw" size="22"></i>
                    </div>

                    <div>
                        <h3 id="statusModalQuestion">¿Deseas cambiar el estado de este usuario?</h3>

                        <p>
                            Se actualizará el estado del usuario 
                            <strong id="statusUserName">Usuario seleccionado</strong>.
                        </p>

                        <small id="statusUserEmail"></small>

                        <div class="status-preview">
                            <span>Estado actual:</span>
                            <strong id="statusCurrentValue">Activo</strong>
                            <i data-lucide="arrow-right" size="16"></i>
                            <span>Nuevo estado:</span>
                            <strong id="statusNewValue">Inactivo</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary" onclick="clearFiltersBeforeSubmit()">
                    Sí, cambiar estado
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CONFIRMAR ELIMINACIÓN -->
<div class="modal-overlay delete-overlay" id="deleteModal">
    <div class="modal-card delete-card">
        <form method="POST" id="deleteUserForm">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteUserId">

            <div class="modal-header delete-header">
                <div class="modal-title-area">
                    <div class="modal-icon delete-icon">
                        <i data-lucide="trash-2"></i>
                    </div>

                    <div>
                        <h2 class="modal-title">Eliminar Usuario</h2>
                        <p class="modal-subtitle">
                            Si este usuario tiene perfil de consultor, también se eliminará su relación de consultor siempre que no tenga trabajo asignado.
                        </p>
                    </div>
                </div>

                <button type="button" class="modal-close" onclick="closeDeleteModal()">
                    <i data-lucide="x" size="18"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="delete-warning-box">
                    <div class="delete-warning-icon">
                        <i data-lucide="alert-triangle" size="22"></i>
                    </div>

                    <div>
                        <h3>¿Deseas eliminar este usuario?</h3>
                        <p>
                            Se eliminará el usuario 
                            <strong id="deleteUserName">Usuario seleccionado</strong>.
                            Si tiene proyectos, actividades o trabajo asignado, el sistema no permitirá eliminarlo.
                        </p>

                        <small id="deleteUserEmail"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-danger" onclick="clearFiltersBeforeSubmit()">
                    Sí, eliminar usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function normalizeText(text) {
        return (text || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function applyFilters() {
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const statusFilter = document.getElementById('statusFilter');
        const visibleCount = document.getElementById('visibleCount');
        const emptyFilterRow = document.getElementById('emptyFilterRow');

        const searchValue = normalizeText(searchInput.value);
        const roleValue = normalizeText(roleFilter.value);
        const statusValue = statusFilter.value;

        const rows = document.querySelectorAll('.user-row');
        let count = 0;

        rows.forEach(row => {
            const rowSearch = normalizeText(row.dataset.search);
            const rowRole = normalizeText(row.dataset.role);
            const rowStatus = row.dataset.status;

            const matchSearch = searchValue === '' || rowSearch.includes(searchValue);
            const matchRole = roleValue === '' || rowRole === roleValue;
            const matchStatus = statusValue === '' || rowStatus === statusValue;

            if (matchSearch && matchRole && matchStatus) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        visibleCount.textContent = count;

        if (emptyFilterRow) {
            emptyFilterRow.style.display = count === 0 ? '' : 'none';
        }
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('statusFilter').value = '';
        applyFilters();
    }

    function clearFiltersBeforeSubmit() {
        clearFilters();
    }

    function openUserCreate() {
        const modal = document.getElementById('userModal');

        document.getElementById('userModalForm').reset();
        document.getElementById('modalAction').value = 'create';
        document.getElementById('modalUserId').value = '';

        document.getElementById('modalTitle').textContent = 'Crear Nuevo Usuario';
        document.getElementById('modalSubtitle').textContent = 'Registra un nuevo acceso para administrador, supervisor o consultor.';
        document.getElementById('modalSubmitBtn').textContent = 'Guardar Usuario';

        document.getElementById('modalPasswordLabel').textContent = 'Contraseña';
        document.getElementById('modalPasswordHelp').textContent = 'Este valor será utilizado para iniciar sesión.';
        document.getElementById('modalContrasenia').required = true;

        document.getElementById('modalEstado').value = '1';

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            document.getElementById('modalNombre').focus();
        }, 240);
    }

    function openUserEdit(button) {
        const modal = document.getElementById('userModal');

        document.getElementById('userModalForm').reset();
        document.getElementById('modalAction').value = 'update';
        document.getElementById('modalUserId').value = button.dataset.id || '';

        document.getElementById('modalNombre').value = button.dataset.nombre || '';
        document.getElementById('modalApellido').value = button.dataset.apellido || '';
        document.getElementById('modalCorreo').value = button.dataset.correo || '';
        document.getElementById('modalRol').value = button.dataset.rol || '';
        document.getElementById('modalEstado').value = button.dataset.estado || '1';

        document.getElementById('modalTitle').textContent = 'Editar Usuario';
        document.getElementById('modalSubtitle').textContent = 'Actualiza la información del usuario seleccionado.';
        document.getElementById('modalSubmitBtn').textContent = 'Guardar Cambios';

        document.getElementById('modalPasswordLabel').textContent = 'Nueva Contraseña';
        document.getElementById('modalPasswordHelp').textContent = 'Déjalo en blanco si no deseas cambiar la contraseña.';
        document.getElementById('modalContrasenia').required = false;

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            document.getElementById('modalNombre').focus();
        }, 240);
    }

    function closeUserModal() {
        const modal = document.getElementById('userModal');

        if (modal) {
            modal.classList.remove('show');
        }

        document.body.style.overflow = '';
    }

    function openStatusModal(button) {
        const modal = document.getElementById('statusModal');

        const estadoActual = button.dataset.estado || '1';
        const estadoActualTexto = estadoActual === '1' ? 'Activo' : 'Inactivo';
        const nuevoEstadoTexto = estadoActual === '1' ? 'Inactivo' : 'Activo';

        document.getElementById('statusUserId').value = button.dataset.id || '';
        document.getElementById('statusUserName').textContent = button.dataset.nombre || 'Usuario seleccionado';
        document.getElementById('statusUserEmail').textContent = button.dataset.correo || '';
        document.getElementById('statusCurrentValue').textContent = estadoActualTexto;
        document.getElementById('statusNewValue').textContent = nuevoEstadoTexto;

        document.getElementById('statusModalQuestion').textContent =
            estadoActual === '1'
                ? '¿Deseas inactivar este usuario?'
                : '¿Deseas activar este usuario?';

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        lucide.createIcons();
    }

    function closeStatusModal() {
        const modal = document.getElementById('statusModal');

        if (modal) {
            modal.classList.remove('show');
        }

        document.body.style.overflow = '';
    }

    function openDeleteModal(button) {
        const modal = document.getElementById('deleteModal');

        document.getElementById('deleteUserId').value = button.dataset.id || '';
        document.getElementById('deleteUserName').textContent = button.dataset.nombre || 'Usuario seleccionado';
        document.getElementById('deleteUserEmail').textContent = button.dataset.correo || '';

        modal.classList.add('show');
        document.body.style.overflow = '';
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');

        if (modal) {
            modal.classList.remove('show');
        }

        document.body.style.overflow = '';
    }

    function closeToast(button) {
        const toast = button.closest('.toast-message');

        if (!toast) {
            return;
        }

        toast.classList.add('toast-hide');

        setTimeout(() => {
            const container = toast.closest('.toast-container');
            toast.remove();

            if (container && container.children.length === 0) {
                container.remove();
            }
        }, 260);
    }

    function autoHideAlertsAndCleanUrl() {
        const toasts = document.querySelectorAll('.toast-message');

        if (toasts.length > 0) {
            setTimeout(() => {
                toasts.forEach(toast => {
                    toast.classList.add('toast-hide');

                    setTimeout(() => {
                        const container = toast.closest('.toast-container');
                        toast.remove();

                        if (container && container.children.length === 0) {
                            container.remove();
                        }
                    }, 260);
                });
            }, 3600);
        }

        const url = new URL(window.location.href);

        if (url.searchParams.has('msg') || url.searchParams.has('type')) {
            url.searchParams.delete('msg');
            url.searchParams.delete('type');

            const cleanUrl = url.pathname + (url.search ? url.search : '');
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeUserModal();
            closeStatusModal();
            closeDeleteModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const statusFilter = document.getElementById('statusFilter');

        const userModal = document.getElementById('userModal');
        const statusModal = document.getElementById('statusModal');
        const deleteModal = document.getElementById('deleteModal');

        searchInput.addEventListener('input', applyFilters);
        roleFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);

        if (userModal) {
            userModal.addEventListener('click', function (event) {
                if (event.target === this) {
                    closeUserModal();
                }
            });
        }

        if (statusModal) {
            statusModal.addEventListener('click', function (event) {
                if (event.target === this) {
                    closeStatusModal();
                }
            });
        }

        if (deleteModal) {
            deleteModal.addEventListener('click', function (event) {
                if (event.target === this) {
                    closeDeleteModal();
                }
            });
        }

        applyFilters();
        autoHideAlertsAndCleanUrl();
        lucide.createIcons();
    });
</script>

<script src="../../js/app.js"></script>

</body>
</html>