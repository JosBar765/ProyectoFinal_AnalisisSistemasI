<?php

require_once '../../../../php/db.php';

header("Content-Type: application/json");

try {

    if (!isset($_GET["id_error"])) {

        echo json_encode([
            "success" => false,
            "message" => "Error inválido"
        ]);

        exit;
    }

    $idError =
        intval($_GET["id_error"]);

    $sql = "
        SELECT
            er.id,
            er.titulo,
            er.descripcion,
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

        INNER JOIN Consultor c
            ON ep.id_consultor = c.id

        INNER JOIN Usuario u
            ON c.id_usuario = u.id

        WHERE er.id = :id_error
    ";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute([
        ":id_error" => $idError
    ]);

    $row =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {

        echo json_encode([
            "success" => false,
            "message" => "Error no encontrado"
        ]);

        exit;
    }

    // IMÁGENES
    $sqlImagenes = "
        SELECT imagen
        FROM ImagenErrorReporte
        WHERE id_error = :id_error
    ";

    $stmtImagenes =
        $pdo->prepare($sqlImagenes);

    $stmtImagenes->execute([
        ":id_error" => $idError
    ]);

    $imagenes = [];

    while (
        $img =
        $stmtImagenes->fetch(PDO::FETCH_ASSOC)
    ) {

        $imagenes[] =
            $img["imagen"];
    }

    echo json_encode([

        "success" => true,

        "error" => [

            "id" =>
            $row["id"],

            "titulo" =>
            $row["titulo"],

            "descripcion" =>
            $row["descripcion"],

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
                "d M Y H:i",
                strtotime(
                    $row["fecha_ejecucion"]
                )
            ),

            "imagenes" =>
            $imagenes
        ]
    ]);
} catch (Throwable $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
