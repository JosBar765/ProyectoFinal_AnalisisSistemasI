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

function redirectConsultores($msg = null, $type = 'success')
{
    $query = $msg ? ('?' . http_build_query([
        'msg' => $msg,
        'type' => $type
    ])) : '';

    header('Location: consultores.php' . $query);
    exit;
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

function badgeEstado($estado)
{
    return ((int)$estado === 1)
        ? '<span class="badge badge-success">Activo</span>'
        : '<span class="badge badge-error">Inactivo</span>';
}

function renderSkills($habilidades)
{
    $habilidades = trim((string)$habilidades);

    if ($habilidades === '') {
        return '<span class="text-muted-small">Sin habilidades registradas</span>';
    }

    $skills = array_filter(array_map('trim', explode(',', $habilidades)));
    $html = '';

    foreach ($skills as $skill) {
        $html .= '<span class="badge badge-neutral skill-badge">' . e($skill) . '</span>';
    }

    return $html;
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

function countTableDependency($pdo, $table, $column, $value)
{
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
        ]
    ];

    foreach ($validacionesConsultor as $v) {
        $cantidad = countTableDependency($pdo, $v['tabla'], $v['columna'], $idConsultor);

        if ($cantidad > 0) {
            $dependencias[] = $cantidad . ' ' . $v['nombre'];
        }
    }

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
        INNER JOIN rol r 
            ON r.id = u.id_rol
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
   ACCIONES POST
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {

        /* =========================
           ASIGNAR USUARIO EXISTENTE COMO CONSULTOR
        ========================= */
        if ($action === 'assign_existing') {
            $idUsuario = (int)($_POST['id_usuario'] ?? 0);
            $perfilTrabajo = trim($_POST['perfil_trabajo'] ?? '');
            $habilidades = trim($_POST['habilidades'] ?? '');

            if ($idUsuario <= 0 || $perfilTrabajo === '') {
                redirectConsultores('Seleccione un usuario consultor y escriba el perfil de trabajo.', 'error');
            }

            $stmtUsuario = $pdo->prepare("
                SELECT 
                    u.id,
                    u.nombre,
                    u.apellido,
                    u.estado,
                    r.nombre AS rol,
                    c.id AS consultor_id
                FROM usuario u
                INNER JOIN rol r 
                    ON r.id = u.id_rol
                LEFT JOIN consultor c 
                    ON c.id_usuario = u.id
                WHERE u.id = ?
                LIMIT 1
            ");

            $stmtUsuario->execute([$idUsuario]);
            $usuarioValidado = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            if (!$usuarioValidado) {
                redirectConsultores('El usuario seleccionado no existe.', 'error');
            }

            if (strtolower($usuarioValidado['rol']) !== 'consultor') {
                redirectConsultores('El usuario seleccionado no tiene rol de consultor.', 'error');
            }

            if ((int)$usuarioValidado['estado'] !== 1) {
                redirectConsultores('El usuario seleccionado está inactivo. Actívelo antes de asignarlo como consultor.', 'error');
            }

            if (!empty($usuarioValidado['consultor_id'])) {
                redirectConsultores('Este usuario ya tiene perfil de consultor asignado.', 'error');
            }

            $pdo->beginTransaction();

            $stmtPerfil = $pdo->prepare("
                INSERT INTO perfiltrabajo
                    (perfil_trabajo, habilidades)
                VALUES
                    (?, ?)
            ");

            $stmtPerfil->execute([
                $perfilTrabajo,
                $habilidades
            ]);

            $idPerfil = (int)$pdo->lastInsertId();

            $stmtConsultor = $pdo->prepare("
                INSERT INTO consultor
                    (id_usuario, id_perfil_trabajo)
                VALUES
                    (?, ?)
            ");

            $stmtConsultor->execute([
                $idUsuario,
                $idPerfil
            ]);

            $pdo->commit();

            redirectConsultores('Usuario asignado como consultor correctamente.');
        }

        /* =========================
           ACTUALIZAR PERFIL
        ========================= */
        if ($action === 'update') {
            $idPerfil = (int)($_POST['id_perfil_trabajo'] ?? 0);
            $perfilTrabajo = trim($_POST['perfil_trabajo'] ?? '');
            $habilidades = trim($_POST['habilidades'] ?? '');

            if ($idPerfil <= 0 || $perfilTrabajo === '') {
                redirectConsultores('Datos inválidos para actualizar el perfil del consultor.', 'error');
            }

            $stmt = $pdo->prepare("
                UPDATE perfiltrabajo
                SET 
                    perfil_trabajo = ?,
                    habilidades = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $perfilTrabajo,
                $habilidades,
                $idPerfil
            ]);

            redirectConsultores('Perfil de consultor actualizado correctamente.');
        }

        /* =========================
           ACTIVAR / INACTIVAR
        ========================= */
        if ($action === 'toggle') {
            $idUsuario = (int)($_POST['id_usuario'] ?? 0);

            if ($idUsuario <= 0) {
                redirectConsultores('Usuario consultor inválido.', 'error');
            }

            if ($idUsuario === (int)$_SESSION["user"]["id"]) {
                redirectConsultores('No puedes cambiar el estado de tu propio usuario.', 'error');
            }

            $stmtValidar = $pdo->prepare("
                SELECT 
                    u.id,
                    r.nombre AS rol
                FROM usuario u
                INNER JOIN rol r 
                    ON r.id = u.id_rol
                INNER JOIN consultor c 
                    ON c.id_usuario = u.id
                WHERE u.id = ?
                LIMIT 1
            ");

            $stmtValidar->execute([$idUsuario]);
            $consultorValidado = $stmtValidar->fetch(PDO::FETCH_ASSOC);

            if (!$consultorValidado) {
                redirectConsultores('El consultor seleccionado no existe.', 'error');
            }

            if (strtolower($consultorValidado['rol']) !== 'consultor') {
                redirectConsultores('El usuario seleccionado ya no tiene rol de consultor.', 'error');
            }

            $stmt = $pdo->prepare("
                UPDATE usuario
                SET estado = CASE WHEN estado = 1 THEN 0 ELSE 1 END
                WHERE id = ?
            ");

            $stmt->execute([$idUsuario]);

            redirectConsultores('Estado del consultor actualizado correctamente.');
        }

        /* =========================
           DESASIGNAR CONSULTOR
           No permite desasignar si tiene trabajo asignado.
        ========================= */
        if ($action === 'unassign') {
            $idConsultor = (int)($_POST['id'] ?? 0);

            if ($idConsultor <= 0) {
                redirectConsultores('Consultor inválido.', 'error');
            }

            $stmtValidar = $pdo->prepare("
                SELECT 
                    c.id,
                    c.id_usuario,
                    c.id_perfil_trabajo,
                    u.nombre,
                    u.apellido,
                    u.correo
                FROM consultor c
                INNER JOIN usuario u 
                    ON u.id = c.id_usuario
                WHERE c.id = ?
                LIMIT 1
            ");

            $stmtValidar->execute([$idConsultor]);
            $consultor = $stmtValidar->fetch(PDO::FETCH_ASSOC);

            if (!$consultor) {
                redirectConsultores('El consultor seleccionado no existe o ya fue desasignado.', 'error');
            }

            $idUsuario = (int)$consultor['id_usuario'];
            $idPerfilTrabajo = (int)$consultor['id_perfil_trabajo'];
            $nombreConsultor = trim($consultor['nombre'] . ' ' . $consultor['apellido']);

            $dependencias = validarTrabajoAsignadoConsultor($pdo, $idConsultor, $idUsuario);

            if (!empty($dependencias)) {
                redirectConsultores(
                    'No se puede desasignar a ' . $nombreConsultor . ' porque tiene trabajo asignado: ' . implode(', ', $dependencias) . '. Primero reasigna o libera esas actividades.',
                    'error'
                );
            }

            $pdo->beginTransaction();

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
                    $stmtEliminarPerfil = $pdo->prepare("
                        DELETE FROM perfiltrabajo
                        WHERE id = ?
                    ");

                    $stmtEliminarPerfil->execute([$idPerfilTrabajo]);
                }
            }

            $pdo->commit();

            redirectConsultores('Usuario desasignado del perfil de consultor correctamente.');
        }

        /* =========================
           ELIMINAR CONSULTOR
           No permite eliminar si tiene trabajo asignado.
        ========================= */
        if ($action === 'delete') {
            $idConsultor = (int)($_POST['id'] ?? 0);
            $idUsuarioPost = (int)($_POST['id_usuario'] ?? 0);

            if ($idConsultor <= 0 || $idUsuarioPost <= 0) {
                redirectConsultores('Consultor inválido.', 'error');
            }

            if ($idUsuarioPost === (int)$_SESSION["user"]["id"]) {
                redirectConsultores('No puedes eliminar tu propio usuario.', 'error');
            }

            $stmtConsultor = $pdo->prepare("
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
                WHERE c.id = ?
                LIMIT 1
            ");

            $stmtConsultor->execute([$idConsultor]);
            $consultor = $stmtConsultor->fetch(PDO::FETCH_ASSOC);

            if (!$consultor) {
                redirectConsultores('El consultor seleccionado no existe o ya fue eliminado.', 'error');
            }

            $idUsuarioReal = (int)$consultor['id_usuario'];
            $idPerfilReal = (int)$consultor['id_perfil_trabajo'];
            $nombreConsultor = trim($consultor['nombre'] . ' ' . $consultor['apellido']);

            if ($idUsuarioReal === (int)$_SESSION["user"]["id"]) {
                redirectConsultores('No puedes eliminar tu propio usuario.', 'error');
            }

            $dependencias = validarTrabajoAsignadoConsultor($pdo, $idConsultor, $idUsuarioReal);

            if (!empty($dependencias)) {
                redirectConsultores(
                    'No se puede eliminar a ' . $nombreConsultor . ' porque tiene trabajo asignado: ' . implode(', ', $dependencias) . '. Primero reasigna o libera esas actividades.',
                    'error'
                );
            }

            $pdo->beginTransaction();

            $stmtDeleteConsultor = $pdo->prepare("
                DELETE FROM consultor
                WHERE id = ?
            ");

            $stmtDeleteConsultor->execute([$idConsultor]);

            if ($idPerfilReal > 0) {
                $stmtUsoPerfil = $pdo->prepare("
                    SELECT COUNT(*)
                    FROM consultor
                    WHERE id_perfil_trabajo = ?
                ");

                $stmtUsoPerfil->execute([$idPerfilReal]);
                $perfilEnUso = (int)$stmtUsoPerfil->fetchColumn();

                if ($perfilEnUso === 0) {
                    $stmtDeletePerfil = $pdo->prepare("
                        DELETE FROM perfiltrabajo
                        WHERE id = ?
                    ");

                    $stmtDeletePerfil->execute([$idPerfilReal]);
                }
            }

            $stmtDeleteUsuario = $pdo->prepare("
                DELETE FROM usuario
                WHERE id = ?
            ");

            $stmtDeleteUsuario->execute([$idUsuarioReal]);

            $pdo->commit();

            redirectConsultores('Consultor y usuario eliminados correctamente.');
        }

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        redirectConsultores('Error DB: ' . $e->getMessage(), 'error');
    }
}

/* =========================
   DATOS PARA LA VISTA
========================= */

$usuariosDisponibles = $pdo->query("
    SELECT 
        u.id,
        u.nombre,
        u.apellido,
        u.correo,
        u.estado
    FROM usuario u
    INNER JOIN rol r 
        ON r.id = u.id_rol
    LEFT JOIN consultor c 
        ON c.id_usuario = u.id
    WHERE LOWER(r.nombre) = 'consultor'
      AND u.estado = 1
      AND c.id IS NULL
    ORDER BY u.nombre, u.apellido
")->fetchAll(PDO::FETCH_ASSOC);

$consultores = $pdo->query("
    SELECT 
        c.id,
        c.id_usuario,
        c.id_perfil_trabajo,
        u.nombre,
        u.apellido,
        u.correo,
        u.estado,
        u.fecha_creacion,
        r.nombre AS rol,
        p.perfil_trabajo,
        p.habilidades
    FROM consultor c
    INNER JOIN usuario u 
        ON u.id = c.id_usuario
    INNER JOIN rol r 
        ON r.id = u.id_rol
    INNER JOIN perfiltrabajo p 
        ON p.id = c.id_perfil_trabajo
    WHERE LOWER(r.nombre) = 'consultor'
    ORDER BY u.nombre, u.apellido
")->fetchAll(PDO::FETCH_ASSOC);

$totalConsultores = $pdo->query("
    SELECT COUNT(*)
    FROM consultor c
    INNER JOIN usuario u 
        ON u.id = c.id_usuario
    INNER JOIN rol r 
        ON r.id = u.id_rol
    WHERE LOWER(r.nombre) = 'consultor'
")->fetchColumn();

$totalConsultoresActivos = $pdo->query("
    SELECT COUNT(*)
    FROM consultor c
    INNER JOIN usuario u 
        ON u.id = c.id_usuario
    INNER JOIN rol r 
        ON r.id = u.id_rol
    WHERE LOWER(r.nombre) = 'consultor'
      AND u.estado = 1
")->fetchColumn();

$totalConsultoresInactivos = $pdo->query("
    SELECT COUNT(*)
    FROM consultor c
    INNER JOIN usuario u 
        ON u.id = c.id_usuario
    INNER JOIN rol r 
        ON r.id = u.id_rol
    WHERE LOWER(r.nombre) = 'consultor'
      AND u.estado = 0
")->fetchColumn();

$totalUsuariosDisponibles = count($usuariosDisponibles);

$totalPerfiles = $pdo->query("
    SELECT COUNT(*)
    FROM consultor c
    INNER JOIN usuario u 
        ON u.id = c.id_usuario
    INNER JOIN rol r 
        ON r.id = u.id_rol
    INNER JOIN perfiltrabajo p 
        ON p.id = c.id_perfil_trabajo
    WHERE LOWER(r.nombre) = 'consultor'
")->fetchColumn();

$totalConHabilidades = $pdo->query("
    SELECT COUNT(*)
    FROM consultor c
    INNER JOIN usuario u 
        ON u.id = c.id_usuario
    INNER JOIN rol r 
        ON r.id = u.id_rol
    INNER JOIN perfiltrabajo p 
        ON p.id = c.id_perfil_trabajo
    WHERE LOWER(r.nombre) = 'consultor'
      AND IFNULL(TRIM(p.habilidades), '') <> ''
")->fetchColumn();

$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? 'success';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Consultores - Specialisterne</title>

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

        .consultores-hero {
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

        .consultores-hero h2 {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .consultores-hero p {
            color: var(--text-muted);
            margin: 0;
            max-width: 920px;
            font-size: 13px;
        }

        .consultores-stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .consultores-stat-card {
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

        .consultores-stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .consultores-stat-icon.total {
            background-color: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .consultores-stat-icon.active {
            background-color: rgba(91, 173, 145, 0.15);
            color: var(--success-color);
        }

        .consultores-stat-icon.inactive {
            background-color: rgba(214, 64, 69, 0.12);
            color: var(--error-color);
        }

        .consultores-stat-icon.available {
            background-color: rgba(232, 168, 56, 0.15);
            color: var(--warning-color);
        }

        .consultores-stat-icon.profile {
            background-color: rgba(44, 62, 80, 0.10);
            color: var(--text-main);
        }

        .consultores-stat-icon.skills {
            background-color: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .consultores-stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.05;
        }

        .consultores-stat-info p {
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

        .consultores-toolbar {
            display: grid;
            grid-template-columns: 1fr 210px 190px auto;
            gap: 12px;
            align-items: center;
            margin-bottom: 18px;
        }

        .consultores-toolbar-actions {
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

        .consultores-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .consultores-table col.col-consultor {
            width: 28%;
        }

        .consultores-table col.col-status {
            width: 12%;
        }

        .consultores-table col.col-profile {
            width: 26%;
        }

        .consultores-table col.col-skills {
            width: 22%;
        }

        .consultores-table col.col-actions {
            width: 12%;
        }

        .consultores-table th,
        .consultores-table td {
            vertical-align: middle;
            padding: 14px 16px;
        }

        .consultores-table th {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            background-color: #f4f6fa;
        }

        .consultores-table th:first-child {
            text-align: left;
            padding-left: 58px;
        }

        .consultores-table td:first-child {
            text-align: left;
            padding-left: 22px;
        }

        .consultores-table th:nth-child(2),
        .consultores-table th:nth-child(5),
        .consultores-table td:nth-child(2),
        .consultores-table td:nth-child(5) {
            text-align: center;
        }

        .consultor-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .consultor-avatar-small {
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

        .consultor-cell strong {
            display: block;
            margin-bottom: 2px;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .consultor-cell small {
            color: var(--text-muted);
            font-size: 12px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-text {
            color: var(--text-main);
            font-size: 13px;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .skills-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .skill-badge {
            margin: 0;
        }

        .text-muted-small {
            font-size: 12px;
            color: var(--text-muted);
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

        .btn-danger {
            background: #ef4444;
            color: #ffffff;
            border: 1px solid #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        .btn-warning-custom {
            background: #fff7e6;
            color: #d97706;
            border: 1px solid #fde3ac;
        }

        .btn-warning-custom:hover {
            background: #ffedd5;
            color: #b45309;
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

        .readonly-input {
            background: var(--bg-color);
            cursor: not-allowed;
        }

        .status-card,
        .unassign-card,
        .delete-card {
            max-width: 560px;
        }

        .status-header {
            background: linear-gradient(135deg, #ffffff 0%, #f4f7fb 100%);
        }

        .unassign-header {
            background: linear-gradient(135deg, #ffffff 0%, #fff7ed 100%);
        }

        .delete-header {
            background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%);
        }

        .status-icon {
            background: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .unassign-icon {
            background: rgba(232, 168, 56, 0.15);
            color: var(--warning-color);
        }

        .delete-icon {
            background: rgba(214, 64, 69, 0.12);
            color: var(--error-color);
        }

        .status-warning-box,
        .unassign-warning-box,
        .delete-warning-box {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 18px;
            border-radius: 16px;
        }

        .status-warning-box {
            border: 1px solid rgba(74, 111, 165, 0.18);
            background: rgba(74, 111, 165, 0.06);
        }

        .unassign-warning-box {
            border: 1px solid rgba(232, 168, 56, 0.22);
            background: rgba(232, 168, 56, 0.08);
        }

        .delete-warning-box {
            border: 1px solid rgba(214, 64, 69, 0.20);
            background: rgba(214, 64, 69, 0.07);
        }

        .status-warning-icon,
        .unassign-warning-icon,
        .delete-warning-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .status-warning-icon {
            background: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .unassign-warning-icon {
            background: rgba(232, 168, 56, 0.15);
            color: var(--warning-color);
        }

        .delete-warning-icon {
            background: rgba(214, 64, 69, 0.12);
            color: var(--error-color);
        }

        .status-warning-box h3,
        .unassign-warning-box h3,
        .delete-warning-box h3 {
            margin: 0 0 6px 0;
            font-size: 17px;
            color: var(--text-main);
        }

        .status-warning-box p,
        .unassign-warning-box p,
        .delete-warning-box p {
            margin: 0;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .status-warning-box small,
        .unassign-warning-box small,
        .delete-warning-box small {
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

        .empty-row {
            display: none;
        }

        @media (max-width: 1550px) {
            .consultores-stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1200px) {
            .consultores-toolbar {
                grid-template-columns: 1fr;
            }

            .consultores-toolbar-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 900px) {
            .consultores-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .consultores-hero {
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
            .consultores-stats-grid {
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

            <a href="usuarios.php">
                <i data-lucide="users"></i>
                Usuarios
            </a>

            <a href="consultores.php" class="active">
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
                    <div class="breadcrumb">Admin / Consultores</div>
                    <h1>Gestión de Consultores</h1>
                </div>

                <button class="btn btn-primary" onclick="openConsultorAssign()" type="button">
                    <i data-lucide="user-plus" size="16"></i>
                    Nuevo Consultor
                </button>
            </div>

            <div class="consultores-hero">
                <div>
                    <h2>Administración de perfiles de consultores</h2>
                    <p>
                        Desde esta sección puedes asignar usuarios existentes con rol consultor, registrar perfiles de trabajo, activar/inactivar accesos, desasignar perfiles o eliminar consultores del sistema.
                    </p>
                </div>

                <div class="consultores-toolbar-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i data-lucide="layout-dashboard" size="16"></i>
                        Dashboard
                    </a>
                </div>
            </div>

            <div class="consultores-stats-grid">
                <div class="consultores-stat-card">
                    <div class="consultores-stat-icon total">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="consultores-stat-info">
                        <h3><?= e($totalConsultores) ?></h3>
                        <p>Total Consultores</p>
                    </div>
                </div>

                <div class="consultores-stat-card">
                    <div class="consultores-stat-icon active">
                        <i data-lucide="user-check"></i>
                    </div>
                    <div class="consultores-stat-info">
                        <h3><?= e($totalConsultoresActivos) ?></h3>
                        <p>Consultores Activos</p>
                    </div>
                </div>

                <div class="consultores-stat-card">
                    <div class="consultores-stat-icon inactive">
                        <i data-lucide="user-x"></i>
                    </div>
                    <div class="consultores-stat-info">
                        <h3><?= e($totalConsultoresInactivos) ?></h3>
                        <p>Consultores Inactivos</p>
                    </div>
                </div>

                <div class="consultores-stat-card">
                    <div class="consultores-stat-icon available">
                        <i data-lucide="user-plus"></i>
                    </div>
                    <div class="consultores-stat-info">
                        <h3><?= e($totalUsuariosDisponibles) ?></h3>
                        <p>Disponibles para Asignar</p>
                    </div>
                </div>

                <div class="consultores-stat-card">
                    <div class="consultores-stat-icon profile">
                        <i data-lucide="badge-check"></i>
                    </div>
                    <div class="consultores-stat-info">
                        <h3><?= e($totalPerfiles) ?></h3>
                        <p>Perfiles Registrados</p>
                    </div>
                </div>

                <div class="consultores-stat-card">
                    <div class="consultores-stat-icon skills">
                        <i data-lucide="sparkles"></i>
                    </div>
                    <div class="consultores-stat-info">
                        <h3><?= e($totalConHabilidades) ?></h3>
                        <p>Con Habilidades</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Lista de Consultores</h2>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                            Consulta, filtra y administra los perfiles registrados.
                        </p>
                    </div>
                </div>

                <div class="consultores-toolbar">
                    <input 
                        type="text" 
                        id="searchInput"
                        class="form-control" 
                        placeholder="Buscar por nombre, correo, perfil o habilidad..."
                        autocomplete="off"
                    >

                    <select id="statusFilter" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>

                    <select id="skillFilter" class="form-control">
                        <option value="">Todos los perfiles</option>
                        <option value="con_habilidades">Con habilidades</option>
                        <option value="sin_habilidades">Sin habilidades</option>
                    </select>

                    <div class="consultores-toolbar-actions">
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                            Limpiar
                        </button>
                    </div>
                </div>

                <div class="result-counter">
                    Mostrando <strong id="visibleCount">0</strong> de <strong id="totalCount"><?= count($consultores) ?></strong> consultor(es)
                </div>

                <div class="table-container">
                    <table class="consultores-table">
                        <colgroup>
                            <col class="col-consultor">
                            <col class="col-status">
                            <col class="col-profile">
                            <col class="col-skills">
                            <col class="col-actions">
                        </colgroup>

                        <thead>
                            <tr>
                                <th>Consultor</th>
                                <th>Estado</th>
                                <th>Perfil Principal</th>
                                <th>Habilidades</th>
                                <th class="actions-header">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="consultoresTableBody">
                        <?php if (empty($consultores)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    No hay consultores registrados.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($consultores as $c): ?>
                            <?php
                                $fullName = trim($c['nombre'] . ' ' . $c['apellido']);
                                $habilidades = trim((string)$c['habilidades']);
                                $hasSkills = $habilidades !== '' ? 'con_habilidades' : 'sin_habilidades';
                                $searchData = strtolower($fullName . ' ' . $c['correo'] . ' ' . $c['perfil_trabajo'] . ' ' . $habilidades);
                            ?>

                            <tr 
                                class="consultor-row"
                                data-search="<?= e($searchData) ?>"
                                data-status="<?= e($c['estado']) ?>"
                                data-skills="<?= e($hasSkills) ?>"
                            >
                                <td>
                                    <div class="consultor-cell">
                                        <div class="consultor-avatar-small">
                                            <?= e(inicialUsuario($c['nombre'], $c['apellido'])) ?>
                                        </div>

                                        <div>
                                            <strong>
                                                <?= e($fullName) ?>
                                            </strong>

                                            <small>
                                                <?= e($c['correo']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?= badgeEstado($c['estado']) ?>
                                </td>

                                <td>
                                    <div class="profile-text" title="<?= e($c['perfil_trabajo']) ?>">
                                        <?= e($c['perfil_trabajo']) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="skills-wrap">
                                        <?= renderSkills($c['habilidades']) ?>
                                    </div>
                                </td>

                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <button 
                                            class="btn btn-secondary action-btn" 
                                            type="button" 
                                            title="Editar perfil"
                                            onclick="openConsultorEdit(this)"
                                            data-consultor="<?= e($fullName) ?>"
                                            data-correo="<?= e($c['correo']) ?>"
                                            data-perfil-id="<?= e($c['id_perfil_trabajo']) ?>"
                                            data-perfil="<?= e($c['perfil_trabajo']) ?>"
                                            data-skills="<?= e($c['habilidades']) ?>"
                                        >
                                            <i data-lucide="edit" size="14"></i>
                                        </button>

                                        <button 
                                            class="btn btn-secondary action-btn" 
                                            type="button"
                                            title="Activar/Inactivar consultor"
                                            onclick="openStatusModal(this)"
                                            data-usuario-id="<?= e($c['id_usuario']) ?>"
                                            data-nombre="<?= e($fullName) ?>"
                                            data-correo="<?= e($c['correo']) ?>"
                                            data-estado="<?= e($c['estado']) ?>"
                                        >
                                            <i data-lucide="refresh-cw" size="14"></i>
                                        </button>

                                        <button 
                                            class="btn btn-warning-custom action-btn" 
                                            type="button"
                                            title="Desasignar consultor"
                                            onclick="openUnassignModal(this)"
                                            data-id="<?= e($c['id']) ?>"
                                            data-perfil-id="<?= e($c['id_perfil_trabajo']) ?>"
                                            data-nombre="<?= e($fullName) ?>"
                                            data-correo="<?= e($c['correo']) ?>"
                                        >
                                            <i data-lucide="unlink" size="14"></i>
                                        </button>

                                        <button 
                                            class="btn btn-danger action-btn" 
                                            type="button"
                                            title="Eliminar consultor"
                                            onclick="openDeleteModal(this)"
                                            data-id="<?= e($c['id']) ?>"
                                            data-usuario-id="<?= e($c['id_usuario']) ?>"
                                            data-perfil-id="<?= e($c['id_perfil_trabajo']) ?>"
                                            data-nombre="<?= e($fullName) ?>"
                                            data-correo="<?= e($c['correo']) ?>"
                                        >
                                            <i data-lucide="trash-2" size="14"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr id="emptyFilterRow" class="empty-row">
                            <td colspan="5" class="text-center">
                                No se encontraron consultores con los filtros aplicados.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- MODAL ASIGNAR / EDITAR CONSULTOR -->
<div class="modal-overlay" id="consultorModal">
    <div class="modal-card">
        <form method="POST" id="consultorModalForm">
            <input type="hidden" name="action" id="modalAction" value="assign_existing">
            <input type="hidden" name="id_perfil_trabajo" id="modalPerfilId">

            <div class="modal-header">
                <div class="modal-title-area">
                    <div class="modal-icon">
                        <i data-lucide="user-plus"></i>
                    </div>

                    <div>
                        <h2 class="modal-title" id="modalTitle">Nuevo Consultor</h2>
                        <p class="modal-subtitle" id="modalSubtitle">
                            Selecciona un usuario con rol consultor y registra su perfil de trabajo.
                        </p>
                    </div>
                </div>

                <button type="button" class="modal-close" onclick="closeConsultorModal()">
                    <i data-lucide="x" size="18"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="modal-grid">

                    <div class="form-group full" id="assignUserGroup">
                        <label class="form-label">Usuario con rol Consultor</label>

                        <select name="id_usuario" class="form-control" id="modalUsuario" onchange="loadSelectedUserInfo()">
                            <option value="">Seleccionar usuario...</option>

                            <?php foreach ($usuariosDisponibles as $u): ?>
                                <option 
                                    value="<?= e($u['id']) ?>"
                                    data-nombre="<?= e($u['nombre']) ?>"
                                    data-apellido="<?= e($u['apellido']) ?>"
                                    data-estado="<?= e($u['estado']) ?>"
                                >
                                    <?= e(trim($u['nombre'] . ' ' . $u['apellido'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (empty($usuariosDisponibles)): ?>
                            <small class="form-help">
                                No hay usuarios activos con rol consultor disponibles para asignar. Primero créalo desde la pantalla Usuarios.
                            </small>
                        <?php else: ?>
                            <small class="form-help">
                                Solo aparecen usuarios activos con rol consultor que aún no tienen perfil asignado.
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group" id="nombreGroup">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="modalNombre" class="form-control readonly-input" readonly>
                    </div>

                    <div class="form-group" id="apellidoGroup">
                        <label class="form-label">Apellido</label>
                        <input type="text" id="modalApellido" class="form-control readonly-input" readonly>
                    </div>

                    <div class="form-group full" id="estadoGroup">
                        <label class="form-label">Estado</label>
                        <input type="text" id="modalEstadoTexto" class="form-control readonly-input" readonly>
                    </div>

                    <div class="form-group full" id="editUserGroup" style="display:none;">
                        <label class="form-label">Consultor</label>
                        <input type="text" class="form-control readonly-input" id="modalConsultorNombre" readonly>
                        <small class="form-help" id="modalConsultorCorreo"></small>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Perfil de Trabajo / Descripción</label>
                        <textarea 
                            name="perfil_trabajo" 
                            class="form-control" 
                            id="modalPerfilTrabajo"
                            rows="4"
                            placeholder="Ejemplo: Consultor especializado en pruebas funcionales, documentación de casos de prueba y validación de interfaces."
                            required
                        ></textarea>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Habilidades</label>
                        <input 
                            name="habilidades" 
                            type="text" 
                            class="form-control" 
                            id="modalHabilidades"
                            placeholder="Ejemplo: Pruebas manuales, UI, Accesibilidad, Reportes"
                        >
                        <small class="form-help">
                            Separá cada habilidad por coma para mostrarla como etiqueta.
                        </small>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeConsultorModal()">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                    Guardar Consultor
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CAMBIAR ESTADO -->
<div class="modal-overlay status-overlay" id="statusModal">
    <div class="modal-card status-card">
        <form method="POST" id="statusConsultorForm">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id_usuario" id="statusUsuarioId">

            <div class="modal-header status-header">
                <div class="modal-title-area">
                    <div class="modal-icon status-icon">
                        <i data-lucide="refresh-cw"></i>
                    </div>

                    <div>
                        <h2 class="modal-title">Cambiar Estado</h2>
                        <p class="modal-subtitle">
                            Esta acción cambiará el estado actual del consultor seleccionado.
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
                        <h3 id="statusModalQuestion">¿Deseas cambiar el estado de este consultor?</h3>

                        <p>
                            Se actualizará el estado del consultor 
                            <strong id="statusConsultorName">Consultor seleccionado</strong>.
                        </p>

                        <small id="statusConsultorEmail"></small>

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

<!-- MODAL DESASIGNAR CONSULTOR -->
<div class="modal-overlay unassign-overlay" id="unassignModal">
    <div class="modal-card unassign-card">
        <form method="POST" id="unassignConsultorForm">
            <input type="hidden" name="action" value="unassign">
            <input type="hidden" name="id" id="unassignConsultorId">
            <input type="hidden" name="id_perfil_trabajo" id="unassignPerfilId">

            <div class="modal-header unassign-header">
                <div class="modal-title-area">
                    <div class="modal-icon unassign-icon">
                        <i data-lucide="unlink"></i>
                    </div>

                    <div>
                        <h2 class="modal-title">Desasignar Consultor</h2>
                        <p class="modal-subtitle">
                            Esta acción quitará el perfil de consultor, pero no eliminará el usuario del sistema.
                        </p>
                    </div>
                </div>

                <button type="button" class="modal-close" onclick="closeUnassignModal()">
                    <i data-lucide="x" size="18"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="unassign-warning-box">
                    <div class="unassign-warning-icon">
                        <i data-lucide="unlink" size="22"></i>
                    </div>

                    <div>
                        <h3>¿Deseas desasignar este consultor?</h3>

                        <p>
                            Se quitará la asignación de 
                            <strong id="unassignConsultorName">Consultor seleccionado</strong>.
                            Si tiene proyecto o trabajo asignado, el sistema no permitirá la desasignación.
                        </p>

                        <small id="unassignConsultorEmail"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeUnassignModal()">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-warning-custom" onclick="clearFiltersBeforeSubmit()">
                    Sí, desasignar consultor
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ELIMINAR CONSULTOR -->
<div class="modal-overlay delete-overlay" id="deleteModal">
    <div class="modal-card delete-card">
        <form method="POST" id="deleteConsultorForm">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteConsultorId">
            <input type="hidden" name="id_usuario" id="deleteUsuarioId">
            <input type="hidden" name="id_perfil_trabajo" id="deletePerfilId">

            <div class="modal-header delete-header">
                <div class="modal-title-area">
                    <div class="modal-icon delete-icon">
                        <i data-lucide="trash-2"></i>
                    </div>

                    <div>
                        <h2 class="modal-title">Eliminar Consultor</h2>
                        <p class="modal-subtitle">
                            Esta acción eliminará el perfil de consultor y el usuario relacionado solamente si no tiene trabajo asignado.
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
                        <h3>¿Deseas eliminar este consultor?</h3>

                        <p>
                            Se eliminará el perfil de 
                            <strong id="deleteConsultorName">Consultor seleccionado</strong>.
                            Si tiene proyecto, ejecución o trabajo asignado, el sistema no permitirá eliminarlo.
                        </p>

                        <small id="deleteConsultorEmail"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-danger" onclick="clearFiltersBeforeSubmit()">
                    Sí, eliminar consultor
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
        const statusFilter = document.getElementById('statusFilter');
        const skillFilter = document.getElementById('skillFilter');
        const visibleCount = document.getElementById('visibleCount');
        const emptyFilterRow = document.getElementById('emptyFilterRow');

        const searchValue = normalizeText(searchInput.value);
        const statusValue = statusFilter.value;
        const skillValue = skillFilter.value;

        const rows = document.querySelectorAll('.consultor-row');
        let count = 0;

        rows.forEach(row => {
            const rowSearch = normalizeText(row.dataset.search);
            const rowStatus = row.dataset.status;
            const rowSkills = row.dataset.skills;

            const matchSearch = searchValue === '' || rowSearch.includes(searchValue);
            const matchStatus = statusValue === '' || rowStatus === statusValue;
            const matchSkills = skillValue === '' || rowSkills === skillValue;

            if (matchSearch && matchStatus && matchSkills) {
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
        document.getElementById('statusFilter').value = '';
        document.getElementById('skillFilter').value = '';
        applyFilters();
    }

    function clearFiltersBeforeSubmit() {
        clearFilters();
    }

    function loadSelectedUserInfo() {
        const select = document.getElementById('modalUsuario');
        const option = select.options[select.selectedIndex];

        if (!option || !option.value) {
            document.getElementById('modalNombre').value = '';
            document.getElementById('modalApellido').value = '';
            document.getElementById('modalEstadoTexto').value = '';
            return;
        }

        const estado = option.dataset.estado === '1' ? 'Activo' : 'Inactivo';

        document.getElementById('modalNombre').value = option.dataset.nombre || '';
        document.getElementById('modalApellido').value = option.dataset.apellido || '';
        document.getElementById('modalEstadoTexto').value = estado;
    }

    function resetConsultorRequiredFields() {
        document.getElementById('modalUsuario').required = false;
        document.getElementById('modalPerfilTrabajo').required = true;
    }

    function openConsultorAssign() {
        const modal = document.getElementById('consultorModal');

        document.getElementById('consultorModalForm').reset();
        resetConsultorRequiredFields();

        document.getElementById('modalAction').value = 'assign_existing';
        document.getElementById('modalPerfilId').value = '';
        document.getElementById('modalUsuario').required = true;

        document.getElementById('assignUserGroup').style.display = '';
        document.getElementById('nombreGroup').style.display = '';
        document.getElementById('apellidoGroup').style.display = '';
        document.getElementById('estadoGroup').style.display = '';
        document.getElementById('editUserGroup').style.display = 'none';

        document.getElementById('modalTitle').textContent = 'Nuevo Consultor';
        document.getElementById('modalSubtitle').textContent = 'Selecciona un usuario existente con rol consultor y registra su perfil de trabajo.';
        document.getElementById('modalSubmitBtn').textContent = 'Guardar Consultor';

        loadSelectedUserInfo();

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            document.getElementById('modalUsuario').focus();
        }, 240);

        lucide.createIcons();
    }

    function openConsultorEdit(button) {
        const modal = document.getElementById('consultorModal');

        document.getElementById('consultorModalForm').reset();
        resetConsultorRequiredFields();

        document.getElementById('modalAction').value = 'update';
        document.getElementById('modalPerfilId').value = button.dataset.perfilId || '';

        document.getElementById('modalConsultorNombre').value = button.dataset.consultor || '';
        document.getElementById('modalConsultorCorreo').textContent = button.dataset.correo || '';
        document.getElementById('modalPerfilTrabajo').value = button.dataset.perfil || '';
        document.getElementById('modalHabilidades').value = button.dataset.skills || '';

        document.getElementById('assignUserGroup').style.display = 'none';
        document.getElementById('nombreGroup').style.display = 'none';
        document.getElementById('apellidoGroup').style.display = 'none';
        document.getElementById('estadoGroup').style.display = 'none';
        document.getElementById('editUserGroup').style.display = '';

        document.getElementById('modalTitle').textContent = 'Editar Perfil de Consultor';
        document.getElementById('modalSubtitle').textContent = 'Actualiza el perfil de trabajo y habilidades del consultor seleccionado.';
        document.getElementById('modalSubmitBtn').textContent = 'Guardar Cambios';

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            document.getElementById('modalPerfilTrabajo').focus();
        }, 240);

        lucide.createIcons();
    }

    function closeConsultorModal() {
        const modal = document.getElementById('consultorModal');

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

        document.getElementById('statusUsuarioId').value = button.dataset.usuarioId || '';
        document.getElementById('statusConsultorName').textContent = button.dataset.nombre || 'Consultor seleccionado';
        document.getElementById('statusConsultorEmail').textContent = button.dataset.correo || '';
        document.getElementById('statusCurrentValue').textContent = estadoActualTexto;
        document.getElementById('statusNewValue').textContent = nuevoEstadoTexto;

        document.getElementById('statusModalQuestion').textContent =
            estadoActual === '1'
                ? '¿Deseas inactivar este consultor?'
                : '¿Deseas activar este consultor?';

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

    function openUnassignModal(button) {
        const modal = document.getElementById('unassignModal');

        document.getElementById('unassignConsultorId').value = button.dataset.id || '';
        document.getElementById('unassignPerfilId').value = button.dataset.perfilId || '';
        document.getElementById('unassignConsultorName').textContent = button.dataset.nombre || 'Consultor seleccionado';
        document.getElementById('unassignConsultorEmail').textContent = button.dataset.correo || '';

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        lucide.createIcons();
    }

    function closeUnassignModal() {
        const modal = document.getElementById('unassignModal');

        if (modal) {
            modal.classList.remove('show');
        }

        document.body.style.overflow = '';
    }

    function openDeleteModal(button) {
        const modal = document.getElementById('deleteModal');

        document.getElementById('deleteConsultorId').value = button.dataset.id || '';
        document.getElementById('deleteUsuarioId').value = button.dataset.usuarioId || '';
        document.getElementById('deletePerfilId').value = button.dataset.perfilId || '';
        document.getElementById('deleteConsultorName').textContent = button.dataset.nombre || 'Consultor seleccionado';
        document.getElementById('deleteConsultorEmail').textContent = button.dataset.correo || '';

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        lucide.createIcons();
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
            closeConsultorModal();
            closeStatusModal();
            closeUnassignModal();
            closeDeleteModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const skillFilter = document.getElementById('skillFilter');

        const consultorModal = document.getElementById('consultorModal');
        const statusModal = document.getElementById('statusModal');
        const unassignModal = document.getElementById('unassignModal');
        const deleteModal = document.getElementById('deleteModal');

        searchInput.addEventListener('input', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        skillFilter.addEventListener('change', applyFilters);

        if (consultorModal) {
            consultorModal.addEventListener('click', function (event) {
                if (event.target === this) {
                    closeConsultorModal();
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

        if (unassignModal) {
            unassignModal.addEventListener('click', function (event) {
                if (event.target === this) {
                    closeUnassignModal();
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