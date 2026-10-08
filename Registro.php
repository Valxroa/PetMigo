<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registro - PetMigo</title>

    <link rel="stylesheet" href="estilos.css">
</head>

<body>

    <div class="login-container">

        <div class="login-card">

            <div class="logo">
                <h1>PetMigo </h1>
                <p>Crea tu cuenta y empieza a cuidar mejor a tus mascotas</p>
            </div>

            <div class="mascotas">
            </div>

            <h2>Crear cuenta</h2>

            <form action="guardar_usuario.php" method="POST">

                <div class="campo">
                    <label for="nombre">Nombre</label>

                    <div class="input-box">
                        <span></span>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            placeholder="Ingresa tu nombre"
                            required
                        >
                    </div>
                </div>


                <div class="campo">
                    <label for="apellido">Apellido</label>

                    <div class="input-box">
                        <span></span>

                        <input
                            type="text"
                            id="apellido"
                            name="apellido"
                            placeholder="Ingresa tu apellido"
                            required
                        >
                    </div>
                </div>


                <div class="campo">
                    <label for="correo">Correo electrónico</label>

                    <div class="input-box">
                        <span></span>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            placeholder="Ingresa tu correo"
                            required
                        >
                    </div>
                </div>


                <div class="campo">
                    <label for="contrasena">Contraseña</label>

                    <div class="input-box">
                        <span></span>

                        <input
                            type="password"
                            id="contrasena"
                            name="contrasena"
                            placeholder="Crea una contraseña"
                            required
                        >
                    </div>
                </div>


                <div class="campo">
                    <label for="telefono">Teléfono</label>

                    <div class="input-box">
                        <span></span>

                        <input
                            type="text"
                            id="telefono"
                            name="telefono"
                            placeholder="Ingresa tu teléfono"
                        >
                    </div>
                </div>


                <button type="submit" class="btn-login">
                    Crear mi cuenta
                </button>

            </form>


            <div class="registro">

                <p>
                    ¿Ya tienes una cuenta?
                    <a href="login.php">Inicia sesión</a>
                </p>

            </div>

        </div>

    </div>

</body>
</html>