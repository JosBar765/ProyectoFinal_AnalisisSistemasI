// Si se sube a un host, darle el valor ""
let base = "/specialisterne";

async function handleLogin(event) {
    event.preventDefault();

    const usuario = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const errorDiv = document.getElementById("loginError");

    errorDiv.style.display = "none";

    try {
        const response = await fetch("php/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                usuario,
                password
            })
        });

        const data = await response.json();

        if (data.success) {
            console.log("Login exitoso");

            if (data.user.id_rol === 1)
                base += "/pages/admin";
            else if (data.user.id_rol === 2)
                base += "/pages/supervisor";
            else if (data.user.id_rol === 3)
                base += "/pages/consultor";

            window.location.href = `${base}/index.php`;

        } else {
            errorDiv.textContent = data.message;
            errorDiv.style.display = "block";
        }

    } catch (error) {
        console.error(error);

        errorDiv.textContent = "Error del servidor";
        errorDiv.style.display = "block";
    }
}