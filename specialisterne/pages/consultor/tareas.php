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
    <title>Mis Tareas - Consultor</title>
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
                <a href="index.html"><i data-lucide="folder-kanban"></i> Mis Proyectos</a>
                <a href="tareas.html" class="active"><i data-lucide="check-square"></i> Mis Tareas</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div></div>
                <div class="user-info">
                    <span class="role-badge" style="background-color: rgba(74, 111, 165, 0.15); color: var(--primary-color);">Consultor</span>
                    <span>María García</span>
                    <div class="avatar" style="background-color: var(--primary-color);">M</div>
                    <a id="logoutBtn" href="../../index.html" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <h1>Historial de Tareas y Ejecuciones</h1>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex gap-2">
                            <select class="form-control" style="width: 250px;">
                                <option value="">Todos los Proyectos</option>
                                <option value="1">Sistema de Facturación Cliente XYZ</option>
                                <option value="2">Portal de RRHH</option>
                            </select>
                            <select class="form-control" style="width: 200px;">
                                <option value="">Todos los Resultados</option>
                                <option value="aprobado">Aprobados</option>
                                <option value="fallido">Fallidos / Reportados</option>
                                <option value="bloqueado">Bloqueados</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Caso de Prueba</th>
                                    <th>Proyecto</th>
                                    <th>Fecha de Ejecución</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Descarga PDF</strong><br>
                                        <span style="font-size: 12px; color: var(--text-muted);">CP-010</span>
                                    </td>
                                    <td>Sistema de Facturación Cliente XYZ</td>
                                    <td>16 May 2026, 11:45</td>
                                    <td>
                                        <span class="badge badge-error"><i data-lucide="x-circle" size="12" style="margin-right: 4px;"></i> Fallido</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Login con credenciales válidas</strong><br>
                                        <span style="font-size: 12px; color: var(--text-muted);">CP-001</span>
                                    </td>
                                    <td>Sistema de Facturación Cliente XYZ</td>
                                    <td>15 May 2026, 09:30</td>
                                    <td>
                                        <span class="badge badge-success"><i data-lucide="check-circle" size="12" style="margin-right: 4px;"></i> Aprobado</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Solicitud de vacaciones anuales</strong><br>
                                        <span style="font-size: 12px; color: var(--text-muted);">CP-045</span>
                                    </td>
                                    <td>Portal de RRHH</td>
                                    <td>10 May 2026, 14:15</td>
                                    <td>
                                        <span class="badge badge-warning" style="background-color: rgba(232, 168, 56, 0.15); color: var(--warning-color);"><i data-lucide="alert-triangle" size="12" style="margin-right: 4px;"></i> Bloqueado</span>
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