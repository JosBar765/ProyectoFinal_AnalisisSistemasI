<?php

session_start();

header("Content-Type: application/json");

require_once "../../../../php/db.php";

try {

    // Validar sesión
    if (!isset($_SESSION["user"])) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "No autorizado"
        ]);

        exit;
    }

    // Validar rol consultor
    if ($_SESSION["user"]["id_rol"] != 3) {

        http_response_code(403);

        echo json_encode([
            "success" => false,
            "message" => "Acceso denegado"
        ]);

        exit;
    }

    $idUsuario = $_SESSION["user"]["id"];

    /*
        Obtener:
        - proyectos asignados al consultor
        - estado del proyecto
        - total de casos
        - casos completados
    */

    $sql = "
        SELECT
            p.id,
            p.nombre,
            p.descripcion,
            p.fecha_fin,
            ep.estado,

            COUNT(cp.id) AS total_casos,

            SUM(
                CASE
                    WHEN ecp.estado = 'Completado'
                    THEN 1
                    ELSE 0
                END
            ) AS casos_completados

        FROM Usuario u

        INNER JOIN Consultor c
            ON c.id_usuario = u.id

        INNER JOIN Proyecto_Consultor pc
            ON pc.id_consultor = c.id

        INNER JOIN Proyecto p
            ON p.id = pc.id_proyecto

        INNER JOIN EstadoProyecto ep
            ON ep.id = p.id_estado_proyecto

        LEFT JOIN FaseProyecto fp
            ON fp.id_proyecto = p.id

        LEFT JOIN CasoPrueba cp
            ON cp.id_fase_proyecto = fp.id

        LEFT JOIN EstadoCasoPrueba ecp
            ON ecp.id = cp.id_estado_caso_prueba

        WHERE u.id = ?

        GROUP BY
            p.id,
            p.nombre,
            p.descripcion,
            p.fecha_fin,
            ep.estado

        ORDER BY p.fecha_fin ASC
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
