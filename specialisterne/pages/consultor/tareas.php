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
    <title>Mis Tareas - Consultor</title>

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

                <a href="index.php">
                    <i data-lucide="folder-kanban"></i>
                    Mis Proyectos
                </a>

                <a href="tareas.php" class="active">
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
                        <h1>Historial de Tareas y Ejecuciones</h1>
                    </div>

                </div>

                <div class="card">

                    <div class="card-header">

                        <div class="d-flex gap-2">

                            <select
                                id="filterProyecto"
                                class="form-control"
                                style="width: 250px;">

                                <option value="">
                                    Todos los Proyectos
                                </option>

                            </select>

                            <select
                                id="filterResultado"
                                class="form-control"
                                style="width: 220px;">

                                <option value="">
                                    Todos los Resultados
                                </option>

                                <option value="Completado">
                                    Completados
                                </option>

                                <option value="Fallido">
                                    Fallidos
                                </option>

                                <option value="En Progreso">
                                    En Progreso
                                </option>

                                <option value="Pendiente">
                                    Pendientes
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="table-container">

                        <table>

                            <thead>

                                <tr>
                                    <th>Caso de Prueba</th>
                                    <th>Proyecto</th>
                                    <th>Fecha de Ejecución</th>
                                    <th>Resultado</th>
                                </tr>

                            </thead>

                            <tbody id="tasksTableBody">
                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </main>

    </div>

    <script src="../../js/app.js"></script>

    <script src="js/comun.js"></script>

    <script src="js/tareas/tareas.js"></script>

</body>

</html>