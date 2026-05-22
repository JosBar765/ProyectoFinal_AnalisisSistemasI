<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $data =
        json_decode(
            file_get_contents("php://input"),
            true
        );

    $idProyecto =
        $data["id_proyecto"] ?? null;

    if (!$idProyecto) {

        echo json_encode([
            "success" => false,
            "message" => "Proyecto inválido"
        ]);

        exit;
    }

    $sql = "
        DELETE FROM Proyecto
        WHERE id = :id_proyecto
    ";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute([
        "id_proyecto" => $idProyecto
    ]);

    echo json_encode([
        "success" => true
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
