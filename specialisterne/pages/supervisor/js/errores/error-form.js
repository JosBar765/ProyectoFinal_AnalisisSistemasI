const ejecucionPrueba =
    document.getElementById("ejecucionPrueba");

const casoPrueba =
    document.getElementById("casoPrueba");

const severidadError =
    document.getElementById("severidadError");

const estadoError =
    document.getElementById("estadoError");

const errorForm =
    document.getElementById("errorForm");

const capturaError =
    document.getElementById("capturaError");

let isSubmitting = false;


// =========================
// INIT
// =========================
document.addEventListener(
    "DOMContentLoaded",
    async () => {

        await Promise.all([
            cargarEjecuciones(),
            cargarSeveridades()
        ]);

        ejecucionPrueba.addEventListener(
            "change",
            cargarCasoPrueba
        );

    }
);


// =========================
// EJECUCIONES
// =========================
async function cargarEjecuciones() {

    try {

        const response = await fetch(
            `${supervisorBase}/php/errores/getEjecucionesPrueba.php?id_proyecto=${getProyectoActivo()}`
        );

        const data = await response.json();

        if (!data.success) {

            showToast(
                data.message,
                "error"
            );

            return;

        }

        ejecucionPrueba.innerHTML = `
            <option value="">
                Selecciona una ejecución
            </option>
        `;

        data.ejecuciones.forEach(e => {

            ejecucionPrueba.innerHTML += `
                <option value="${e.id}">
                    ${e.caso_prueba} - ${e.consultor}
                </option>
            `;

        });

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar ejecuciones",
            "error"
        );

    }

}


// =========================
// CASO PRUEBA
// =========================
async function cargarCasoPrueba() {

    try {

        const idEjecucion =
            ejecucionPrueba.value;

        if (!idEjecucion) {

            casoPrueba.innerHTML = "";
            return;

        }

        const response = await fetch(
            `${supervisorBase}/php/errores/getCasoPrueba.php?id_ejecucion=${idEjecucion}`
        );

        const data = await response.json();

        if (!data.success) {

            showToast(
                data.message,
                "error"
            );

            return;

        }

        casoPrueba.innerHTML = `
            <option>
                ${data.caso_prueba}
            </option>
        `;

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar caso",
            "error"
        );

    }

}


// =========================
// SEVERIDADES
// =========================
async function cargarSeveridades() {

    try {

        const response = await fetch(
            `${supervisorBase}/php/errores/getSeveridades.php`
        );

        const data = await response.json();

        if (!data.success) {

            showToast(
                data.message,
                "error"
            );

            return;

        }

        severidadError.innerHTML = "";

        data.severidades.forEach(s => {

            severidadError.innerHTML += `
                <option value="${s.id}">
                    ${s.severidad_error}
                </option>
            `;

        });

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar severidades",
            "error"
        );

    }

}


// =========================
// VALIDAR IMÁGENES
// =========================
function validarImagenes(files) {

    const formatosValidos = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    for (const file of files) {

        if (
            !formatosValidos.includes(file.type)
        ) {

            showToast(
                `Formato inválido: ${file.name}`,
                "error"
            );

            return false;

        }

        const sizeMB =
            file.size / (1024 * 1024);

        if (sizeMB > 1) {

            showToast(
                `${file.name} supera 1MB`,
                "error"
            );

            return false;

        }

    }

    return true;

}


// =========================
// BLOQUEAR FORM
// =========================
function toggleFormState(disabled) {

    const elements =
        errorForm.querySelectorAll(
            "input, textarea, select, button"
        );

    elements.forEach(element => {

        element.disabled = disabled;

    });

}


// =========================
// SUBMIT
// =========================
errorForm.addEventListener(
    "submit",
    async (e) => {

        e.preventDefault();

        // IDEMPOTENCIA
        if (isSubmitting) {
            return;
        }

        const files =
            capturaError.files;

        if (
            files.length > 0 &&
            !validarImagenes(files)
        ) {
            return;
        }

        try {

            isSubmitting = true;

            toggleFormState(true);

            const submitButton =
                errorForm.querySelector(
                    'button[type="submit"]'
                );

            submitButton.innerHTML = `
                Guardando...
            `;

            const formData =
                new FormData();

            formData.append(
                "id_ejecucion",
                ejecucionPrueba.value
            );

            formData.append(
                "titulo",
                document
                    .getElementById("tituloError")
                    .value
                    .trim()
            );

            formData.append(
                "descripcion",
                document
                    .getElementById("descripcionError")
                    .value
                    .trim()
            );

            formData.append(
                "id_severidad",
                severidadError.value
            );

            formData.append(
                "estado",
                estadoError.value === "Abierto"
                    ? 1
                    : 0
            );

            for (const file of files) {

                formData.append(
                    "imagenes[]",
                    file
                );

            }

            const response = await fetch(
                `${supervisorBase}/php/errores/createError.php`,
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

                isSubmitting = false;

                toggleFormState(false);

                submitButton.innerHTML =
                    "Guardar Error";

                return;

            }

            showToast(
                "Error registrado correctamente",
                "success"
            );

            setTimeout(() => {

                window.location.href =
                    "errores.php";

            }, 1000);

        } catch (error) {

            console.error(error);

            showToast(
                "Error al registrar",
                "error"
            );

            isSubmitting = false;

            toggleFormState(false);

            const submitButton =
                errorForm.querySelector(
                    'button[type="submit"]'
                );

            submitButton.innerHTML =
                "Guardar Error";

        }

    }
);