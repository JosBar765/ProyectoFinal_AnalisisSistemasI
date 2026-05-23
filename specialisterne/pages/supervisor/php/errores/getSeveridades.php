<?php

require_once '../../../../php/db.php';

header('Content-Type: application/json');

try {

    $stmt = $pdo->query("
        SELECT *
        FROM SeveridadError
        ORDER BY id DESC
    ");

    echo json_encode([
        'success' => true,
        'severidades' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
