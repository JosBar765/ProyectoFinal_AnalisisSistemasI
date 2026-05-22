<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';
require_once "../../../../php/envLoader.php";

try {

    $idProyecto =
        $_POST["id_proyecto"] ?? null;

    $titulo =
        trim($_POST["titulo"] ?? "");

    $descripcion =
        trim($_POST["descripcion"] ?? "");

    if (

        !$idProyecto ||
        empty($titulo)

    ) {

        throw new Exception(
            "Completa los campos requeridos"
        );
    }

    if (!isset($_FILES["archivo"])) {

        throw new Exception(
            "Debes seleccionar un PDF"
        );
    }

    $archivo =
        $_FILES["archivo"];

    if ($archivo["error"] !== UPLOAD_ERR_OK) {

        throw new Exception(
            "Error al subir el archivo"
        );
    }

    if ($archivo["size"] > 25 * 1024 * 1024) {

        throw new Exception(
            "El PDF supera los 25 MB"
        );
    }

    $finfo =
        finfo_open(FILEINFO_MIME_TYPE);

    $mime =
        finfo_file(
            $finfo,
            $archivo["tmp_name"]
        );

    if ($mime !== "application/pdf") {

        throw new Exception(
            "Solo se permiten PDFs"
        );
    }

    // =========================
    // OBTENER PROYECTO
    // =========================

    $stmt = $pdo->prepare("
        SELECT nombre
        FROM Proyecto
        WHERE id = ?
    ");

    $stmt->execute([$idProyecto]);

    $proyecto =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proyecto) {

        throw new Exception(
            "Proyecto no encontrado"
        );
    }

    // =========================
    // CLOUDINARY
    // =========================

    $cloud_name =
        getenv('CLOUDINARY_CLOUD_NAME');

    $api_key =
        getenv('CLOUDINARY_API_KEY');

    $api_secret =
        getenv('CLOUDINARY_API_SECRET');

    $timestamp =
        time();

    $nombreProyecto =
        preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '_',
            $proyecto["nombre"]
        );

    $tituloManual =
        preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '_',
            $titulo
        );

    $fechaSubida =
        date("Ymd_His");

    $public_id =
        "{$nombreProyecto}-{$tituloManual}-{$fechaSubida}";

    $folder =
        "manuales";

    $params = [

        "folder" =>
        $folder,

        "public_id" =>
        $public_id,

        "timestamp" =>
        $timestamp

    ];

    ksort($params);

    $toSign = "";

    foreach ($params as $k => $v) {

        $toSign .= "$k=$v&";
    }

    $toSign =
        rtrim($toSign, "&");

    $signature =
        sha1(
            $toSign . $api_secret
        );

    $ch = curl_init();

    curl_setopt(
        $ch,
        CURLOPT_URL,
        "https://api.cloudinary.com/v1_1/$cloud_name/raw/upload"
    );

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, [

        "file" =>
        new CURLFile(
            $archivo["tmp_name"],
            "application/pdf",
            $archivo["name"]
        ),

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

    ]);

    $response =
        curl_exec($ch);

    if (curl_errno($ch)) {

        $error =
            curl_error($ch);

        curl_close($ch);

        throw new Exception(
            "Cloudinary: $error"
        );
    }

    curl_close($ch);

    $result =
        json_decode($response, true);

    if (!isset($result["secure_url"])) {

        throw new Exception(
            $result["error"]["message"]
                ?? "No se pudo subir el PDF"
        );
    }

    // =========================
    // INSERTAR EN DB
    // =========================

    $stmt = $pdo->prepare("
        INSERT INTO ManualPrueba (

            id_proyecto,
            titulo,
            descripcion,
            url,
            fecha_subida

        )

        VALUES (

            :id_proyecto,
            :titulo,
            :descripcion,
            :url,
            NOW()

        )
    ");

    $stmt->execute([

        "id_proyecto" =>
        $idProyecto,

        "titulo" =>
        $titulo,

        "descripcion" =>
        $descripcion,

        "url" =>
        $result["secure_url"]

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
