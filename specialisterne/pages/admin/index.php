<?php
session_start();

require_once '../../php/path.php';
require_once '../../php/db.php';

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["user"]["id_rol"] != 1) {
    header("Location: {$path}/403.php");
    exit;
}

/* =========================
   CONSULTAS PRINCIPALES
========================= */

$totalUsuariosOperativos = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE id_rol <> 1
")->fetchColumn();

$totalAdministradores = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE id_rol = 1 AND estado = 1
")->fetchColumn();

$totalSupervisores = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE id_rol = 2 AND estado = 1
")->fetchColumn();

$totalConsultores = $pdo->query("
    SELECT COUNT(*) 
    FROM usuario
    WHERE id_rol = 3 AND estado = 1
")->fetchColumn();

$totalProyectosActivos = $pdo->query("
    SELECT COUNT(*)
    FROM proyecto p
    INNER JOIN estadoproyecto ep 
        ON ep.id = p.id_estado_proyecto
    WHERE ep.estado = 'Activo'
")->fetchColumn();

$totalProyectosPausa = $pdo->query("
    SELECT COUNT(*)
    FROM proyecto p
    INNER JOIN estadoproyecto ep 
        ON ep.id = p.id_estado_proyecto
    WHERE ep.estado = 'En Pausa'
")->fetchColumn();

$totalProyectosFinalizados = $pdo->query("
    SELECT COUNT(*)
    FROM proyecto p
    INNER JOIN estadoproyecto ep 
        ON ep.id = p.id_estado_proyecto
    WHERE ep.estado = 'Finalizado'
")->fetchColumn();

$totalProyectos = $pdo->query("
    SELECT COUNT(*) 
    FROM proyecto
")->fetchColumn();

$ultimosUsuarios = $pdo->query("
    SELECT 
        u.id,
        u.nombre,
        u.apellido,
        u.correo,
        u.estado,
        u.fecha_creacion,
        r.nombre AS rol
    FROM usuario u
    INNER JOIN rol r 
        ON r.id = u.id_rol
    ORDER BY u.fecha_creacion DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$proyectos = $pdo->query("
    SELECT 
        p.id,
        p.nombre,
        p.descripcion,
        p.fecha_inicio,
        p.fecha_fin,
        ep.estado
    FROM proyecto p
    INNER JOIN estadoproyecto ep 
        ON ep.id = p.id_estado_proyecto
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CÁLCULOS DE PORCENTAJES
========================= */

$totalPersonalOperativo = $totalConsultores + $totalSupervisores;

$porcentajeConsultores = $totalPersonalOperativo > 0
    ? round(($totalConsultores / $totalPersonalOperativo) * 100)
    : 0;

$porcentajeSupervisores = $totalPersonalOperativo > 0
    ? round(($totalSupervisores / $totalPersonalOperativo) * 100)
    : 0;

$porcentajeProyectosActivos = $totalProyectos > 0
    ? round(($totalProyectosActivos / $totalProyectos) * 100)
    : 0;

$porcentajeProyectosPausa = $totalProyectos > 0
    ? round(($totalProyectosPausa / $totalProyectos) * 100)
    : 0;

$porcentajeProyectosFinalizados = $totalProyectos > 0
    ? round(($totalProyectosFinalizados / $totalProyectos) * 100)
    : 0;

/* =========================
   FUNCIONES
========================= */

function badgeEstado($estado)
{
    $estadoTexto = strtolower(trim((string)$estado));

    if ($estado == 1 || $estadoTexto === 'activo') {
        return '<span class="badge badge-success">Activo</span>';
    }

    if ($estadoTexto === 'en pausa') {
        return '<span class="badge badge-warning">En Pausa</span>';
    }

    if ($estadoTexto === 'finalizado') {
        return '<span class="badge badge-neutral">Finalizado</span>';
    }

    return '<span class="badge badge-error">Inactivo</span>';
}

function badgeRol($rol)
{
    $rol = strtolower(trim((string)$rol));

    if ($rol === 'admin') {
        return '<span class="badge badge-neutral">Administrador</span>';
    }

    if ($rol === 'supervisor') {
        return '<span class="badge badge-warning">Supervisor</span>';
    }

    return '<span class="badge badge-primary">Consultor</span>';
}

function formatoFecha($fecha)
{
    if (!$fecha) {
        return 'Sin fecha';
    }

    return date("d/m/Y", strtotime($fecha));
}

function inicialUsuario($nombre, $apellido = '')
{
    $nombre = trim((string)$nombre);
    $apellido = trim((string)$apellido);

    $inicialNombre = $nombre !== '' ? strtoupper(substr($nombre, 0, 1)) : '';
    $inicialApellido = $apellido !== '' ? strtoupper(substr($apellido, 0, 1)) : '';

    return $inicialNombre . $inicialApellido;
}

/* =========================
   USUARIO CONECTADO
========================= */

$usuarioConectado = null;

if (isset($_SESSION["user"]["id"])) {
    $stmtUsuarioActual = $pdo->prepare("
        SELECT 
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

$inicialesConectado = strtoupper(
    substr($nombreConectado, 0, 1) . substr($apellidoConectado, 0, 1)
);

if ($inicialesConectado === "") {
    $inicialesConectado = "A";
}

$nombreSesion = $_SESSION["user"]["nombre"] ?? 'admin';
$inicialSesion = strtoupper(substr($nombreSesion, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Specialisterne</title>

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

        .admin-hero {
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

        .admin-hero h2 {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .admin-hero p {
            color: var(--text-muted);
            margin: 0;
            max-width: 780px;
            font-size: 13px;
        }

        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 22px;
        }

        .admin-stat-card {
            background-color: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 16px;
            min-height: 104px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .admin-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.06);
        }

        .admin-stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .admin-stat-icon.users {
            background-color: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .admin-stat-icon.admins {
            background-color: rgba(44, 62, 80, 0.10);
            color: var(--text-main);
        }

        .admin-stat-icon.supervisores {
            background-color: rgba(232, 168, 56, 0.15);
            color: var(--warning-color);
        }

        .admin-stat-icon.consultores {
            background-color: rgba(74, 111, 165, 0.12);
            color: var(--primary-color);
        }

        .admin-stat-icon.proyectos {
            background-color: rgba(91, 173, 145, 0.15);
            color: var(--success-color);
        }

        .admin-stat-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 58px;
        }

        .admin-stat-info h3 {
            font-size: 30px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.05;
        }

        .admin-stat-info p {
            margin: 6px 0 0 0;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.2;
        }

        .admin-stat-note {
            display: block;
            margin-top: 3px;
            color: var(--text-muted);
            font-size: 11px;
            line-height: 1.2;
        }

        .admin-summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .summary-row {
            margin-bottom: 16px;
        }

        .summary-row:last-child {
            margin-bottom: 0;
        }

        .summary-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: var(--text-muted);
            gap: 10px;
        }

        .summary-label strong {
            color: var(--text-main);
        }

        .summary-label span {
            white-space: nowrap;
        }

        .summary-bar {
            height: 8px;
            background-color: var(--border-color);
            border-radius: 20px;
            overflow: hidden;
        }

        .summary-fill {
            height: 100%;
            border-radius: 20px;
        }

        .summary-fill.primary {
            background-color: var(--primary-color);
        }

        .summary-fill.warning {
            background-color: var(--warning-color);
        }

        .summary-fill.success {
            background-color: var(--success-color);
        }

        .summary-fill.neutral {
            background-color: var(--text-muted);
        }

        .admin-dashboard-content {
            display: grid;
            grid-template-columns: 1.45fr 1fr;
            gap: 20px;
            align-items: start;
        }

        .user-profile-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-mini-avatar {
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

        .user-profile-cell strong {
            display: block;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .user-profile-cell small {
            color: var(--text-muted);
            font-size: 12px;
        }

        .admin-project-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .admin-project-item {
            border: 1px solid var(--border-color);
            border-radius: 14px;
            background-color: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .admin-project-item strong {
            display: block;
            color: var(--text-main);
            margin-bottom: 5px;
            font-weight: 600;
        }

        .admin-project-item small {
            color: var(--text-muted);
            font-size: 12px;
        }

        .admin-project-info {
            min-width: 0;
        }

        .admin-project-status {
            flex-shrink: 0;
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

        @media (max-width: 1500px) {
            .admin-stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1100px) {
            .admin-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .admin-summary-grid,
            .admin-dashboard-content {
                grid-template-columns: 1fr;
            }

            .admin-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .quick-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 650px) {
            .admin-stats-grid {
                grid-template-columns: 1fr;
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
                <a href="index.php" class="active">
                    <i data-lucide="layout-dashboard"></i>
                    Dashboard
                </a>

                <a href="usuarios.php">
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
                    <?= htmlspecialchars(ucfirst($rolConectado)) ?>
                </span>

                <span>
                    <?= htmlspecialchars($nombreCompletoConectado) ?>
                </span>

                <div class="avatar">
                    <?= htmlspecialchars($inicialesConectado) ?>
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

                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb">Admin / Dashboard</div>
                        <h1>Dashboard Principal</h1>
                    </div>
                </div>

                <div class="admin-hero">
                    <div>
                        <h2>Resumen general del sistema</h2>
                        <p>
                            Vista administrativa de usuarios, roles operativos y estado actual de proyectos registrados en la plataforma.
                        </p>
                    </div>

                    <div class="quick-actions">
                        <a href="usuarios.php" class="btn btn-secondary">
                            <i data-lucide="user-plus" size="16"></i>
                            Gestionar Usuarios
                        </a>

                        <a href="consultores.php" class="btn btn-secondary">
                            <i data-lucide="user-check" size="16"></i>
                            Ver Consultores
                        </a>
                    </div>
                </div>

                <div class="admin-stats-grid">

                    <div class="admin-stat-card">
                        <div class="admin-stat-icon users">
                            <i data-lucide="users"></i>
                        </div>

                        <div class="admin-stat-info">
                            <h3><?= $totalUsuariosOperativos ?></h3>
                            <p>Usuarios Operativos</p>
                            <small class="admin-stat-note">No incluye administradores</small>
                        </div>
                    </div>

                    <div class="admin-stat-card">
                        <div class="admin-stat-icon admins">
                            <i data-lucide="shield"></i>
                        </div>

                        <div class="admin-stat-info">
                            <h3><?= $totalAdministradores ?></h3>
                            <p>Administradores</p>
                        </div>
                    </div>

                    <div class="admin-stat-card">
                        <div class="admin-stat-icon supervisores">
                            <i data-lucide="user-cog"></i>
                        </div>

                        <div class="admin-stat-info">
                            <h3><?= $totalSupervisores ?></h3>
                            <p>Supervisores Activos</p>
                        </div>
                    </div>

                    <div class="admin-stat-card">
                        <div class="admin-stat-icon consultores">
                            <i data-lucide="user-check"></i>
                        </div>

                        <div class="admin-stat-info">
                            <h3><?= $totalConsultores ?></h3>
                            <p>Consultores Activos</p>
                        </div>
                    </div>

                    <div class="admin-stat-card">
                        <div class="admin-stat-icon proyectos">
                            <i data-lucide="briefcase"></i>
                        </div>

                        <div class="admin-stat-info">
                            <h3><?= $totalProyectosActivos ?></h3>
                            <p>Proyectos Activos</p>
                        </div>
                    </div>

                </div>

                <div class="admin-summary-grid">

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Distribución de Usuarios Operativos</h2>
                        </div>

                        <div class="summary-row">
                            <div class="summary-label">
                                <strong>Consultores</strong>
                                <span><?= $totalConsultores ?> usuario(s) · <?= $porcentajeConsultores ?>%</span>
                            </div>
                            <div class="summary-bar">
                                <div class="summary-fill primary" style="width: <?= $porcentajeConsultores ?>%;"></div>
                            </div>
                        </div>

                        <div class="summary-row">
                            <div class="summary-label">
                                <strong>Supervisores</strong>
                                <span><?= $totalSupervisores ?> usuario(s) · <?= $porcentajeSupervisores ?>%</span>
                            </div>
                            <div class="summary-bar">
                                <div class="summary-fill warning" style="width: <?= $porcentajeSupervisores ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Resumen de Proyectos</h2>
                        </div>

                        <div class="summary-row">
                            <div class="summary-label">
                                <strong>Activos</strong>
                                <span><?= $totalProyectosActivos ?> proyecto(s) · <?= $porcentajeProyectosActivos ?>%</span>
                            </div>
                            <div class="summary-bar">
                                <div class="summary-fill success" style="width: <?= $porcentajeProyectosActivos ?>%;"></div>
                            </div>
                        </div>

                        <div class="summary-row">
                            <div class="summary-label">
                                <strong>En Pausa</strong>
                                <span><?= $totalProyectosPausa ?> proyecto(s) · <?= $porcentajeProyectosPausa ?>%</span>
                            </div>
                            <div class="summary-bar">
                                <div class="summary-fill warning" style="width: <?= $porcentajeProyectosPausa ?>%;"></div>
                            </div>
                        </div>

                        <div class="summary-row">
                            <div class="summary-label">
                                <strong>Finalizados</strong>
                                <span><?= $totalProyectosFinalizados ?> proyecto(s) · <?= $porcentajeProyectosFinalizados ?>%</span>
                            </div>
                            <div class="summary-bar">
                                <div class="summary-fill neutral" style="width: <?= $porcentajeProyectosFinalizados ?>%;"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="admin-dashboard-content">

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Actividad reciente de usuarios</h2>
                            <a href="usuarios.php" class="btn btn-secondary">Ver todos</a>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Rol</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (count($ultimosUsuarios) > 0): ?>
                                        <?php foreach ($ultimosUsuarios as $usuario): ?>
                                            <tr>
                                                <td>
                                                    <div class="user-profile-cell">
                                                        <div class="user-mini-avatar">
                                                            <?= htmlspecialchars(inicialUsuario($usuario["nombre"], $usuario["apellido"])) ?>
                                                        </div>

                                                        <div>
                                                            <strong>
                                                                <?= htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]) ?>
                                                            </strong>
                                                            <small>
                                                                <?= htmlspecialchars($usuario["correo"]) ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <?= badgeRol($usuario["rol"]) ?>
                                                </td>

                                                <td>
                                                    <?= formatoFecha($usuario["fecha_creacion"]) ?>
                                                </td>

                                                <td>
                                                    <?= badgeEstado($usuario["estado"]) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                No hay usuarios registrados.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Estado operativo de proyectos</h2>
                        </div>

                        <div class="admin-project-list">
                            <?php if (count($proyectos) > 0): ?>
                                <?php foreach ($proyectos as $proyecto): ?>
                                    <div class="admin-project-item">
                                        <div class="admin-project-info">
                                            <strong>
                                                <?= htmlspecialchars($proyecto["nombre"]) ?>
                                            </strong>

                                            <small>
                                                <?= formatoFecha($proyecto["fecha_inicio"]) ?>
                                                -
                                                <?= formatoFecha($proyecto["fecha_fin"]) ?>
                                            </small>
                                        </div>

                                        <div class="admin-project-status">
                                            <?= badgeEstado($proyecto["estado"]) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="color: var(--text-muted);">
                                    No hay proyectos registrados.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
</body>

</html>