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

    <title>
        Casos de Prueba - Specialisterne
    </title>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="../../css/styles.css">

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

                <a href="index.php">
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

                <a href="casos.php" class="active">
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

        <!-- Main -->
        <main class="main-content">

            <header class="top-header">

                <div
                    style="
                        display: flex;
                        align-items: center;
                        gap: 15px;
                    ">

                    <span style="font-weight: 500;">
                        Proyecto Activo:
                    </span>

                    <select
                        id="proyectoSelect"
                        class="form-control"
                        style="
                            width: 300px;
                            padding: 6px;
                        ">
                    </select>

                </div>

                <div class="user-info">

                    <span
                        class="role-badge"
                        style="
                            background-color: rgba(232, 168, 56, 0.15);
                            color: var(--warning-color);
                        ">

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

            <!-- Content -->
            <div class="content-area">

                <div class="page-header">

                    <div class="page-title">

                        <div class="breadcrumb">
                            Supervisor / Casos de Prueba
                        </div>

                        <h1>
                            Gestión de Casos de Prueba
                        </h1>

                    </div>

                    <button
                        class="btn btn-primary"
                        id="btnNuevoCaso">

                        <i data-lucide="plus"></i>
                        Nuevo Caso

                    </button>

                </div>

                <!-- Form -->
                <div class="card" id="casoForm"
                    style=" 
                    display: none; 
                    border-left: 4px solid var(--primary-color);
                ">
                    <div class="card-header">
                        <div>
                            <h2 id="caseFormTitle" class="card-title">Crear Caso de Prueba</h2>
                            <p id="caseFormHint" style="
                                font-size: 12px;
                                color: var(--text-muted);
                                margin-top: 4px;
                            ">Completa la información del nuevo caso.</p>
                        </div>

                        <button id="btnCerrarCasoForm" class="btn btn-secondary">
                            <i data-lucide="x" size="16"></i>
                        </button>
                    </div>

                    <form id="formCaso">
                        <div class="dashboard-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    Fase del Proyecto
                                </label>

                                <select id="casePhase" class="form-control" required></select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Título del Caso
                                </label>

                                <input type="text" id="caseTitle" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Descripción Breve
                            </label>

                            <input type="text" id="caseDesc" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Instrucciones Paso a Paso
                            </label>

                            <textarea id="caseSteps" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Resultado Esperado
                            </label>

                            <textarea id="caseExpected" class="form-control" style="min-height: 60px;" required></textarea>
                        </div>

                        <div class="dashboard-grid" id="estadoContainer" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">
                                    Estado del Caso
                                </label>

                                <select id="caseStatus" class="form-control"></select>
                            </div>
                        </div>

                        <div
                            class="d-flex gap-2"
                            style="
                            justify-content: space-between;
                            align-items: center;
                        ">
                            <button id="caseSubmit" type="submit" class="btn btn-primary">
                                Guardar Caso
                            </button>

                            <button type="button" id="btnCancelarCaso" class="btn btn-secondary">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Casos -->
                <div class="card">
                    <!-- Tabs -->
                    <div class="tabs" id="tabsFases"></div>

                    <!-- Contenido -->
                    <div id="tabContentContainer"></div>
                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>
    <script src="js/comun.js"></script>
    <script src="js/casos.js"></script>
    <script src="js/casos-agregar.js"></script>
    <script src="js/casos-modificar.js"></script>

</body>

</html>