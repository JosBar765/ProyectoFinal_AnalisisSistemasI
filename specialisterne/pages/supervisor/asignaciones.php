<?php
session_start();

require_once '../../php/path.php';

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
                    <select id="proyectoAsignacionesSelect" class="form-control" style="width: 300px; padding: 6px;"></select>
                </div>
                <div class="user-info">
                    <span class="role-badge" style="background-color: rgba(232, 168, 56, 0.15); color: var(--warning-color);">Supervisor</span>
                    <span>
                        <?= $_SESSION["user"]["nombre"] . " " . $_SESSION["user"]["apellido"] ?>
                    </span>
                    <div class="avatar" style="background-color: var(--warning-color);">
                        <?= strtoupper(substr($_SESSION["user"]["nombre"], 0, 1)) . strtoupper(substr($_SESSION["user"]["apellido"], 0, 1)) ?>
                    </div>
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

                        <div
                            id="consultoresDisponiblesContainer"
                            style="display: flex; flex-direction: column; gap: 15px;">
                        </div>
                    </div>

                    <!-- Consultores Asignados al Proyecto -->
                    <div class="card" style="border-top: 4px solid var(--success-color);">

                        <div class="card-header">
                            <h2 class="card-title">Asignados al Proyecto Actual</h2>
                            <span class="badge badge-success" id="contadorAsignados">0 Asignados</span>
                        </div>

                        <div
                            id="consultoresAsignadosContainer"
                            style="display: flex; flex-direction: column; gap: 15px;">
                        </div>

                        <div
                            id="emptyAsignadosState"
                            style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 13px; font-style: italic;">
                            Use el botón asignar para incluir más consultores.
                        </div>

                    </div>

                </div>

            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
    <script src="js/comun.js"></script>
    <script src="js/asignaciones/asignaciones.js"></script>
</body>

</html>