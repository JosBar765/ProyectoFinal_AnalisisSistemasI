const supervisorBase = `${base}/pages/supervisor`;

const proyectoSelect = document.getElementById("proyectoSelect");

const avanceGeneral = document.getElementById("avanceGeneral");
const estadoProyecto = document.getElementById("estadoProyecto");
const barraProgresoGeneral = document.getElementById("barraProgresoGeneral");
const casosEjecutadosTexto = document.getElementById("casosEjecutadosTexto");

const statPendientes = document.getElementById("statPendientes");
const statEnProgreso = document.getElementById("statEnProgreso");
const statCompletados = document.getElementById("statCompletados");
const statFallidos = document.getElementById("statFallidos");

const tablaFasesBody = document.getElementById("tablaFasesBody");

const erroresRecientesContainer = document.getElementById("erroresRecientesContainer");

document.addEventListener("DOMContentLoaded", async () => {

    const idFromURL = getProyectoFromURL();

    await cargarProyectos(idFromURL);

    proyectoSelect.addEventListener("change", async () => {

        const idProyecto = proyectoSelect.value;

        await cargarDashboard(idProyecto);

    });

});

async function cargarProyectos(selectedId = null) {

    const response = await fetch(`${supervisorBase}/php/proyectos/getProyectos.php`);
    const data = await response.json();

    proyectoSelect.innerHTML = "";

    data.forEach(proyecto => {
        proyectoSelect.innerHTML += `
            <option value="${proyecto.id}">
                ${proyecto.nombre}
            </option>
        `;
    });

    // decidir qué proyecto seleccionar
    let idFinal = selectedId;

    if (!idFinal && data.length > 0) {
        idFinal = data[0].id;
    }

    // setear valor visual del select
    proyectoSelect.value = idFinal;

    // cargar dashboard inicial
    await cargarDashboard(idFinal);
}

async function cargarDashboard(idProyecto) {

    await Promise.all([
        cargarResumen(idProyecto),
        cargarFases(idProyecto),
        cargarErrores(idProyecto)
    ]);

}

async function cargarResumen(idProyecto) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/dashboard/getDashboardResumen.php?id_proyecto=${idProyecto}`
        );

        const data = await response.json();

        avanceGeneral.textContent =
            `Avance General: ${data.avance}%`;

        estadoProyecto.textContent =
            data.estado;

        barraProgresoGeneral.style.width =
            `${data.avance}%`;

        casosEjecutadosTexto.textContent =
            `${data.ejecutados} de ${data.total_casos} Casos de Prueba Ejecutados`;

        statPendientes.textContent =
            data.pendientes;

        statEnProgreso.textContent =
            data.en_progreso;

        statCompletados.textContent =
            data.completados;

        statFallidos.textContent =
            data.fallidos;

    } catch (error) {

        console.error(error);

    }

}

async function cargarFases(idProyecto) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/dashboard/getDashboardFases.php?id_proyecto=${idProyecto}`
        );

        const data = await response.json();

        tablaFasesBody.innerHTML = "";

        data.forEach(fase => {

            let badgeClass = "badge-neutral";

            if (fase.estado === "Completado") {
                badgeClass = "badge-success";
            }

            else if (fase.estado === "En Progreso") {
                badgeClass = "badge-primary";
            }

            else if (fase.estado === "Fallido") {
                badgeClass = "badge-error";
            }

            tablaFasesBody.innerHTML += `
                <tr>

                    <td>
                        ${fase.fase}
                    </td>

                    <td>

                        <div style="display: flex; align-items: center; gap: 10px;">

                            <div
                                class="progress-container"
                                style="flex: 1; margin: 0;">

                                <div
                                    class="progress-bar"
                                    style="width: ${fase.avance}%;">
                                </div>

                            </div>

                            <span style="font-size: 12px;">
                                ${fase.avance}%
                            </span>

                        </div>

                    </td>

                    <td>
                        <span class="badge ${badgeClass}">
                            ${fase.estado}
                        </span>
                    </td>

                </tr>
            `;

        });

    } catch (error) {

        console.error(error);

    }

}

async function cargarErrores(idProyecto) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/dashboard/getDashboardErrores.php?id_proyecto=${idProyecto}`
        );

        const data = await response.json();

        erroresRecientesContainer.innerHTML = "";

        data.forEach(error => {

            let severidadColor = "#6B7280";

            if (error.severidad === "ALTO") {
                severidadColor = "var(--warning-color)";
            }

            else if (error.severidad === "CRITICO") {
                severidadColor = "#D64045";
            }

            else if (error.severidad === "MEDIO") {
                severidadColor = "var(--primary-color)";
            }

            erroresRecientesContainer.innerHTML += `
                <div
                    style="
                        padding-bottom: 10px;
                        border-bottom: 1px solid var(--border-color);
                    ">

                    <div class="d-flex justify-between align-center mb-1">

                        <span
                            class="badge badge-error"
                            style="
                                background-color: ${severidadColor};
                                color: white;
                            ">

                            ${error.severidad}

                        </span>

                        <span
                            style="
                                font-size: 12px;
                                color: var(--text-muted);
                            ">

                            ${error.fecha}

                        </span>

                    </div>

                    <p
                        style="
                            font-size: 13px;
                            font-weight: 500;
                        ">

                        ${error.titulo}

                    </p>

                    <p
                        style="
                            font-size: 12px;
                            color: var(--text-muted);
                        ">

                        Por: ${error.consultor}

                    </p>

                </div>
            `;

        });

    } catch (error) {

        console.error(error);

    }

}

function getProyectoFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get("id_proyecto");
}