document.addEventListener("DOMContentLoaded", function () {

    const formulario = document.querySelector("form");

    const correo = document.getElementById("correo");

    const contrasena = document.getElementById("contrasena");


    formulario.addEventListener("submit", function (evento) {

        // Verificar que el correo no esté vacío

        if (correo.value.trim() === "") {

            evento.preventDefault();

            alert("Por favor, ingresa tu correo electrónico.");

            correo.focus();

            return;
        }


        // Verificar que la contraseña no esté vacía

        if (contrasena.value.trim() === "") {

            evento.preventDefault();

            alert("Por favor, ingresa tu contraseña.");

            contrasena.focus();

            return;
        }


        // Verificar formato del correo

        const formatoCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


        if (!formatoCorreo.test(correo.value)) {

            evento.preventDefault();

            alert("Ingresa un correo electrónico válido.");

            correo.focus();

            return;
        }

    });

});