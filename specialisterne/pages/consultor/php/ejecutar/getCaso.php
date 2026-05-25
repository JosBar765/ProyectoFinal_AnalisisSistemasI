// php/casos/getCaso.php

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

$idCaso = $_GET["id"] ?? null;

if (!$idCaso || !is_numeric($idCaso)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Caso inválido"
    ]);

    exit;
}

$stmt = $pdo->prepare("
    SELECT
        cp.id,
        cp.titulo,
        cp.descripcion,
        cp.pasos,
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

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "No tienes acceso a este caso"
    ]);

    exit;
}

echo json_encode([
    "success" => true,
    "case" => $caso
]);
