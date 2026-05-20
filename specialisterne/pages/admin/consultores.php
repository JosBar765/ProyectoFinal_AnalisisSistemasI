<?php
session_start();

// Si se sube a un host, darle el valor ""
$path = "/specialisterne";

if (!isset($_SESSION["user"])) {
    header("Location: {$path}/index.php");
    exit;
}

if ($_SESSION["id_rol"] != 1) {
    header("Location: {$path}/403.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Consultores - Specialisterne</title>
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
                <a href="index.php"><i data-lucide="layout-dashboard"></i> Dashboard</a>
                <a href="usuarios.php"><i data-lucide="users"></i> Usuarios</a>
                <a href="consultores.php" class="active"><i data-lucide="user-check"></i> Consultores</a>
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
                    <a href="../../index.php" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb">Admin / Consultores</div>
                        <h1>Gestión de Consultores</h1>
                    </div>
                    <button class="btn btn-primary" onclick="openConsultorCreate()"><i data-lucide="plus"></i> Registrar Consultor</button>
                </div>

                <!-- Formulario -->
                <div class="card" id="consultorFormCard" style="display: none; border-left: 4px solid var(--primary-color);">
                    <div class="card-header">
                        <h2 class="card-title">Registrar Perfil de Consultor</h2>
                        <button class="btn btn-secondary" onclick="closeConsultorForms()"><i data-lucide="x" size="16"></i></button>
                    </div>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Usuario (solo rol Consultor)</label>
                            <select class="form-control" required>
                                <option value="">Seleccione un usuario...</option>
                                <option value="1">María García (maria.garcia@specialisterne.com)</option>
                                <option value="2">Carlos López (carlos.lopez@specialisterne.com)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Perfil de Trabajo / Descripción</label>
                            <textarea class="form-control" placeholder="Ej. Excelente en validación de UI y tareas repetitivas. Requiere pausas regulares." required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Habilidades (separadas por coma)</label>
                            <input type="text" class="form-control" placeholder="Pruebas manuales, UI, Accesibilidad">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Perfil</button>
                            <button type="button" class="btn btn-secondary" onclick="closeConsultorForms()">Cancelar</button>
                        </div>
                    </form>
                </div>

                <!-- Formulario Editar (Oculto por defecto) -->
                <div class="card" id="consultorEditCard" style="display: none; border-left: 4px solid var(--primary-color);">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Editar Perfil de Consultor</h2>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Actualiza el perfil y habilidades del consultor.</p>
                        </div>
                        <button class="btn btn-secondary" onclick="closeConsultorForms()"><i data-lucide="x" size="16"></i></button>
                    </div>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Consultor</label>
                            <input type="text" class="form-control" id="consultorEditUser" readonly style="background: var(--bg-color);">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Perfil de Trabajo / Descripción</label>
                            <textarea class="form-control" id="consultorEditPerfil" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Habilidades (separadas por coma)</label>
                            <input type="text" class="form-control" id="consultorEditSkills" placeholder="Pruebas manuales, UI, Accesibilidad">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <button type="button" class="btn btn-secondary" onclick="closeConsultorForms()">Cancelar</button>
                        </div>
                    </form>
                </div>

                <!-- Tabla -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Perfiles de Consultores</h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Consultor</th>
                                    <th>Perfil Principal</th>
                                    <th>Habilidades</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>María García</strong><br>
                                        <span style="font-size: 12px; color: var(--text-muted);">maria.garcia@...</span>
                                    </td>
                                    <td style="max-width: 250px;">Alta atención al detalle, pruebas funcionales estructuradas.</td>
                                    <td>
                                        <span class="badge badge-neutral" style="margin:2px;">Pruebas Funcionales</span>
                                        <span class="badge badge-neutral" style="margin:2px;">Atención al detalle</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openConsultorEdit(this)" data-consultor="María García" data-perfil="Alta atención al detalle, pruebas funcionales estructuradas." data-skills="Pruebas Funcionales, Atención al detalle"><i data-lucide="edit" size="14"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Carlos López</strong><br>
                                        <span style="font-size: 12px; color: var(--text-muted);">carlos.lopez@...</span>
                                    </td>
                                    <td style="max-width: 250px;">Validación de UI, reconocimiento de patrones repetitivos.</td>
                                    <td>
                                        <span class="badge badge-neutral" style="margin:2px;">UI/UX</span>
                                        <span class="badge badge-neutral" style="margin:2px;">Patrones</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openConsultorEdit(this)" data-consultor="Carlos López" data-perfil="Validación de UI, reconocimiento de patrones repetitivos." data-skills="UI/UX, Patrones"><i data-lucide="edit" size="14"></i></button>
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
        function openConsultorCreate() {
            closeConsultorForms();
            document.getElementById('consultorFormCard').style.display = 'block';
        }

        function openConsultorEdit(button) {
            closeConsultorForms();
            document.getElementById('consultorEditUser').value = button.getAttribute('data-consultor') || '';
            document.getElementById('consultorEditPerfil').value = button.getAttribute('data-perfil') || '';
            document.getElementById('consultorEditSkills').value = button.getAttribute('data-skills') || '';
            document.getElementById('consultorEditCard').style.display = 'block';
        }

        function closeConsultorForms() {
            document.getElementById('consultorFormCard').style.display = 'none';
            document.getElementById('consultorEditCard').style.display = 'none';
        }
    </script>
    <script src="../../js/app.js"></script>
</body>

</html>