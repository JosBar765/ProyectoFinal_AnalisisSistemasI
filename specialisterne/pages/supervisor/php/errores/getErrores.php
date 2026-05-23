<?php

require_once '../../../../php/db.php';

header("Content-Type: application/json");

try {

    if (!isset($_GET["id_proyecto"])) {

        echo json_encode([
            "success" => false,
            "message" => "Proyecto inválido"
        ]);

        exit;
    }

    $idProyecto =
        intval($_GET["id_proyecto"]);

    $severidad =
        $_GET["severidad"] ?? "";

    $estado =
        $_GET["estado"] ?? "";

    $sql = "
        SELECT
            er.id,
            er.titulo,
            er.estado,

            se.severidad_error AS severidad,

            cp.titulo AS caso_prueba,

            CONCAT(
                u.nombre,
                ' ',
                u.apellido
            ) AS consultor,

            ep.fecha_ejecucion

        FROM ErrorReporte er

        INNER JOIN SeveridadError se
            ON er.id_severidad_error = se.id

        INNER JOIN EjecucionPrueba ep
            ON er.id_ejecucion = ep.id

        INNER JOIN CasoPrueba cp
            ON ep.id_caso_prueba = cp.id

        INNER JOIN FaseProyecto fp
            ON cp.id_fase_proyecto = fp.id

        INNER JOIN Consultor c
            ON ep.id_consultor = c.id

        INNER JOIN Usuario u
            ON c.id_usuario = u.id

        WHERE fp.id_proyecto = :id_proyecto
    ";

    $params = [
        ":id_proyecto" => $idProyecto
    ];

    // FILTRO SEVERIDAD
    if (!empty($severidad)) {

        $sql .= "
            AND se.severidad_error = :severidad
        ";

        $params[":severidad"] =
            $severidad;
    }

    // FILTRO ESTADO
    if ($estado !== "") {

        $sql .= "
            AND er.estado = :estado
        ";

        $params[":estado"] =
            intval($estado);
    }

    $sql .= "
        ORDER BY ep.fecha_ejecucion DESC
    ";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute($params);

    $errores = [];

    while (
        $row =
        $stmt->fetch(PDO::FETCH_ASSOC)
    ) {

        $errores[] = [

            "id" =>
            $row["id"],

            "titulo" =>
            $row["titulo"],

            "estado" =>
            $row["estado"],

            "severidad" =>
            $row["severidad"],

            "caso_prueba" =>
            $row["caso_prueba"],

            "consultor" =>
            $row["consultor"],

            "fecha" =>
            date(
                "d M Y",
                strtotime(
                    $row["fecha_ejecucion"]
                )
            )
        ];
    }

    echo json_encode([
        "success" => true,
        "errores" => $errores
    ]);
} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
