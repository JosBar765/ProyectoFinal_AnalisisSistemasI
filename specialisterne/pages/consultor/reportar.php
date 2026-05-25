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

$idCaso = $_GET["id"] ?? null;

if (
    !$idCaso
    || !is_numeric($idCaso)
) {

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDAR ACCESO AL CASO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        cp.id,
        cp.titulo,

        p.id AS id_proyecto,
        p.nombre AS proyecto

    FROM CasoPrueba cp

    INNER JOIN FaseProyecto fp
        ON fp.id = cp.id_fase_proyecto

    INNER JOIN Proyecto p
        ON p.id = fp.id_proyecto

    INNER JOIN EstadoProyecto ep
        ON ep.id = p.id_estado_proyecto

    INNER JOIN Proyecto_Consultor pc
        ON pc.id_proyecto = p.id

    INNER JOIN Consultor c
        ON c.id = pc.id_consultor

    WHERE cp.id = ?
    AND c.id_usuario = ?
    AND ep.estado = 'Activo'
");

$stmt->execute([
    $idCaso,
    $user["id"]
]);

$caso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$caso) {

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| OBTENER SEVERIDADES
|--------------------------------------------------------------------------
*/

$stmtSeveridades = $pdo->query("
    SELECT
        id,
        severidad_error
    FROM SeveridadError
");

$severidades =
    $stmtSeveridades->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Reportar Error - Specialisterne
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
                        <?= htmlspecialchars($caso["proyecto"]) ?>
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
                                href="ejecutar.php?id=<?= $caso["id"] ?>"
                                style="
                                    color: var(--text-muted);
                                    text-decoration: none;
                                ">

                                <i
                                    data-lucide="arrow-left"
                                    size="14">
                                </i>

                                Volver a ejecución

                            </a>

                        </div>

                        <h1 style="margin-top: 10px;">

                            Reportar Error Encontrado

                        </h1>

                    </div>

                </div>

                <div
                    class="card"
                    style="
                        max-width: 800px;
                        margin: 0 auto;
                        border-top: 4px solid var(--error-color);
                    ">

                    <div
                        style="
                            background: var(--bg-color);
                            padding: 15px;
                            border-radius: 8px;
                            margin-bottom: 25px;
                            border-left: 4px solid var(--text-muted);
                        ">

                        <span
                            style="
                                font-size: 12px;
                                color: var(--text-muted);
                                text-transform: uppercase;
                                font-weight: 600;
                            ">

                            Reportando fallo para el caso:

                        </span>

                        <h3
                            style="
                                font-size: 16px;
                                margin-top: 5px;
                            ">

                            <?= $caso["id"] ?>:
                            <?= htmlspecialchars($caso["titulo"]) ?>

                        </h3>

                    </div>

                    <form id="errorForm">

                        <div class="form-group">

                            <label
                                class="form-label"
                                style="
                                    font-size: 16px;
                                    font-weight: 600;
                                ">

                                ¿Qué ocurrió? (Título corto)

                            </label>

                            <input
                                type="text"
                                id="tituloInput"
                                class="form-control"
                                placeholder="Ej. El botón no hace nada al hacer clic"
                                style="
                                    font-size: 16px;
                                    padding: 12px;
                                "
                                required>

                        </div>

                        <div class="form-group">

                            <label class="form-label">

                                Descripción detallada

                            </label>

                            <textarea
                                id="descripcionInput"
                                class="form-control"
                                placeholder="Explica exactamente qué estabas haciendo y qué falló..."
                                style="min-height: 120px;"
                                required></textarea>

                        </div>

                        <div class="form-group">

                            <label class="form-label">

                                Severidad del error

                            </label>

                            <div
                                id="severityContainer"
                                style="
                                    display: flex;
                                    gap: 15px;
                                    flex-wrap: wrap;
                                ">

                                <?php foreach ($severidades as $index => $severidad): ?>

                                    <label
                                        class="severity-option"
                                        style="
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                            background: #fff;
                                            border: 1px solid var(--border-color);
                                            padding: 10px 15px;
                                            border-radius: 8px;
                                            cursor: pointer;
                                        ">

                                        <input
                                            type="radio"
                                            name="severidad"
                                            value="<?= $severidad["id"] ?>"
                                            <?= $index === 0 ? "checked" : "" ?>>

                                        <?= htmlspecialchars($severidad["severidad_error"]) ?>

                                    </label>

                                <?php endforeach; ?>

                            </div>

                        </div>

                        <div
                            class="form-group"
                            style="margin-top: 30px;">

                            <label class="form-label">

                                Adjuntar Capturas de Pantalla

                            </label>

                            <label
                                for="imagenesInput"
                                style="
                                    border: 2px dashed var(--border-color);
                                    border-radius: 8px;
                                    padding: 40px;
                                    text-align: center;
                                    background: var(--bg-color);
                                    cursor: pointer;
                                    display: block;
                                ">

                                <i
                                    data-lucide="image-plus"
                                    size="32"
                                    style="
                                        color: var(--text-muted);
                                        margin-bottom: 10px;
                                    ">
                                </i>

                                <p
                                    style="
                                        font-size: 15px;
                                        font-weight: 500;
                                    ">

                                    Haz clic aquí para seleccionar imágenes

                                </p>

                                <p
                                    style="
                                        font-size: 13px;
                                        color: var(--text-muted);
                                    ">

                                    o arrastra archivos aquí

                                </p>

                                <input
                                    type="file"
                                    id="imagenesInput"
                                    multiple
                                    accept="image/*"
                                    style="display: none;">

                            </label>

                            <div
                                id="previewContainer"
                                style="
                                    display: flex;
                                    gap: 10px;
                                    flex-wrap: wrap;
                                    margin-top: 15px;
                                ">
                            </div>

                        </div>

                        <div
                            class="d-flex gap-2"
                            style="
                                margin-top: 30px;
                                border-top: 1px solid var(--border-color);
                                padding-top: 20px;
                            ">

                            <button
                                type="submit"
                                class="btn btn-primary"
                                style="
                                    font-size: 16px;
                                    padding: 12px 24px;
                                ">

                                <i
                                    data-lucide="send"
                                    size="18">
                                </i>

                                Enviar Reporte

                            </button>

                            <a
                                href="ejecutar.php?id=<?= $caso["id"] ?>"
                                class="btn btn-secondary"
                                style="
                                    font-size: 16px;
                                    padding: 12px 24px;
                                ">

                                Cancelar

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </main>

    </div>

    <script>
        const CASE_ID = <?= $caso["id"] ?>;
        const PROJECT_ID = <?= $caso["id_proyecto"] ?>;
    </script>

    <script src="../../js/app.js"></script>
    <script src="js/comun.js"></script>
    <script src="js/reportar/reportar.js"></script>

</body>

</html>