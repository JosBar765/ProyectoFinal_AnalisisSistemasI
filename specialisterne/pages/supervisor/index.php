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
    <title>Dashboard Supervisor - Specialisterne</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <div class="layout">

        <!-- Sidebar -->
        <aside class="sidebar">

            <div class="sidebar-header">
                <h2>Specialisterne</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php" class="active">
                    <i data-lucide="layout-dashboard"></i>
                    Dashboard
                </a>

                <a href="proyectos.php">
                    <i data-lucide="folder-kanban"></i>
                    Proyectos
                </a>

                <a href="manuales.php">
                    <i data-lucide="book-open"></i>
                    Manuales
                </a>

                <a href="casos.php">
                    <i data-lucide="list-checks"></i>
                    Casos de Prueba
                </a>

                <a href="asignaciones.php">
                    <i data-lucide="users"></i>
                    Asignaciones
                </a>

                <a href="errores.php">
                    <i data-lucide="bug"></i>
                    Errores
                </a>
            </nav>

        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <header class="top-header">

                <div style="display: flex; align-items: center; gap: 15px;">

                    <span style="font-weight: 500;">
                        Proyecto Activo:
                    </span>

                    <select
                        id="proyectoSelect"
                        class="form-control"
                        style="width: 300px; padding: 6px;">
                    </select>

                </div>

                <div class="user-info">

                    <span
                        class="role-badge"
                        style="background-color: rgba(232, 168, 56, 0.15); color: var(--warning-color);">
                        Supervisor
                    </span>

                    <span>
                        <?= $_SESSION["user"]["nombre"] . " " . $_SESSION["user"]["apellido"] ?>
                    </span>

                    <div
                        class="avatar"
                        style="background-color: var(--warning-color);">

                        <?= strtoupper(substr($_SESSION["user"]["nombre"], 0, 1)) ?>

                    </div>

                    <a
                        id="logoutBtn"
                        href="../../index.php"
                        title="Cerrar sesión"
                        style="color: var(--text-muted);">

                        <i data-lucide="log-out" size="18"></i>

                    </a>

                </div>

            </header>

            <div class="content-area">

                <div class="page-header">

                    <div class="page-title">

                        <div class="breadcrumb">
                            Supervisor / Dashboard
                        </div>

                        <h1>
                            Dashboard del Proyecto
                        </h1>

                    </div>

                </div>

                <!-- Progreso General -->
                <div class="card mb-4">

                    <div class="card-header" style="margin-bottom: 5px;">

                        <h2
                            id="avanceGeneral"
                            class="card-title">

                            Avance General: 0%

                        </h2>

                        <span
                            id="estadoProyecto"
                            class="badge badge-success">

                            --

                        </span>

                    </div>

                    <div
                        class="progress-container"
                        style="height: 12px;">

                        <div
                            id="barraProgresoGeneral"
                            class="progress-bar"
                            style="width: 0%;">

                        </div>

                    </div>

                    <p
                        id="casosEjecutadosTexto"
                        style="color: var(--text-muted); font-size: 13px; margin-top: 8px;">

                        0 de 0 Casos de Prueba Ejecutados

                    </p>

                </div>

                <!-- Resumen Casos -->
                <div class="dashboard-grid">

                    <!-- Pendientes -->
                    <div
                        class="stat-card"
                        style="border-left: 4px solid var(--text-muted);">

                        <div
                            class="stat-icon"
                            style="background: none;">

                            <i
                                data-lucide="clock"
                                style="color: var(--text-muted);">

                            </i>

                        </div>

                        <div class="stat-info">

                            <h3 id="statPendientes">
                                0
                            </h3>

                            <p>
                                Casos Pendientes
                            </p>

                        </div>

                    </div>

                    <!-- En progreso -->
                    <div
                        class="stat-card"
                        style="border-left: 4px solid var(--primary-color);">

                        <div
                            class="stat-icon"
                            style="background: none;">

                            <i
                                data-lucide="play-circle"
                                style="color: var(--primary-color);">

                            </i>

                        </div>

                        <div class="stat-info">

                            <h3 id="statEnProgreso">
                                0
                            </h3>

                            <p>
                                En Progreso
                            </p>

                        </div>

                    </div>

                    <!-- Completados -->
                    <div
                        class="stat-card"
                        style="border-left: 4px solid var(--success-color);">

                        <div
                            class="stat-icon"
                            style="background: none;">

                            <i
                                data-lucide="check-circle"
                                style="color: var(--success-color);">

                            </i>

                        </div>

                        <div class="stat-info">

                            <h3 id="statCompletados">
                                0
                            </h3>

                            <p>
                                Completados
                            </p>

                        </div>

                    </div>

                    <!-- Fallidos -->
                    <div
                        class="stat-card"
                        style="border-left: 4px solid var(--error-color);">

                        <div
                            class="stat-icon"
                            style="background: none;">

                            <i
                                data-lucide="x-circle"
                                style="color: var(--error-color);">

                            </i>

                        </div>

                        <div class="stat-info">

                            <h3 id="statFallidos">
                                0
                            </h3>

                            <p>
                                Fallidos
                            </p>

                        </div>

                    </div>

                </div>

                <div
                    class="dashboard-grid"
                    style="grid-template-columns: 2fr 1fr;">

                    <!-- Tabla de Fases -->
                    <div class="card">

                        <div class="card-header">

                            <h2 class="card-title">
                                Progreso por Fase
                            </h2>

                        </div>

                        <div class="table-container">

                            <table>

                                <thead>

                                    <tr>
                                        <th>Fase</th>
                                        <th>Progreso</th>
                                        <th>Estado</th>
                                    </tr>

                                </thead>

                                <tbody id="tablaFasesBody">
                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- Errores recientes -->
                    <div class="card">

                        <div class="card-header">

                            <h2 class="card-title">
                                Errores Recientes
                            </h2>

                            <a
                                href="errores.php"
                                style="font-size: 13px; color: var(--primary-color); text-decoration: none;">

                                Ver todos

                            </a>

                        </div>

                        <div
                            id="erroresRecientesContainer"
                            style="display: flex; flex-direction: column; gap: 15px;">

                        </div>

                    </div>

                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>
    <script src="./js/dashboard.js"></script>

</body>

</html>