<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];


/* =========================================
   RECIBIR ID DE LA MASCOTA
========================================= */

$id_mascota_filtro = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $id_mascota_filtro = intval($_GET['id']);

}


/* =========================================
   VERIFICAR QUE LA MASCOTA PERTENEZCA
   AL USUARIO
========================================= */

$nombre_mascota_filtro = "";

if ($id_mascota_filtro !== null) {

    $sql_mascota = "SELECT id_mascota, nombre
                    FROM mascota
                    WHERE id_mascota = ?
                    AND id_usuario = ?";

    $stmt_mascota =
        $conexion->prepare($sql_mascota);

    $stmt_mascota->bind_param(
        "ii",
        $id_mascota_filtro,
        $id_usuario
    );

    $stmt_mascota->execute();

    $resultado_mascota =
        $stmt_mascota->get_result();

    if ($resultado_mascota->num_rows == 0) {

        die("La mascota no pertenece al usuario.");

    }

    $mascota_filtro =
        $resultado_mascota->fetch_assoc();

    $nombre_mascota_filtro =
        $mascota_filtro['nombre'];

    $stmt_mascota->close();

}


/* =========================================
   OBTENER MASCOTAS DEL USUARIO
========================================= */

$sql_mascotas = "SELECT id_mascota, nombre
                 FROM mascota
                 WHERE id_usuario = ?
                 ORDER BY nombre";

$stmt_mascotas =
    $conexion->prepare($sql_mascotas);

$stmt_mascotas->bind_param(
    "i",
    $id_usuario
);

$stmt_mascotas->execute();

