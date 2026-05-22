<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $data =
        json_decode(
            file_get_contents("php://input"),
            true
        );

    $idFaseProyecto =
        $data["id_fase_proyecto"] ?? null;

    $titulo =
        trim($data["titulo"] ?? "");

    $descripcion =
        trim($data["descripcion"] ?? "");

    $instrucciones =
        trim($data["instrucciones"] ?? "");

    $resultadoEsperado =
        trim($data["resultado_esperado"] ?? "");

    if (
        !$idFaseProyecto ||
        empty($titulo) ||
        empty($descripcion) ||
        empty($instrucciones) ||
        empty($resultadoEsperado)
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Completa todos los campos"
        ]);

        exit;
    }

    // Pendiente
    $idEstadoCaso = 1;

    $sql = "
        INSERT INTO CasoPrueba (

            id_fase_proyecto,
            titulo,
            descripcion,
            instrucciones,
            resultado_esperado,
            id_estado_caso_prueba

        )
        VALUES (

            :id_fase_proyecto,
            :titulo,
            :descripcion,
            :instrucciones,
            :resultado_esperado,
            :id_estado_caso_prueba

        )
    ";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute([

        "id_fase_proyecto" =>
        $idFaseProyecto,

        "titulo" =>
        $titulo,

        "descripcion" =>
        $descripcion,

        "instrucciones" =>
        $instrucciones,

        "resultado_esperado" =>
        $resultadoEsperado,

        "id_estado_caso_prueba" =>
        $idEstadoCaso

    ]);

    echo json_encode([
        "success" => true
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
