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
    <title>Ejecutar Prueba - Consultor</title>
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
                <a href="index.html" class="active"><i data-lucide="folder-kanban"></i> Mis Proyectos</a>
                <a href="tareas.html"><i data-lucide="check-square"></i> Mis Tareas</a>
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
                    <a id="logoutBtn" href="../../index.html" title="Cerrar sesión" style="color: var(--text-muted);"><i data-lucide="log-out" size="18"></i></a>
                </div>
            </header>

            <div class="content-area">
                <div class="page-header">
                    <div class="page-title">
                        <div class="breadcrumb"><a href="casos.html" style="color: var(--text-muted); text-decoration: none;"><i data-lucide="arrow-left" size="14"></i> Volver a la lista de casos</a></div>
                        <h1 style="margin-top: 10px;">Ejecutar Caso: CP-015</h1>
                    </div>
                </div>

                <div class="dashboard-grid" style="grid-template-columns: 3fr 2fr;">

                    <!-- Información del Caso (Solo lectura) -->
                    <div class="card" style="border-top: 4px solid var(--primary-color);">
                        <h2 class="card-title" style="font-size: 20px; margin-bottom: 20px;">Validar generación de factura B</h2>

                        <div style="margin-bottom: 20px;">
                            <h3 style="font-size: 14px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;">Instrucciones Paso a Paso:</h3>
                            <div style="background: var(--bg-color); padding: 15px; border-radius: 8px; font-size: 15px; line-height: 1.6; white-space: pre-line;">
                                1. Iniciar sesión con usuario válido.
                                2. Ir al menú lateral izquierdo y hacer clic en "Facturación".
                                3. Hacer clic en el botón verde "Nueva Factura".
                                4. En el campo "Tipo de Factura", seleccionar "Factura B".
                                5. Agregar un producto de prueba con precio $1000.
                                6. Hacer clic en "Calcular Totales".
                            </div>
                        </div>

                        <div>
                            <h3 style="font-size: 14px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;">Resultado Esperado:</h3>
                            <div style="background: rgba(91, 173, 145, 0.1); border-left: 4px solid var(--success-color); padding: 15px; border-radius: 4px; font-size: 15px;">
                                El sistema debe mostrar en el resumen de totales:
                                - Subtotal: $1000
                                - IVA (21%): $210
                                - Total a Pagar: $1210
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Ejecución -->
                    <div class="card" style="background: #F8F9FB; border: 1px solid var(--border-color);">
                        <h2 class="card-title" style="margin-bottom: 20px;">Registrar Resultado</h2>

                        <form action="casos.html" data-redirect="casos.html" id="ejecucionForm">
                            <div class="form-group">
                                <label class="form-label" style="font-size: 16px;">¿El resultado fue el esperado?</label>
                                <select class="form-control" id="resultadoSelect" style="font-size: 16px; padding: 12px;" required onchange="handleResultadoChange()">
                                    <option value="">Seleccione el resultado...</option>
                                    <option value="aprobado">✅ Sí, funcionó correctamente (Aprobado)</option>
                                    <option value="fallido">❌ No, ocurrió un error (Fallido)</option>
                                    <option value="bloqueado">⚠️ No pude realizar la prueba (Bloqueado)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Observaciones (Opcional si fue aprobado)</label>
                                <textarea class="form-control" placeholder="Escribe aquí si notaste algo raro o por qué falló..."></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Fecha y Hora de Ejecución</label>
                                <input type="text" class="form-control" value="18 May 2026, 14:35" readonly style="background: #e9ecef; cursor: not-allowed;">
                            </div>

                            <!-- Botón para reportar error visible solo si falla -->
                            <div id="errorReportBtn" style="display: none; margin-bottom: 20px; background: rgba(214, 64, 69, 0.1); padding: 15px; border-radius: 8px; border-left: 4px solid var(--error-color);">
                                <p style="font-size: 14px; margin-bottom: 10px; color: var(--error-color); font-weight: 500;"><i data-lucide="alert-triangle" size="16"></i> Has marcado la prueba como Fallida.</p>
                                <a href="reportar.html" class="btn btn-danger" style="width: 100%;">Proceder a Reportar Error Fijo</a>
                                <p style="font-size: 12px; margin-top: 10px; text-align: center; color: var(--text-muted);">Al hacer clic, guardarás el estado y podrás adjuntar capturas de pantalla del error.</p>
                            </div>

                            <div class="d-flex gap-2" id="submitButtons">
                                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">Guardar Ejecución</button>
                                <a href="casos.html" class="btn btn-secondary" style="padding: 12px;">Cancelar</a>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
    <script>
        function handleResultadoChange() {
            const select = document.getElementById('resultadoSelect');
            const errorBtn = document.getElementById('errorReportBtn');
            const submitBtns = document.getElementById('submitButtons');

            if (select.value === 'fallido') {
                errorBtn.style.display = 'block';
                submitBtns.style.display = 'none';
            } else {
                errorBtn.style.display = 'none';
                submitBtns.style.display = 'flex';
            }
        }
    </script>
</body>

</html>