<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include './conexion.php';

$id_usuario = $_SESSION['id_usuario'];


// ========================================
// OBTENER INFORMACIÓN DEL USUARIO
// ========================================

$sql = "SELECT id_usuario, nombre, apellido, correo, telefono
        FROM usuario
        WHERE id_usuario = ?";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $id_usuario);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    echo "Usuario no encontrado.";
    exit();
}

$usuario = $resultado->fetch_assoc();

$stmt->close();


// ========================================
// SABER SI ESTAMOS EDITANDO
// ========================================

$editar = isset($_GET['editar']) && $_GET['editar'] == '1';


// ========================================
// MENSAJES
// ========================================

$mensaje = "";
$tipo_mensaje = "";

if (isset($_GET['actualizado']) && $_GET['actualizado'] == '1') {

    $mensaje = "¡Perfil actualizado correctamente!";

    $tipo_mensaje = "exito";
}

if (isset($_GET['error'])) {

    $mensaje = htmlspecialchars(
        $_GET['error'],
        ENT_QUOTES,
        'UTF-8'
    );

    $tipo_mensaje = "error";
}

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
        Mi perfil - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="estilos.css"
    >

</head>


<body>


<div class="dashboard">


    <!-- ========================================
         BARRA LATERAL
    ========================================= -->

    <aside class="sidebar">


        <div class="sidebar-logo">

            <h1>
                PetMigo 
            </h1>

        </div>


        <nav>


            <a href="inicio.php">
                 Inicio
            </a>


            <a href="mascotas.php">
                 Mis mascotas
            </a>


            <a href="citas.php">
                 Citas
            </a>


            <a href="alimentacion.php">
                 Alimentación
            </a>


            <a href="calendario.php">
                 Calendario
            </a>


            <a href="recordatorios.php">
                 Recordatorios
            </a>


            <a
                href="perfil.php"
                class="menu-activo"
            >
                 Perfil
            </a>


        </nav>


        <div class="sidebar-footer">

            <a href="cerrar_sesion.php">
                 Cerrar sesión
            </a>

        </div>


    </aside>



    <!-- ========================================
         CONTENIDO PRINCIPAL
    ========================================= -->

    <main class="main-content">


        <header class="page-header">


            <div>

                <h2>
                     Mi perfil
                </h2>


                <p>
                    Consulta y administra tu información personal.
                </p>

            </div>

        </header>

        <!-- ========================================
             MENSAJE
        ========================================= -->

        <?php if ($mensaje != ""): ?>

            <div
                class="perfil-mensaje <?php echo $tipo_mensaje; ?>"
            >

                <?php echo $mensaje; ?>

            </div>

        <?php endif; ?>



        <!-- ========================================
             PERFIL
        ========================================= -->

        <section class="perfil-usuario">


            <div class="perfil-usuario-card">


                <!-- ==================================
                     AVATAR
                ================================== -->

                <div class="perfil-avatar">

                    👤

                </div>



                <?php if (!$editar): ?>


                    <!-- ==================================
                         MODO VISUALIZACIÓN
                    ================================== -->


                    <h1>

                        <?php

                        echo htmlspecialchars(
                            $usuario['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>

                        <?php

                        echo htmlspecialchars(
                            $usuario['apellido'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>

                    </h1>


                    <p class="perfil-usuario-subtitulo">

                        Usuario de PetMigo

                    </p>



                    <!-- ==================================
                         INFORMACIÓN DEL USUARIO
                    ================================== -->

                    <div class="perfil-usuario-datos">


                        <!-- NOMBRE -->

                        <div class="perfil-usuario-dato">


                            <div class="perfil-usuario-icono">
                                👤
                            </div>


                            <div>

                                <span>
                                    Nombre
                                </span>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $usuario['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </strong>

                            </div>


                        </div>



                        <!-- APELLIDO -->

                        <div class="perfil-usuario-dato">


                            <div class="perfil-usuario-icono">
                                👤
                            </div>


                            <div>

                                <span>
                                    Apellido
                                </span>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $usuario['apellido'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </strong>

                            </div>


                        </div>



                        <!-- CORREO -->

                        <div class="perfil-usuario-dato">


                            <div class="perfil-usuario-icono">
                                📧
                            </div>


                            <div>

                                <span>
                                    Correo electrónico
                                </span>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $usuario['correo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </strong>

                            </div>


                        </div>



                        <!-- TELÉFONO -->

                        <div class="perfil-usuario-dato">


                            <div class="perfil-usuario-icono">
                                📱
                            </div>


                            <div>

                                <span>
                                    Teléfono
                                </span>


                                <strong>

                                    <?php

                                    if (
                                        !empty(
                                            $usuario['telefono']
                                        )
                                    ) {

                                        echo htmlspecialchars(
                                            $usuario['telefono'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                    } else {

                                        echo "No registrado";

                                    }

                                    ?>

                                </strong>

                            </div>


                        </div>


                    </div>



                    <!-- ==================================
                         BOTONES
                    ================================== -->

                    <div class="perfil-usuario-botones">


                        <a
                            href="inicio.php"
                            class="btn-cancelar"
                        >
                             Volver
                        </a>


                        <a
                            href="perfil.php?editar=1"
                            class="btn-agregar"
                        >
                             Editar perfil
                        </a>


                    </div>



                <?php else: ?>


                    <!-- ==================================
                         MODO EDICIÓN
                    ================================== -->


                    <h1>
                         Editar perfil
                    </h1>


                    <p class="perfil-usuario-subtitulo">

                        Actualiza tu información personal.

                    </p>



                    <!-- ==================================
                         FORMULARIO
                    ================================== -->

                    <form
                        action="guardar_perfil.php"
                        method="POST"
                        class="perfil-form"
                    >


                        <!-- NOMBRE -->

                        <div class="form-group">


                            <label for="nombre">
                                Nombre
                            </label>


                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="<?php

                                echo htmlspecialchars(
                                    $usuario['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>"
                                maxlength="50"
                                required
                            >


                        </div>



                        <!-- APELLIDO -->

                        <div class="form-group">


                            <label for="apellido">
                                Apellido
                            </label>


                            <input
                                type="text"
                                id="apellido"
                                name="apellido"
                                value="<?php

                                echo htmlspecialchars(
                                    $usuario['apellido'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>"
                                maxlength="50"
                                required
                            >


                        </div>



                        <!-- CORREO -->

                        <div class="form-group">


                            <label for="correo">
                                Correo electrónico
                            </label>


                            <input
                                type="email"
                                id="correo"
                                name="correo"
                                value="<?php

                                echo htmlspecialchars(
                                    $usuario['correo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>"
                                maxlength="100"
                                required
                            >


                        </div>



                        <!-- TELÉFONO -->

                        <div class="form-group">


                            <label for="telefono">
                                Teléfono
                            </label>


                            <input
                                type="text"
                                id="telefono"
                                name="telefono"
                                value="<?php

                                echo htmlspecialchars(
                                    $usuario['telefono'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>"
                                maxlength="20"
                                placeholder="Ejemplo: 3001234567"
                            >


                        </div>



                        <!-- BOTONES -->

                        <div class="perfil-usuario-botones">


                            <a
                                href="perfil.php"
                                class="btn-cancelar"
                            >
                                 Cancelar
                            </a>


                            <button
                                type="submit"
                                class="btn-agregar"
                            >
                                 Guardar cambios
                            </button>

                        </div>

                    </form>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>

</html>