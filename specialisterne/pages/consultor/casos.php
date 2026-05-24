<?php

session_start();

require_once '../../php/path.php';
require_once '../../php/db.php';

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["user"]["id_rol"] != 3) {
    header("Location: {$path}/403.php");
    exit;
}

$user = $_SESSION["user"];

$idProyecto = $_GET["id"] ?? null;

if (!$idProyecto || !is_numeric($idProyecto)) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        p.id,
        p.nombre
    FROM Proyecto p
    INNER JOIN Proyecto_Consultor pc
        ON pc.id_proyecto = p.id
    INNER JOIN Consultor c
        ON c.id = pc.id_consultor
    INNER JOIN EstadoProyecto ep
        ON ep.id = p.id_estado_proyecto
    WHERE p.id = ?
    AND c.id_usuario = ?
    AND ep.estado = 'Activo'
");

$stmt->execute([
    $idProyecto,
    $user["id"]
]);

$proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proyecto) {
    header("Location: index.php");
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

    <style>
        .caso-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;

            padding: 20px;

            background-color: white;

            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);

            margin-bottom: 15px;

            transition:
                border-color 0.2s,
                box-shadow 0.2s;
        }

        .caso-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 8px 16px -4px rgba(74, 111, 165, 0.10);
        }

        .caso-completado {
            background-color: var(--bg-color);
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

                <a
                    href="index.php"
                    class="active">

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

                <div
                    style="
                        display: flex;
                        align-items: center;
                        gap: 15px;
                        font-size: 16px;
                    ">

                    <span
                        style="
                            font-weight: 500;
                            color: var(--text-muted);
                        ">

                        Proyecto:

                    </span>

                    <strong>
                        <?= htmlspecialchars($proyecto["nombre"]) ?>
                    </strong>

                </div>

                <div class="user-info">

                    <span
                        class="role-badge"
                        style="
                            background-color: rgba(74, 111, 165, 0.15);
                            color: var(--primary-color);
                        ">

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

                        <i
                            data-lucide="log-out"
                            size="18">
                        </i>

                    </a>

                </div>

            </header>

            <div class="content-area">

                <div class="page-header">

                    <div class="page-title">

                        <div class="breadcrumb">

                            <a
                                href="index.php"
                                style="
                                    color: var(--text-muted);
                                    text-decoration: none;
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 5px;
                                ">

                                <i
                                    data-lucide="arrow-left"
                                    size="14">
                                </i>

                                Volver a Mis Proyectos

                            </a>

                        </div>

                        <h1 style="margin-top: 10px;">

                            Casos de Prueba Asignados

                        </h1>

                    </div>

                </div>

                <div class="card">

                    <div
                        id="tabsContainer"
                        class="tabs">
                    </div>

                    <div
                        id="casesContainer"
                        class="tab-content-container">
                    </div>

                </div>

            </div>

        </main>

    </div>

    <script>
        const PROJECT_ID = <?= $proyecto["id"] ?>;
    </script>

    <script src="../../js/app.js"></script>
    <script src="js/comun.js"></script>
    <script src="js/casos/casos.js"></script>

</body>

</html>