$resultado_mascotas =
    $stmt_mascotas->get_result();

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
        Historial de salud - PetMigo
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
         CONTENIDO PRINCIPAL
    ========================== -->

    <main class="main-content">


        <!-- =========================
             ENCABEZADO
        ========================== -->

        <header class="page-header">


            <div>

                <h2>
                    Historial de salud

                    <?php

                    if ($id_mascota_filtro !== null) {

                        echo "Historial de "
                            . htmlspecialchars(
                                $nombre_mascota_filtro
                            );

                    } else {

                        echo "Historial de salud";

                    }

                    ?>

                </h2>


                <p>

                    <?php

                    if ($id_mascota_filtro !== null) {

                        echo "Consulta y registra el historial de salud de esta mascota.";

                    } else {

                        echo "Registra y consulta el historial de salud de tus mascotas.";

                    }

                    ?>

                </p>

            </div>


        </header>



        <!-- =========================
             FORMULARIO
        ========================== -->

        <section class="historial-formulario">


            <h2>

                 Registrar historial

            </h2>


            <form
                action="guardar_historial.php"
                method="POST"
            >


                <!-- MASCOTA -->

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

                                $seleccionada =
                                    "selected";

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



                <!-- FECHA -->

                <div class="form-grupo">

                    <label>

                        Fecha:

                    </label>


                    <input
                        type="date"
                        name="fecha"
                        required
                    >

                </div>



                <!-- TIPO DE REGISTRO -->

                <div class="form-grupo">

                    <label>

                        Tipo de registro:

                    </label>


                    <input
                        type="text"
                        name="tipo_registro"
                        placeholder="Ejemplo: Consulta veterinaria"
                        required
                    >

                </div>



                <!-- DESCRIPCIÓN -->

                <div class="form-grupo">

                    <label>

                        Descripción:

                    </label>


                    <textarea
                        name="descripcion"
                        placeholder="Describe el evento de salud"
                    ></textarea>

                </div>



                <!-- OBSERVACIONES -->

                <div class="form-grupo">

                    <label>

                        Observaciones:

                    </label>


                    <textarea
                        name="observaciones"
                        placeholder="Notas adicionales"
                    ></textarea>

                </div>



                <!-- BOTÓN -->

                <button type="submit">

                    Registrar historial

                </button>


            </form>


        </section>



        <!-- =========================
             HISTORIALES REGISTRADOS
        ========================== -->

        <section class="historial-registros">


            <h2>

                 Historiales registrados

                <?php

                if ($id_mascota_filtro !== null) {

                    echo " de "
                        . htmlspecialchars(
                            $nombre_mascota_filtro
                        );

                }

                ?>

            </h2>



            <div class="historial-lista">


                <?php

                /* =========================================
                   MOSTRAR HISTORIAL
                ========================================= */

                if ($id_mascota_filtro !== null) {

                    $sql_historial =
                        "SELECT historial_salud.fecha,
                                historial_salud.tipo_registro,
                                historial_salud.descripcion,
                                historial_salud.observaciones,
                                mascota.nombre AS mascota
                         FROM historial_salud
                         INNER JOIN mascota
                             ON historial_salud.id_mascota =
                                mascota.id_mascota
                         WHERE mascota.id_usuario = ?
                         AND historial_salud.id_mascota = ?
                         ORDER BY historial_salud.fecha DESC";


                    $stmt_historial =
                        $conexion->prepare(
                            $sql_historial
                        );


                    $stmt_historial->bind_param(
                        "ii",
                        $id_usuario,
                        $id_mascota_filtro
                    );


                } else {


                    $sql_historial =
                        "SELECT historial_salud.fecha,
                                historial_salud.tipo_registro,
                                historial_salud.descripcion,
                                historial_salud.observaciones,
                                mascota.nombre AS mascota
                         FROM historial_salud
                         INNER JOIN mascota
                             ON historial_salud.id_mascota =
                                mascota.id_mascota
                         WHERE mascota.id_usuario = ?
                         ORDER BY historial_salud.fecha DESC";


                    $stmt_historial =
                        $conexion->prepare(
                            $sql_historial
                        );


                    $stmt_historial->bind_param(
                        "i",
                        $id_usuario
                    );

                }


                $stmt_historial->execute();


                $resultado_historial =
                    $stmt_historial->get_result();


                if (
                    $resultado_historial->num_rows > 0
                ) {


                    while (
                        $historial =
                        $resultado_historial->fetch_assoc()
                    ) {


                ?>


                        <div class="historial-card">


                            <div class="historial-card-encabezado">


                                <div>

                                    <span class="historial-icono">

                                        

                                    </span>


                                    <h3>

                                        <?php

                                        echo htmlspecialchars(
                                            $historial['tipo_registro']
                                        );

                                        ?>

                                    </h3>

                                </div>


                                <span class="historial-fecha">

                                    <?php

                                    echo htmlspecialchars(
                                        $historial['fecha']
                                    );

                                    ?>

                                </span>


                            </div>



                            <p>

                                <strong>
                                    Mascota:
                                </strong>

                                <?php

                                echo htmlspecialchars(
                                    $historial['mascota']
                                );

                                ?>

                            </p>



                            <p>

                                <strong>
                                    Descripción:
                                </strong>

                                <?php

                                if (
                                    !empty(
                                        $historial['descripcion']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $historial['descripcion']
                                    );

                                } else {

                                    echo "Sin descripción";

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
                                        $historial['observaciones']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $historial['observaciones']
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


                    <div class="historial-vacio">

                        <p>

                            No hay registros de historial.

                        </p>

                    </div>


                <?php

                }

                ?>


            </div>


        </section>



        <!-- =========================
             BOTÓN VOLVER
        ========================== -->

        <div class="historial-volver">


            <?php

            if ($id_mascota_filtro !== null) {

            ?>


                <a
                    href="Perfil_Mascota.php?id=<?php echo $id_mascota_filtro; ?>"
                    class="btn-cancelar"
                >

                    ← Volver al perfil de la mascota

                </a>


            <?php

            } else {

            ?>


                <a
                    href="inicio.php"
                    class="btn-cancelar"
                >

                     Volver al inicio

                </a>


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

$stmt_historial->close();

$conexion->close();

?>