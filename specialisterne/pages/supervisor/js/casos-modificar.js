const btnCerrarCasoForm =
    document.getElementById("btnCerrarCasoForm");

const btnCancelarCaso =
    document.getElementById("btnCancelarCaso");

document.addEventListener("DOMContentLoaded", () => {

    btnCerrarCasoForm.addEventListener(
        "click",
        cerrarFormularioCaso
    );

    btnCancelarCaso.addEventListener(
        "click",
        cerrarFormularioCaso
    );

    btnEliminarCaso.addEventListener(
        "click",
        eliminarCaso
    );

});

document.addEventListener("click", async e => {

    const btnEditar =
        e.target.closest(".btnEditarCaso");

    if (!btnEditar) {
        return;
    }

    await abrirFormularioEditar(btnEditar);

});

async function cargarEstadosCaso() {

    const response = await fetch(
        `${supervisorBase}/php/casos/getEstadosCaso.php`
    );

    const data = await response.json();

    if (!data.success) {

        showToast(data.message, "error");
        return false;

    }

    caseStatus.innerHTML = "";

    data.estados.forEach(estado => {

        caseStatus.innerHTML += `
            <option value="${estado.id}">
                ${estado.estado}
            </option>
        `;

    });

    return true;

}

async function abrirFormularioEditar(button) {

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

        const estadosCargados =
            await cargarEstadosCaso();

        if (!estadosCargados) {
            return;
        }

        abrirModoEditar({

            id:
                button.dataset.id,

            fase:
                button.dataset.fase,

            titulo:
                button.dataset.titulo,

            descripcion:
                button.dataset.descripcion,

            instrucciones:
                button.dataset.instrucciones,

            resultado:
                button.dataset.resultado,

            estadoId:
                button.dataset.estadoId

        });

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar el caso",
            "error"
        );

    }

}

async function eliminarCaso() {

    try {

        const confirmar =
            confirm(
                "¿Eliminar este caso de prueba?"
            );

        if (!confirmar) {
            return;
        }

        const response = await fetch(
            `${supervisorBase}/php/casos/deleteCaso.php`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_caso: idCasoEditar
                })
            }
        );

        const data = await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        showToast(
            "Caso eliminado correctamente",
            "success"
        );

        cerrarFormularioCaso();

        await cargarCasosProyecto(
            getProyectoActivo()
        );

    } catch (error) {

        console.error(error);

        showToast(
            "Error al eliminar el caso",
            "error"
        );

    }

}