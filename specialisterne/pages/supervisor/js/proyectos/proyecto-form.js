const formProyecto = document.getElementById("proyectoForm");
const fasesContainer = document.getElementById("fasesContainer");
const btnAgregarFase = document.getElementById("btnAgregarFase");

let contadorFases = 1;

document.addEventListener("DOMContentLoaded", () => {

    btnAgregarFase.addEventListener(
        "click",
        agregarFase
    );

    formProyecto.addEventListener(
        "submit",
        guardarProyecto
    );

});

function agregarFase() {

    contadorFases++;

    const faseDiv = document.createElement("div");

    faseDiv.className = "card";

    faseDiv.style.backgroundColor = "var(--bg-color)";
    faseDiv.style.marginBottom = "0";

    faseDiv.innerHTML = `
        <div
            class="d-flex justify-between align-center mb-2">

            <strong>
                Fase ${contadorFases}
            </strong>

            <button
                type="button"
                class="btn btn-danger btnEliminarFase"
                style="padding: 4px 8px;">

                <i
                    data-lucide="trash-2"
                    size="14">
                </i>

            </button>

        </div>

        <div class="form-group">

            <input
                type="text"
                class="form-control mb-2 inputNombreFase"
                placeholder="Nombre de la fase"
                required>

            <input
                type="text"
                class="form-control inputDescripcionFase"
                placeholder="Descripción breve"
                required>

        </div>
    `;

    fasesContainer.appendChild(faseDiv);

    const btnEliminar =
        faseDiv.querySelector(".btnEliminarFase");

    btnEliminar.addEventListener(
        "click",
        () => {

            faseDiv.remove();
            renumerarFases();

        }
    );

    lucide.createIcons({
        root: faseDiv
    });

}

function renumerarFases() {

    const fases =
        fasesContainer.querySelectorAll(".card");

    contadorFases = 0;

    fases.forEach((fase, index) => {

        contadorFases++;

        fase.querySelector("strong").textContent =
            `Fase ${index + 1}`;

    });

}

async function guardarProyecto(event) {

    try {

        event.preventDefault();

        const nombre =
            document.getElementById("nombreProyecto").value.trim();

        const descripcion =
            document.getElementById("descripcionProyecto").value.trim();

        const fechaInicio =
            document.getElementById("fechaInicio").value;

        const fechaFin =
            document.getElementById("fechaFin").value;

        const estado =
            document.getElementById("estadoProyecto").value;

        const fasesDOM =
            fasesContainer.querySelectorAll(".card");

        const fases = [];

        fasesDOM.forEach(fase => {

            const nombreFase =
                fase.querySelector(".inputNombreFase")
                    .value
                    .trim();

            const descripcionFase =
                fase.querySelector(".inputDescripcionFase")
                    .value
                    .trim();

            fases.push({
                nombre: nombreFase,
                descripcion: descripcionFase
            });

        });

        const body = {

            nombre,
            descripcion,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            estado,
            fases

        };

        const response = await fetch(`${supervisorBase}/php/proyectos/createProyecto.php`,
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
            "Proyecto creado correctamente"
        );

        setTimeout(() => {

            window.location.href =
                "proyectos.php";

        }, 1500);

    } catch (error) {

        console.error(error);

        showToast(
            "Error al guardar el proyecto",
            "error"
        );

    }

}