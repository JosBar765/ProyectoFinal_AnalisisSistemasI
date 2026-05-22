const proyectoSelect =
    document.getElementById("proyectoSelect");

const manualesContainer =
    document.getElementById("manualesContainer");

const manualForm =
    document.getElementById("manualForm");

const formManual =
    document.getElementById("formManual");

const btnAbrirManualForm =
    document.getElementById("btnAbrirManualForm");

const btnCerrarManualForm =
    document.getElementById("btnCerrarManualForm");

const btnCancelarManual =
    document.getElementById("btnCancelarManual");

const manualTitle =
    document.getElementById("manualTitle");

const manualDescription =
    document.getElementById("manualDescription");

const manualFile =
    document.getElementById("manualFile");

document.addEventListener("DOMContentLoaded", async () => {

    btnAbrirManualForm.addEventListener(
        "click",
        abrirFormularioManual
    );

    btnCerrarManualForm.addEventListener(
        "click",
        cerrarFormularioManual
    );

    btnCancelarManual.addEventListener(
        "click",
        cerrarFormularioManual
    );

    formManual.addEventListener(
        "submit",
        guardarManual
    );

    await cargarSelectProyectos(
        proyectoSelect,
        cargarManualesProyecto
    );

});

function abrirFormularioManual() {

    manualForm.style.display =
        "block";

}

function cerrarFormularioManual() {

    manualForm.style.display =
        "none";

    formManual.reset();
    // Nos aseguramos de limpiar el estado de envío al cerrar
    formManual.removeAttribute("data-submitting");

}

async function guardarManual(e) {

    e.preventDefault();

    // Si ya tiene el atributo, cortamos la ejecución de inmediato
    if (formManual.getAttribute("data-submitting") === "true") {
        return;
    }

    // Bloqueo inmediato en la primera línea de ejecución del evento
    formManual.setAttribute("data-submitting", "true");

    const btnSubmit = formManual.querySelector('button[type="submit"]');
    const originalText = btnSubmit ? btnSubmit.innerHTML : 'Guardar';

    try {

        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.style.pointerEvents = 'none'; // Desactiva interacciones del mouse a nivel de navegador
            btnSubmit.innerHTML = 'Subiendo...';
        }

        const archivo =
            manualFile.files[0];

        if (!archivo) {

            showToast(
                "Selecciona un PDF",
                "error"
            );
            return;
        }

        if (archivo.type !== "application/pdf") {

            showToast(
                "Solo se permiten PDFs",
                "error"
            );
            return;
        }

        const limite =
            25 * 1024 * 1024;

        if (archivo.size > limite) {

            showToast(
                "El PDF supera los 25 MB",
                "error"
            );
            return;
        }

        const formData =
            new FormData();

        formData.append(
            "id_proyecto",
            getProyectoActivo()
        );

        formData.append(
            "titulo",
            manualTitle.value
        );

        formData.append(
            "descripcion",
            manualDescription.value
        );

        formData.append(
            "archivo",
            archivo
        );

        const response = await fetch(
            `${supervisorBase}/php/manuales/createManual.php`,
            {
                method: "POST",
                body: formData
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
            "Manual subido correctamente",
            "success"
        );

        cerrarFormularioManual();

        await cargarManualesProyecto(
            getProyectoActivo()
        );

    } catch (error) {

        console.error(error);

        showToast(
            "Error al subir el manual",
            "error"
        );

    } finally {

        // IMPORTANTE: Liberamos el formulario SIEMPRE al terminar el proceso completo
        formManual.removeAttribute("data-submitting");

        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.style.pointerEvents = 'auto';
            btnSubmit.innerHTML = originalText;
        }

    }

}

async function cargarManualesProyecto(idProyecto) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/manuales/getManualesProyecto.php?id_proyecto=${idProyecto}`
        );

        const data =
            await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        renderizarManuales(
            data.manuales
        );

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar los manuales",
            "error"
        );

    }

}

function renderizarManuales(manuales) {

    if (manuales.length === 0) {

        manualesContainer.innerHTML = `
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
                        No hay manuales
                    </h2>

                    <p style="color: var(--text-muted);">
                        Este proyecto aún no tiene manuales cargados.
                    </p>

                </div>

            </div>
        `;

        lucide.createIcons();
        return;

    }

    let rows = "";

    manuales.forEach(manual => {

        rows += `
            <tr>

                <td>

                    <strong>
                        ${manual.titulo}
                    </strong>

                </td>

                <td>
                    ${manual.descripcion ?? ""}
                </td>

                <td>

                    <a
                        href="${manual.url}"
                        target="_blank"
                        style="color: var(--primary-color);">

                        <i
                            data-lucide="external-link"
                            size="16">
                        </i>

                        Ver PDF

                    </a>

                </td>

                <td>
                    ${manual.fecha_subida}
                </td>

                <td>

                    <button
                        class="btn btn-danger btnEliminarManual"
                        style="padding: 4px 8px;"
                        data-id="${manual.id}">

                        <i
                            data-lucide="trash-2"
                            size="14">
                        </i>

                    </button>

                </td>

            </tr>
        `;

    });

    manualesContainer.innerHTML = `
        <table>

            <thead>

                <tr>

                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Enlace</th>
                    <th>Fecha</th>
                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

                ${rows}

            </tbody>

        </table>
    `;

    lucide.createIcons();

    document.querySelectorAll(".btnEliminarManual")
        .forEach(button => {

            button.addEventListener(
                "click",
                async () => {

                    const idManual =
                        button.dataset.id;

                    const confirmar =
                        confirm(
                            "¿Eliminar este manual?"
                        );

                    if (!confirmar) {
                        return;
                    }

                    try {

                        const formData =
                            new FormData();

                        formData.append(
                            "id_manual",
                            idManual
                        );

                        const response =
                            await fetch(
                                `${supervisorBase}/php/manuales/deleteManual.php`,
                                {
                                    method: "POST",
                                    body: formData
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
                            "Manual eliminado",
                            "success"
                        );

                        await cargarManualesProyecto(
                            getProyectoActivo()
                        );

                    } catch (error) {

                        console.error(error);

                        showToast(
                            "Error al eliminar el manual",
                            "error"
                        );

                    }

                }
            );

        });

}