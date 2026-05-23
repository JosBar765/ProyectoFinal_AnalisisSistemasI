<?php

require_once '../../../../php/db.php';
require_once '../../../../php/envLoader.php';

header('Content-Type: application/json');

try {

    $pdo->beginTransaction();

    $idEjecucion =
        $_POST['id_ejecucion'];

    $titulo =
        trim($_POST['titulo']);

    $descripcion =
        trim($_POST['descripcion']);

    $idSeveridad =
        $_POST['id_severidad'];

    $estado =
        $_POST['estado'];

    // =========================
    // CREAR ERROR
    // =========================
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
        $estado
    ]);

    $idError =
        $pdo->lastInsertId();

    // =========================
    // CLOUDINARY
    // =========================
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

            curl_setopt($ch, CURLOPT_POST, true);

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
                json_decode($response, true);

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

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Error registrado correctamente'
    ]);
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
