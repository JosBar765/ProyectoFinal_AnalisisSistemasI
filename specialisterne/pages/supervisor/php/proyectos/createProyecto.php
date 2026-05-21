<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!$data) {

        echo json_encode([
            "success" => false,
            "message" => "Datos inválidos"
        ]);

        exit;
    }

    $nombre =
        trim($data["nombre"] ?? "");

    $descripcion =
        trim($data["descripcion"] ?? "");

    $fechaInicio =
        $data["fecha_inicio"] ?? "";

    $fechaFin =
        $data["fecha_fin"] ?? "";

    $estado =
        trim($data["estado"] ?? "");

    $fases =
        $data["fases"] ?? [];

    if (
        empty($nombre) ||
        empty($descripcion) ||
        empty($fechaInicio) ||
        empty($fechaFin) ||
        empty($estado)
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Completa todos los campos"
        ]);

        exit;
    }

    if (count($fases) === 0) {

        echo json_encode([
            "success" => false,
            "message" => "Debes agregar al menos una fase"
        ]);

        exit;
    }

    $pdo->beginTransaction();

    /*
        Obtener ID del estado
    */

    $sqlEstado = "
        SELECT id
        FROM EstadoProyecto
        WHERE estado = :estado
        LIMIT 1
    ";

    $stmtEstado = $pdo->prepare($sqlEstado);

    $stmtEstado->execute([
        "estado" => $estado
    ]);

    $estadoProyecto =
        $stmtEstado->fetch(PDO::FETCH_ASSOC);

    if (!$estadoProyecto) {

        throw new Exception(
            "Estado de proyecto inválido"
        );
    }

    /*
        Insertar proyecto
    */

    $sqlProyecto = "
        INSERT INTO Proyecto (

            nombre,
            descripcion,
            fecha_inicio,
            fecha_fin,
            id_estado_proyecto

        )
        VALUES (

            :nombre,
            :descripcion,
            :fecha_inicio,
            :fecha_fin,
            :id_estado_proyecto

        )
    ";

    $stmtProyecto = $pdo->prepare($sqlProyecto);

    $stmtProyecto->execute([

        "nombre" => $nombre,
        "descripcion" => $descripcion,
        "fecha_inicio" => $fechaInicio,
        "fecha_fin" => $fechaFin,

        "id_estado_proyecto" =>
        $estadoProyecto["id"]

    ]);

    $idProyecto =
        $pdo->lastInsertId();

    /*
        Insertar fases
    */

    $sqlFase = "
        INSERT INTO FaseProyecto (

            id_proyecto,
            nombre,
            descripcion

        )
        VALUES (

            :id_proyecto,
            :nombre,
            :descripcion

        )
    ";

    $stmtFase = $pdo->prepare($sqlFase);

    foreach ($fases as $fase) {

        $nombreFase =
            trim($fase["nombre"] ?? "");

        $descripcionFase =
            trim($fase["descripcion"] ?? "");

        if (
            empty($nombreFase) ||
            empty($descripcionFase)
        ) {

            throw new Exception(
                "Todas las fases deben estar completas"
            );
        }

        $stmtFase->execute([

            "id_proyecto" => $idProyecto,
            "nombre" => $nombreFase,
            "descripcion" => $descripcionFase

        ]);
    }

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Proyecto creado correctamente"
    ]);
} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();
    }

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
