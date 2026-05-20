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
    <title>Casos de Prueba - Specialisterne</title>
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
                <a href="manuales.html"><i data-lucide="book-open"></i> Manuales</a>
                <a href="casos.html" class="active"><i data-lucide="list-checks"></i> Casos de Prueba</a>
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
                        <div class="breadcrumb">Supervisor / Casos de Prueba</div>
                        <h1>Gestión de Casos de Prueba</h1>
                    </div>
                    <button class="btn btn-primary" onclick="openCaseCreate()"><i data-lucide="plus"></i> Nuevo Caso</button>
                </div>

                <!-- Formulario -->
                <div class="card" id="casoForm" style="display: none; border-left: 4px solid var(--primary-color);">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title" id="caseFormTitle">Crear Caso de Prueba</h2>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;" id="caseFormHint">Completa la informacion del nuevo caso.</p>
                        </div>
                        <button class="btn btn-secondary" onclick="closeCaseForm()"><i data-lucide="x" size="16"></i></button>
                    </div>
                    <form>
                        <div class="dashboard-grid">
                            <div class="form-group">
                                <label class="form-label">Fase del Proyecto</label>
                                <select class="form-control" id="casePhase" required>
                                    <option value="login">Módulo Login</option>
                                    <option value="facturacion">Módulo Facturación</option>
                                    <option value="reportes">Módulo Reportes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Título del Caso</label>
                                <input type="text" class="form-control" id="caseTitle" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Descripción Breve</label>
                            <input type="text" class="form-control" id="caseDesc" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Instrucciones Paso a Paso</label>
                            <textarea class="form-control" id="caseSteps" placeholder="1. Entrar a...&#10;2. Hacer clic en...&#10;3. Ingresar el valor..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Resultado Esperado</label>
                            <textarea class="form-control" id="caseExpected" style="min-height: 60px;" placeholder="El sistema debe mostrar el mensaje..." required></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="caseSubmit">Guardar Caso</button>
                            <button type="button" class="btn btn-secondary" onclick="closeCaseForm()">Cancelar</button>
                        </div>
                    </form>
                </div>

                <!-- Lista por Fases -->
                <div class="card">
                    <div class="tabs">
                        <div class="tab active" data-tab="fase1" data-group="fases">Módulo Login</div>
                        <div class="tab" data-tab="fase2" data-group="fases">Módulo Facturación</div>
                        <div class="tab" data-tab="fase3" data-group="fases">Módulo Reportes</div>
                    </div>

                    <div class="tab-content-container">
                        <!-- Tab Content 1 -->
                        <div id="fase1" class="tab-content active" data-tab-group="fases">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Descripción Corta</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CP-001</td>
                                            <td><strong>Login credenciales válidas</strong></td>
                                            <td>Verificar login con credenciales válidas</td>
                                            <td><span class="badge badge-success">Completado</span></td>
                                            <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openCaseEdit(this)" data-case-id="CP-001" data-case-title="Login credenciales válidas" data-case-desc="Verificar login con credenciales válidas" data-case-phase="login" data-case-steps="1. Ingresar correo válido.&#10;2. Ingresar contraseña válida.&#10;3. Hacer clic en Iniciar sesión." data-case-expected="El sistema permite el acceso y muestra el dashboard."><i data-lucide="edit" size="14"></i></button></td>
                                        </tr>
                                        <tr>
                                            <td>CP-002</td>
                                            <td><strong>Login contraseña incorrecta</strong></td>
                                            <td>Verificar login con contraseña incorrecta</td>
                                            <td><span class="badge badge-success">Completado</span></td>
                                            <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openCaseEdit(this)" data-case-id="CP-002" data-case-title="Login contraseña incorrecta" data-case-desc="Verificar login con contraseña incorrecta" data-case-phase="login" data-case-steps="1. Ingresar correo válido.&#10;2. Ingresar contraseña incorrecta.&#10;3. Hacer clic en Iniciar sesión." data-case-expected="El sistema muestra mensaje de error y no permite el acceso."><i data-lucide="edit" size="14"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Content 2 -->
                        <div id="fase2" class="tab-content" data-tab-group="fases">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Descripción Corta</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CP-003</td>
                                            <td><strong>Generar Factura A</strong></td>
                                            <td>Validar generación de factura A con RUT válido</td>
                                            <td><span class="badge badge-primary">En Progreso</span></td>
                                            <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openCaseEdit(this)" data-case-id="CP-003" data-case-title="Generar Factura A" data-case-desc="Validar generación de factura A con RUT válido" data-case-phase="facturacion" data-case-steps="1. Ingresar al módulo Facturación.&#10;2. Seleccionar Factura A.&#10;3. Completar datos del cliente." data-case-expected="El sistema genera la factura sin errores."><i data-lucide="edit" size="14"></i></button></td>
                                        </tr>
                                        <tr>
                                            <td>CP-004</td>
                                            <td><strong>Validar IVA</strong></td>
                                            <td>El cálculo del IVA debe ser 21%</td>
                                            <td><span class="badge badge-error">Fallido</span></td>
                                            <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openCaseEdit(this)" data-case-id="CP-004" data-case-title="Validar IVA" data-case-desc="El cálculo del IVA debe ser 21%" data-case-phase="facturacion" data-case-steps="1. Generar factura B.&#10;2. Agregar producto.&#10;3. Calcular totales." data-case-expected="El IVA mostrado es 21% sobre el subtotal."><i data-lucide="edit" size="14"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Content 3 -->
                        <div id="fase3" class="tab-content" data-tab-group="fases">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Descripción Corta</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CP-005</td>
                                            <td><strong>Descarga PDF</strong></td>
                                            <td>Verificar descarga de reporte PDF</td>
                                            <td><span class="badge badge-neutral">Pendiente</span></td>
                                            <td><button class="btn btn-secondary" style="padding: 4px 8px;" onclick="openCaseEdit(this)" data-case-id="CP-005" data-case-title="Descarga PDF" data-case-desc="Verificar descarga de reporte PDF" data-case-phase="reportes" data-case-steps="1. Ir a Reportes.&#10;2. Seleccionar reporte.&#10;3. Descargar PDF." data-case-expected="Se descarga un PDF valido y legible."><i data-lucide="edit" size="14"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function openCaseCreate() {
            resetCaseForm();
            document.getElementById('caseFormTitle').textContent = 'Crear Caso de Prueba';
            document.getElementById('caseFormHint').textContent = 'Completa la informacion del nuevo caso.';
            document.getElementById('caseSubmit').textContent = 'Guardar Caso';
            document.getElementById('casoForm').style.display = 'block';
        }

        function openCaseEdit(button) {
            resetCaseForm();
            document.getElementById('caseFormTitle').textContent = 'Editar Caso de Prueba';
            document.getElementById('caseFormHint').textContent = 'Actualiza los datos del caso seleccionado.';
            document.getElementById('caseSubmit').textContent = 'Guardar Cambios';
            document.getElementById('caseTitle').value = button.getAttribute('data-case-title') || '';
            document.getElementById('caseDesc').value = button.getAttribute('data-case-desc') || '';
            document.getElementById('casePhase').value = button.getAttribute('data-case-phase') || 'login';
            document.getElementById('caseSteps').value = button.getAttribute('data-case-steps') || '';
            document.getElementById('caseExpected').value = button.getAttribute('data-case-expected') || '';
            document.getElementById('casoForm').style.display = 'block';
        }

        function closeCaseForm() {
            document.getElementById('casoForm').style.display = 'none';
        }

        function resetCaseForm() {
            document.getElementById('caseTitle').value = '';
            document.getElementById('caseDesc').value = '';
            document.getElementById('casePhase').value = 'login';
            document.getElementById('caseSteps').value = '';
            document.getElementById('caseExpected').value = '';
        }
    </script>
    <script src="../../js/app.js"></script>
</body>

</html>