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

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$idCaso =
    $data["id_caso"] ?? null;

$resultado =
    trim($data["resultado"] ?? "");

$observaciones =
    trim($data["observaciones"] ?? "");

/*
|--------------------------------------------------------------------------
| VALIDACIONES
|--------------------------------------------------------------------------
*/

if (
    !$idCaso
    || !is_numeric($idCaso)
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Caso inválido"
    ]);

    exit;
}

$resultadosValidos = [
    "Completado",
    "Fallido",
    "Pendiente"
];

if (
    !in_array(
        $resultado,
        $resultadosValidos
    )
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Resultado inválido"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| OBTENER CONSULTOR
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM Consultor
    WHERE id_usuario = ?
");

$stmt->execute([
    $user["id"]
]);

$consultor =
    $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consultor) {

    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Consultor no encontrado"
    ]);

    exit;
}

$idConsultor =
    $consultor["id"];

/*
|--------------------------------------------------------------------------
| VALIDAR ACCESO AL CASO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        cp.id
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

$caso =
    $stmt->fetch(PDO::FETCH_ASSOC);

if (!$caso) {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "No tienes acceso a este caso"
    ]);

    exit;
}

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | INSERTAR EJECUCIÓN
    |--------------------------------------------------------------------------
    */

    $stmtInsert = $pdo->prepare("
        INSERT INTO EjecucionPrueba (
            id_consultor,
            id_caso_prueba,
            resultado,
            observaciones,
            fecha_ejecucion
        )
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmtInsert->execute([
        $idConsultor,
        $idCaso,
        $resultado,
        $observaciones ?: null
    ]);

    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DEL CASO A EN PROGRESO
    |--------------------------------------------------------------------------
    */

    $stmtUpdate = $pdo->prepare("
        UPDATE CasoPrueba
        SET id_estado_caso_prueba = 2
        WHERE id = ?
    ");

    $stmtUpdate->execute([
        $idCaso
    ]);

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Ejecución registrada correctamente"
    ]);
} catch (PDOException $error) {

    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Error al registrar ejecución"
    ]);
}
