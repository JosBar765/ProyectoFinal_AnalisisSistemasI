<?php
session_start();

// Si se sube a un host, darle el valor ""
$path = "/specialisterne";

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["user"]["id_rol"] != 2) {
    header("Location: {$path}/403.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Consultores - Specialisterne</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Specialisterne</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php"><i data-lucide="layout-dashboard"></i> Dashboard</a>
                <a href="proyectos.php"><i data-lucide="folder-kanban"></i> Proyectos</a>
                <a href="manuales.php"><i data-lucide="book-open"></i> Manuales</a>
                <a href="casos.php"><i data-lucide="list-checks"></i> Casos de Prueba</a>
                <a href="asignaciones.php" class="active"><i data-lucide="users"></i> Asignaciones</a>
                <a href="errores.php"><i data-lucide="bug"></i> Errores</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-weight: 500;">Proyecto Activo:</span>
                    <select class="form-control" style="width: 300px; padding: 6px;">
                        <option>Sistema de Facturación Cliente XYZ</option>
                    </select>
                </div>
                <div class="user-info">
                    <span class="role-badge" style="background-color: rgba(232, 168, 56, 0.15); color: var(--warning-color);">Supervisor</span>
                    <span>Laura Martínez</span>
                    <div class="avatar" style="background-color: var(--warning-color);">L</div>
                    <a id="logoutBtn" href="../../index.php" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb">Supervisor / Asignaciones</div>
                        <h1>Asignación de Consultores</h1>
                    </div>
                </div>

                <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">

                    <!-- Consultores Disponibles -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Consultores Disponibles</h2>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 15px;">

                            <div class="card" style="background: var(--bg-color); box-shadow: none; margin: 0;">
                                <div class="d-flex justify-between align-center">
                                    <div>
                                        <strong>Carlos López</strong>
                                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Validación de UI, patrones repetitivos.</p>
                                        <div style="margin-top: 8px;">
                                            <span class="badge badge-neutral" style="font-size: 11px;">UI/UX</span>
                                            <span class="badge badge-neutral" style="font-size: 11px;">Patrones</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" onclick="showToast('Consultor asignado al proyecto')">Asignar <i data-lucide="arrow-right" size="16"></i></button>
                                </div>
                            </div>

                            <div class="card" style="background: var(--bg-color); box-shadow: none; margin: 0;">
                                <div class="d-flex justify-between align-center">
                                    <div>
                                        <strong>Ana Rodríguez</strong>
                                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Pruebas de accesibilidad y flujos cortos.</p>
                                        <div style="margin-top: 8px;">
                                            <span class="badge badge-neutral" style="font-size: 11px;">Accesibilidad</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" onclick="showToast('Consultor asignado al proyecto')">Asignar <i data-lucide="arrow-right" size="16"></i></button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Consultores Asignados al Proyecto -->
                    <div class="card" style="border-top: 4px solid var(--success-color);">
                        <div class="card-header">
                            <h2 class="card-title">Asignados al Proyecto Actual</h2>
                            <span class="badge badge-success">1 Asignado</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 15px;">

                            <div class="card" style="background: white; border: 1px solid var(--border-color); box-shadow: none; margin: 0;">
                                <div class="d-flex justify-between align-center">
                                    <div class="d-flex align-center gap-3">
                                        <div class="avatar" style="background-color: var(--primary-color);">M</div>
                                        <div>
                                            <strong>María García</strong>
                                            <p style="font-size: 12px; color: var(--text-muted);">Asignado: 15 May 2026</p>
                                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Perfil: Alta atención al detalle, pruebas funcionales.</p>
                                        </div>
                                    </div>
                                    <button class="btn btn-danger" onclick="showToast('Consultor removido del proyecto', 'warning')"><i data-lucide="user-minus" size="16"></i> Remover</button>
                                </div>
                            </div>

                            <div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13px; font-style: italic;">
                                Arrastre o use el botón asignar para incluir más consultores.
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
</body>

</html>