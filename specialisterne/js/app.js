document.addEventListener('DOMContentLoaded', () => {
    // Inicializar iconos Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Funcionalidad de pestañas (Tabs)
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.getAttribute('data-tab');
            const tabGroup = tab.parentElement;
            const contentGroup = tabGroup.nextElementSibling;

            // Remover active de los tabs de este grupo
            tabGroup.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            // Añadir active al clickeado
            tab.classList.add('active');

            // Ocultar todos los contenidos asociados (asumiendo que están juntos o en un container)
            if (contentGroup && contentGroup.classList.contains('tab-content-container')) {
                contentGroup.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                const targetContent = contentGroup.querySelector(`#${tabId}`);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            } else {
                // Alternativa: buscar en todo el documento (menos preciso pero útil)
                document.querySelectorAll(`.tab-content[data-tab-group="${tab.getAttribute('data-group')}"]`).forEach(c => c.classList.remove('active'));
                const targetContent = document.getElementById(tabId);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            }
        });
    });

    // Simulador de Formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Ignorar el form de login para que redirija
        if (form.id === 'loginForm') return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            showToast('Acción realizada con éxito (Simulación)');

            // Opcional: limpiar formulario después de un rato o redirigir
            setTimeout(() => {
                const redirect = form.getAttribute('data-redirect');
                if (redirect) {
                    window.location.href = redirect;
                }
            }, 1500);
        });
    });
});

// Función para mostrar Toast (notificación)
function showToast(message, type = 'success') {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    let icon = 'check-circle';
    if (type === 'error') icon = 'alert-circle';
    if (type === 'warning') icon = 'alert-triangle';

    toast.innerHTML = `
        <i data-lucide="${icon}" class="${type === 'success' ? 'text-success' : ''}"></i>
        <span>${message}</span>
    `;

    toastContainer.appendChild(toast);

    if (typeof lucide !== 'undefined') {
        lucide.createIcons({ root: toast });
    }

    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Remove after 3s
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}
