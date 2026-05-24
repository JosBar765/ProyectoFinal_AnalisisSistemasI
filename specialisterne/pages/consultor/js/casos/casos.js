document.addEventListener(
    "DOMContentLoaded",
    () => {
        obtenerCasos();
    }
);

async function obtenerCasos() {

    try {

        const response = await fetch(
            `${consultorBase}/php/casos/getCasos.php?id=${PROJECT_ID}`
        );

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        renderizarTabs(data);
        renderizarPendientes(data.pendientes);
        renderizarCompletados(data.completados);

        inicializarTabs();

        lucide.createIcons();

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar casos",
            "error"
        );

    }

}

function renderizarTabs(data) {

    const tabsContainer =
        document.getElementById(
            "tabsContainer"
        );

    tabsContainer.innerHTML = `
        <div
            id="pendingTab"
            class="tab active"
            data-tab="pendientes"
            data-group="estado">

            Pendientes (${data.pendientes.length})

        </div>

        <div
            id="completedTab"
            class="tab"
            data-tab="completados"
            data-group="estado">

            Completados (${data.completados.length})

        </div>
    `;

}

function renderizarPendientes(pendientes) {

    const container =
        document.getElementById(
            "casesContainer"
        );

    let html = `
        <div
            id="pendientes"
            class="tab-content active"
            data-tab-group="estado">
    `;

    if (pendientes.length === 0) {

        html += `
            <div class="text-center">
                No hay casos pendientes.
            </div>
        `;

    } else {

        pendientes.forEach(caso => {

            html += `
                <div class="caso-card">

                    <div>

                        <div
                            class="d-flex align-center gap-3 mb-2">

                            <h3
                                style="
                                    margin: 0;
                                    font-size: 16px;
                                ">

                                ${caso.titulo}

                            </h3>

                            <span class="badge badge-neutral">

                                ${String(caso.id)}

                            </span>

                        </div>

                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 14px;
                                margin-bottom: 10px;
                            ">

                            Módulo:
                            ${caso.fase}

                        </p>

                        <p style="font-size: 14px;">

                            ${caso.descripcion}

                        </p>

                    </div>

                    <div>

                        <a
                            href="ejecutar.php?id=${caso.id}"
                            class="btn btn-primary"
                            style="padding: 12px 24px;">

                            Ejecutar Prueba

                            <i
                                data-lucide="play"
                                size="16">
                            </i>

                        </a>

                    </div>

                </div>
            `;

        });

    }

    html += `</div>`;

    container.innerHTML = html;

}

function renderizarCompletados(completados) {

    const pendientes =
        document.getElementById(
            "pendientes"
        );

    let html = `
        <div
            id="completados"
            class="tab-content"
            data-tab-group="estado">
    `;

    if (completados.length === 0) {

        html += `
            <div class="text-center">
                No hay casos completados.
            </div>
        `;

    } else {

        completados.forEach(caso => {

            const badge = obtenerBadge(caso.estado);
            const icon = obtenerIcon(caso.estado);

            html += `
                <div
                    class="caso-card"
                    style="background: var(--bg-color);">

                    <div>

                        <div
                            class="
                                d-flex
                                align-center
                                gap-3
                                mb-2
                            ">

                            <h3
                                style="
                                    margin: 0;
                                    font-size: 16px;
                                    color: var(--text-muted);
                                ">

                                ${caso.titulo}

                            </h3>

                            <span class="badge badge-neutral">

                                ${String(caso.id)}

                            </span>

                        </div>

                        <div
                            class="d-flex align-center gap-2"
                            style="
                                font-size: 14px;
                                font-weight: 500;
                            ">

                            <span class="badge ${badge}">

                                <i
                                    data-lucide="${icon}"
                                    size="14">
                                </i>

                                ${caso.estado}

                            </span>

                        </div>

                    </div>

                    <div>

                        <span
                            style="
                                color: var(--text-muted);
                                font-size: 13px;
                            ">

                            ${caso.fecha_ejecucion ?? "Sin fecha"}

                        </span>

                    </div>

                </div>
            `;

        });

    }

    html += `</div>`;

    pendientes.insertAdjacentHTML(
        "afterend",
        html
    );

}

function obtenerBadge(estado) {

    switch (estado) {

        case "Completado":
            return "badge-success";

        case "Fallido":
            return "badge-error";

        case "En Progreso":
            return "badge-warning";

        default:
            return "badge-primary";

    }

}

function obtenerIcon(estado) {

    switch (estado) {

        case "Completado":
            return "check-circle";

        case "Fallido":
            return "x-circle";

        case "En Progreso":
            return "loader-circle";

        default:
            return "clock-3";

    }

}

function inicializarTabs() {

    const tabs = document.querySelectorAll(".tab");

    tabs.forEach(tab => {

        tab.addEventListener(
            "click",
            () => {

                const tabGroup =
                    tab.dataset.group;

                const targetTab =
                    tab.dataset.tab;

                document
                    .querySelectorAll(
                        `.tab[data-group="${tabGroup}"]`
                    )
                    .forEach(t => {
                        t.classList.remove("active");
                    });

                tab.classList.add("active");

                document
                    .querySelectorAll(
                        `.tab-content[data-tab-group="${tabGroup}"]`
                    )
                    .forEach(content => {
                        content.classList.remove("active");
                    });

                document
                    .getElementById(targetTab)
                    .classList.add("active");

            }
        );

    });

}