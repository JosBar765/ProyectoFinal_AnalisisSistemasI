<?php

session_start();

header("Content-Type: application/json");

require_once "../../../../php/db.php";

try {

    if (!isset($_SESSION["user"])) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "No autorizado"
        ]);

        exit;
    }

    if ($_SESSION["user"]["id_rol"] != 3) {

        http_response_code(403);

        echo json_encode([
            "success" => false,
            "message" => "Acceso denegado"
        ]);

        exit;
    }

    $idUsuario = $_SESSION["user"]["id"];

    $sql = "
        SELECT
            ep.id,
            ep.resultado,
            ep.fecha_ejecucion,

            cp.id AS id_caso_prueba,
            cp.titulo AS caso_prueba,

            p.id AS id_proyecto,
            p.nombre AS proyecto

        FROM Usuario u

        INNER JOIN Consultor c
            ON c.id_usuario = u.id

        INNER JOIN EjecucionPrueba ep
            ON ep.id_consultor = c.id

        INNER JOIN CasoPrueba cp
            ON cp.id = ep.id_caso_prueba

        INNER JOIN FaseProyecto fp
            ON fp.id = cp.id_fase_proyecto

        INNER JOIN Proyecto p
            ON p.id = fp.id_proyecto

        WHERE u.id = ?
        AND p.id_estado_proyecto <> 2

        ORDER BY ep.fecha_ejecucion DESC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$idUsuario]);

    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tasks as &$task) {

        $task["fecha_ejecucion"] = date(
            "d M Y, H:i",
            strtotime($task["fecha_ejecucion"])
        );
    }

    echo json_encode([
        "success" => true,
        "tasks" => $tasks
    ]);
} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
