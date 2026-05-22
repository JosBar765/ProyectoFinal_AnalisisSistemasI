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

    <title>Crear Proyecto - Specialisterne</title>

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

                <a href="index.php">
                    <i data-lucide="layout-dashboard"></i>
                    Dashboard
                </a>

                <a href="proyectos.php" class="active">
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

        <!-- Main -->
        <main class="main-content">

            <header class="top-header">

                <div></div>

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

                            <a
                                href="proyectos.php"
                                style="
                                    color: var(--text-muted);
                                    text-decoration: none;
                                ">

                                Supervisor / Proyectos

                            </a>

                            / Nuevo

                        </div>

                        <h1>
                            Crear Proyecto
                        </h1>

                    </div>

                </div>

                <div
                    class="card"
                    style="max-width: 800px;">

                    <form id="proyectoForm">

                        <!-- Nombre -->
                        <div class="form-group">

                            <label class="form-label">
                                Nombre del Proyecto
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="nombreProyecto"
                                required
                                placeholder="Ej. Sistema de Inventario V2">

                        </div>

                        <!-- Descripción -->
                        <div class="form-group">

                            <label class="form-label">
                                Descripción
                            </label>

                            <textarea
                                class="form-control"
                                id="descripcionProyecto"
                                required
                                placeholder="Descripción general de lo que se va a probar..."></textarea>

                        </div>

                        <!-- Fechas -->
                        <div class="dashboard-grid">

                            <div class="form-group">

                                <label class="form-label">
                                    Fecha de Inicio
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="fechaInicio"
                                    required>

                            </div>

                            <div class="form-group">

                                <label class="form-label">
                                    Fecha de Fin
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="fechaFin"
                                    required>

                            </div>

                            <div class="form-group">

                                <label class="form-label">
                                    Estado
                                </label>

                                <select
                                    class="form-control"
                                    id="estadoProyecto">

                                    <option value="Activo">
                                        Activo
                                    </option>

                                    <option value="En Pausa">
                                        En Pausa
                                    </option>

                                    <option value="Finalizado">
                                        Finalizado
                                    </option>

                                </select>

                            </div>

                        </div>

                        <!-- Fases -->
                        <div
                            style="
                                margin-top: 30px;
                                margin-bottom: 20px;
                                border-top: 1px solid var(--border-color);
                                padding-top: 20px;
                            ">

                            <div class="d-flex justify-between align-center mb-3">

                                <h3>
                                    Fases del Proyecto
                                </h3>

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    id="btnAgregarFase">

                                    <i data-lucide="plus" size="16"></i>
                                    Agregar Fase

                                </button>

                            </div>

                            <!-- Container -->
                            <div
                                id="fasesContainer"
                                style="
                                    display: flex;
                                    flex-direction: column;
                                    gap: 15px;
                                ">

                                <!-- Fase inicial -->
                                <div
                                    class="card fase-item"
                                    style="
                                        background-color: var(--bg-color);
                                        margin-bottom: 0;
                                    ">

                                    <div class="d-flex justify-between align-center mb-2">

                                        <strong>
                                            Fase 1
                                        </strong>

                                        <button
                                            type="button"
                                            class="btn btn-danger"
                                            style="padding: 4px 8px;"
                                            disabled>

                                            <i data-lucide="trash-2" size="14"></i>

                                        </button>

                                    </div>

                                    <div class="form-group">

                                        <input
                                            type="text"
                                            class="
                                                form-control
                                                mb-2
                                                inputNombreFase
                                            "
                                            placeholder="Nombre de la fase"
                                            required>

                                        <input
                                            type="text"
                                            class="
                                                form-control
                                                inputDescripcionFase
                                            "
                                            placeholder="Descripción breve"
                                            required>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Buttons -->
                        <div
                            class="d-flex gap-2"
                            style="margin-top: 30px;">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Guardar Proyecto

                            </button>

                            <a
                                href="proyectos.php"
                                class="btn btn-secondary">

                                Cancelar

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>
    <script src="js/proyecto-form.js"></script>

</body>

</html>