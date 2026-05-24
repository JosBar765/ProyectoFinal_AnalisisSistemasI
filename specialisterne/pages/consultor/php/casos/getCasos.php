<?php

session_start();

header('Content-Type: application/json');

require_once '../../../../php/db.php';

if (!isset($_SESSION["user"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);

    exit;
}

$user = $_SESSION["user"];

$idProyecto = $_GET["id"] ?? null;

if (!$idProyecto || !is_numeric($idProyecto)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Proyecto inválido"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDAR ACCESO AL PROYECTO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        p.id
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

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "No tienes acceso a este proyecto"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| OBTENER CASOS
|--------------------------------------------------------------------------
*/

$stmtConsultor = $pdo->prepare("
    SELECT id
    FROM Consultor
    WHERE id_usuario = ?
");

$stmtConsultor->execute([
    $user["id"]
]);

$consultor = $stmtConsultor->fetch(PDO::FETCH_ASSOC);

if (!$consultor) {

    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Consultor no encontrado"
    ]);

    exit;
}

$idConsultor = $consultor["id"];

$stmt = $pdo->prepare("
    SELECT
        cp.id,
        cp.titulo,
        cp.descripcion,
        f.nombre AS fase,
        ecp.estado,

        ep.resultado,
        ep.fecha_ejecucion

    FROM CasoPrueba cp

    INNER JOIN FaseProyecto f
        ON f.id = cp.id_fase_proyecto

    INNER JOIN EstadoCasoPrueba ecp
        ON ecp.id = cp.id_estado_caso_prueba

    LEFT JOIN EjecucionPrueba ep
    ON ep.id = (
        SELECT ep2.id
        FROM EjecucionPrueba ep2
        WHERE ep2.id_caso_prueba = cp.id
        ORDER BY ep2.fecha_ejecucion DESC
        LIMIT 1
    )

    WHERE f.id_proyecto = ?

    ORDER BY
        CASE
            WHEN ecp.estado = 'Pendiente' THEN 1
            WHEN ecp.estado = 'En Progreso' THEN 2
            WHEN ecp.estado = 'Completado' THEN 3
            WHEN ecp.estado = 'Fallido' THEN 4
            ELSE 5
        END,
        cp.id ASC
");

$stmt->execute([
    $idProyecto
]);

$casos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pendientes = [];
$completados = [];

foreach ($casos as $caso) {
    $estado = $caso["estado"];

    if ($estado === "Pendiente" || $estado === "En Progreso") {
        $pendientes[] = $caso;
    } else {
        $completados[] = $caso;
    }
}

echo json_encode([
    "success" => true,
    "pendientes" => $pendientes,
    "completados" => $completados
]);
