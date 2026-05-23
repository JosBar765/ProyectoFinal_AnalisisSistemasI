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
    <title>Errores Reportados - Specialisterne</title>

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
                <a href="asignaciones.php"><i data-lucide="users"></i> Asignaciones</a>
                <a href="errores.php" class="active"><i data-lucide="bug"></i> Errores</a>
            </nav>

        </aside>

        <main class="main-content">

            <header class="top-header">

                <div style="display: flex; align-items: center; gap: 15px;">

                    <span style="font-weight: 500;">
                        Proyecto Activo:
                    </span>

                    <select
                        id="proyectoErroresSelect"
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

                        <?= strtoupper(substr($_SESSION["user"]["nombre"], 0, 1)) . strtoupper(substr($_SESSION["user"]["apellido"], 0, 1)) ?>

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
                            Supervisor / Errores
                        </div>

                        <h1>Errores Reportados</h1>

                    </div>

                    <a
                        href="error-form.php"
                        class="btn btn-primary">

                        <i data-lucide="plus"></i>
                        Registrar Error

                    </a>

                </div>

                <!-- DETALLE ERROR -->

                <div
                    class="card"
                    id="errorDetail"
                    style="display: none; border-left: 4px solid var(--error-color);">

                    <div class="card-header">

                        <div class="d-flex align-center gap-3">

                            <h2
                                class="card-title"
                                id="detailCodigoError">
                            </h2>

                            <span
                                id="detailBadgeSeveridad"
                                class="badge">
                            </span>

                        </div>

                        <button
                            class="btn btn-secondary"
                            id="btnCerrarDetalle">

                            <i data-lucide="x" size="16"></i>

                        </button>

                    </div>

                    <div
                        style="background: var(--bg-color); padding: 15px; border-radius: 8px; margin-bottom: 20px;">

                        <h3
                            id="detailTitulo"
                            style="font-size: 16px; margin-bottom: 10px;">
                        </h3>

                        <p
                            id="detailDescripcion"
                            style="font-size: 14px; margin-bottom: 15px;">
                        </p>

                        <div
                            class="dashboard-grid"
                            style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">

                            <div>

                                <strong
                                    style="font-size: 12px; color: var(--text-muted); display: block;">

                                    Reportado por:

                                </strong>

                                <span
                                    id="detailConsultor"
                                    style="font-size: 14px;">
                                </span>

                            </div>

                            <div>

                                <strong
                                    style="font-size: 12px; color: var(--text-muted); display: block;">

                                    Caso Vinculado:

                                </strong>

                                <span
                                    id="detailCaso"
                                    style="font-size: 14px;">
                                </span>

                            </div>

                        </div>

                    </div>

                    <h3 style="font-size: 14px; margin-bottom: 10px;">
                        Evidencias
                    </h3>

                    <div
                        id="detailImagenes"
                        style="display: flex; gap: 15px; flex-wrap: wrap;">
                    </div>

                </div>

                <!-- TABLA -->

                <div class="card">

                    <div class="card-header">

                        <div class="d-flex gap-2">

                            <select
                                id="filtroSeveridad"
                                class="form-control">

                                <option value="">
                                    Todas las Severidades
                                </option>

                                <option value="BAJO">BAJO</option>
                                <option value="MEDIO">MEDIO</option>
                                <option value="ALTO">ALTO</option>
                                <option value="CRITICO">CRÍTICO</option>

                            </select>

                            <select
                                id="filtroEstado"
                                class="form-control">

                                <option value="">
                                    Todos los Estados
                                </option>

                                <option value="1">
                                    Abierto
                                </option>

                                <option value="0">
                                    Resuelto
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="table-container">

                        <table>

                            <thead>

                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Severidad</th>
                                    <th>Estado</th>
                                    <th>Caso de Prueba</th>
                                    <th>Consultor</th>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                </tr>

                            </thead>

                            <tbody id="erroresTableBody">
                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>

    <script src="js/comun.js"></script>

    <script src="js/errores/errores.js"></script>

</body>

</html>