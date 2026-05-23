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

    <title>Registrar Error - Specialisterne</title>

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

                <a href="errores.php" class="active">
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

            <!-- Content -->
            <div class="content-area">

                <div class="page-header">

                    <div class="page-title">

                        <div class="breadcrumb">

                            <a
                                href="errores.php"
                                style="
                                    color: var(--text-muted);
                                    text-decoration: none;
                                ">

                                Supervisor / Errores

                            </a>

                            / Nuevo

                        </div>

                        <h1>
                            Registrar Error
                        </h1>

                    </div>

                </div>

                <div
                    class="card"
                    style="max-width: 800px;">

                    <form id="errorForm" enctype="multipart/form-data">

                        <!-- Identificadores -->
                        <div class="dashboard-grid">

                            <div class="form-group">

                                <label class="form-label">
                                    Ejecución de Prueba
                                </label>

                                <select
                                    class="form-control"
                                    id="ejecucionPrueba"
                                    required>

                                    <option value="">
                                        Selecciona una ejecución
                                    </option>

                                </select>

                            </div>

                            <div class="form-group">

                                <label class="form-label">
                                    Caso de Prueba
                                </label>

                                <select
                                    class="form-control"
                                    id="casoPrueba"
                                    required>

                                    <option value="">
                                        Selecciona un caso
                                    </option>

                                </select>

                            </div>

                        </div>

                        <!-- Detalle -->
                        <div class="form-group">

                            <label class="form-label">
                                Título del Error
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="tituloError"
                                required
                                placeholder="Ej. Cálculo de IVA incorrecto en facturas B">

                        </div>

                        <div class="form-group">

                            <label class="form-label">
                                Descripción
                            </label>

                            <textarea
                                class="form-control"
                                id="descripcionError"
                                required
                                placeholder="Describe los pasos y el resultado esperado..."></textarea>

                        </div>

                        <div class="dashboard-grid">

                            <div class="form-group">

                                <label class="form-label">
                                    Severidad
                                </label>

                                <select
                                    class="form-control"
                                    id="severidadError"
                                    required>

                                    <option value="Critico">
                                        Crítico
                                    </option>

                                    <option value="Alto">
                                        Alto
                                    </option>

                                    <option value="Medio">
                                        Medio
                                    </option>

                                    <option value="Bajo">
                                        Bajo
                                    </option>

                                </select>

                            </div>

                            <div class="form-group">

                                <label class="form-label">
                                    Estado
                                </label>

                                <select
                                    class="form-control"
                                    id="estadoError">

                                    <option value="Abierto">
                                        Abierto
                                    </option>

                                    <option value="Resuelto">
                                        Resuelto
                                    </option>

                                </select>

                            </div>

                        </div>

                        <!-- Evidencias -->
                        <div
                            style="
                                margin-top: 20px;
                                border-top: 1px solid var(--border-color);
                                padding-top: 20px;
                            ">

                            <div class="form-group">

                                <label class="form-label">
                                    Evidencia (Imagen)
                                </label>

                                <input
                                    type="file"
                                    class="form-control"
                                    id="capturaError"
                                    accept=".jpg,.jpeg,.png">

                                <small
                                    style="
                                        display: block;
                                        margin-top: 6px;
                                        color: var(--text-muted);
                                    ">

                                    Formatos permitidos: JPG o PNG.

                                </small>

                            </div>

                        </div>

                        <!-- Buttons -->
                        <div
                            class="d-flex gap-2"
                            style="margin-top: 30px;">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Guardar Error

                            </button>

                            <a
                                href="errores.php"
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
    <script src="js/comun.js"></script>
    <script src="js/errores/error-form.js"></script>

</body>

</html>
