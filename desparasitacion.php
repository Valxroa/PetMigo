<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];


// =========================
// COMPROBAR SI VIENE UNA MASCOTA
// =========================

$id_mascota_filtro = null;

if (isset($_GET['id'])) {

    $id_mascota_filtro = $_GET['id'];

    $sql_verificar = "SELECT id_mascota, nombre
                      FROM mascota
                      WHERE id_mascota = ?
                      AND id_usuario = ?";

    $stmt_verificar = $conexion->prepare($sql_verificar);

    $stmt_verificar->bind_param(
        "ii",
        $id_mascota_filtro,
        $id_usuario
    );

    $stmt_verificar->execute();

    $resultado_verificar = $stmt_verificar->get_result();

    if ($resultado_verificar->num_rows == 0) {

        echo "Mascota no encontrada.";
        exit();

    }

    $mascota_seleccionada =
        $resultado_verificar->fetch_assoc();

    $stmt_verificar->close();
}


// =========================
// MASCOTAS DEL USUARIO
// =========================

$sql_mascotas = "SELECT id_mascota, nombre
                 FROM mascota
                 WHERE id_usuario = ?
                 ORDER BY nombre";

$stmt_mascotas = $conexion->prepare($sql_mascotas);

$stmt_mascotas->bind_param(
    "i",
    $id_usuario
);

$stmt_mascotas->execute();

$resultado_mascotas =
    $stmt_mascotas->get_result();


// =========================
// DESPARASITACIONES REGISTRADAS
// =========================

if ($id_mascota_filtro !== null) {

    $sql_desparasitacion = "SELECT
                                desparasitacion.producto,
                                desparasitacion.fecha_aplicacion,
                                desparasitacion.proxima_aplicacion,
                                desparasitacion.observaciones,
                                mascota.nombre AS mascota
                            FROM desparasitacion
                            INNER JOIN mascota
                                ON desparasitacion.id_mascota = mascota.id_mascota
                            WHERE mascota.id_usuario = ?
                            AND desparasitacion.id_mascota = ?
                            ORDER BY desparasitacion.fecha_aplicacion DESC";

    $stmt_desparasitacion =
        $conexion->prepare($sql_desparasitacion);

    $stmt_desparasitacion->bind_param(
        "ii",
        $id_usuario,
        $id_mascota_filtro
    );

} else {

    $sql_desparasitacion = "SELECT
                                desparasitacion.producto,
                                desparasitacion.fecha_aplicacion,
                                desparasitacion.proxima_aplicacion,
                                desparasitacion.observaciones,
                                mascota.nombre AS mascota
                            FROM desparasitacion
                            INNER JOIN mascota
                                ON desparasitacion.id_mascota = mascota.id_mascota
                            WHERE mascota.id_usuario = ?
                            ORDER BY desparasitacion.fecha_aplicacion DESC";

    $stmt_desparasitacion =
        $conexion->prepare($sql_desparasitacion);

    $stmt_desparasitacion->bind_param(
        "i",
        $id_usuario
    );
}

$stmt_desparasitacion->execute();

