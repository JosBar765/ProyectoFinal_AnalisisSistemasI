<?php
session_start();

// Si se sube a un host, darle el valor ""
$path = "/specialisterne";

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Proyecto - Specialisterne</title>
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
                <a href="proyectos.html" class="active"><i data-lucide="folder-kanban"></i> Proyectos</a>
                <a href="manuales.html"><i data-lucide="book-open"></i> Manuales</a>
                <a href="casos.html"><i data-lucide="list-checks"></i> Casos de Prueba</a>
                <a href="asignaciones.html"><i data-lucide="users"></i> Asignaciones</a>
                <a href="errores.html"><i data-lucide="bug"></i> Errores</a>
            </nav>
        </aside>

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
                        <div class="breadcrumb"><a href="proyectos.html" style="color: var(--text-muted); text-decoration: none;">Supervisor / Proyectos</a> / Nuevo</div>
                        <h1>Crear Proyecto</h1>
                    </div>
                </div>

                <div class="card" style="max-width: 800px;">
                    <form action="proyectos.html" data-redirect="proyectos.html">
                        <div class="form-group">
                            <label class="form-label">Nombre del Proyecto</label>
                            <input type="text" class="form-control" required placeholder="Ej. Sistema de Inventario V2">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" required placeholder="Descripción general de lo que se va a probar..."></textarea>
                        </div>

                        <div class="dashboard-grid">
                            <div class="form-group">
                                <label class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Fecha de Fin</label>
                                <input type="date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-control">
                                    <option value="activo">Activo</option>
                                    <option value="pausa">En Pausa</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top: 30px; margin-bottom: 20px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                            <div class="d-flex justify-between align-center mb-3">
                                <h3>Fases del Proyecto</h3>
                                <button type="button" class="btn btn-secondary" onclick="agregarFase()"><i data-lucide="plus" size="16"></i> Agregar Fase</button>
                            </div>

                            <div id="fasesContainer" style="display: flex; flex-direction: column; gap: 15px;">
                                <!-- Fase 1 (Base) -->
                                <div class="card" style="background-color: var(--bg-color); margin-bottom: 0;">
                                    <div class="d-flex justify-between align-center mb-2">
                                        <strong>Fase 1</strong>
                                        <button type="button" class="btn btn-danger" style="padding: 4px 8px;" disabled><i data-lucide="trash-2" size="14"></i></button>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control mb-2" placeholder="Nombre de la fase (Ej. Módulo Login)" required>
                                        <input type="text" class="form-control" placeholder="Descripción breve" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2" style="margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
                            <a href="proyectos.html" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
    <script>
        let contadorFases = 1;

        function agregarFase() {
            contadorFases++;
            const container = document.getElementById('fasesContainer');
            const faseDiv = document.createElement('div');
            faseDiv.className = 'card';
            faseDiv.style = 'background-color: var(--bg-color); margin-bottom: 0;';
            faseDiv.innerHTML = `
                <div class="d-flex justify-between align-center mb-2">
                    <strong>Fase ${contadorFases}</strong>
                    <button type="button" class="btn btn-danger" style="padding: 4px 8px;" onclick="this.closest('.card').remove()"><i data-lucide="trash-2" size="14"></i></button>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control mb-2" placeholder="Nombre de la fase" required>
                    <input type="text" class="form-control" placeholder="Descripción breve" required>
                </div>
            `;
            container.appendChild(faseDiv);
            lucide.createIcons({
                root: faseDiv
            });
        }
    </script>
</body>

</html>