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

    $sqlProyecto = "
        SELECT
            p.nombre,
            ep.estado
        FROM Proyecto p
        JOIN EstadoProyecto ep
            ON p.id_estado_proyecto = ep.id
        WHERE p.id = :id_proyecto
        LIMIT 1
    ";

    $stmtProyecto = $pdo->prepare($sqlProyecto);

    $stmtProyecto->execute([
        "id_proyecto" => $idProyecto
    ]);

    $proyecto = $stmtProyecto->fetch(PDO::FETCH_ASSOC);

    $sqlCasos = "
        SELECT
            ecp.estado,
            COUNT(*) AS total
        FROM CasoPrueba cp

        JOIN FaseProyecto fp
            ON cp.id_fase_proyecto = fp.id

        JOIN EstadoCasoPrueba ecp
            ON cp.id_estado_caso_prueba = ecp.id

        WHERE fp.id_proyecto = :id_proyecto

        GROUP BY ecp.estado
    ";

    $stmtCasos = $pdo->prepare($sqlCasos);

    $stmtCasos->execute([
        "id_proyecto" => $idProyecto
    ]);

    $casos = $stmtCasos->fetchAll(PDO::FETCH_ASSOC);

    $pendientes = 0;
    $enProgreso = 0;
    $completados = 0;
    $fallidos = 0;

    foreach ($casos as $caso) {

        switch ($caso["estado"]) {

            case "Pendiente":
                $pendientes = (int)$caso["total"];
                break;

            case "En Progreso":
                $enProgreso = (int)$caso["total"];
                break;

            case "Completado":
                $completados = (int)$caso["total"];
                break;

            case "Fallido":
                $fallidos = (int)$caso["total"];
                break;
        }
    }

    $totalCasos =
        $pendientes +
        $enProgreso +
        $completados +
        $fallidos;

    $ejecutados =
        $completados +
        $fallidos;

    $avance = 0;

    if ($totalCasos > 0) {

        $avance =
            round(($ejecutados / $totalCasos) * 100);
    }

    echo json_encode([

        "proyecto" => $proyecto["nombre"],
        "estado" => $proyecto["estado"],

        "avance" => $avance,

        "total_casos" => $totalCasos,
        "ejecutados" => $ejecutados,

        "pendientes" => $pendientes,
        "en_progreso" => $enProgreso,
        "completados" => $completados,
        "fallidos" => $fallidos

    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
