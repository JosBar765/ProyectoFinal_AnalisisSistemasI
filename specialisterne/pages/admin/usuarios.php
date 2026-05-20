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
    <title>Gestión de Usuarios - Specialisterne</title>
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
                <a href="usuarios.html" class="active"><i data-lucide="users"></i> Usuarios</a>
                <a href="consultores.html"><i data-lucide="user-check"></i> Consultores</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div></div>
                <div class="user-info">
                    <span class="role-badge">Administrador</span>
                    <span>Admin User</span>
                    <div class="avatar">A</div>
                    <a href="../../index.html" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb">Admin / Usuarios</div>
                        <h1>Gestión de Usuarios</h1>
                    </div>
                    <button class="btn btn-primary" onclick="openUserCreate()"><i data-lucide="plus"></i> Nuevo Usuario</button>
                </div>

                <!-- Formulario (Oculto por defecto) -->
                <div class="card" id="userFormCard" style="display: none; border-left: 4px solid var(--primary-color);">
                    <div class="card-header">
                        <h2 class="card-title">Crear Nuevo Usuario</h2>
                        <button class="btn btn-secondary" onclick="closeUserForms()"><i data-lucide="x" size="16"></i></button>
                    </div>
                    <form>
                        <div class="dashboard-grid">
                            <div class="form-group">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Rol</label>
                                <select class="form-control" required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="admin">Administrador</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="consultor">Consultor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-control">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                            <button type="button" class="btn btn-secondary" onclick="closeUserForms()">Cancelar</button>
                        </div>
                    </form>
                </div>

                <!-- Formulario Editar (Oculto por defecto) -->
                <div class="card" id="userEditCard" style="display: none; border-left: 4px solid var(--primary-color);">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Editar Usuario</h2>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Actualiza los datos del usuario seleccionado.</p>
                        </div>
                        <button class="btn btn-secondary" onclick="closeUserForms()"><i data-lucide="x" size="16"></i></button>
                    </div>
                    <form>
                        <div class="dashboard-grid">
                            <div class="form-group">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="userEditName" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="userEditEmail" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="userEditPassword" placeholder="Dejar en blanco para no cambiar">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Rol</label>
                                <select class="form-control" id="userEditRole" required>
                                    <option value="admin">Administrador</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="consultor">Consultor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-control" id="userEditStatus">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <button type="button" class="btn btn-secondary" onclick="closeUserForms()">Cancelar</button>
                        </div>
                    </form>
                </div>

                <!-- Tabla -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Lista de Usuarios</h2>
                        <select class="form-control" style="width: 200px;">
                            <option value="">Todos los roles</option>
                            <option value="admin">Administradores</option>
                            <option value="supervisor">Supervisores</option>
                            <option value="consultor">Consultores</option>
                        </select>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Admin Default</td>
                                    <td>admin@specialisterne.com</td>
                                    <td><span class="badge badge-neutral">Administrador</span></td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openUserEdit(this)" data-user-name="Admin Default" data-user-email="admin@specialisterne.com" data-user-role="admin" data-user-status="activo"><i data-lucide="edit" size="14"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Laura Martínez</td>
                                    <td>supervisor@specialisterne.com</td>
                                    <td><span class="badge badge-warning">Supervisor</span></td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openUserEdit(this)" data-user-name="Laura Martínez" data-user-email="supervisor@specialisterne.com" data-user-role="supervisor" data-user-status="activo"><i data-lucide="edit" size="14"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>María García</td>
                                    <td>maria.garcia@specialisterne.com</td>
                                    <td><span class="badge badge-primary">Consultor</span></td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openUserEdit(this)" data-user-name="María García" data-user-email="maria.garcia@specialisterne.com" data-user-role="consultor" data-user-status="activo"><i data-lucide="edit" size="14"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Carlos López</td>
                                    <td>carlos.lopez@specialisterne.com</td>
                                    <td><span class="badge badge-primary">Consultor</span></td>
                                    <td><span class="badge badge-error">Inactivo</span></td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openUserEdit(this)" data-user-name="Carlos López" data-user-email="carlos.lopez@specialisterne.com" data-user-role="consultor" data-user-status="inactivo"><i data-lucide="edit" size="14"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script>
        function openUserCreate() {
            closeUserForms();
            document.getElementById('userFormCard').style.display = 'block';
        }

        function openUserEdit(button) {
            closeUserForms();
            document.getElementById('userEditName').value = button.getAttribute('data-user-name') || '';
            document.getElementById('userEditEmail').value = button.getAttribute('data-user-email') || '';
            document.getElementById('userEditRole').value = button.getAttribute('data-user-role') || 'consultor';
            document.getElementById('userEditStatus').value = button.getAttribute('data-user-status') || 'activo';
            document.getElementById('userEditPassword').value = '';
            document.getElementById('userEditCard').style.display = 'block';
        }

        function closeUserForms() {
            document.getElementById('userFormCard').style.display = 'none';
            document.getElementById('userEditCard').style.display = 'none';
        }
    </script>
    <script src="../../js/app.js"></script>
</body>

</html>