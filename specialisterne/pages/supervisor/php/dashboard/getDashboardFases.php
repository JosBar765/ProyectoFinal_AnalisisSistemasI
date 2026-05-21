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

            fp.nombre AS fase,

            COUNT(cp.id) AS total_casos,

            SUM(
                CASE
                    WHEN ecp.estado = 'Completado'
                    THEN 1
                    ELSE 0
                END
            ) AS completados,

            SUM(
                CASE
                    WHEN ecp.estado = 'Fallido'
                    THEN 1
                    ELSE 0
                END
            ) AS fallidos,

            SUM(
                CASE
                    WHEN ecp.estado = 'En Progreso'
                    THEN 1
                    ELSE 0
                END
            ) AS en_progreso

        FROM FaseProyecto fp

        LEFT JOIN CasoPrueba cp
            ON fp.id = cp.id_fase_proyecto

        LEFT JOIN EstadoCasoPrueba ecp
            ON cp.id_estado_caso_prueba = ecp.id

        WHERE fp.id_proyecto = :id_proyecto

        GROUP BY fp.id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "id_proyecto" => $idProyecto
    ]);

    $fases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultado = [];

    foreach ($fases as $fase) {

        $total =
            (int)$fase["total_casos"];

        $completados =
            (int)$fase["completados"];

        $fallidos =
            (int)$fase["fallidos"];

        $enProgreso =
            (int)$fase["en_progreso"];

        $avance = 0;

        if ($total > 0) {

            $avance =
                round((($completados + $fallidos) / $total) * 100);
        }

        $estado = "Pendiente";

        if ($avance == 100) {
            $estado = "Completado";
        } else if ($enProgreso > 0) {
            $estado = "En Progreso";
        }

        $resultado[] = [
            "fase" => $fase["fase"],
            "avance" => $avance,
            "estado" => $estado
        ];
    }

    echo json_encode($resultado);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