$resultado_desparasitacion =
    $stmt_desparasitacion->get_result();

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
        Desparasitación - PetMigo
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

            <a href="mascotas.php">

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


        <!-- =========================
             ENCABEZADO
        ========================== -->

        <header class="page-header">


            <div>

                <h2>

                    <?php

                    if ($id_mascota_filtro !== null) {

                        echo "Desparasitación de "
                            . htmlspecialchars(
                                $mascota_seleccionada['nombre']
                            );

                    } else {

                        echo "Desparasitación";

                    }

                    ?>

                </h2>


                <p>

                    <?php

                    if ($id_mascota_filtro !== null) {

                        echo "Consulta y registra las desparasitaciones de esta mascota.";

                    } else {

                        echo "Registra y consulta las desparasitaciones de tus mascotas.";

                    }

                    ?>

                </p>

            </div>


        </header>



        <!-- =========================
             DESPARASITACIONES
        ========================== -->

        <section class="desparasitacion-seccion">


            <h2>

                 Desparasitaciones registradas

                <?php

                if ($id_mascota_filtro !== null) {

                    echo " de "
                        . htmlspecialchars(
                            $mascota_seleccionada['nombre']
                        );

                }

                ?>

            </h2>


            <div class="desparasitacion-lista">


                <?php

                if (
                    $resultado_desparasitacion->num_rows > 0
                ) {


                    while (
                        $desparasitacion =
                        $resultado_desparasitacion->fetch_assoc()
                    ) {

                ?>


                    <div class="desparasitacion-card">


                        <h3>

                             Desparasitación de

                            <?php

                            echo htmlspecialchars(
                                $desparasitacion['mascota']
                            );

                            ?>

                        </h3>


                        <p>

                            <strong>
                                Producto:
                            </strong>

                            <?php

                            echo htmlspecialchars(
                                $desparasitacion['producto']
                            );

                            ?>

                        </p>


                        <p>

                            <strong>
                                Fecha de aplicación:
                            </strong>

                            <?php

                            echo htmlspecialchars(
                                $desparasitacion['fecha_aplicacion']
                            );

                            ?>

                        </p>


                        <p>

                            <strong>
                                Próxima aplicación:
                            </strong>

                            <?php

                            if (
                                !empty(
                                    $desparasitacion['proxima_aplicacion']
                                )
                            ) {

                                echo htmlspecialchars(
                                    $desparasitacion['proxima_aplicacion']
                                );

                            } else {

                                echo "No registrada";

                            }

                            ?>

                        </p>


                        <p>

                            <strong>
                                Observaciones:
                            </strong>

                            <?php

                            if (
                                !empty(
                                    $desparasitacion['observaciones']
                                )
                            ) {

                                echo htmlspecialchars(
                                    $desparasitacion['observaciones']
                                );

                            } else {

                                echo "Sin observaciones";

                            }

                            ?>

                        </p>


                    </div>


                <?php

                    }


                } else {

                ?>


                    <div class="desparasitacion-vacio">

                        <p>

                            <?php

                            if ($id_mascota_filtro !== null) {

                                echo "Esta mascota todavía no tiene desparasitaciones registradas.";

                            } else {

                                echo "No hay desparasitaciones registradas.";

                            }

                            ?>

                        </p>

                    </div>


                <?php

                }

                ?>


            </div>


        </section>



        <!-- =========================
             FORMULARIO
        ========================== -->

        <section class="desparasitacion-formulario">


            <h2>

                 Registrar desparasitación

            </h2>


            <form
                action="guardar_desparasitacion.php"
                method="POST"
            >


                <div class="form-grupo">

                    <label>

                        Mascota:

                    </label>


                    <select
                        name="id_mascota"
                        required
                    >

                        <option value="">

                            Seleccione una mascota

                        </option>


                        <?php

                        while (
                            $mascota =
                            $resultado_mascotas->fetch_assoc()
                        ) {

                            $seleccionada = "";


                            if (
                                $id_mascota_filtro !== null
                                &&
                                $mascota['id_mascota']
                                == $id_mascota_filtro
                            ) {

                                $seleccionada = "selected";

                            }


                            echo "<option value='"
                                . htmlspecialchars(
                                    $mascota['id_mascota']
                                )
                                . "' "
                                . $seleccionada
                                . ">";


                            echo htmlspecialchars(
                                $mascota['nombre']
                            );


                            echo "</option>";

                        }

                        ?>

                    </select>

                </div>



                <div class="form-grupo">

                    <label>

                        Producto:

                    </label>


                    <input
                        type="text"
                        name="producto"
                        placeholder="Ejemplo: NexGard"
                        required
                    >

                </div>



                <div class="form-grupo">

                    <label>

                        Fecha de aplicación:

                    </label>


                    <input
                        type="date"
                        name="fecha_aplicacion"
                        required
                    >

                </div>



                <div class="form-grupo">

                    <label>

                        Próxima aplicación:

                    </label>


                    <input
                        type="date"
                        name="proxima_aplicacion"
                    >

                </div>



                <div class="form-grupo">

                    <label>

                        Observaciones:

                    </label>


                    <textarea
                        name="observaciones"
                        placeholder="Notas adicionales"
                    ></textarea>

                </div>



                <button type="submit">

                    Registrar desparasitación

                </button>


            </form>


        </section>



        <!-- =========================
             VOLVER
        ========================== -->

        <div class="desparasitacion-volver">


            <?php

            if ($id_mascota_filtro !== null) {

            ?>


                <a
                    href="Perfil_Mascota.php?id=<?php echo $id_mascota_filtro; ?>"
                    class="btn-cancelar"
                >

                    ← Volver al perfil

                </a>


            <?php

            } else {

            ?>


                <a
                    href="inicio.php"
                    class="btn-cancelar"
                >

                    ← Volver al inicio

                </a>


            <?php

            ?>


            <?php

            }

            ?>


        </div>


    </main>


</div>


</body>

</html>


<?php

$stmt_mascotas->close();

$stmt_desparasitacion->close();

$conexion->close();

?>