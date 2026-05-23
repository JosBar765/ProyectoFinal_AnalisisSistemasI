<?php

session_start();

header("Content-Type: application/json");

require_once '../../../../php/db.php';

if (!isset($_SESSION["user"])) {

    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);

    exit;
}

if ($_SESSION["user"]["id_rol"] != 2) {

    echo json_encode([
        "success" => false,
        "message" => "Acceso denegado"
    ]);

    exit;
}

$idError =
    $_POST["id_error"] ?? null;

$estado =
    $_POST["estado"] ?? null;

if (
    !$idError &&
    $idError != 0
) {

    echo json_encode([
        "success" => false,
        "message" => "ID de error inválido"
    ]);

    exit;
}

if (
    $estado === null ||
    !in_array($estado, ["0", "1", 0, 1], true)
) {

    echo json_encode([
        "success" => false,
        "message" => "Estado inválido"
    ]);

    exit;
}

try {

    $sql = "
        UPDATE ErrorReporte
        SET estado = ?
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $estado,
        $idError
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Estado actualizado"
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
