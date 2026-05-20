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
    <title>Dashboard Administrador - Specialisterne</title>
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
                <a href="usuarios.html"><i data-lucide="users"></i> Usuarios</a>
                <a href="consultores.html"><i data-lucide="user-check"></i> Consultores</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div></div> <!-- Spacer -->
                <div class="user-info">
                    <span class="role-badge">Administrador</span>
                    <span>Admin User</span>
                    <div class="avatar">A</div>
                    <a href="../../index.html" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <!-- Content -->
            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb">Admin / Dashboard</div>
                        <h1>Dashboard Principal</h1>
                    </div>
                </div>

                <!-- Resumen Cards -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i data-lucide="users"></i></div>
                        <div class="stat-info">
                            <h3>24</h3>
                            <p>Total Usuarios</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i data-lucide="user-check"></i></div>
                        <div class="stat-info">
                            <h3>15</h3>
                            <p>Consultores Activos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i data-lucide="briefcase"></i></div>
                        <div class="stat-info">
                            <h3>8</h3>
                            <p>Proyectos en Curso</p>
                        </div>
                    </div>
                </div>

                <!-- Tabla de últimos usuarios -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Últimos Usuarios Registrados</h2>
                        <a href="usuarios.html" class="btn btn-secondary">Ver todos</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Fecha de Creación</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>María García</td>
                                    <td><span class="badge badge-primary">Consultor</span></td>
                                    <td>18 May 2026</td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                </tr>
                                <tr>
                                    <td>Carlos López</td>
                                    <td><span class="badge badge-primary">Consultor</span></td>
                                    <td>15 May 2026</td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                </tr>
                                <tr>
                                    <td>Laura Martínez</td>
                                    <td><span class="badge badge-warning">Supervisor</span></td>
                                    <td>10 May 2026</td>
                                    <td><span class="badge badge-success">Activo</span></td>
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