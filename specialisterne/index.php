<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specialisterne Testing Platform - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="logo">
                <!-- Se usaría el logo real, esto es placeholder -->
                <i data-lucide="shield-check" style="width: 48px; height: 48px; color: var(--primary-color);"></i>
                <h1 style="font-size: 20px; margin-top: 10px;">Specialisterne Testing Platform</h1>
            </div>
            
            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="form-group" style="text-align: left;">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" id="email" class="form-control" placeholder="usuario@specialisterne.com" required>
                </div>
                
                <div class="form-group" style="text-align: left;">
                    <label class="form-label">Contraseña</label>
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <div id="loginError" style="color: var(--error-color); font-size: 13px; margin-bottom: 15px; display: none;">
                    Credenciales inválidas.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Iniciar Sesión</button>
            </form>

            <div style="margin-top: 20px; font-size: 12px; color: var(--text-muted); text-align: left; background: var(--bg-color); padding: 10px; border-radius: 4px;">
                <strong>Demo Access:</strong><br>
                Admin: admin@specialisterne.com<br>
                Supervisor: supervisor@specialisterne.com<br>
                Consultor: consultor@specialisterne.com<br>
                <em>(Cualquier contraseña funciona)</em>
            </div>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script>
        function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('email').value.toLowerCase();
            
            if (email.includes('admin')) {
                window.location.href = 'pages/admin/index.html';
            } else if (email.includes('supervisor')) {
                window.location.href = 'pages/supervisor/index.html';
            } else if (email.includes('consultor')) {
                window.location.href = 'pages/consultor/index.html';
            } else {
                document.getElementById('loginError').style.display = 'block';
            }
        }
    </script>
</body>
</html>
