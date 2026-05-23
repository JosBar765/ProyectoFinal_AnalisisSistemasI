<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $idProyecto = $_POST["id_proyecto"] ?? null;
    $idConsultor = $_POST["id_consultor"] ?? null;

    if (!$idProyecto || !$idConsultor) {
        throw new Exception("Datos incompletos");
    }

    // evitar duplicados
    $check = $pdo->prepare("
        SELECT 1 
        FROM Proyecto_Consultor
        WHERE id_proyecto = ? AND id_consultor = ?
    ");

    $check->execute([$idProyecto, $idConsultor]);

    if ($check->fetch()) {
        throw new Exception("Ya está asignado");
    }

    $stmt = $pdo->prepare("
        INSERT INTO Proyecto_Consultor (
            id_proyecto,
            id_consultor,
            fecha_asignacion
        ) VALUES (
            ?, ?, NOW()
        )
    ");

    $stmt->execute([$idProyecto, $idConsultor]);

    echo json_encode([
        "success" => true,
        "message" => "Consultor agregado"
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
