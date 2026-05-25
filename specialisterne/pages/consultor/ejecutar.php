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

if (!$idCaso || !is_numeric($idCaso)) {

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDAR QUE EL CASO PERTENEZCA A UN PROYECTO DEL CONSULTOR
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        cp.id,
        cp.titulo,
        cp.descripcion,
        cp.instrucciones,
        cp.resultado_esperado,

        p.id AS id_proyecto,
        p.nombre AS proyecto,

        ecp.estado

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

    INNER JOIN EstadoCasoPrueba ecp
        ON ecp.id = cp.id_estado_caso_prueba

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

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Ejecutar Prueba - Specialisterne
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
                                href="casos.php?id=<?= $caso["id_proyecto"] ?>"
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

                                Volver a la lista de casos

                            </a>

                        </div>

                        <h1 style="margin-top: 10px;">

                            Ejecutar Caso:
                            <?= $caso["id"] ?>

                        </h1>

                    </div>

                </div>

                <div
                    class="dashboard-grid"
                    style="grid-template-columns: 3fr 2fr;">

                    <!-- INFORMACIÓN DEL CASO -->
                    <div
                        class="card"
                        style="border-top: 4px solid var(--primary-color);">

                        <h2
                            class="card-title"
                            style="
                                font-size: 20px;
                                margin-bottom: 20px;
                            ">

                            <?= htmlspecialchars($caso["titulo"]) ?>

                        </h2>

                        <div style="margin-bottom: 20px;">

                            <h3
                                style="
                                    font-size: 14px;
                                    color: var(--text-muted);
                                    margin-bottom: 8px;
                                    text-transform: uppercase;
                                ">

                                Instrucciones Paso a Paso:

                            </h3>

                            <div
                                style="
                                    background: var(--bg-color);
                                    padding: 15px;
                                    border-radius: 8px;
                                    font-size: 15px;
                                    line-height: 1.6;
                                    white-space: pre-line;
                                ">

                                <?= htmlspecialchars($caso["instrucciones"]) ?>

                            </div>

                        </div>

                        <div>

                            <h3
                                style="
                                    font-size: 14px;
                                    color: var(--text-muted);
                                    margin-bottom: 8px;
                                    text-transform: uppercase;
                                ">

                                Resultado Esperado:

                            </h3>

                            <div
                                style="
                                    background: rgba(91, 173, 145, 0.1);
                                    border-left: 4px solid var(--success-color);
                                    padding: 15px;
                                    border-radius: 4px;
                                    font-size: 15px;
                                    white-space: pre-line;
                                ">

                                <?= htmlspecialchars($caso["resultado_esperado"]) ?>

                            </div>

                        </div>

                    </div>

                    <!-- FORMULARIO -->
                    <div
                        class="card"
                        style="
                            background: #F8F9FB;
                            border: 1px solid var(--border-color);
                        ">

                        <h2
                            class="card-title"
                            style="margin-bottom: 20px;">

                            Registrar Resultado

                        </h2>

                        <form id="executionForm">

                            <div class="form-group">

                                <label
                                    class="form-label"
                                    style="font-size: 16px;">

                                    ¿El resultado fue el esperado?

                                </label>

                                <select
                                    class="form-control"
                                    id="resultadoSelect"
                                    style="
                                        font-size: 16px;
                                        padding: 12px;
                                    "
                                    required>

                                    <option value="">
                                        Seleccione el resultado...
                                    </option>

                                    <option value="Completado">
                                        ✅ Sí, funcionó correctamente (Completado)
                                    </option>

                                    <option value="Fallido">
                                        ❌ No, ocurrió un error (Fallido)
                                    </option>

                                    <option value="Pendiente">
                                        ⚠️ No pude realizar la prueba (Pendiente)
                                    </option>

                                </select>

                            </div>

                            <div class="form-group">

                                <label class="form-label">

                                    Observaciones

                                </label>

                                <textarea
                                    id="observacionesInput"
                                    class="form-control"
                                    placeholder="Escribe aquí observaciones de la ejecución..."
                                    rows="5"></textarea>

                            </div>

                            <div class="form-group">

                                <label class="form-label">

                                    Fecha y Hora de Ejecución

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="executionDate"
                                    readonly
                                    style="
                                        background: #e9ecef;
                                        cursor: not-allowed;
                                    ">

                            </div>

                            <div
                                id="errorReportBtn"
                                style="
                                    display: none;
                                    margin-bottom: 20px;
                                    background: rgba(214, 64, 69, 0.1);
                                    padding: 15px;
                                    border-radius: 8px;
                                    border-left: 4px solid var(--error-color);
                                ">

                                <p
                                    style="
                                        font-size: 14px;
                                        margin-bottom: 10px;
                                        color: var(--error-color);
                                        font-weight: 500;
                                    ">

                                    <i
                                        data-lucide="alert-triangle"
                                        size="16">
                                    </i>

                                    Has marcado la prueba como Fallida.

                                </p>

                                <a
                                    id="reportErrorLink"
                                    href="#"
                                    class="btn btn-danger"
                                    style="width: 100%;">

                                    Proceder a Reportar Error

                                </a>

                            </div>

                            <div
                                class="d-flex gap-2"
                                id="submitButtons">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    style="
                                        flex: 1;
                                        padding: 12px;
                                    ">

                                    Guardar Ejecución

                                </button>

                                <a
                                    href="casos.php?id=<?= $caso["id_proyecto"] ?>"
                                    class="btn btn-secondary"
                                    style="padding: 12px;">

                                    Cancelar

                                </a>

                            </div>

                        </form>

                    </div>

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
    <script src="js/ejecutar/ejecutar.js"></script>

</body>

</html>