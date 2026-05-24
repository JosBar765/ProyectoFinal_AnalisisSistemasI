<?php
session_start();

require_once '../../php/path.php';

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["user"]["id_rol"] != 3) {
    header("Location: {$path}/403.php");
    exit;
}

$user = $_SESSION["user"];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proyectos - Specialisterne</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .consultor-card {
            border: 2px solid transparent;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .consultor-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 8px 16px -4px rgba(74, 111, 165, 0.15);
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
                    <i data-lucide="folder-kanban"></i>
                    Mis Proyectos
                </a>

                <a href="tareas.php">
                    <i data-lucide="check-square"></i>
                    Mis Tareas
                </a>
            </nav>
        </aside>

        <main class="main-content">

            <header class="top-header">

                <div></div>

                <div class="user-info">

                    <span
                        class="role-badge"
                        style="background-color: rgba(74, 111, 165, 0.15); color: var(--primary-color);">
                        Consultor
                    </span>

                    <span>
                        <?= htmlspecialchars($user["nombre"] . " " . $user["apellido"]) ?>
                    </span>

                    <div class="avatar">
                        <?= strtoupper(substr($user["nombre"], 0, 1)) . strtoupper(substr($user["apellido"], 0, 1)) ?>
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
                        <h1>Mis Proyectos Asignados</h1>
                    </div>
                </div>

                <!-- CONTENEDOR DINÁMICO -->
                <div
                    id="projectsContainer"
                    class="dashboard-grid">
                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>
    <script src="js/comun.js"></script>
    <script src="js/proyectos/proyectos.js"></script>

</body>

</html>