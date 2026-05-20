<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos - Specialisterne</title>
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
                <a href="index.html"><i data-lucide="layout-dashboard"></i> Dashboard</a>
                <a href="proyectos.html" class="active"><i data-lucide="folder-kanban"></i> Proyectos</a>
                <a href="manuales.html"><i data-lucide="book-open"></i> Manuales</a>
                <a href="casos.html"><i data-lucide="list-checks"></i> Casos de Prueba</a>
                <a href="asignaciones.html"><i data-lucide="users"></i> Asignaciones</a>
                <a href="errores.html"><i data-lucide="bug"></i> Errores</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div></div>
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
                        <div class="breadcrumb">Supervisor / Proyectos</div>
                        <h1>Lista de Proyectos</h1>
                    </div>
                    <a href="proyecto-form.html" class="btn btn-primary"><i data-lucide="plus"></i> Nuevo Proyecto</a>
                </div>

                <div class="dashboard-grid">
                    <!-- Proyecto Card 1 -->
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="d-flex justify-between align-center mb-3">
                                <h2 class="card-title" style="margin: 0;">Sistema de Facturación Cliente XYZ</h2>
                                <span class="badge badge-success">Activo</span>
                            </div>
                            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                Pruebas del nuevo sistema de facturación electrónica. Incluye validación de módulos de login, reportes y generación de PDFs.
                            </p>
                            <div class="d-flex gap-3 mb-4" style="font-size: 12px; color: var(--text-muted);">
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="14"></i> Inicio: 01 May 2026</span>
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="14"></i> Fin: 30 Jun 2026</span>
                            </div>
                            <div class="progress-container mb-1"><div class="progress-bar" style="width: 42%;"></div></div>
                            <span style="font-size: 12px; color: var(--text-muted);">Avance: 42%</span>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="index.html" class="btn btn-secondary" style="width: 100%;">Ver Detalles</a>
                        </div>
                    </div>

                    <!-- Proyecto Card 2 -->
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="d-flex justify-between align-center mb-3">
                                <h2 class="card-title" style="margin: 0;">App Móvil Delivery</h2>
                                <span class="badge badge-warning">En Pausa</span>
                            </div>
                            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                Validación de flujos de compra y seguimiento de pedidos en Android y iOS.
                            </p>
                            <div class="d-flex gap-3 mb-4" style="font-size: 12px; color: var(--text-muted);">
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="14"></i> Inicio: 15 Mar 2026</span>
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="14"></i> Fin: 15 Jul 2026</span>
                            </div>
                            <div class="progress-container mb-1"><div class="progress-bar" style="width: 75%; background-color: var(--warning-color);"></div></div>
                            <span style="font-size: 12px; color: var(--text-muted);">Avance: 75%</span>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="#" class="btn btn-secondary" style="width: 100%;">Ver Detalles</a>
                        </div>
                    </div>

                    <!-- Proyecto Card 3 -->
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="d-flex justify-between align-center mb-3">
                                <h2 class="card-title" style="margin: 0;">Portal de Empleados ERP</h2>
                                <span class="badge badge-neutral">Finalizado</span>
                            </div>
                            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                Actualización de la intranet para solicitud de vacaciones y gestión de nóminas.
                            </p>
                            <div class="d-flex gap-3 mb-4" style="font-size: 12px; color: var(--text-muted);">
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="14"></i> Inicio: 01 Ene 2026</span>
                                <span class="d-flex align-center gap-2"><i data-lucide="calendar" size="14"></i> Fin: 30 Abr 2026</span>
                            </div>
                            <div class="progress-container mb-1"><div class="progress-bar" style="width: 100%;"></div></div>
                            <span style="font-size: 12px; color: var(--text-muted);">Avance: 100%</span>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="#" class="btn btn-secondary" style="width: 100%;">Ver Detalles</a>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="../../js/app.js"></script>
</body>
</html>
