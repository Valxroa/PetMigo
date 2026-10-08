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
        Iniciar sesión - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="estilos.css"
    >

</head>


<script src="scrip.js"></script>


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
                    Tu compañero para el cuidado de tus mascotas.
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
                Bienvenido de nuevo
            </h2>


            <p>
                Inicia sesión para continuar
            </p>



            <!-- =========================
                 FORMULARIO
            ========================== -->

            <form
                action="validar_login.php"
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



                <!-- CONTRASEÑA -->

                <div class="campo">


                    <label for="contrasena">
                        Contraseña
                    </label>


                    <div class="input-box">


                        <span>
                        </span>


                        <input
                            type="password"
                            id="contrasena"
                            name="contrasena"
                            placeholder="Ingresa tu contraseña"
                            required
                        >


                    </div>


                </div>



                <!-- =========================
                     RECORDAR / RECUPERAR
                ========================== -->

                <div class="recordar">


                    <label>

                        <input
                            type="checkbox"
                            name="recordar"
                        >

                        Recordarme

                    </label>


                    <a href="recuperar_contrasena.php">

                        ¿Olvidaste tu contraseña?

                    </a>


                </div>



                <!-- =========================
                     BOTÓN
                ========================== -->

                <button
                    type="submit"
                    class="btn-login"
                >

                    Iniciar sesión

                </button>


            </form>



            <!-- =========================
                 REGISTRO
            ========================== -->

            <div class="registro">


                <p>

                    ¿No tienes una cuenta?


                    <a href="registro.php">

                        Regístrate aquí

                    </a>


                </p>


            </div>


        </section>


    </main>


</body>

</html>