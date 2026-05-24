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
    <title>Reportar Error - Consultor</title>
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
                        <div class="breadcrumb"><a href="casos.php" style="color: var(--text-muted); text-decoration: none;"><i data-lucide="arrow-left" size="14"></i> Volver a la lista de casos</a></div>
                        <h1 style="margin-top: 10px;">Reportar Error Encontrado</h1>
                    </div>
                </div>

                <div class="card" style="max-width: 800px; margin: 0 auto; border-top: 4px solid var(--error-color);">

                    <div style="background: var(--bg-color); padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid var(--text-muted);">
                        <span style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Reportando fallo para el caso:</span>
                        <h3 style="font-size: 16px; margin-top: 5px;">CP-015: Validar generación de factura B</h3>
                    </div>

                    <form action="casos.php" data-redirect="casos.php">
                        <div class="form-group">
                            <label class="form-label" style="font-size: 16px; font-weight: 600;">¿Qué ocurrió? (Título corto)</label>
                            <input type="text" class="form-control" placeholder="Ej. El botón no hace nada al hacer clic" style="font-size: 16px; padding: 12px;" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descripción detallada</label>
                            <textarea class="form-control" placeholder="Explica exactamente qué estabas haciendo y qué falló..." style="min-height: 120px;" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Severidad del error</label>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                <label style="display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid var(--border-color); padding: 10px 15px; border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="severidad" value="critico" required> 🔴 Crítico (Impide avanzar)
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid var(--border-color); padding: 10px 15px; border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="severidad" value="alto"> 🟠 Alto (Fallo importante)
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid var(--border-color); padding: 10px 15px; border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="severidad" value="medio" checked> 🟡 Medio (Fallo menor)
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid var(--border-color); padding: 10px 15px; border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="severidad" value="bajo"> ⚪ Bajo (Diseño o texto)
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 30px;">
                            <label class="form-label">Adjuntar Capturas de Pantalla (Evidencia)</label>
                            <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 40px; text-align: center; background: var(--bg-color); cursor: pointer;">
                                <i data-lucide="image-plus" size="32" style="color: var(--text-muted); margin-bottom: 10px;"></i>
                                <p style="font-size: 15px; font-weight: 500;">Haz clic aquí para seleccionar imágenes</p>
                                <p style="font-size: 13px; color: var(--text-muted);">o arrastra los archivos aquí</p>
                                <input type="file" style="display: none;" multiple>
                            </div>
                        </div>

                        <div class="d-flex gap-2" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                            <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 12px 24px;"><i data-lucide="send" size="18"></i> Enviar Reporte de Error</button>
                            <a href="ejecutar.php" class="btn btn-secondary" style="font-size: 16px; padding: 12px 24px;">Cancelar</a>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
    <script>
        // Simple script to highlight selected severity
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                document.querySelectorAll('input[type="radio"]').forEach(r => {
                    r.parentElement.style.borderColor = 'var(--border-color)';
                    r.parentElement.style.backgroundColor = '#fff';
                });
                if (e.target.checked) {
                    e.target.parentElement.style.borderColor = 'var(--primary-color)';
                    e.target.parentElement.style.backgroundColor = 'rgba(74, 111, 165, 0.05)';
                }
            });
        });
    </script>
</body>

</html>