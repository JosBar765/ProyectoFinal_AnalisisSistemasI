const supervisorBase = `${base}/pages/supervisor`;

function getProyectoActivo() {

    return localStorage.getItem("proyectoActivo");

}

function setProyectoActivo(idProyecto) {

    localStorage.setItem(
        "proyectoActivo",
        idProyecto
    );

}

async function cargarSelectProyectos(
    selectElement,
    onChangeCallback = null
) {

    try {

        const response = await fetch(
            `${supervisorBase}/php/proyectos/getProyectos.php`
        );

        const proyectos = await response.json();

        selectElement.innerHTML = "";

        proyectos.forEach(proyecto => {

            selectElement.innerHTML += `
                <option value="${proyecto.id}">
                    ${proyecto.nombre}
                </option>
            `;

        });

        let proyectoActivo =
            getProyectoActivo();

        // si no hay uno guardado
        if (!proyectoActivo && proyectos.length > 0) {

            proyectoActivo =
                proyectos[0].id;

            setProyectoActivo(proyectoActivo);

        }

        selectElement.value =
            proyectoActivo;

        selectElement.addEventListener(
            "change",
            async () => {

                const nuevoId =
                    selectElement.value;

                setProyectoActivo(nuevoId);

                if (onChangeCallback) {

                    await onChangeCallback(nuevoId);

                }

            }
        );

        // carga inicial
        if (onChangeCallback) {

            await onChangeCallback(
                proyectoActivo
            );

        }

    } catch (error) {

        console.error(error);

    }

}