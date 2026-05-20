<?php

header('Content-Type: application/json');
session_start();

require 'db.php';

try {
    $input = json_decode(file_get_contents("php://input"), true);

    $usuario = $input["usuario"] ?? "";
    $password = $input["password"] ?? "";

    if (!$usuario || !$password) {
        echo json_encode([
            "success" => false,
            "message" => "Datos incompletos"
        ]);
        exit;
    }

    $sql = "
        SELECT 
            u.id,
            u.nombre,
            u.apellido,
            u.correo,
            u.contrasenia,
            u.id_rol,
            u.estado,
            u.fecha_creacion,
            r.nombre as rol
        FROM Usuario u
        JOIN Rol r ON u.id_rol = r.id
        WHERE u.correo = :usuario AND u.estado = 1
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["usuario" => $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            "success" => false,
            "message" => "Usuario no encontrado"
        ]);
        exit;
    }

    if ($password !== $user["contrasenia"]) {
        echo json_encode([
            "success" => false,
            "message" => "Contraseña incorrecta"
        ]);
        exit;
    }

    $_SESSION["user"] = [
        "id" => $user["id"],
        "nombre" => $user["nombre"],
        "apellido" => $user["apellido"],
        "correo" => $user["correo"],
        "id_rol" => (int)$user["id_rol"],
        "rol" => $user["rol"],
        "fecha_creacion" => $user["fecha_creacion"],
    ];

    echo json_encode([
        "success" => true,
        "user" => $_SESSION["user"]
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
