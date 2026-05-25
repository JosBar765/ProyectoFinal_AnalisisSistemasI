document.addEventListener(
    "DOMContentLoaded",
    () => {

        inicializarFecha();
        inicializarEventos();

    }
);

function inicializarFecha() {

    const input =
        document.getElementById(
            "executionDate"
        );

    const fecha =
        new Date();

    input.value =
        fecha.toLocaleString(
            "es-GT",
            {
                dateStyle: "medium",
                timeStyle: "short"
            }
        );

}

function inicializarEventos() {

    const resultadoSelect =
        document.getElementById(
            "resultadoSelect"
        );

    const form =
        document.getElementById(
            "executionForm"
        );

    resultadoSelect.addEventListener(
        "change",
        manejarResultado
    );

    form.addEventListener(
        "submit",
        guardarEjecucion
    );

}

function manejarResultado() {

    const resultado =
        document.getElementById(
            "resultadoSelect"
        ).value;

    const errorContainer =
        document.getElementById(
            "errorReportBtn"
        );

    const reportLink =
        document.getElementById(
            "reportErrorLink"
        );

    const submitButtons =
        document.getElementById(
            "submitButtons"
        );

    if (resultado === "Fallido") {

        errorContainer.style.display =
            "block";

        submitButtons.style.display =
            "none";

        reportLink.href =
            `reportar.php?id=${CASE_ID}`;

    } else {

        errorContainer.style.display =
            "none";

        submitButtons.style.display =
            "flex";

    }

}

async function guardarEjecucion(event) {

    event.preventDefault();

    try {

        const resultado =
            document.getElementById(
                "resultadoSelect"
            ).value;

        const observaciones =
            document.getElementById(
                "observacionesInput"
            ).value.trim();

        if (!resultado) {

            showToast(
                "Seleccione un resultado",
                "warning"
            );

            return;
        }

        const response = await fetch(
            `${consultorBase}/php/ejecutar/createEjecucion.php`,
            {
                method: "POST",

                headers: {
                    "Content-Type":
                        "application/json"
                },

                body: JSON.stringify({
                    id_caso: CASE_ID,
                    resultado,
                    observaciones
                })
            }
        );

        const data =
            await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        showToast(
            data.message,
            "success"
        );

        setTimeout(() => {

            window.location.href =
                `casos.php?id=${PROJECT_ID}`;

        }, 1200);

    } catch (error) {

        console.error(error);

        showToast(
            error.message ||
            "Error al guardar ejecución",
            "error"
        );

    }

}