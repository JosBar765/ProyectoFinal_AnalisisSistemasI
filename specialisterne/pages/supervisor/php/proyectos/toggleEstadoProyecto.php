<?php

session_start();

require_once '../../../../php/db.php';

header('Content-Type: application/json');

try {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $idProyecto =
        (int)($data["id_proyecto"] ?? 0);

    if ($idProyecto <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Proyecto inválido"
        ]);

        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE proyecto
        SET id_estado_proyecto =
            CASE
                WHEN id_estado_proyecto = 1
                    THEN 2
                ELSE 1
            END
        WHERE id = ?
    ");

    $stmt->execute([$idProyecto]);

    echo json_encode([
        "success" => true
    ]);
} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
