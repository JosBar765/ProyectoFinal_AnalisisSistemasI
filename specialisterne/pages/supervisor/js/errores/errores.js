const proyectoErroresSelect =
    document.getElementById("proyectoErroresSelect");

const erroresTableBody =
    document.getElementById("erroresTableBody");

const filtroSeveridad =
    document.getElementById("filtroSeveridad");

const filtroEstado =
    document.getElementById("filtroEstado");

const errorDetail =
    document.getElementById("errorDetail");


// =========================
// INIT
// =========================
document.addEventListener(
    "DOMContentLoaded",
    async () => {

        await cargarSelectProyectos(
            proyectoErroresSelect,
            cargarErrores
        );

        filtroSeveridad.addEventListener(
            "change",
            () => cargarErrores(getProyectoActivo())
        );

        filtroEstado.addEventListener(
            "change",
            () => cargarErrores(getProyectoActivo())
        );

        document
            .getElementById("btnCerrarDetalle")
            .addEventListener("click", () => {

                errorDetail.style.display = "none";

            });

    }
);


// =========================
// CARGAR ERRORES
// =========================
async function cargarErrores(idProyecto) {

    try {

        const severidad =
            filtroSeveridad.value;

        const estado =
            filtroEstado.value;

        const response = await fetch(
            `${supervisorBase}/php/errores/getErrores.php?id_proyecto=${idProyecto}&severidad=${severidad}&estado=${estado}`
        );

        const data = await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        renderErrores(data.errores);

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar errores",
            "error"
        );

    }

}


// =========================
// RENDER TABLA
// =========================
function renderErrores(errores) {

    erroresTableBody.innerHTML = "";

    if (errores.length === 0) {

        erroresTableBody.innerHTML = `
            <tr>
                <td colspan="8">

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
                            No hay errores
                        </h2>

                    </div>

                </td>
            </tr>
        `;

        lucide.createIcons();

        return;

    }

    errores.forEach(error => {

        erroresTableBody.innerHTML += `
            <tr>

                <td>${error.id}</td>

                <td>
                    <strong>
                        ${error.titulo}
                    </strong>
                </td>

                <td>
                    ${getBadgeSeveridad(error.severidad)}
                </td>

                <td>
                    ${getBadgeEstado(error.estado)}
                </td>

                <td>
                    ${error.caso_prueba}
                </td>

                <td>
                    ${error.consultor}
                </td>

                <td>
                    ${error.fecha}
                </td>

                <td>

                    <div
                        style="
                            display: flex;
                            gap: 8px;
                        ">

                        <button
                            class="btn btn-secondary btnVerDetalle"
                            style="padding: 4px 8px;"
                            data-id="${error.id}">

                            <i data-lucide="eye" size="14"></i>

                        </button>

                        <button
                            class="btn btn-primary btnCambiarEstado"
                            style="padding: 4px 8px;"
                            data-id="${error.id}"
                            data-estado="${error.estado}">

                            <i data-lucide="refresh-cw" size="14"></i>

                        </button>

                    </div>

                </td>

            </tr>
        `;

    });

    lucide.createIcons();

    document
        .querySelectorAll(".btnVerDetalle")
        .forEach(btn => {

            btn.addEventListener(
                "click",
                async () => {

                    const idError =
                        btn.dataset.id;

                    mostrarDetalleError(idError);

                }
            );

        });


    // CAMBIAR ESTADO
    document
        .querySelectorAll(".btnCambiarEstado")
        .forEach(btn => {

            btn.addEventListener(
                "click",
                async () => {

                    const idError =
                        btn.dataset.id;

                    const estadoActual =
                        btn.dataset.estado;

                    try {

                        const response = await fetch(
                            `${supervisorBase}/php/errores/cambiarEstadoError.php`,
                            {
                                method: "POST",
                                body: (() => {

                                    const fd =
                                        new FormData();

                                    fd.append(
                                        "id_error",
                                        idError
                                    );

                                    fd.append(
                                        "estado",
                                        estadoActual == 1 ? 0 : 1
                                    );

                                    return fd;

                                })()
                            }
                        );

                        const data =
                            await response.json();

                        if (!data.success) {

                            showToast(
                                data.message,
                                "error"
                            );

                            return;

                        }

                        showToast(
                            "Estado actualizado",
                            "success"
                        );

                        cargarErrores(
                            getProyectoActivo()
                        );

                    } catch (error) {

                        console.error(error);

                        showToast(
                            "Error al actualizar estado",
                            "error"
                        );

                    }

                }
            );

        });

}


