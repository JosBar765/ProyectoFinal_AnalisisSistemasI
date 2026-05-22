const proyectosContainer = document.getElementById("proyectosContainer");

document.addEventListener("DOMContentLoaded", async () => {
    await cargarProyectos();
});

async function cargarProyectos() {

    try {

        const response = await fetch(`${supervisorBase}/php/proyectos/getProyectosDetalle.php`);

        const data = await response.json();

        proyectosContainer.innerHTML = "";

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        if (data.proyectos.length === 0) {

            proyectosContainer.innerHTML = `
                <div class="card">

                    <div
                        style="
                            text-align: center;
                            padding: 40px 20px;
                        ">

                        <i
                            data-lucide="folder-x"
                            style="
                                width: 50px;
                                height: 50px;
                                color: var(--text-muted);
                                margin-bottom: 15px;
                            ">
                        </i>

                        <h2
                            style="
                                margin-bottom: 10px;
                            ">

                            No hay proyectos registrados

                        </h2>

                        <p
                            style="
                                color: var(--text-muted);
                                margin-bottom: 20px;
                            ">

                            Crea tu primer proyecto para comenzar.

                        </p>

                        <a
                            href="proyecto-form.php"
                            class="btn btn-primary">

                            <i data-lucide="plus"></i>
                            Nuevo Proyecto

                        </a>

                    </div>

                </div>
            `;

            lucide.createIcons();
            return;

        }

        data.proyectos.forEach(proyecto => {

            let badgeClass = "badge-neutral";
            let progressColor = "";

            switch (proyecto.estado) {

                case "Activo":
                    badgeClass = "badge-primary";
                    progressColor = "background-color: var(--primary-color);";
                    break;

                case "En Pausa":
                    badgeClass = "badge-warning";
                    progressColor = "background-color: var(--warning-color);";
                    break;

                case "Finalizado":
                    badgeClass = "badge-success";
                    progressColor = "background-color: var(--success-color);";
                    break;

                case "Cancelado":
                    badgeClass = "badge-error";
                    progressColor = "background-color: var(--error-color);";
                    break;

            }

            proyectosContainer.innerHTML += `
                <div
                    class="card"
                    style="
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                    ">

                    <div>

                        <div
                            class="d-flex justify-between align-center mb-3">

                            <h2
                                class="card-title"
                                style="margin: 0;">

                                ${proyecto.nombre}

                            </h2>

                            <span class="badge ${badgeClass}">

                                ${proyecto.estado}

                            </span>

                        </div>

                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 13px;
                                margin-bottom: 15px;
                                display: -webkit-box;
                                -webkit-line-clamp: 2;
                                -webkit-box-orient: vertical;
                                overflow: hidden;
                            ">

                            ${proyecto.descripcion}

                        </p>

                        <div
                            class="d-flex gap-3 mb-4"
                            style="
                                font-size: 12px;
                                color: var(--text-muted);
                                flex-wrap: wrap;
                            ">

                            <span
                                class="d-flex align-center gap-2">

                                <i
                                    data-lucide="calendar"
                                    size="14">
                                </i>

                                Inicio:
                                ${proyecto.fecha_inicio}

                            </span>

                            <span
                                class="d-flex align-center gap-2">

                                <i
                                    data-lucide="calendar"
                                    size="14">
                                </i>

                                Fin:
                                ${proyecto.fecha_fin}

                            </span>

                        </div>

                        <div class="progress-container mb-1">

                            <div
                                class="progress-bar"
                                style="
                                    width: ${proyecto.avance}%;
                                    ${progressColor}
                                ">
                            </div>

                        </div>

                        <span
                            style="
                                font-size: 12px;
                                color: var(--text-muted);
                            ">

                            Avance:
                            ${proyecto.avance}%

                        </span>

                    </div>

                    <div class="text-center"
                        style = "
                            display: flex;
                            flex-direction: row;
                            gap: 10px;
                        "
                    >

                        <a href="index.php" class="btn btn-secondary" style="width: 100%;"
                            onclick="
                                localStorage.setItem(
                                    'proyectoActivo',
                                    ${proyecto.id}
                                );
                            "
                        >
                            Ver Detalles
                        </a>

                        <button
                            class="btn btn-danger btnEliminarProyecto"

                            data-id="${proyecto.id}"
                            data-nombre="${proyecto.nombre}"

                            style="
                                padding: 0 14px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            "
                        >
                            <i data-lucide="trash-2" size="16"></i>
                        </button>
                    </div>

                </div>
            `;

        });

        lucide.createIcons();

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar los proyectos",
            "error"
        );

    }

}

document.addEventListener("click", async e => {

    const btnEliminar =
        e.target.closest(".btnEliminarProyecto");

    if (!btnEliminar) {
        return;
    }

    const idProyecto =
        btnEliminar.dataset.id;

    const nombreProyecto =
        btnEliminar.dataset.nombre;

    const confirmar =
        confirm(
            `¿Eliminar el proyecto "${nombreProyecto}"?`
        );

    if (!confirmar) {
        return;
    }

    try {

        const response = await fetch(`${supervisorBase}/php/proyectos/deleteProyecto.php`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_proyecto: idProyecto
                })
            }
        );

        const data = await response.json();

        if (!data.success) {

            showToast(data.message, "error");
            return;

        }

        showToast(
            "Proyecto eliminado correctamente",
            "success"
        );

        await cargarProyectos();

    } catch (error) {

        console.error(error);

        showToast(
            "Error al eliminar el proyecto",
            "error"
        );

    }

});