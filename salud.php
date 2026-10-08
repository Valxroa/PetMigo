<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

include '../conexion.php';

$id_usuario = $_SESSION['id_usuario'];


// =====================================================
// OBTENER LAS MASCOTAS DEL USUARIO
// =====================================================

$sql = "SELECT mascota.id_mascota,
               mascota.nombre,
               mascota.sexo,
               especie.nombre AS especie,
               raza.nombre AS raza
        FROM mascota
        INNER JOIN raza
            ON mascota.id_raza = raza.id_raza
        INNER JOIN especie
            ON raza.id_especie = especie.id_especie
        WHERE mascota.id_usuario = ?
        ORDER BY mascota.nombre";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

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
        Salud - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="../estilos.css"
    >

</head>

<body>

<div class="dashboard">


    <!-- =================================================
         BARRA LATERAL
    ================================================== -->

    <aside class="sidebar">

        <div class="sidebar-logo">

            <h1>
                PetMigo 
            </h1>

        </div>


        <nav>

            <a href="../inicio.php">
                 Inicio
            </a>

            <a href="../mascotas.php">
                 Mis mascotas
            </a>

            <a href="../citas.php">
                 Citas
            </a>

            <a href="../calendario.php">
                 Calendario
            </a>

            <a href="../alimentacion.php">
                 Alimentación
            </a>

            <a href="../recordatorios.php">
                 Recordatorios
            </a>

            <a
                href="salud.php"
                class="menu-activo"
            >
                 Salud
            </a>

            <a href="../perfil.php">
                 Perfil
            </a>

        </nav>


        <div class="sidebar-footer">

            <a href="../cerrar_sesion.php">
                 Cerrar sesión
            </a>

        </div>

    </aside>


    <!-- =================================================
         CONTENIDO PRINCIPAL
    ================================================== -->

    <main class="main-content">


        <!-- ENCABEZADO -->

        <header class="page-header">

            <div>

                <h2>
                     Salud de mis mascotas
                </h2>

                <p>
                    Selecciona una mascota para consultar
                    y administrar su información de salud.
                </p>

            </div>

        </header>


        <!-- =================================================
             MASCOTAS
        ================================================== -->

        <section class="mascotas-section">

            <h2>
                Selecciona una mascota
            </h2>


            <div class="mascotas-grid">


                <?php

                if ($resultado->num_rows > 0) {

                    while (
                        $mascota = $resultado->fetch_assoc()
                    ) {

                ?>

                    <div class="mascota-card">


                        <div class="mascota-foto">

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


                        <div class="mascota-info">

                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['nombre']
                                );

                                ?>

                            </h3>


                            <p class="mascota-especie">

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


                            <p>

                                <strong>
                                    Sexo:
                                </strong>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['sexo']
                                );

                                ?>

                            </p>


                            <a
                                href="detalle_salud.php?id=<?php echo $mascota['id_mascota']; ?>"
                                class="btn-perfil"
                            >
                                Ver salud
                            </a>

                        </div>

                    </div>


                <?php

                    }

                } else {

                ?>


                    <div class="sin-mascotas">

                        <div>
                        </div>

                        <h3>
                            Aún no tienes mascotas registradas
                        </h3>

                        <p>
                            Primero debes registrar una mascota
                            para poder consultar su información
                            de salud.
                        </p>

                        <a
                            href="../mascotas.php#agregar-mascota"
                            class="btn-agregar"
                        >
                             Agregar mascota
                        </a>

                    </div>


                <?php

                }

                ?>

            </div>

        </section>


    </main>

</div>

</body>

</html>
