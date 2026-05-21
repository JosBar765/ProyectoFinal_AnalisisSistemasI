<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $sql = "
        SELECT
            id,
            nombre
        FROM Proyecto
        ORDER BY fecha_inicio DESC
    ";

    $stmt = $pdo->query($sql);

    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($proyectos);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
