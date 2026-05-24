<?php
session_start();

// Si se sube a un host, darle el valor ""
$path = "/specialisterne";

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["user"]["id_rol"] != 3) {
    header("Location: {$path}/403.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casos de Prueba - Consultor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .caso-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            transition: border-color 0.2s;
        }

        .caso-card:hover {
            border-color: var(--primary-color);
        }
    </style>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Specialisterne</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="active"><i data-lucide="folder-kanban"></i> Mis Proyectos</a>
                <a href="tareas.php"><i data-lucide="check-square"></i> Mis Tareas</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: 15px; font-size: 16px;">
                    <span style="font-weight: 500; color: var(--text-muted);">Proyecto:</span>
                    <strong>Sistema de Facturación Cliente XYZ</strong>
                </div>
                <div class="user-info">
                    <span class="role-badge" style="background-color: rgba(74, 111, 165, 0.15); color: var(--primary-color);">Consultor</span>
                    <span>María García</span>
                    <div class="avatar" style="background-color: var(--primary-color);">M</div>
                    <a id="logoutBtn" href="../../index.php" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb"><a href="index.php" style="color: var(--text-muted); text-decoration: none;"><i data-lucide="arrow-left" size="14"></i> Volver a Mis Proyectos</a></div>
                        <h1 style="margin-top: 10px;">Casos de Prueba Asignados</h1>
                    </div>
                </div>

                <div class="card">
                    <div class="tabs">
                        <div class="tab active" data-tab="pendientes" data-group="estado">Pendientes (2)</div>
                        <div class="tab" data-tab="completados" data-group="estado">Completados (4)</div>
                    </div>

                    <div class="tab-content-container">
                        <!-- Casos Pendientes -->
                        <div id="pendientes" class="tab-content active" data-tab-group="estado">

                            <div class="caso-card">
                                <div>
                                    <div class="d-flex align-center gap-3 mb-2">
                                        <h3 style="margin: 0; font-size: 16px;">Validar generación de factura B</h3>
                                        <span class="badge badge-neutral">CP-015</span>
                                    </div>
                                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 10px;">Módulo: Facturación</p>
                                    <p style="font-size: 14px;">El sistema debe calcular correctamente el IVA (21%) al generar una factura B.</p>
                                </div>
                                <div>
                                    <a href="ejecutar.php" class="btn btn-primary" style="padding: 12px 24px;">Ejecutar Prueba <i data-lucide="play" size="16"></i></a>
                                </div>
                            </div>

                            <div class="caso-card">
                                <div>
                                    <div class="d-flex align-center gap-3 mb-2">
                                        <h3 style="margin: 0; font-size: 16px;">Verificar mensaje de error en login</h3>
                                        <span class="badge badge-neutral">CP-003</span>
                                    </div>
                                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 10px;">Módulo: Login</p>
                                    <p style="font-size: 14px;">Ingresar una contraseña incorrecta y verificar que aparece el mensaje rojo.</p>
                                </div>
                                <div>
                                    <a href="ejecutar.php" class="btn btn-primary" style="padding: 12px 24px;">Ejecutar Prueba <i data-lucide="play" size="16"></i></a>
                                </div>
                            </div>

                        </div>

                        <!-- Casos Completados -->
                        <div id="completados" class="tab-content" data-tab-group="estado">

                            <div class="caso-card" style="background: var(--bg-color);">
                                <div>
                                    <div class="d-flex align-center gap-3 mb-2">
                                        <h3 style="margin: 0; font-size: 16px; color: var(--text-muted);">Login con credenciales válidas</h3>
                                        <span class="badge badge-neutral">CP-001</span>
                                    </div>
                                    <div class="d-flex align-center gap-2" style="font-size: 14px; color: var(--success-color); font-weight: 500;">
                                        <i data-lucide="check-circle" size="16"></i> Prueba Exitosa (Aprobado)
                                    </div>
                                </div>
                                <div>
                                    <span style="color: var(--text-muted); font-size: 13px;">Ejecutado: 15 May 2026</span>
                                </div>
                            </div>

                            <div class="caso-card" style="background: var(--bg-color);">
                                <div>
                                    <div class="d-flex align-center gap-3 mb-2">
                                        <h3 style="margin: 0; font-size: 16px; color: var(--text-muted);">Descarga de PDF de factura</h3>
                                        <span class="badge badge-neutral">CP-010</span>
                                    </div>
                                    <div class="d-flex align-center gap-2" style="font-size: 14px; color: var(--error-color); font-weight: 500;">
                                        <i data-lucide="x-circle" size="16"></i> Prueba Fallida (Reportado)
                                    </div>
                                </div>
                                <div>
                                    <span style="color: var(--text-muted); font-size: 13px;">Ejecutado: 16 May 2026</span>
                                </div>
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