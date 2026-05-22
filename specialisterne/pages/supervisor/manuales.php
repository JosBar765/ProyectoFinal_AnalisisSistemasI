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
        Manuales de Prueba - Specialisterne
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

                <a href="manuales.php" class="active">
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

            <div class="content-area">

                <div class="page-header">

                    <div class="page-title">

                        <div class="breadcrumb">
                            Supervisor / Manuales
                        </div>

                        <h1>
                            Manuales del Proyecto
                        </h1>

                    </div>

                    <button
                        id="btnAbrirManualForm"
                        class="btn btn-primary">

                        <i data-lucide="upload"></i>
                        Subir Manual

                    </button>

                </div>

                <!-- Formulario -->
                <div
                    class="card"
                    id="manualForm"
                    style="
                        display: none;
                        border-left: 4px solid var(--primary-color);
                    ">

                    <div class="card-header">

                        <h2 class="card-title">
                            Añadir Nuevo Manual de Referencia
                        </h2>

                        <button
                            id="btnCerrarManualForm"
                            class="btn btn-secondary">

                            <i data-lucide="x" size="16"></i>

                        </button>

                    </div>

                    <form id="formManual" enctype="multipart/form-data">

                        <div class="form-group">

                            <label class="form-label">
                                Título del Manual
                            </label>

                            <input
                                type="text"
                                id="manualTitle"
                                class="form-control"
                                required>

                        </div>

                        <div class="form-group">

                            <label class="form-label">
                                Descripción
                            </label>

                            <textarea id="manualDescription" class="form-control" style="min-height: 60px;"></textarea>

                        </div>

                        <div class="form-group">

                            <label class="form-label">
                                Archivo PDF
                            </label>

                            <input
                                type="file"
                                id="manualFile"
                                class="form-control"
                                accept="application/pdf"
                                required>

                            <small
                                style="
                                color: var(--text-muted);
                                margin-top: 5px;
                                display: block;
                            ">
                                * Solo PDFs menores a 25 MB.
                            </small>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Guardar Manual

                            </button>

                            <button
                                type="button"
                                id="btnCancelarManual"
                                class="btn btn-secondary">

                                Cancelar

                            </button>

                        </div>

                    </form>

                </div>

                <!-- Tabla -->
                <div class="card">

                    <div
                        class="table-container"
                        id="manualesContainer">

                        <!-- JS -->

                    </div>

                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>
    <script src="js/comun.js"></script>
    <script src="js/manuales/manuales.js"></script>

</body>

</html>