document.addEventListener("DOMContentLoaded", () => {
    obtenerProyectos();
});

async function obtenerProyectos() {

    try {

        const response = await fetch(
            `${consultorBase}/php/proyectos/getProyectos.php`
        );

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        renderizarProyectos(data.projects);

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar proyectos",
            "error"
        );

    }

}

function renderizarProyectos(proyectos) {

    const container =
        document.getElementById("projectsContainer");

    container.innerHTML = "";

    if (proyectos.length === 0) {

        container.innerHTML = `
            <div class="card text-center">
                <p>No tienes proyectos asignados.</p>
            </div>
        `;

        return;
    }

    proyectos.forEach(proyecto => {

        const card = document.createElement("div");

        card.className =
            "card consultor-card";

        card.style.display = "flex";
        card.style.flexDirection = "column";
        card.style.justifyContent = "space-between";

        const badgeClass =
            obtenerClaseEstado(
                proyecto.estado
            );

        const progreso =
            calcularProgreso(
                proyecto.casos_completados,
                proyecto.total_casos
            );

        card.innerHTML = `
            <div>

                <div class="d-flex justify-between align-center mb-3">

                    <h2
                        class="card-title"
                        style="margin: 0; font-size: 18px;">

                        ${proyecto.nombre}

                    </h2>

                    <span class="badge ${badgeClass}">
                        ${proyecto.estado}
                    </span>

                </div>

                <div
                    class="d-flex gap-3 mb-4"
                    style="font-size: 13px; color: var(--text-muted);">

                    <span class="d-flex align-center gap-2">
                        <i data-lucide="calendar" size="16"></i>

                        Fin:
                        ${proyecto.fecha_fin}
                    </span>

                </div>

                <div
                    style="
                        background-color: var(--bg-color);
                        padding: 15px;
                        border-radius: 8px;
                        margin-bottom: 20px;
                    ">

                    <div class="d-flex justify-between align-center mb-2">

                        <span style="font-weight: 500;">
                            Tu progreso
                        </span>

                        <span
                            style="
                                font-weight: 600;
                                color: var(--primary-color);
                            ">

                            ${proyecto.casos_completados}
                            /
                            ${proyecto.total_casos}
                            Casos

                        </span>

                    </div>

                    <div class="progress-container mb-1">

                        <div
                            class="progress-bar"
                            style="width: ${progreso}%;">
                        </div>

                    </div>

                </div>

            </div>

            <div class="text-center">

            ${(() => {
                if (proyecto.estado === "En Pausa") {
                    return `
                <a class="btn btn-secondary" style="width: 100%; padding: 12px; font-size: 16px;">
                    En Pausa
                </a>
            `;
                } else if (proyecto.estado === "Activo") {
                    return `
                <a href="casos.php?id=${proyecto.id}" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px;">
                    Ver Casos de Prueba
                    <i data-lucide="arrow-right" size="18"></i>
                </a>
            `;
                } else {
                    return `
                <a class="btn btn-secondary" style="width: 100%; padding: 12px; font-size: 16px;">
                    Finalizado
                </a>
            `;
                }
            })()
            }

            </div >
        `;

        container.appendChild(card);

    });

    lucide.createIcons();
}

function obtenerClaseEstado(estado) {

    switch (estado) {

        case "Activo":
            return "badge-success";

        case "En Pausa":
            return "badge-warning";

        case "Finalizado":
            return "badge-neutral";

        default:
            return "badge-primary";
    }

}

function calcularProgreso(
    completados,
    total
) {

    if (total === 0) {
        return 0;
    }

    return Math.round(
        (completados / total) * 100
    );

}