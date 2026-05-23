<?php

header("Content-Type: application/json");

require_once '../../../../php/db.php';

try {

    $idProyecto = $_GET["id_proyecto"] ?? null;

    if (!$idProyecto) {
        throw new Exception("Proyecto inválido");
    }

    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            u.nombre,
            u.apellido,
            p.perfil_trabajo,
            pc.fecha_asignacion
        FROM Proyecto_Consultor pc
        INNER JOIN Consultor c ON c.id = pc.id_consultor
        INNER JOIN Usuario u ON u.id = c.id_usuario
        INNER JOIN PerfilTrabajo p ON p.id = c.id_perfil_trabajo
        WHERE pc.id_proyecto = ?
    ");

    $stmt->execute([$idProyecto]);

    $consultores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "consultores" => $consultores
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
