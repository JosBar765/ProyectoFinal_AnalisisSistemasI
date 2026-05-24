document.addEventListener("DOMContentLoaded", () => {

    obtenerTareas();

    obtenerFiltros();

    document
        .getElementById("filterProyecto")
        .addEventListener("change", filtrarTareas);

    document
        .getElementById("filterResultado")
        .addEventListener("change", filtrarTareas);

});

let tareas = [];

async function obtenerTareas() {

    try {

        const response = await fetch(
            `${consultorBase}/php/tareas/getTareas.php`
        );

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        tareas = data.tasks;

        renderizarTareas(tareas);

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar tareas",
            "error"
        );

    }

}

async function obtenerFiltros() {

    try {

        const response = await fetch(
            `${consultorBase}/php/tareas/getProyectos.php`
        );

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        renderizarFiltroProyectos(
            data.projects
        );

    } catch (error) {

        console.error(error);

    }

}

function renderizarFiltroProyectos(projects) {

    const select =
        document.getElementById(
            "filterProyecto"
        );

    projects.forEach(project => {

        select.innerHTML += `
            <option value="${project.id}">
                ${project.nombre}
            </option>
        `;

    });

}

function renderizarTareas(tasks) {

    const tbody =
        document.getElementById(
            "tasksTableBody"
        );

    tbody.innerHTML = "";

    if (tasks.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td
                    colspan="4"
                    class="text-center">

                    No hay tareas registradas.

                </td>
            </tr>
        `;

        return;
    }

    tasks.forEach(task => {

        const badgeClass =
            obtenerBadgeResultado(
                task.resultado
            );

        const icon =
            obtenerIconResultado(
                task.resultado
            );

        tbody.innerHTML += `
            <tr>

                <td>

                    <strong>
                        ${task.caso_prueba}
                    </strong>

                    <br>

                    <span
                        style="
                            font-size: 12px;
                            color: var(--text-muted);
                        ">

                        ${String(task.id_caso_prueba)}

                    </span>

                </td>

                <td>
                    ${task.proyecto}
                </td>

                <td>
                    ${task.fecha_ejecucion}
                </td>

                <td>

                    <span class="badge ${badgeClass}">

                        <i
                            data-lucide="${icon}"
                            size="12">
                        </i>

                        ${task.resultado}

                    </span>

                </td>

            </tr>
        `;

    });

    lucide.createIcons();

}

function filtrarTareas() {

    const proyecto =
        document.getElementById(
            "filterProyecto"
        ).value;

    const resultado =
        document.getElementById(
            "filterResultado"
        ).value;

    let filtradas = [...tareas];

    if (proyecto) {

        filtradas = filtradas.filter(
            task =>
                task.id_proyecto == proyecto
        );

    }

    if (resultado) {

        filtradas = filtradas.filter(
            task =>
                task.resultado === resultado
        );

    }

    renderizarTareas(filtradas);

}

function obtenerBadgeResultado(resultado) {

    switch (resultado) {

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

function obtenerIconResultado(resultado) {

    switch (resultado) {

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