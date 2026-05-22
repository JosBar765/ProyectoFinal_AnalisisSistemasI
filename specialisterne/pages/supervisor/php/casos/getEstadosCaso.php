<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $sql = "
        SELECT
            id,
            estado
        FROM EstadoCasoPrueba
        ORDER BY id ASC
    ";

    $stmt = $pdo->query($sql);

    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "estados" => $estados
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
