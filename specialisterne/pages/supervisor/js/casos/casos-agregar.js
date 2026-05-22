const btnNuevoCaso =
    document.getElementById("btnNuevoCaso");

document.addEventListener("DOMContentLoaded", () => {

    btnNuevoCaso.addEventListener(
        "click",
        abrirFormularioAgregar
    );

    formCaso.addEventListener(
        "submit",
        guardarCaso
    );

});

async function abrirFormularioAgregar() {

    try {

        const idProyecto =
            getProyectoActivo();

        const response = await fetch(
            `${supervisorBase}/php/proyectos/getFasesProyecto.php?id_proyecto=${idProyecto}`
        );

        const data = await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        casePhase.innerHTML = "";

        data.fases.forEach(fase => {

            casePhase.innerHTML += `
                <option value="${fase.id}">
                    ${fase.nombre}
                </option>
            `;

        });

        abrirModoCrear();

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar las fases",
            "error"
        );

    }

}

async function guardarCaso(e) {

    e.preventDefault();

    try {

        const body = {

            titulo:
                caseTitle.value,

            descripcion:
                caseDesc.value,

            instrucciones:
                caseSteps.value,

            resultado_esperado:
                caseExpected.value

        };

        let endpoint =
            "createCaso.php";

        if (modoEdicion) {

            endpoint =
                "updateCaso.php";

            body.id_caso =
                idCasoEditar;

            body.id_estado_caso_prueba =
                caseStatus.value;

        } else {

            body.id_fase_proyecto =
                casePhase.value;

        }

        const response = await fetch(
            `${supervisorBase}/php/casos/${endpoint}`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(body)
            }
        );

        const data = await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        showToast(
            modoEdicion
                ? "Caso actualizado correctamente"
                : "Caso creado correctamente",
            "success"
        );

        cerrarFormularioCaso();

        await cargarCasosProyecto(
            getProyectoActivo()
        );

    } catch (error) {

        console.error(error);

        showToast(
            "Error al guardar el caso",
            "error"
        );

    }

}