<?php

include './conexion.php';

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Recuperar contraseña - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="estilos.css"
    >

</head>


<body>


<main class="login-container">


    <section class="login-card">


        <!-- =========================
             LOGO
        ========================== -->

        <div class="logo">

            <h1>
                 PetMigo
            </h1>

            <p>
                Recuperación de contraseña
            </p>

        </div>



        <!-- =========================
             MASCOTAS
        ========================== -->

        <div class="mascotas">

        </div>



        <!-- =========================
             TÍTULO
        ========================== -->

        <h2>
            ¿Olvidaste tu contraseña?
        </h2>


        <p>
            Ingresa tu correo electrónico para continuar.
        </p>



        <!-- =========================
             FORMULARIO
        ========================== -->

        <form
            action="guardar_nueva_contrasena.php"
            method="POST"
        >


            <!-- CORREO -->

            <div class="campo">


                <label for="correo">

                    Correo electrónico

                </label>


                <div class="input-box">


                    <span>
                    </span>


                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        placeholder="Ingresa tu correo"
                        required
                    >


                </div>


            </div>



            <!-- NUEVA CONTRASEÑA -->

            <div class="campo">


                <label for="contrasena">

                    Nueva contraseña

                </label>


                <div class="input-box">


                    <span>
                    </span>


                    <input
                        type="password"
                        id="contrasena"
                        name="contrasena"
                        placeholder="Ingresa una nueva contraseña"
                        minlength="6"
                        required
                    >


                </div>


            </div>



            <!-- CONFIRMAR CONTRASEÑA -->

            <div class="campo">


                <label for="confirmar_contrasena">

                    Confirmar contraseña

                </label>


                <div class="input-box">


                    <span>
                    </span>


                    <input
                        type="password"
                        id="confirmar_contrasena"
                        name="confirmar_contrasena"
                        placeholder="Repite la nueva contraseña"
                        minlength="6"
                        required
                    >


                </div>


            </div>



            <!-- BOTÓN -->

            <button
                type="submit"
                class="btn-login"
            >

                 Cambiar contraseña

            </button>


        </form>



        <!-- VOLVER -->

        <div class="registro">


            <p>

                <a href="login.php">

                    ← Volver al inicio de sesión

                </a>

            </p>


        </div>


    </section>


</main>


</body>

</html>