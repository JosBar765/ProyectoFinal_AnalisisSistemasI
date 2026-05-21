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

            er.titulo,

            se.severidad_error AS severidad,

            CONCAT(u.nombre, ' ', u.apellido)
                AS consultor,

            ep.fecha_ejecucion AS fecha

        FROM ErrorReporte er

        JOIN SeveridadError se
            ON er.id_severidad_error = se.id

        JOIN EjecucionPrueba ep
            ON er.id_ejecucion = ep.id

        JOIN CasoPrueba cp
            ON ep.id_caso_prueba = cp.id

        JOIN FaseProyecto fp
            ON cp.id_fase_proyecto = fp.id

        JOIN Consultor c
            ON ep.id_consultor = c.id

        JOIN Usuario u
            ON c.id_usuario = u.id

        WHERE fp.id_proyecto = :id_proyecto

        ORDER BY ep.fecha_ejecucion DESC

        LIMIT 5
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "id_proyecto" => $idProyecto
    ]);

    $errores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($errores);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
