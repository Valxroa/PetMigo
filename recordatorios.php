<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];


// =====================================================
// OBTENER MASCOTAS DEL USUARIO
// =====================================================

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


// =====================================================
// OBTENER RECORDATORIOS REGISTRADOS
// =====================================================

$sql_recordatorios = "SELECT
                          recordatorio.titulo,
                          recordatorio.descripcion,
                          recordatorio.fecha,
                          recordatorio.hora,
                          recordatorio.estado,
                          mascota.nombre AS mascota
                       FROM recordatorio
                       INNER JOIN mascota
                           ON recordatorio.id_mascota =
                              mascota.id_mascota
                       WHERE mascota.id_usuario = ?
                       ORDER BY recordatorio.fecha ASC,
                                recordatorio.hora ASC";

$stmt_recordatorios = $conexion->prepare(
    $sql_recordatorios
);

$stmt_recordatorios->bind_param(
    "i",
    $id_usuario
);

$stmt_recordatorios->execute();

$resultado_recordatorios =
    $stmt_recordatorios->get_result();

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
        Recordatorios - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="estilos.css?v=7"
    >

</head>


<body>


<div class="dashboard">


    <!-- =====================================================
         BARRA LATERAL
    ====================================================== -->

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


            <a href="calendario.php">
                 Calendario
            </a>


            <a href="alimentacion.php">
                 Alimentación
            </a>


            <a
                href="recordatorios.php"
                class="menu-activo"
            >
                 Recordatorios
            </a>


            <a href="perfil.php">
                Perfil
            </a>


        </nav>


        <div class="sidebar-footer">

            <a href="cerrar_sesion.php">
                 Cerrar sesión
            </a>

        </div>


    </aside>



    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->

    <main class="main-content recordatorios-page">


        <!-- =================================================
             ENCABEZADO
        ================================================== -->

        <header class="page-header">


            <div>

                <span class="page-kicker">
                    ORGANIZACIÓN
                </span>


                <h1>
                     Recordatorios
                </h1>


                <p>
                    Consulta y registra los recordatorios
                    de tus mascotas.
                </p>

            </div>


        </header>



        <!-- =================================================
             RECORDATORIOS REGISTRADOS
        ================================================== -->

        <section class="recordatorios-section">


            <div class="section-title">


                <div>

                    <span class="section-kicker">
                        TUS RECORDATORIOS
                    </span>


                    <h2>
                         Recordatorios registrados
                    </h2>

                </div>


            </div>



            <div class="recordatorios-grid">


                <?php

                if (
                    $resultado_recordatorios->num_rows > 0
                ) {

                    while (
                        $recordatorio =
                        $resultado_recordatorios->fetch_assoc()
                    ) {

                ?>


                        <article class="recordatorio-card">


                            <div class="recordatorio-icon">
                            </div>


                            <div class="recordatorio-content">


                                <h3>

                                    <?php

                                    echo htmlspecialchars(
                                        $recordatorio['titulo']
                                    );

                                    ?>

                                </h3>


                                <p class="recordatorio-mascota">

                                    <?php

                                    echo htmlspecialchars(
                                        $recordatorio['mascota']
                                    );

                                    ?>

                                </p>


                                <p>

                                    <strong>
                                        Descripción:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $recordatorio['descripcion']
                                    );

                                    ?>

                                </p>


                                <div class="recordatorio-info">


                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            $recordatorio['fecha']
                                        );

                                        ?>

                                    </span>


                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            $recordatorio['hora']
                                        );

                                        ?>

                                    </span>


                                </div>


                                <span
                                    class="recordatorio-estado <?php
                                    echo strtolower(
                                        $recordatorio['estado']
                                    );
                                    ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $recordatorio['estado']
                                    );

                                    ?>

                                </span>


                            </div>


                        </article>


                <?php

                    }

                } else {

                ?>


                    <div class="empty-state">


                        <div class="empty-icon">
                        </div>


                        <h3>
                            No hay recordatorios registrados
                        </h3>


                        <p>
                            Cuando registres un recordatorio,
                            aparecerá aquí.
                        </p>


                    </div>


                <?php

                }

                ?>


            </div>


        </section>



        <!-- =================================================
             REGISTRAR RECORDATORIO
        ================================================== -->

        <section class="agregar-section">


            <div class="form-card">


                <div class="section-title">


                    <div>

                        <span class="section-kicker">
                            NUEVO RECORDATORIO
                        </span>


                        <h2>
                             Registrar recordatorio
                        </h2>

                    </div>


                </div>



                <form
                    action="guardar_recordatorio.php"
                    method="POST"
                    class="mascota-form"
                >


                    <!-- MASCOTA -->

                    <div class="form-group">


                        <label for="id_mascota">
                             Mascota
                        </label>


                        <select
                            id="id_mascota"
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

                            ?>


                                <option
                                    value="<?php
                                    echo $mascota['id_mascota'];
                                    ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $mascota['nombre']
                                    );

                                    ?>

                                </option>


                            <?php

                            }

                            ?>


                        </select>


                    </div>



                    <!-- TÍTULO -->

                    <div class="form-group">


                        <label for="titulo">
                             Título
                        </label>


                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            placeholder="Ej. Dar medicamento"
                            required
                        >


                    </div>



                    <!-- DESCRIPCIÓN -->

                    <div class="form-group">


                        <label for="descripcion">
                             Descripción
                        </label>


                        <textarea
                            id="descripcion"
                            name="descripcion"
                            placeholder="Escribe los detalles del recordatorio..."
                        ></textarea>


                    </div>



                    <!-- FECHA Y HORA -->

                    <div class="form-row">


                        <div class="form-group">


                            <label for="fecha">
                                 Fecha
                            </label>


                            <input
                                type="date"
                                id="fecha"
                                name="fecha"
                                required
                            >


                        </div>



                        <div class="form-group">


                            <label for="hora">
                                 Hora
                            </label>


                            <input
                                type="time"
                                id="hora"
                                name="hora"
                            >


                        </div>


                    </div>



                    <!-- ESTADO -->

                    <div class="form-group">


                        <label for="estado">
                             Estado
                        </label>


                        <select
                            id="estado"
                            name="estado"
                            required
                        >


                            <option value="Pendiente">
                                Pendiente
                            </option>


                            <option value="Completado">
                                Completado
                            </option>


                        </select>


                    </div>



                    <!-- BOTÓN -->

                    <button
                        type="submit"
                        class="btn-principal"
                    >

                         Registrar recordatorio

                    </button>


                </form>


            </div>


        </section>



        <!-- =================================================
             VOLVER
        ================================================== -->

        <div class="volver-container">


            <button
                type="button"
                class="btn-secundario"
                onclick="window.location.href='inicio.php'"
            >

                Volver al inicio

            </button>


        </div>


    </main>


</div>



<script src="scrip.js"></script>


</body>

</html>


<?php

$stmt_recordatorios->close();

$stmt_mascotas->close();

$conexion->close();

?>