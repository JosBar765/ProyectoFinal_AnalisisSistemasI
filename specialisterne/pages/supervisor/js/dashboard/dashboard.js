const proyectoSelect = document.getElementById("proyectoSelect");

const nombreProyecto = document.getElementById("nombreProyecto");
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

    await cargarSelectProyectos(
        proyectoSelect,
        cargarDashboard
    );

});

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

        nombreProyecto.textContent =
            `${data.proyecto}`;

        let badgeClass = "badge-neutral";
        let progressBarColor = "primary-color";

        avanceGeneral.textContent =
            `Avance General: ${data.avance}%`;

        // Color del badge y general progress bar
        if (data.estado === "Finalizado") {
            badgeClass = "badge-success";
            progressBarColor = "success-color";
        }
        else if (data.estado === "Activo") {
            badgeClass = "badge-primary";
            progressBarColor = "primary-color";
        }
        else if (data.estado === "En Pausa") {
            badgeClass = "badge-warning";
            progressBarColor = "warning-color";
        }

        estadoProyecto.textContent =
            data.estado;

        estadoProyecto.className =
            `badge ${badgeClass}`;

        barraProgresoGeneral.style.backgroundColor =
            `var(--${progressBarColor})`

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
            let progressBarColor = "primary-color";

            // Color del badge y progress bar de cada fase
            if (fase.estado === "Completado") {
                badgeClass = "badge-success";
                progressBarColor = "success-color";
            }
            else if (fase.estado === "En Progreso") {
                badgeClass = "badge-primary";
                progressBarColor = "primary-color";
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
                                    style="
                                        width: ${fase.avance}%;
                                        background-color: var(--${progressBarColor});
                                    ">
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
                severidadColor = "var(--error-color)";
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