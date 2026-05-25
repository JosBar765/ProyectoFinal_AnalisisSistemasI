const proyectoSelect =
    document.getElementById("proyectoSelect");

const tabsContainer =
    document.getElementById("tabsFases");

const tabContentContainer =
    document.getElementById("tabContentContainer");

const casoForm =
    document.getElementById("casoForm");

const formCaso =
    document.getElementById("formCaso");

const casePhase =
    document.getElementById("casePhase");

const caseTitle =
    document.getElementById("caseTitle");

const caseDesc =
    document.getElementById("caseDesc");

const caseSteps =
    document.getElementById("caseSteps");

const caseExpected =
    document.getElementById("caseExpected");

const estadoContainer =
    document.getElementById("estadoContainer");

const caseStatus =
    document.getElementById("caseStatus");

const caseFormTitle =
    document.getElementById("caseFormTitle");

const caseFormHint =
    document.getElementById("caseFormHint");

const caseSubmit =
    document.getElementById("caseSubmit");

let modoEdicion = false;

let idCasoEditar = null;

document.addEventListener("DOMContentLoaded", async () => {

    await cargarSelectProyectos(
        proyectoSelect,
        cargarCasosProyecto
    );

});

async function cargarCasosProyecto(idProyecto) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/casos/getCasosPorProyecto.php?id_proyecto=${idProyecto}`
        );

        const data = await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        renderizarCasos(data.casos);

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar los casos",
            "error"
        );

    }

}

function abrirModoCrear() {

    modoEdicion = false;

    idCasoEditar = null;

    resetFormulario();

    caseFormTitle.textContent =
        "Crear Caso de Prueba";

    caseFormHint.textContent =
        "Completa la información del nuevo caso.";

    caseSubmit.textContent =
        "Guardar Caso";

    estadoContainer.style.display =
        "none";

    casePhase.disabled = false;

    casoForm.style.display = "block";

}

function abrirModoEditar(caso) {

    modoEdicion = true;

    idCasoEditar = caso.id;

    caseFormTitle.textContent =
        "Editar Caso de Prueba";

    caseFormHint.textContent =
        "Actualiza los datos del caso seleccionado.";

    caseSubmit.textContent =
        "Guardar Cambios";

    estadoContainer.style.display =
        "grid";

    casePhase.disabled = true;

    casePhase.value =
        caso.fase;

    caseTitle.value =
        caso.titulo;

    caseDesc.value =
        caso.descripcion;

    caseSteps.value =
        caso.instrucciones;

    caseExpected.value =
        caso.resultado;

    caseStatus.value =
        caso.estadoId;

    casoForm.style.display = "block";

}

function cerrarFormularioCaso() {

    casoForm.style.display = "none";

}

function resetFormulario() {

    formCaso.reset();

    casePhase.disabled = false;

}

function renderizarCasos(casos) {

    tabsContainer.innerHTML = "";
    tabContentContainer.innerHTML = "";

    if (casos.length === 0) {

        tabContentContainer.innerHTML = `
            <div class="card">

                <div
                    style="
                        text-align: center;
                        padding: 40px 20px;
                    ">

                    <i
                        data-lucide="list-x"
                        style="
                            width: 50px;
                            height: 50px;
                            color: var(--text-muted);
                            margin-bottom: 15px;
                        ">
                    </i>

                    <h2 style="margin-bottom: 10px;">
                        No hay casos de prueba
                    </h2>

                    <p style="color: var(--text-muted);">
                        Este proyecto aún no tiene casos registrados.
                    </p>

                </div>

            </div>
        `;

        lucide.createIcons();

        return;

    }

    const fasesMap = new Map();

    casos.forEach(caso => {

        if (!fasesMap.has(caso.fase_id)) {

            fasesMap.set(caso.fase_id, {
                nombre: caso.fase_nombre,
                casos: []
            });

        }

        fasesMap
            .get(caso.fase_id)
            .casos
            .push(caso);

    });

    let index = 0;

    fasesMap.forEach((fase, faseId) => {

        const tabId = `fase_${faseId}`;

        tabsContainer.innerHTML += `
            <div
                class="tab ${index === 0 ? "active" : ""}"
                data-tab="${tabId}"
                data-group="fases">

                ${fase.nombre}

            </div>
        `;

        let rows = "";

        fase.casos.forEach(caso => {

            let badgeClass = "badge-neutral";

            switch (caso.estado) {

                case "En Progreso":
                    badgeClass = "badge-primary";
                    break;

                case "Completado":
                    badgeClass = "badge-success";
                    break;

                case "Pendiente":
                    badgeClass = "badge-warning";
                    break;

                case "Fallido":
                    badgeClass = "badge-error";
                    break;

            }

            rows += `
                <tr>

                    <td>${caso.id}</td>

                    <td>
                        <strong>
                            ${caso.titulo}
                        </strong>
                    </td>

                    <td>
                        ${caso.descripcion}
                    </td>

                    <td>
                        <span class="badge ${badgeClass}">
                            ${caso.estado}
                        </span>
                    </td>

                    <td
                        style="
                            display: flex;
                            gap: 8px;
                        ">

                        <button
                            class="btn btn-secondary btnEditarCaso"
                            style="padding: 4px 8px;"

                            data-id="${caso.id}"
                            data-fase="${caso.fase_id}"
                            data-titulo="${caso.titulo}"
                            data-descripcion="${caso.descripcion}"
                            data-instrucciones="${caso.instrucciones}"
                            data-resultado="${caso.resultado_esperado}"
                            data-estado-id="${caso.id_estado_caso_prueba}">

                            <i data-lucide="edit" size="14"></i>

                        </button>

                        <button
                            class="btn btn-secondary"
                            style="padding: 4px 8px;"

                            onclick="
                                window.location.href=
                                'caso-reporte.php?id=${caso.id}'
                            ">

                            <i data-lucide="eye" size="14"></i>

                        </button>

                        <button
                            class="btn btn-danger btnEliminarCasoTabla"
                            style="padding: 4px 8px;"
                            data-id="${caso.id}">

                            <i data-lucide="trash-2" size="14"></i>

                        </button>

                    </td>

                </tr>
            `;

        });

        tabContentContainer.innerHTML += `
            <div
                id="${tabId}"
                class="tab-content ${index === 0 ? "active" : ""}"
                data-tab-group="fases">

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>

                            </tr>

                        </thead>

                        <tbody>

                            ${rows}

                        </tbody>

                    </table>

                </div>

            </div>
        `;

        index++;

    });

    inicializarTabs();

    lucide.createIcons();

}

function inicializarTabs() {

    const tabs =
        document.querySelectorAll(".tab");

    tabs.forEach(tab => {

        tab.addEventListener("click", () => {

            const group =
                tab.dataset.group;

            const target =
                tab.dataset.tab;

            document
                .querySelectorAll(
                    `.tab[data-group="${group}"]`
                )
                .forEach(t =>
                    t.classList.remove("active")
                );

            document
                .querySelectorAll(
                    `.tab-content[data-tab-group="${group}"]`
                )
                .forEach(c =>
                    c.classList.remove("active")
                );

            tab.classList.add("active");

            document
                .getElementById(target)
                .classList.add("active");

        });

    });

}