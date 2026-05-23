<?php

require_once '../../../../php/db.php';

header('Content-Type: application/json');

try {

    $idProyecto =
        $_GET['id_proyecto'] ?? null;

    if (!$idProyecto) {

        throw new Exception(
            "Proyecto inválido"
        );
    }

    $stmt = $pdo->prepare("
        SELECT
            ep.id,
            cp.titulo AS caso_prueba,
            CONCAT(
                u.nombre,
                ' ',
                u.apellido
            ) AS consultor

        FROM EjecucionPrueba ep

        INNER JOIN CasoPrueba cp
            ON cp.id = ep.id_caso_prueba

        INNER JOIN FaseProyecto fp
            ON fp.id = cp.id_fase_proyecto

        INNER JOIN Consultor c
            ON c.id = ep.id_consultor

        INNER JOIN Usuario u
            ON u.id = c.id_usuario

        WHERE fp.id_proyecto = ?

        ORDER BY ep.id DESC
    ");

    $stmt->execute([
        $idProyecto
    ]);

    echo json_encode([
        'success' => true,
        'ejecuciones' =>
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
