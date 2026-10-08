<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include './conexion.php';

$id_usuario = $_SESSION['id_usuario'];

if (!isset($_GET['id'])) {
    header("Location: mascotas.php");
    exit();
}

$id_mascota = $_GET['id'];

$sql = "SELECT mascota.id_mascota,
               mascota.nombre,
               mascota.fecha_nacimiento,
               mascota.sexo,
               mascota.peso,
               raza.nombre AS raza,
               especie.nombre AS especie
        FROM mascota
        INNER JOIN raza
            ON mascota.id_raza = raza.id_raza
        INNER JOIN especie
            ON raza.id_especie = especie.id_especie
        WHERE mascota.id_mascota = ?
        AND mascota.id_usuario = ?";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_mascota,
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {

    echo "Mascota no encontrada.";
    exit();

}

$mascota = $resultado->fetch_assoc();

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
        Perfil de <?php echo htmlspecialchars($mascota['nombre']); ?>
        - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="estilos.css"
    >

</head>

<body>

<div class="dashboard">


    <!-- =========================
         BARRA LATERAL
    ========================== -->

    <aside class="sidebar">

        <div class="sidebar-logo">

            <h1>
                PetMigo 
            </h1>

        </div>


        <nav>

            <!-- INICIO -->

            <a href="inicio.php">

                 Inicio

            </a>


            <!-- MASCOTAS -->

            <a
                href="mascotas.php"
                class="menu-activo"
            >

                 Mis mascotas

            </a>


            <!-- CITAS -->

            <a href="citas.php">

                 Citas

            </a>


            <!-- ALIMENTACIÓN -->

            <a href="alimentacion.php">

                 Alimentación

            </a>


            <!-- RECORDATORIOS -->

            <a href="recordatorios.php">

                 Recordatorios

            </a>


            <!-- PERFIL -->

            <a href="perfil.php">

                 Perfil

            </a>

        </nav>


        <!-- CERRAR SESIÓN -->

        <div class="sidebar-footer">

            <a href="cerrar_sesion.php">

                 Cerrar sesión

            </a>

        </div>

    </aside>



    <!-- =========================
         CONTENIDO
    ========================== -->

    <main class="main-content">


        <!-- ENCABEZADO -->

        <header class="page-header">

            <div>

                <h2>

                     Perfil de

                    <?php

                    echo htmlspecialchars(
                        $mascota['nombre']
                    );

                    ?>

                </h2>


                <p>

                    Información de tu mascota.

                </p>

            </div>


            <a
                href="mascotas.php"
                class="btn-agregar"
            >

                ← Volver a mis mascotas

            </a>

        </header>



        <!-- =========================
             PERFIL
        ========================== -->

        <section class="mascota-perfil">


            <div class="mascota-perfil-card">


                <!-- ICONO -->

                <div class="mascota-perfil-foto">

                    <?php

                    if (
                        strtolower(
                            $mascota['especie']
                        ) == 'gato'
                    ) {

                        echo "🐱";

                    } else {

                        echo "🐶";

                    }

                    ?>

                </div>



                <!-- NOMBRE -->

                <h1>

                    <?php

                    echo htmlspecialchars(
                        $mascota['nombre']
                    );

                    ?>

                </h1>


                <p class="perfil-especie">

                    <?php

                    echo htmlspecialchars(
                        $mascota['especie']
                    );

                    ?>

                    -

                    <?php

                    echo htmlspecialchars(
                        $mascota['raza']
                    );

                    ?>

                </p>



                <!-- =========================
                     INFORMACIÓN
                ========================== -->

                <div class="perfil-datos">


                    <div class="dato">

                        <span>
                        </span>

                        <div>

                            <strong>
                                Fecha de nacimiento
                            </strong>

                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['fecha_nacimiento']
                                );

                                ?>

                            </p>

                        </div>

                    </div>



                    <div class="dato">

                        <span>
                        </span>

                        <div>

                            <strong>
                                Sexo
                            </strong>

                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['sexo']
                                );

                                ?>

                            </p>

                        </div>

                    </div>



                    <div class="dato">

                        <span>
                        </span>

                        <div>

                            <strong>
                                Peso
                            </strong>

                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['peso']
                                );

                                ?>

                                kg

                            </p>

                        </div>

                    </div>



                    <div class="dato">

                        <span>
                        </span>

                        <div>

                            <strong>
                                Especie
                            </strong>

                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['especie']
                                );

                                ?>

                            </p>

                        </div>

                    </div>



                    <div class="dato">

                        <span>
                        </span>

                        <div>

                            <strong>
                                Raza
                            </strong>

                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['raza']
                                );

                                ?>

                            </p>

                        </div>

                    </div>


                </div>



                <!-- =========================
                     SALUD DE LA MASCOTA
                ========================== -->

                <div class="salud-mascota">


                    <h2>

                         Salud de

                        <?php

                        echo htmlspecialchars(
                            $mascota['nombre']
                        );

                        ?>

                    </h2>


                    <p>

                        Consulta y administra la información
                        de salud de tu mascota.

                    </p>



                    <div class="salud-opciones">


                        <!-- VACUNAS -->

                        <a
                            href="vacunas.php?id=<?php echo $mascota['id_mascota']; ?>"
                            class="salud-card"
                        >

                            <div class="salud-icon">
                            </div>


                            <h3>

                                Vacunas

                            </h3>


                            <p>

                                Registra y consulta
                                las vacunas.

                            </p>

                        </a>



                        <!-- DESPARASITACIÓN -->

                        <a
                            href="desparasitacion.php?id=<?php echo $mascota['id_mascota']; ?>"
                            class="salud-card"
                        >

                            <div class="salud-icon">
                            </div>


                            <h3>

                                Desparasitación

                            </h3>


                            <p>

                                Registra y consulta
                                las desparasitaciones.

                            </p>

                        </a>



                        <!-- HISTORIAL -->

                        <a
                            href="historial.php?id=<?php echo $mascota['id_mascota']; ?>"
                            class="salud-card"
                        >

                            <div class="salud-icon">
                            </div>


                            <h3>

                                Historial de salud

                            </h3>


                            <p>

                                Consulta los registros
                                de salud.

                            </p>

                        </a>


                    </div>

                </div>



                <!-- =========================
                     BOTÓN VOLVER
                ========================== -->
                <div class="perfil-botones">
                    <a
                        href="mascotas.php"
                        class="btn-cancelar"
                    >
                         Volver
                    </a>

                </div>


            </div>

        </section>


    </main>

</div>

</body>

</html>