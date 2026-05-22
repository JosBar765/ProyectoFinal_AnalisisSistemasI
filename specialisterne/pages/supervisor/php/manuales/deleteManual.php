<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';
require_once "../../../../php/envLoader.php";

try {

    $idManual = $_POST["id_manual"] ?? null;

    if (!$idManual) {
        throw new Exception("Manual inválido");
    }

    // =========================
    // OBTENER LA URL DEL MANUAL
    // =========================
    // Solo necesitamos la URL, ya no necesitamos el proyecto ni el título
    $stmt = $pdo->prepare("
        SELECT url 
        FROM ManualPrueba 
        WHERE id = ?
    ");

    $stmt->execute([$idManual]);
    $manual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$manual) {
        throw new Exception("Manual no encontrado");
    }

    // =========================
    // EXTRAER PUBLIC ID DE LA URL
    // =========================
    // La URL viene así: https://res.cloudinary.com/cloud/raw/upload/v1730000000/manuales/archivo.pdf

    $url = $manual["url"];
    $parts = explode('/upload/', $url);

    if (count($parts) < 2) {
        throw new Exception("Formato de URL de Cloudinary inválido");
    }

    $ruta = $parts[1]; // Esto nos da: v1730000000/manuales/archivo.pdf

    // Quitamos la carpeta de versión de Cloudinary (ej: v123456/) usando una expresión regular
    $publicId = preg_replace('/^v\d+\//', '', $ruta);

    // $publicId ahora es exactamente: manuales/TuProyecto-Titulo-Fecha.pdf
    // OJO: Para archivos RAW, Cloudinary exige la extensión .pdf en el public_id, por lo que así está perfecto.

    // =========================
    // CLOUDINARY DELETE
    // =========================

    // Usamos getenv() para mantener consistencia con tu createManual.php
    $cloud_name = getenv('CLOUDINARY_CLOUD_NAME');
    $api_key = getenv('CLOUDINARY_API_KEY');
    $api_secret = getenv('CLOUDINARY_API_SECRET');

    $timestamp = time();

    $params = [
        "public_id" => $publicId,
        "timestamp" => $timestamp
    ];

    ksort($params);

    $toSign = "";
    foreach ($params as $k => $v) {
        $toSign .= "$k=$v&";
    }
    $toSign = rtrim($toSign, "&");

    $signature = sha1($toSign . $api_secret);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/raw/destroy");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "public_id" => $publicId,
        "api_key" => $api_key,
        "timestamp" => $timestamp,
        "signature" => $signature
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception("Cloudinary error: $err");
    }

    curl_close($ch);

    $result = json_decode($response, true);

    // Verificamos si la respuesta de Cloudinary fue exitosa ("ok" o "not found" si ya había sido borrado antes manualmante)
    if (!isset($result["result"]) || !in_array($result["result"], ["ok", "not found"])) {
        throw new Exception("Cloudinary rechazó la eliminación: " . ($result['error']['message'] ?? 'Error desconocido'));
    }

    // =========================
    // DELETE DB
    // =========================
    $stmt = $pdo->prepare("DELETE FROM ManualPrueba WHERE id = ?");
    $stmt->execute([$idManual]);

    echo json_encode([
        "success" => true
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
