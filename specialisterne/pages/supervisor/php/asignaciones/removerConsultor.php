<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $idProyecto = $_POST["id_proyecto"] ?? null;
    $idConsultor = $_POST["id_consultor"] ?? null;

    if (!$idProyecto || !$idConsultor) {
        throw new Exception("Datos incompletos");
    }

    $stmt = $pdo->prepare("
        DELETE FROM Proyecto_Consultor
        WHERE id_proyecto = ?
        AND id_consultor = ?
    ");

    $stmt->execute([$idProyecto, $idConsultor]);

    echo json_encode([
        "success" => true,
        "message" => "Consultor eliminado"
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
