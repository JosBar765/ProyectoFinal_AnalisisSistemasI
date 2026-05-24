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
        SELECT DISTINCT
            p.id,
            p.nombre

        FROM Usuario u

        INNER JOIN Consultor c
            ON c.id_usuario = u.id

        INNER JOIN Proyecto_Consultor pc
            ON pc.id_consultor = c.id

        INNER JOIN Proyecto p
            ON p.id = pc.id_proyecto

        WHERE u.id = ?
        AND p.id_estado_proyecto <> 2

        ORDER BY p.nombre ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$idUsuario]);

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "projects" => $projects
    ]);
} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
