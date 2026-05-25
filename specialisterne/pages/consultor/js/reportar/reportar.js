document.addEventListener(
    "DOMContentLoaded",
    () => {

        inicializarEventos();
        lucide.createIcons();

    }
);

function inicializarEventos() {

    inicializarSeveridades();
    inicializarImagenes();

    const form =
        document.getElementById(
            "errorForm"
        );

    form.addEventListener(
        "submit",
        enviarReporte
    );

}

function inicializarSeveridades() {

    const radios =
        document.querySelectorAll(
            'input[name="severidad"]'
        );

    radios.forEach(radio => {

        radio.addEventListener(
            "change",
            () => {

                radios.forEach(r => {

                    r.parentElement.style.borderColor =
                        "var(--border-color)";

                    r.parentElement.style.backgroundColor =
                        "#fff";

                });

                if (radio.checked) {

                    radio.parentElement.style.borderColor =
                        "var(--primary-color)";

                    radio.parentElement.style.backgroundColor =
                        "rgba(74, 111, 165, 0.05)";

                }

            }
        );

    });

    const checked =
        document.querySelector(
            'input[name="severidad"]:checked'
        );

    if (checked) {
        checked.dispatchEvent(
            new Event("change")
        );
    }

}

function inicializarImagenes() {

    const input =
        document.getElementById(
            "imagenesInput"
        );

    input.addEventListener(
        "change",
        renderizarPreviews
    );

}

function renderizarPreviews(event) {

    const files =
        Array.from(event.target.files);

    const container =
        document.getElementById(
            "previewContainer"
        );

    container.innerHTML = "";

    files.forEach(file => {

        const reader =
            new FileReader();

        reader.onload = e => {

            container.innerHTML += `
                <img
                    src="${e.target.result}"
                    style="
                        width: 100px;
                        height: 100px;
                        object-fit: cover;
                        border-radius: 8px;
                        border: 1px solid var(--border-color);
                    ">
            `;

        };

        reader.readAsDataURL(file);

    });

}

async function enviarReporte(event) {

    event.preventDefault();

    try {

        const titulo =
            document.getElementById(
                "tituloInput"
            ).value.trim();

        const descripcion =
            document.getElementById(
                "descripcionInput"
            ).value.trim();

        const severidad =
            document.querySelector(
                'input[name="severidad"]:checked'
            )?.value;

        const imagenes =
            document.getElementById(
                "imagenesInput"
            ).files;

        if (
            !titulo
            || !descripcion
            || !severidad
        ) {

            showToast(
                "Complete todos los campos",
                "warning"
            );

            return;
        }

        const formData =
            new FormData();

        formData.append(
            "id_caso",
            CASE_ID
        );

        formData.append(
            "titulo",
            titulo
        );

        formData.append(
            "descripcion",
            descripcion
        );

        formData.append(
            "id_severidad",
            severidad
        );

        Array.from(imagenes)
            .forEach(imagen => {

                formData.append(
                    "imagenes[]",
                    imagen
                );

            });

        const response = await fetch(
            `${consultorBase}/php/reportar/createReporte.php`,
            {
                method: "POST",
                body: formData
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

        }, 1000);

    } catch (error) {

        console.error(error);

        showToast(
            error.message ||
            "Error al registrar reporte",
            "error"
        );

    }

}