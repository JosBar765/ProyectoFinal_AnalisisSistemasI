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
            u.correo,
            p.perfil_trabajo,
            p.habilidades
        FROM Consultor c
        INNER JOIN Usuario u ON u.id = c.id_usuario
        INNER JOIN PerfilTrabajo p ON p.id = c.id_perfil_trabajo
        WHERE c.id NOT IN (
            SELECT id_consultor 
            FROM Proyecto_Consultor 
            WHERE id_proyecto = ?
        )
        ORDER BY u.id
    ");

    $stmt->execute([$idProyecto]);

    $consultores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = array_map(function ($c) {

        return [
            "id" => $c["id"],
            "nombre" => $c["nombre"],
            "apellido" => $c["apellido"],
            "descripcion" => $c["perfil_trabajo"],
            "skills" => $c["habilidades"]
                ? array_map("trim", explode(",", $c["habilidades"]))
                : []
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
