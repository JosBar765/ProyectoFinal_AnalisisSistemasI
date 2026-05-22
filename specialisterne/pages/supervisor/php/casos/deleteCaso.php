<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $idCaso =
        $data["id_caso"] ?? null;

    if (!$idCaso) {

        echo json_encode([
            "success" => false,
            "message" => "Caso inválido"
        ]);

        exit;
    }

    $sql = "
        DELETE FROM CasoPrueba
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "id" => $idCaso
    ]);

    if ($stmt->rowCount() === 0) {

        echo json_encode([
            "success" => false,
            "message" => "El caso no existe"
        ]);

        exit;
    }

    echo json_encode([
        "success" => true
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
