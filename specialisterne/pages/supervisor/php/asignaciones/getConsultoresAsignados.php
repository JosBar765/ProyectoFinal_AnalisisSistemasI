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
        ORDER BY pc.fecha_asignacion
    ");

    $stmt->execute([$idProyecto]);

    $consultores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = array_map(function ($c) {

        return [
            "id" => $c["id"],
            "nombre" => $c["nombre"],
            "apellido" => $c["apellido"],
            "descripcion" => $c["perfil_trabajo"],
            "fecha_asignacion" => $c["fecha_asignacion"]
        ];
    }, $consultores);

    echo json_encode([
        "success" => true,
        "consultores" => $data
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
