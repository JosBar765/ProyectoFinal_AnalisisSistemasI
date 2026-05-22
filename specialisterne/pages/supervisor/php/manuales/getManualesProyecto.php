<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $idProyecto =
        $_GET["id_proyecto"] ?? null;

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
            titulo,
            descripcion,
            url,

            DATE_FORMAT(
                fecha_subida,
                '%d/%m/%Y %H:%i'
            ) AS fecha_subida

        FROM ManualPrueba

        WHERE id_proyecto = :id_proyecto

        ORDER BY fecha_subida DESC
    ";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute([
        "id_proyecto" => $idProyecto
    ]);

    $manuales =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "manuales" => $manuales
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
