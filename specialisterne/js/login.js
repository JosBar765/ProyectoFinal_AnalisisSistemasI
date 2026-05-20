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
            console.log("Login exitoso", data.user);

            // Redirección
            window.location.href = "403.php";
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