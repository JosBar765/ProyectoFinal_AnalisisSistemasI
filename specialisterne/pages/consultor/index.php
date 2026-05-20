<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: /index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proyectos - Specialisterne</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .consultor-card {
            border: 2px solid transparent;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .consultor-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 8px 16px -4px rgba(74, 111, 165, 0.15);
        }
    </style>
</head>

<body>
    <div class="layout">
        <!-- Sidebar para Consultor: Más minimalista -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Specialisterne</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.html" class="active"><i data-lucide="folder-kanban"></i> Mis Proyectos</a>
                <a href="tareas.html"><i data-lucide="check-square"></i> Mis Tareas</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div></div>
                <div class="user-info">
                    <span class="role-badge" style="background-color: rgba(74, 111, 165, 0.15); color: var(--primary-color);">Consultor</span>
                    <span>María García</span>
                    <div class="avatar" style="background-color: var(--primary-color);">M</div>
                    <a href="../../index.html" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <h1>Mis Proyectos Asignados</h1>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <!-- Proyecto Asignado 1 -->
                    <div class="card consultor-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="d-flex justify-between align-center mb-3">
                                <h2 class="card-title" style="margin: 0; font-size: 18px;">Sistema de Facturación Cliente XYZ</h2>
                                <span class="badge badge-success">Activo</span>
                            </div>
                            <div class="d-flex gap-3 mb-4" style="font-size: 13px; color: var(--text-muted);">
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="16"></i> Fin: 30 Jun 2026</span>
                            </div>

                            <div style="background-color: var(--bg-color); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                <div class="d-flex justify-between align-center mb-2">
                                    <span style="font-weight: 500;">Tu progreso</span>
                                    <span style="font-weight: 600; color: var(--primary-color);">6 / 15 Casos</span>
                                </div>
                                <div class="progress-container mb-1">
                                    <div class="progress-bar" style="width: 40%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="casos.html" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px;">Ver Casos de Prueba <i data-lucide="arrow-right" size="18"></i></a>
                        </div>
                    </div>

                    <!-- Proyecto Asignado 2 -->
                    <div class="card consultor-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="d-flex justify-between align-center mb-3">
                                <h2 class="card-title" style="margin: 0; font-size: 18px;">Portal de RRHH</h2>
                                <span class="badge badge-warning">En Pausa</span>
                            </div>
                            <div class="d-flex gap-3 mb-4" style="font-size: 13px; color: var(--text-muted);">
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="16"></i> Fin: 15 Ago 2026</span>
                            </div>

                            <div style="background-color: var(--bg-color); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                <div class="d-flex justify-between align-center mb-2">
                                    <span style="font-weight: 500;">Tu progreso</span>
                                    <span style="font-weight: 600; color: var(--primary-color);">10 / 10 Casos</span>
                                </div>
                                <div class="progress-container mb-1">
                                    <div class="progress-bar" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="#" class="btn btn-secondary" style="width: 100%; padding: 12px; font-size: 16px;">Proyecto en Pausa</a>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="../../js/app.js"></script>
</body>

</html>