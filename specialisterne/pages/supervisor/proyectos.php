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
    <title>Proyectos - Specialisterne</title>

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

        <!-- Main Content -->
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
                        <?= $_SESSION["user"]["nombre"] ?>
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
                            Supervisor / Proyectos
                        </div>

                        <h1>
                            Lista de Proyectos
                        </h1>

                    </div>

                    <a
                        href="proyecto-form.php"
                        class="btn btn-primary">

                        <i data-lucide="plus"></i>
                        Nuevo Proyecto

                    </a>

                </div>

                <!-- Cards dinámicas -->
                <div
                    class="dashboard-grid"
                    id="proyectosContainer">

                    <!-- JS -->

                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>
    <script src="js/proyectos.js"></script>

</body>

</html>