// =========================
// DETALLE
// =========================
async function mostrarDetalleError(idError) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/errores/getDetalleError.php?id_error=${idError}`
        );

        const data = await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        const error =
            data.error;

        document.getElementById(
            "detailCodigoError"
        ).textContent =
            `Detalle del Error: ${error.id}`;

        document.getElementById(
            "detailTitulo"
        ).textContent =
            error.titulo;

        document.getElementById(
            "detailDescripcion"
        ).textContent =
            error.descripcion;

        document.getElementById(
            "detailConsultor"
        ).textContent =
            `${error.consultor} (${error.fecha})`;

        document.getElementById(
            "detailCaso"
        ).textContent =
            error.caso_prueba;

        const badge =
            document.getElementById(
                "detailBadgeSeveridad"
            );

        badge.innerHTML =
            error.severidad;

        applyBadgeSeveridad(
            badge,
            error.severidad
        );

        const detailImagenes =
            document.getElementById(
                "detailImagenes"
            );

        detailImagenes.innerHTML = "";

        error.imagenes.forEach(img => {

            detailImagenes.innerHTML += `
        <img
            src="${img}"
            onclick="window.open('${img}', '_blank')"
            style="
                width: 150px;
                height: 100px;
                object-fit: cover;
                border-radius: 6px;
                border: 1px solid var(--border-color);
                cursor: pointer;
                transition: 0.2s;
            "
            onmouseover="this.style.opacity='0.8'"
            onmouseout="this.style.opacity='1'">
    `;

        });

        errorDetail.style.display =
            "block";

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar detalle",
            "error"
        );

    }

}


// =========================
// BADGES
// =========================
function getBadgeSeveridad(severidad) {

    const severidadUpper =
        severidad.toUpperCase();

    if (severidadUpper === "CRITICO") {

        return `
            <span class="badge badge-error"> CRÍTICO</span>
        `;

    }

    if (severidadUpper === "ALTO") {

        return `
            <span
                class="badge badge-warning">

                ALTO

            </span>
        `;

    }

    if (severidadUpper === "MEDIO") {

        return `
            <span
                class="badge badge-primary">

                MEDIO

            </span>
        `;

    }

    return `
        <span class="badge badge-neutral">
            BAJO
        </span>
    `;
}


function applyBadgeSeveridad(
    badge,
    severidad
) {

    badge.className = "badge";

    const severidadUpper =
        severidad.toUpperCase();

    if (severidadUpper === "CRITICO") {

        badge.classList.add("badge-error");

        badge.style.backgroundColor =
            "#D64045";

        badge.style.color =
            "white";

        return;

    }

    if (severidadUpper === "ALTO") {

        badge.classList.add("badge-warning");

        badge.style.backgroundColor =
            "var(--warning-color)";

        badge.style.color =
            "white";

        return;

    }

    if (severidadUpper === "MEDIO") {

        badge.classList.add("badge-primary");

        return;

    }

    badge.classList.add("badge-neutral");

}

function getBadgeEstado(estado) {

    if (estado == 1) {

        return `
            <span class="badge badge-error">
                ABIERTO
            </span>
        `;

    }

    return `
        <span class="badge badge-success">
            RESUELTO
        </span>
    `;

}