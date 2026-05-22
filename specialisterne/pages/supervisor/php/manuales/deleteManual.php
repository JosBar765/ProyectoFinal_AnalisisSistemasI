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
    $url = $manual["url"];
    $parts = explode('/upload/', $url);

    if (count($parts) < 2) {
        throw new Exception("Formato de URL de Cloudinary inválido");
    }

    $ruta = $parts[1];

    // Quitamos la carpeta de versión (ej: v123456/)
    $publicId = preg_replace('/^v\d+\//', '', $ruta);

    // [!] SOLUCIÓN: Decodificamos la URL para transformar los %20, %C3%B3, etc. a sus caracteres originales
    $publicId = urldecode($publicId);

    // =========================
    // CLOUDINARY DELETE
    // =========================
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
