<?php

session_start();

header('Content-Type: application/json');

require_once '../../../../php/db.php';
require_once '../../../../php/envLoader.php';

if (!isset($_SESSION["user"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "No autorizado"
    ]);

    exit;
}

$user = $_SESSION["user"];

try {

    /*
    |--------------------------------------------------------------------------
    | VALIDAR DATOS
    |--------------------------------------------------------------------------
    */

    $idCaso =
        $_POST["id_caso"] ?? null;

    $titulo =
        trim($_POST["titulo"] ?? "");

    $descripcion =
        trim($_POST["descripcion"] ?? "");

    $idSeveridad =
        $_POST["id_severidad"] ?? null;

    if (
        !$idCaso
        || !is_numeric($idCaso)
    ) {

        throw new Exception(
            "Caso inválido"
        );
    }

    if (empty($titulo)) {

        throw new Exception(
            "Ingrese el título del error"
        );
    }

    if (empty($descripcion)) {

        throw new Exception(
            "Ingrese la descripción del error"
        );
    }

    if (
        !$idSeveridad
        || !is_numeric($idSeveridad)
    ) {

        throw new Exception(
            "Seleccione una severidad válida"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER CONSULTOR
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM Consultor
        WHERE id_usuario = ?
    ");

    $stmt->execute([
        $user["id"]
    ]);

    $consultor =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$consultor) {

        throw new Exception(
            "Consultor no encontrado"
        );
    }

    $idConsultor =
        $consultor["id"];

    /*
    |--------------------------------------------------------------------------
    | VALIDAR ACCESO AL CASO
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            cp.id
        FROM CasoPrueba cp

        INNER JOIN FaseProyecto fp
            ON fp.id = cp.id_fase_proyecto

        INNER JOIN Proyecto p
            ON p.id = fp.id_proyecto

        INNER JOIN EstadoProyecto ep
            ON ep.id = p.id_estado_proyecto

        INNER JOIN Proyecto_Consultor pc
            ON pc.id_proyecto = p.id

        INNER JOIN Consultor c
            ON c.id = pc.id_consultor

        WHERE cp.id = ?
        AND c.id_usuario = ?
        AND ep.estado = 'Activo'
    ");

    $stmt->execute([
        $idCaso,
        $user["id"]
    ]);

    $caso =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$caso) {

        throw new Exception(
            "No tienes acceso a este caso"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INICIAR TRANSACCIÓN
    |--------------------------------------------------------------------------
    */

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | CREAR EJECUCIÓN FALLIDA
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO EjecucionPrueba
        (
            id_consultor,
            id_caso_prueba,
            resultado,
            observaciones,
            fecha_ejecucion
        )
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $idConsultor,
        $idCaso,
        "Fallido",
        $descripcion
    ]);

    $idEjecucion =
        $pdo->lastInsertId();

    /*
    |--------------------------------------------------------------------------
    | CREAR ERROR
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO ErrorReporte
        (
            id_ejecucion,
            titulo,
            descripcion,
            id_severidad_error,
            estado
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $idEjecucion,
        $titulo,
        $descripcion,
        $idSeveridad,
        1
    ]);

    $idError =
        $pdo->lastInsertId();

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR ESTADO DEL CASO
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE CasoPrueba
        SET id_estado_caso_prueba = 2
        WHERE id = ?
    ");

    $stmt->execute([
        $idCaso
    ]);

    /*
    |--------------------------------------------------------------------------
    | CLOUDINARY
    |--------------------------------------------------------------------------
    */

    $cloud_name =
        $_ENV['CLOUDINARY_CLOUD_NAME'];

    $api_key =
        $_ENV['CLOUDINARY_API_KEY'];

    $api_secret =
        $_ENV['CLOUDINARY_API_SECRET'];

    if (!empty($_FILES['imagenes']['tmp_name'])) {

        foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpFile) {

            if (!file_exists($tmpFile)) {
                continue;
            }

            $stmt = $pdo->query("
                SELECT COALESCE(MAX(id),0)+1 AS id
                FROM ImagenErrorReporte
            ");

            $idImagen =
                $stmt->fetch()['id'];

            $timestamp =
                time();

            $folder =
                "capturas/$idError";

            $public_id =
                (string)$idImagen;

            $params = [
                "folder" => $folder,
                "public_id" => $public_id,
                "timestamp" => $timestamp
            ];

            ksort($params);

            $toSign = "";

            foreach ($params as $k => $v) {

                $toSign .= "$k=$v&";
            }

            $toSign =
                rtrim($toSign, "&");

            $signature =
                sha1($toSign . $api_secret);

            $ch = curl_init();

            curl_setopt(
                $ch,
                CURLOPT_URL,
                "https://api.cloudinary.com/v1_1/$cloud_name/image/upload"
            );

            curl_setopt(
                $ch,
                CURLOPT_POST,
                true
            );

            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                [
                    "file" =>
                    new CURLFile($tmpFile),

                    "api_key" =>
                    $api_key,

                    "timestamp" =>
                    $timestamp,

                    "folder" =>
                    $folder,

                    "public_id" =>
                    $public_id,

                    "signature" =>
                    $signature
                ]
            );

            $response =
                curl_exec($ch);

            curl_close($ch);

            $result =
                json_decode(
                    $response,
                    true
                );

            if (!isset($result['secure_url'])) {

                throw new Exception(
                    "Error al subir imágenes a Cloudinary"
                );
            }

            $stmt = $pdo->prepare("
                INSERT INTO ImagenErrorReporte
                (
                    id,
                    id_error,
                    imagen,
                    fecha_subida
                )
                VALUES (?, ?, ?, NOW())
            ");

            $stmt->execute([
                $idImagen,
                $idError,
                $result['secure_url']
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | COMMIT
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Reporte registrado correctamente"
    ]);
} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
