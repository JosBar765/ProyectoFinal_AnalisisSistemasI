<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $idProyecto = $_GET["id_proyecto"] ?? null;

    if (!$idProyecto) {

        echo json_encode([
            "success" => false,
            "message" => "Proyecto inválido"
        ]);

        exit;
    }

    $sql = "
        SELECT
            id,
            nombre,
            descripcion

        FROM FaseProyecto

        WHERE id_proyecto = :id_proyecto

        ORDER BY id ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "id_proyecto" => $idProyecto
    ]);

    $fases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "fases" => $fases
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
