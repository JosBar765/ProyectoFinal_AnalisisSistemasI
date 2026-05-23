<?php

require_once '../../../../php/db.php';

header('Content-Type: application/json');

try {

    $idEjecucion =
        $_GET['id_ejecucion'] ?? 0;

    $stmt = $pdo->prepare("
        SELECT cp.titulo
        FROM EjecucionPrueba ep
        INNER JOIN CasoPrueba cp
            ON cp.id = ep.id_caso_prueba
        WHERE ep.id = ?
    ");

    $stmt->execute([$idEjecucion]);

    $caso =
        $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'caso_prueba' => $caso['titulo']
    ]);
} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
