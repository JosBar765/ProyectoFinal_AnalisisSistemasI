<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $data =
        json_decode(
            file_get_contents("php://input"),
            true
        );

    $idCaso =
        $data["id_caso"] ?? null;

    $titulo =
        trim($data["titulo"] ?? "");

    $descripcion =
        trim($data["descripcion"] ?? "");

    $instrucciones =
        trim($data["instrucciones"] ?? "");

    $resultadoEsperado =
        trim($data["resultado_esperado"] ?? "");

    $idEstadoCasoPrueba =
        $data["id_estado_caso_prueba"] ?? null;

    if (

        !$idCaso ||
        !$idEstadoCasoPrueba ||

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

    $sql = "
        UPDATE CasoPrueba
        SET

            titulo =
                :titulo,

            descripcion =
                :descripcion,

            instrucciones =
                :instrucciones,

            resultado_esperado =
                :resultado_esperado,

            id_estado_caso_prueba =
                :id_estado_caso_prueba

        WHERE id = :id_caso
    ";

    $stmt =
        $pdo->prepare($sql);

    $stmt->execute([

        "id_caso" =>
        $idCaso,

        "titulo" =>
        $titulo,

        "descripcion" =>
        $descripcion,

        "instrucciones" =>
        $instrucciones,

        "resultado_esperado" =>
        $resultadoEsperado,

        "id_estado_caso_prueba" =>
        $idEstadoCasoPrueba

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
