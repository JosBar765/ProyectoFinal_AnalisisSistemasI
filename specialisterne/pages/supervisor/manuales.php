<?php
session_start();

// Si se sube a un host, darle el valor ""
$path = "/specialisterne";

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["id_rol"] != 2) {
    header("Location: {$path}/403.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manuales de Prueba - Specialisterne</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Specialisterne</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.html"><i data-lucide="layout-dashboard"></i> Dashboard</a>
                <a href="proyectos.html"><i data-lucide="folder-kanban"></i> Proyectos</a>
                <a href="manuales.html" class="active"><i data-lucide="book-open"></i> Manuales</a>
                <a href="casos.html"><i data-lucide="list-checks"></i> Casos de Prueba</a>
                <a href="asignaciones.html"><i data-lucide="users"></i> Asignaciones</a>
                <a href="errores.html"><i data-lucide="bug"></i> Errores</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-weight: 500;">Proyecto Activo:</span>
                    <select class="form-control" style="width: 300px; padding: 6px;">
                        <option>Sistema de Facturación Cliente XYZ</option>
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
                        <div class="breadcrumb">Supervisor / Manuales</div>
                        <h1>Manuales del Proyecto</h1>
                    </div>
                    <button class="btn btn-primary" onclick="document.getElementById('manualForm').style.display='block'"><i data-lucide="upload"></i> Subir Manual</button>
                </div>

                <div class="card" id="manualForm" style="display: none; border-left: 4px solid var(--primary-color);">
                    <div class="card-header">
                        <h2 class="card-title">Añadir Nuevo Manual de Referencia</h2>
                        <button class="btn btn-secondary" onclick="document.getElementById('manualForm').style.display='none'"><i data-lucide="x" size="16"></i></button>
                    </div>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Título del Manual</label>
                            <input type="text" class="form-control" required placeholder="Ej. Guía de Interfaz Módulo Login">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" style="min-height: 60px;" placeholder="Breve descripción del contenido"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">URL del Documento / Archivo Adjunto</label>
                            <input type="text" class="form-control" placeholder="https://docs.google.com/..." required>
                            <small style="color: var(--text-muted); margin-top: 5px; display: block;">* Por ahora solo se admiten enlaces externos.</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Manual</button>
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('manualForm').style.display='none'">Cancelar</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Enlace</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Guía Visual - Login</strong></td>
                                    <td>Mockups y flujos esperados de la pantalla de inicio de sesión.</td>
                                    <td><a href="#" style="color: var(--primary-color);"><i data-lucide="external-link" size="16"></i> Ver Docs</a></td>
                                    <td>15 May 2026</td>
                                    <td>
                                        <button class="btn btn-danger" style="padding: 4px 8px;"><i data-lucide="trash-2" size="14"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Reglas de Negocio Facturación</strong></td>
                                    <td>Detalle de cálculos de IVA y descuentos por cliente.</td>
                                    <td><a href="#" style="color: var(--primary-color);"><i data-lucide="external-link" size="16"></i> Ver PDF</a></td>
                                    <td>18 May 2026</td>
                                    <td>
                                        <button class="btn btn-danger" style="padding: 4px 8px;"><i data-lucide="trash-2" size="14"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
</body>

</html>