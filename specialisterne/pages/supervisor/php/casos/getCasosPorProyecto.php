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
            cp.id,
            cp.titulo,
            cp.descripcion,
            cp.instrucciones,
            cp.resultado_esperado,
            cp.id_estado_caso_prueba,

            fp.id AS fase_id,
            fp.nombre AS fase_nombre,

            ecp.estado

        FROM CasoPrueba cp

        INNER JOIN FaseProyecto fp
            ON fp.id = cp.id_fase_proyecto

        INNER JOIN EstadoCasoPrueba ecp
            ON ecp.id = cp.id_estado_caso_prueba

        WHERE fp.id_proyecto = :id_proyecto

        ORDER BY
            fp.id ASC,
            cp.id ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "id_proyecto" => $idProyecto
    ]);

    $casos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "casos" => $casos
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
