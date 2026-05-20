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
    <title>Dashboard Supervisor - Specialisterne</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Specialisterne</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.html" class="active"><i data-lucide="layout-dashboard"></i> Dashboard</a>
                <a href="proyectos.html"><i data-lucide="folder-kanban"></i> Proyectos</a>
                <a href="manuales.html"><i data-lucide="book-open"></i> Manuales</a>
                <a href="casos.html"><i data-lucide="list-checks"></i> Casos de Prueba</a>
                <a href="asignaciones.html"><i data-lucide="users"></i> Asignaciones</a>
                <a href="errores.html"><i data-lucide="bug"></i> Errores</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-weight: 500;">Proyecto Activo:</span>
                    <select class="form-control" style="width: 300px; padding: 6px;">
                        <option>Sistema de Facturación Cliente XYZ</option>
                        <option>App Móvil Delivery</option>
                    </select>
                </div>
                <div class="user-info">
                    <span class="role-badge" style="background-color: rgba(232, 168, 56, 0.15); color: var(--warning-color);">Supervisor</span>
                    <span>Laura Martínez</span>
                    <div class="avatar" style="background-color: var(--warning-color);">L</div>
                    <a href="../../index.html" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb">Supervisor / Dashboard</div>
                        <h1>Dashboard del Proyecto</h1>
                    </div>
                </div>

                <!-- Progreso General -->
                <div class="card mb-4">
                    <div class="card-header" style="margin-bottom: 5px;">
                        <h2 class="card-title">Avance General: 42%</h2>
                        <span class="badge badge-success">Activo</span>
                    </div>
                    <div class="progress-container" style="height: 12px;">
                        <div class="progress-bar" style="width: 42%;"></div>
                    </div>
                    <p style="color: var(--text-muted); font-size: 13px; margin-top: 8px;">21 de 50 Casos de Prueba Ejecutados</p>
                </div>

                <!-- Resumen Casos -->
                <div class="dashboard-grid">
                    <div class="stat-card" style="border-left: 4px solid var(--text-muted);">
                        <div class="stat-icon" style="background: none;"><i data-lucide="clock" style="color: var(--text-muted);"></i></div>
                        <div class="stat-info">
                            <h3>29</h3>
                            <p>Casos Pendientes</p>
                        </div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid var(--primary-color);">
                        <div class="stat-icon" style="background: none;"><i data-lucide="play-circle" style="color: var(--primary-color);"></i></div>
                        <div class="stat-info">
                            <h3>5</h3>
                            <p>En Progreso</p>
                        </div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid var(--success-color);">
                        <div class="stat-icon" style="background: none;"><i data-lucide="check-circle" style="color: var(--success-color);"></i></div>
                        <div class="stat-info">
                            <h3>14</h3>
                            <p>Completados</p>
                        </div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid var(--error-color);">
                        <div class="stat-icon" style="background: none;"><i data-lucide="x-circle" style="color: var(--error-color);"></i></div>
                        <div class="stat-info">
                            <h3>2</h3>
                            <p>Fallidos</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
                    <!-- Tabla de Fases -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Progreso por Fase</h2>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Fase</th>
                                        <th>Progreso</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Módulo Login</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="progress-container" style="flex: 1; margin: 0;">
                                                    <div class="progress-bar" style="width: 100%;"></div>
                                                </div>
                                                <span style="font-size: 12px;">100%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-success">Completado</span></td>
                                    </tr>
                                    <tr>
                                        <td>Módulo Facturación</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="progress-container" style="flex: 1; margin: 0;">
                                                    <div class="progress-bar" style="width: 45%; background-color: var(--primary-color);"></div>
                                                </div>
                                                <span style="font-size: 12px;">45%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-primary">En Progreso</span></td>
                                    </tr>
                                    <tr>
                                        <td>Módulo Reportes</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="progress-container" style="flex: 1; margin: 0;">
                                                    <div class="progress-bar" style="width: 0%; background-color: var(--border-color);"></div>
                                                </div>
                                                <span style="font-size: 12px;">0%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-neutral">Pendiente</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Errores Recientes -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Errores Recientes</h2>
                            <a href="errores.html" style="font-size: 13px; color: var(--primary-color); text-decoration: none;">Ver todos</a>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="padding-bottom: 10px; border-bottom: 1px solid var(--border-color);">
                                <div class="d-flex justify-between align-center mb-1">
                                    <span class="badge badge-error" style="background-color: var(--warning-color); color: white;">ALTO</span>
                                    <span style="font-size: 12px; color: var(--text-muted);">Hace 2h</span>
                                </div>
                                <p style="font-size: 13px; font-weight: 500;">Botón 'Generar Factura' no responde en Firefox</p>
                                <p style="font-size: 12px; color: var(--text-muted);">Por: María García</p>
                            </div>
                            <div style="padding-bottom: 10px;">
                                <div class="d-flex justify-between align-center mb-1">
                                    <span class="badge badge-error" style="background-color: #D64045; color: white;">CRÍTICO</span>
                                    <span style="font-size: 12px; color: var(--text-muted);">Ayer</span>
                                </div>
                                <p style="font-size: 13px; font-weight: 500;">Cálculo de IVA incorrecto en facturas B</p>
                                <p style="font-size: 12px; color: var(--text-muted);">Por: Carlos López</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="../../js/app.js"></script>
</body>

</html>