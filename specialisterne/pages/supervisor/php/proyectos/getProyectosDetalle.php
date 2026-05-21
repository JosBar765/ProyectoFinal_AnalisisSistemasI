<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $sql = "
        SELECT
            p.id,
            p.nombre,
            p.descripcion,

            DATE_FORMAT(
                p.fecha_inicio,
                '%d/%m/%Y'
            ) AS fecha_inicio,

            DATE_FORMAT(
                p.fecha_fin,
                '%d/%m/%Y'
            ) AS fecha_fin,

            ep.estado,

            COALESCE(
                ROUND(
                    (
                        SUM(
                            CASE
                                WHEN ecp.estado IN (
                                    'Completado',
                                    'Fallido'
                                )
                                THEN 1
                                ELSE 0
                            END
                        ) * 100
                    ) /
                    NULLIF(COUNT(cp.id), 0)
                ),
                0
            ) AS avance

        FROM Proyecto p

        INNER JOIN EstadoProyecto ep
            ON ep.id = p.id_estado_proyecto

        LEFT JOIN FaseProyecto fp
            ON fp.id_proyecto = p.id

        LEFT JOIN CasoPrueba cp
            ON cp.id_fase_proyecto = fp.id

        LEFT JOIN EstadoCasoPrueba ecp
            ON ecp.id = cp.id_estado_caso_prueba

        GROUP BY
            p.id,
            p.nombre,
            p.descripcion,
            p.fecha_inicio,
            p.fecha_fin,
            ep.estado

        ORDER BY p.fecha_inicio DESC
    ";

    $stmt = $pdo->query($sql);

    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "proyectos" => $proyectos
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
