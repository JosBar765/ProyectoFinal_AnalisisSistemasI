const proyectoAsignacionesSelect =
    document.getElementById("proyectoAsignacionesSelect");

const consultoresDisponiblesContainer =
    document.getElementById("consultoresDisponiblesContainer");

const consultoresAsignadosContainer =
    document.getElementById("consultoresAsignadosContainer");

const contadorAsignados =
    document.getElementById("contadorAsignados");

const emptyAsignadosState =
    document.getElementById("emptyAsignadosState");


document.addEventListener("DOMContentLoaded", async () => {

    await cargarSelectProyectos(
        proyectoAsignacionesSelect,
        cargarAsignacionesProyecto
    );

});


// =========================
// CARGA GENERAL
// =========================
async function cargarAsignacionesProyecto(idProyecto) {

    await Promise.all([
        cargarConsultoresDisponibles(idProyecto),
        cargarConsultoresAsignados(idProyecto)
    ]);

}


// =========================
// DISPONIBLES
// =========================
async function cargarConsultoresDisponibles(idProyecto) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/asignaciones/getConsultoresDisponibles.php?id_proyecto=${idProyecto}`
        );

        const data = await response.json();

        if (!data.success) {
            showToast(data.message, "error");
            return;
        }

        renderConsultoresDisponibles(data.consultores);

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar consultores disponibles",
            "error"
        );
    }
}


function renderConsultoresDisponibles(consultores) {

    consultoresDisponiblesContainer.innerHTML = "";

    consultores.forEach(c => {

        consultoresDisponiblesContainer.innerHTML += `
            <div class="card" style="background: var(--bg-color); box-shadow: none; margin: 0;">

                <div class="d-flex justify-between align-center">

                    <div>
                        <strong>${c.nombre}</strong>

                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">
                            ${c.descripcion ?? ""}
                        </p>

                        <div style="margin-top: 8px;">
                            ${(c.skills || []).map(s => `
                                <span class="badge badge-neutral" style="font-size: 11px;">
                                    ${s}
                                </span>
                            `).join("")}
                        </div>
                    </div>

                    <button
                        class="btn btn-primary btnAsignar"
                        data-id="${c.id}">
                        Asignar <i data-lucide="arrow-right" size="16"></i>
                    </button>

                </div>

            </div>
        `;

    });

    lucide.createIcons();

    document.querySelectorAll(".btnAsignar").forEach(btn => {

        btn.addEventListener("click", async () => {

            const idConsultor = btn.dataset.id;

            // endpoint futuro
            const response = await fetch(
                `${supervisorBase}/php/asignaciones/asignarConsultor.php`,
                {
                    method: "POST",
                    body: (() => {
                        const fd = new FormData();
                        fd.append("id_consultor", idConsultor);
                        fd.append("id_proyecto", getProyectoActivo());
                        return fd;
                    })()
                }
            );

            const data = await response.json();

            if (!data.success) {
                showToast(data.message, "error");
                return;
            }

            showToast("Consultor agregado", "success");

            cargarAsignacionesProyecto(getProyectoActivo());

        });

    });

}


// =========================
// ASIGNADOS
// =========================
async function cargarConsultoresAsignados(idProyecto) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/asignaciones/getConsultoresAsignados.php?id_proyecto=${idProyecto}`
        );

        const data = await response.json();

        if (!data.success) {
            showToast(data.message, "error");
            return;
        }

        renderConsultoresAsignados(data.consultores);

    } catch (error) {

        console.error(error);

        showToast(
            "Error al cargar consultores asignados",
            "error"
        );
    }
}


function renderConsultoresAsignados(consultores) {

    consultoresAsignadosContainer.innerHTML = "";

    contadorAsignados.textContent =
        `${consultores.length} Asignados`;

    if (consultores.length === 0) {

        emptyAsignadosState.style.display = "block";
        return;
    }

    emptyAsignadosState.style.display = "none";

    consultores.forEach(c => {

        consultoresAsignadosContainer.innerHTML += `
            <div class="card" style="background: white; border: 1px solid var(--border-color); box-shadow: none; margin: 0;">

                <div class="d-flex justify-between align-center">

                    <div class="d-flex align-center gap-3">

                        <div class="avatar" style="background-color: var(--primary-color);">
                            ${c.nombre.charAt(0)}
                        </div>

                        <div>
                            <strong>${c.nombre}</strong>

                            <p style="font-size: 12px; color: var(--text-muted);">
                                Asignado: ${c.fecha_asignacion}
                            </p>

                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                ${c.descripcion ?? ""}
                            </p>
                        </div>

                    </div>

                    <button
                        class="btn btn-danger btnRemover"
                        data-id="${c.id}">
                        <i data-lucide="user-minus" size="16"></i>
                        Remover
                    </button>

                </div>

            </div>
        `;

    });

    lucide.createIcons();

    document.querySelectorAll(".btnRemover").forEach(btn => {

        btn.addEventListener("click", async () => {

            const idConsultor = btn.dataset.id;

            const response = await fetch(
                `${supervisorBase}/php/asignaciones/removerConsultor.php`,
                {
                    method: "POST",
                    body: (() => {
                        const fd = new FormData();
                        fd.append("id_consultor", idConsultor);
                        fd.append("id_proyecto", getProyectoActivo());
                        return fd;
                    })()
                }
            );

            const data = await response.json();

            if (!data.success) {
                showToast(data.message, "error");
                return;
            }

            showToast("Consultor eliminado del proyecto", "warning");

            cargarAsignacionesProyecto(getProyectoActivo());

        });

    });

}