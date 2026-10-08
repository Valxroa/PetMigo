<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include './conexion.php';

$id_usuario = $_SESSION['id_usuario'];


// =========================
// COMPROBAR SI VIENE UNA MASCOTA
// =========================

$id_mascota_filtro = null;

if (isset($_GET['id'])) {

    $id_mascota_filtro = $_GET['id'];

    // Comprobar que la mascota pertenece al usuario
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

    $mascota_seleccionada = $resultado_verificar->fetch_assoc();

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

$resultado_mascotas = $stmt_mascotas->get_result();


// =========================
// VACUNAS REGISTRADAS
// =========================

if ($id_mascota_filtro !== null) {

    // Mostrar solamente las vacunas de la mascota seleccionada

    $sql_vacunas = "SELECT vacuna.nombre_vacuna,
                           vacuna.fecha_aplicacion,
                           vacuna.proxima_dosis,
                           vacuna.observaciones,
                           mascota.nombre AS mascota
                    FROM vacuna
                    INNER JOIN mascota
                        ON vacuna.id_mascota = mascota.id_mascota
                    WHERE mascota.id_usuario = ?
                    AND vacuna.id_mascota = ?
                    ORDER BY vacuna.fecha_aplicacion DESC";

    $stmt_vacunas = $conexion->prepare($sql_vacunas);

    $stmt_vacunas->bind_param(
        "ii",
        $id_usuario,
        $id_mascota_filtro
    );

} else {

    // Mostrar las vacunas de todas las mascotas del usuario

    $sql_vacunas = "SELECT vacuna.nombre_vacuna,
                           vacuna.fecha_aplicacion,
                           vacuna.proxima_dosis,
                           vacuna.observaciones,
                           mascota.nombre AS mascota
                    FROM vacuna
                    INNER JOIN mascota
                        ON vacuna.id_mascota = mascota.id_mascota
                    WHERE mascota.id_usuario = ?
                    ORDER BY vacuna.fecha_aplicacion DESC";

    $stmt_vacunas = $conexion->prepare($sql_vacunas);

    $stmt_vacunas->bind_param(
        "i",
        $id_usuario
    );
}

$stmt_vacunas->execute();

$resultado_vacunas = $stmt_vacunas->get_result();

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
        Vacunas - PetMigo
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


            <a href="inicio.php">

                 Inicio

            </a>


            <a href="mascotas.php">

                 Mis mascotas

            </a>


            <a
                href="vacunas.php"
                class="menu-activo"
            >

                 Salud

            </a>


            <a href="#">
                 Citas
            </a>


            <a href="#">
                 Alimentación
            </a>


            <a href="#">
                 Calendario
            </a>


            <a href="#">
                 Recordatorios
            </a>


            <a href="#">
                 Perfil
            </a>


        </nav>

        <div class="sidebar-footer">

            <a href="login.php">
                 Cerrar sesión
            </a>

        </div>

    </aside>


    <!-- =========================
         CONTENIDO
    ========================== -->

    <main class="main-content vacunas-page">


        <!-- ENCABEZADO -->

        <header class="page-header">


            <div>


                <h2>
                    <?php
                    if ($id_mascota_filtro !== null) {
                        echo "Vacunas de "
                            . htmlspecialchars(
                                $mascota_seleccionada['nombre']
                            );
                    } else {
                        echo "Salud y vacunas";
                    }
                    ?>

                </h2>

                <p>

                    <?php

                    if ($id_mascota_filtro !== null) {
                        echo "Consulta y registra las vacunas de esta mascota.";
                    } else {
                        echo "Lleva el control de las vacunas y cuidados de tus mascotas.";

                    }

                    ?>

                </p>


            </div>


            <a
                href="#agregar-vacuna"
                class="btn-agregar"
            >

                 Registrar vacuna

            </a>


        </header>



        <!-- =========================
             VACUNAS REGISTRADAS
        ========================== -->

        <section class="vacunas-section">


            <h2>

             Vacunas registradas

                <?php

                if ($id_mascota_filtro !== null) {

                    echo " de "
                        . htmlspecialchars(
                            $mascota_seleccionada['nombre']
                        );

                }

                ?>

            </h2>


            <div class="vacunas-grid">


                <?php


                if ($resultado_vacunas->num_rows > 0) {


                    while (
                        $vacuna =
                        $resultado_vacunas->fetch_assoc()
                    ) {


                ?>


                    <div class="vacuna-card">


                        <div class="vacuna-icon">

                        </div>


                        <div class="vacuna-info">


                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $vacuna['nombre_vacuna']
                                );

                                ?>

                            </h3>


                            <p class="vacuna-mascota">

                                <?php

                                echo htmlspecialchars(
                                    $vacuna['mascota']
                                );

                                ?>

                            </p>


                            <p>

                                <strong>
                                    Fecha de aplicación:
                                </strong>

                                <?php

                                echo htmlspecialchars(
                                    $vacuna['fecha_aplicacion']
                                );

                                ?>

                            </p>


                            <p>

                                <strong>
                                    Próxima dosis:
                                </strong>


                                <?php

                                if (
                                    !empty(
                                        $vacuna['proxima_dosis']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $vacuna['proxima_dosis']
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
                                        $vacuna['observaciones']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $vacuna['observaciones']
                                    );

                                } else {

                                    echo "Sin observaciones";

                                }

                                ?>

                            </p>


                        </div>


                    </div>


                <?php
                    }

                } else {

                ?>
                    <div class="sin-vacunas">


                        <div class="sin-vacunas-icon">


                        </div>


                        <h3>

                            No hay vacunas registradas

                        </h3>


                        <p>

                            <?php

                            if ($id_mascota_filtro !== null) {

                                echo "Esta mascota todavía no tiene vacunas registradas.";

                            } else {

                                echo "Registra las vacunas de tus mascotas para llevar un mejor control de su salud.";

                            }

                            ?>

                        </p>


                        <a
                            href="#agregar-vacuna"
                            class="btn-agregar"
                        >

                            + Registrar vacuna

                        </a>


                    </div>


                <?php


                }


                ?>


            </div>


        </section>



        <!-- =========================
             FORMULARIO
        ========================== -->

        <section
            class="agregar-section"
            id="agregar-vacuna"
        >


            <div class="form-card">


                <div class="form-header">


                    <div>


                        <h2>

                             Registrar vacuna

                        </h2>


                        <p>

                            Completa la información
                            de la vacuna.

                        </p>


                    </div>


                </div>



                <form
                    action="guardar_vacuna.php"
                    method="POST"
                    class="mascota-form"
                >


                    <!-- MASCOTA -->

                    <div class="form-group">


                        <label for="id_mascota">

                            Mascota

                        </label>


                        <select
                            name="id_mascota"
                            id="id_mascota"
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



                    <!-- NOMBRE VACUNA -->

                    <div class="form-group">


                        <label for="nombre_vacuna">

                            Nombre de la vacuna

                        </label>


                        <input
                            type="text"
                            id="nombre_vacuna"
                            name="nombre_vacuna"
                            placeholder="Ej. Rabia"
                            required
                        >


                    </div>



                    <!-- FECHA APLICACIÓN -->

                    <div class="form-group">


                        <label for="fecha_aplicacion">

                            Fecha de aplicación

                        </label>


                        <input
                            type="date"
                            id="fecha_aplicacion"
                            name="fecha_aplicacion"
                            required
                        >


                    </div>



                    <!-- PRÓXIMA DOSIS -->

                    <div class="form-group">


                        <label for="proxima_dosis">

                            Próxima dosis

                        </label>


                        <input
                            type="date"
                            id="proxima_dosis"
                            name="proxima_dosis"
                        >


                    </div>



                    <!-- OBSERVACIONES -->

                    <div class="form-group">


                        <label for="observaciones">

                            Observaciones

                        </label>


                        <textarea
                            id="observaciones"
                            name="observaciones"
                            placeholder="Escribe alguna observación..."
                            rows="4"
                        ></textarea>


                    </div>



                    <!-- BOTONES -->

                    <div class="form-buttons">


                        <?php

                        if ($id_mascota_filtro !== null) {

                        ?>

                            <a
                                href="perfil_mascota.php?id=<?php echo $id_mascota_filtro; ?>"
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

                                Cancelar

                            </a>

                        <?php

                        }

                        ?>


                        <button
                            type="submit"
                            class="btn-guardar"
                        >

                            Guardar vacuna

                        </button>


                    </div>


                </form>


            </div>


        </section>


    </main>


</div>


</body>

</html>


<?php

$stmt_mascotas->close();

$stmt_vacunas->close();

$conexion->close();

?>
