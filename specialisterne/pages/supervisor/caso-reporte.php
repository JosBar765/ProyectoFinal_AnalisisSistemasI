<?php

session_start();

require_once '../../php/path.php';
require_once '../../php/db.php';

if (!isset($_SESSION["user"])) {

    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["user"]["id_rol"] != 2) {

    header("Location: {$path}/403.php");
    exit;
}

$idCaso =
    filter_input(
        INPUT_GET,
        "id",
        FILTER_VALIDATE_INT
    );

if (!$idCaso || $idCaso <= 0) {

    header("Location: casos.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        cp.id,
        cp.titulo,
        cp.descripcion,
        cp.instrucciones,
        cp.resultado_esperado,

        ecp.estado,

        fp.nombre AS fase,

        p.nombre AS proyecto,
        p.id AS id_proyecto

    FROM CasoPrueba cp

    INNER JOIN EstadoCasoPrueba ecp
        ON ecp.id = cp.id_estado_caso_prueba

    INNER JOIN FaseProyecto fp
        ON fp.id = cp.id_fase_proyecto

    INNER JOIN Proyecto p
        ON p.id = fp.id_proyecto

    WHERE cp.id = ?

    LIMIT 1
");

$stmt->execute([$idCaso]);

$caso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$caso) {

    header("Location: casos.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        ep.id,
        ep.resultado,
        ep.observaciones,
        ep.fecha_ejecucion,

        CONCAT(
            u.nombre,
            ' ',
            u.apellido
        ) AS consultor

    FROM EjecucionPrueba ep

    INNER JOIN Consultor c
        ON c.id = ep.id_consultor

    INNER JOIN Usuario u
        ON u.id = c.id_usuario

    WHERE ep.id_caso_prueba = ?

    ORDER BY ep.fecha_ejecucion DESC
");

$stmt->execute([$idCaso]);

$ejecuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Reporte Caso - Specialisterne
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

                <a
                    href="casos.php"
                    class="active">

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

                </div>

            </header>

            <div class="content-area">

                <div class="page-header">

                    <div class="page-title">

                        <div class="breadcrumb">

                            <a
                                href="casos.php"
                                style="
                                    color: var(--text-muted);
                                    text-decoration: none;
                                ">

                                Supervisor / Casos

                            </a>

                            / Reporte

                        </div>

                        <h1>
                            <?= $caso["titulo"] ?>
                        </h1>

                    </div>

                </div>

                <!-- Información -->
                <div class="card mb-3">

                    <div class="dashboard-grid">

                        <div>

                            <h3>
                                Proyecto
                            </h3>

                            <p>
                                <?= $caso["proyecto"] ?>
                            </p>

                        </div>

                        <div>

                            <h3>
                                Fase
                            </h3>

                            <p>
                                <?= $caso["fase"] ?>
                            </p>

                        </div>

                        <div>

                            <h3>
                                Estado
                            </h3>

                            <p>
                                <?= $caso["estado"] ?>
                            </p>

                        </div>

                    </div>

                </div>

                <!-- Descripción -->
                <div class="card mb-3">

                    <h3 class="mb-2">
                        Descripción
                    </h3>

                    <p>
                        <?= $caso["descripcion"] ?>
                    </p>

                </div>

                <!-- Instrucciones -->
                <div class="card mb-3">

                    <h3 class="mb-2">
                        Instrucciones
                    </h3>

                    <p>
                        <?= nl2br($caso["instrucciones"]) ?>
                    </p>

                </div>

                <!-- Resultado Esperado -->
                <div class="card mb-3">

                    <h3 class="mb-2">
                        Resultado Esperado
                    </h3>

                    <p>
                        <?= nl2br($caso["resultado_esperado"]) ?>
                    </p>

                </div>

                <!-- Historial -->
                <div class="card">

                    <div class="d-flex justify-between align-center mb-3">

                        <h3>
                            Historial de Ejecuciones
                        </h3>

                    </div>

                    <?php if (count($ejecuciones) === 0): ?>

                        <p
                            style="
                                color: var(--text-muted);
                            ">

                            Este caso aún no ha sido ejecutado.

                        </p>

                    <?php else: ?>

                        <div class="table-container">

                            <table>

                                <thead>

                                    <tr>

                                        <th>ID</th>
                                        <th>Consultor</th>
                                        <th>Resultado</th>
                                        <th>Observaciones</th>
                                        <th>Fecha</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($ejecuciones as $ejecucion): ?>

                                        <tr>

                                            <td>
                                                <?= $ejecucion["id"] ?>
                                            </td>

                                            <td>
                                                <?= $ejecucion["consultor"] ?>
                                            </td>

                                            <td>
                                                <?= $ejecucion["resultado"] ?>
                                            </td>

                                            <td>
                                                <?= $ejecucion["observaciones"] ?>
                                            </td>

                                            <td>
                                                <?= $ejecucion["fecha_ejecucion"] ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </main>

    </div>

    <script>
        lucide.createIcons();
    </script>

</body>

</html>