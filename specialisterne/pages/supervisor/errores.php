<?php
session_start();

require_once '../../php/path.php';

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["user"]["id_rol"] != 2) {
    header("Location: {$path}/403.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Errores Reportados - Specialisterne</title>
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
                <a href="index.php"><i data-lucide="layout-dashboard"></i> Dashboard</a>
                <a href="proyectos.php"><i data-lucide="folder-kanban"></i> Proyectos</a>
                <a href="manuales.php"><i data-lucide="book-open"></i> Manuales</a>
                <a href="casos.php"><i data-lucide="list-checks"></i> Casos de Prueba</a>
                <a href="asignaciones.php"><i data-lucide="users"></i> Asignaciones</a>
                <a href="errores.php" class="active"><i data-lucide="bug"></i> Errores</a>
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
                    <span>
                        <?= $_SESSION["user"]["nombre"] . " " . $_SESSION["user"]["apellido"] ?>
                    </span>
                    <div class="avatar" style="background-color: var(--warning-color);">
                        <?= strtoupper(substr($_SESSION["user"]["nombre"], 0, 1)) . strtoupper(substr($_SESSION["user"]["apellido"], 0, 1)) ?>
                    </div>
                    <a id="logoutBtn" href="../../index.php" title="Cerrar sesión" style="color: var(--text-muted);">
                        <i data-lucide="log-out" size="18"></i>
                    </a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb">Supervisor / Errores</div>
                        <h1>Errores Reportados</h1>
                    </div>
                </div>

                <!-- Detalle de Error (Oculto por defecto) -->
                <div class="card" id="errorDetail" style="display: none; border-left: 4px solid var(--error-color);">
                    <div class="card-header">
                        <div class="d-flex align-center gap-3">
                            <h2 class="card-title">Detalle del Error: ERR-104</h2>
                            <span class="badge badge-error" style="background-color: #D64045; color: white;">CRÍTICO</span>
                        </div>
                        <button class="btn btn-secondary" onclick="document.getElementById('errorDetail').style.display='none'"><i data-lucide="x" size="16"></i></button>
                    </div>

                    <div style="background: var(--bg-color); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="font-size: 16px; margin-bottom: 10px;">Cálculo de IVA incorrecto en facturas B</h3>
                        <p style="font-size: 14px; margin-bottom: 15px;">Al generar una factura tipo B para un cliente consumidor final, el sistema calcula el IVA al 10.5% en lugar de tomar el precio final o calcularlo al 21% según las reglas de negocio.</p>

                        <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                            <div>
                                <strong style="font-size: 12px; color: var(--text-muted); display: block;">Reportado por:</strong>
                                <span style="font-size: 14px;">Carlos López (18 May 2026 14:30)</span>
                            </div>
                            <div>
                                <strong style="font-size: 12px; color: var(--text-muted); display: block;">Caso Vinculado:</strong>
                                <span style="font-size: 14px;">CP-004: Validar IVA</span>
                            </div>
                        </div>
                    </div>

                    <h3 style="font-size: 14px; margin-bottom: 10px;">Evidencias (Imágenes adjuntas)</h3>
                    <div style="display: flex; gap: 15px;">
                        <!-- Mockup de imagen -->
                        <div style="width: 150px; height: 100px; background: #E0E4EF; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                            <i data-lucide="image" size="32"></i>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Errores -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex gap-2">
                            <select class="form-control">
                                <option value="">Todas las Severidades</option>
                                <option value="critico">Crítico</option>
                                <option value="alto">Alto</option>
                                <option value="medio">Medio</option>
                                <option value="bajo">Bajo</option>
                            </select>
                            <select class="form-control">
                                <option value="">Todos los Estados</option>
                                <option value="abierto">Abierto</option>
                                <option value="resuelto">Resuelto</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Severidad</th>
                                    <th>Caso de Prueba</th>
                                    <th>Consultor</th>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ERR-104</td>
                                    <td><strong>Cálculo de IVA incorrecto en facturas B</strong></td>
                                    <td><span class="badge badge-error" style="background-color: #D64045; color: white;">CRÍTICO</span></td>
                                    <td>CP-004: Validar IVA</td>
                                    <td>Carlos López</td>
                                    <td>18 May 2026</td>
                                    <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="document.getElementById('errorDetail').style.display='block'"><i data-lucide="eye" size="14"></i></button></td>
                                </tr>
                                <tr>
                                    <td>ERR-103</td>
                                    <td><strong>Botón 'Generar Factura' no responde en Firefox</strong></td>
                                    <td><span class="badge badge-warning" style="background-color: var(--warning-color); color: white;">ALTO</span></td>
                                    <td>CP-003: Generar Factura A</td>
                                    <td>María García</td>
                                    <td>18 May 2026</td>
                                    <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="document.getElementById('errorDetail').style.display='block'"><i data-lucide="eye" size="14"></i></button></td>
                                </tr>
                                <tr>
                                    <td>ERR-102</td>
                                    <td><strong>Color de texto con bajo contraste en botón 'Cancelar'</strong></td>
                                    <td><span class="badge badge-neutral">BAJO</span></td>
                                    <td>CP-001: Login</td>
                                    <td>Carlos López</td>
                                    <td>16 May 2026</td>
                                    <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="document.getElementById('errorDetail').style.display='block'"><i data-lucide="eye" size="14"></i></button></td>
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