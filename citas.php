<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

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
        Citas - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="./estilos.css?v=4"
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

            <a
                href="citas.php"
                class="menu-activo"
            >
                 Citas
            </a>

            <a href="calendario.php">
                 Calendario
            </a>

            <a href="alimentacion.php">
                 Alimentación
            </a>

            <a href="recordatorios.php">
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

    <main class="main-content citas-page">


        <!-- ENCABEZADO -->

        <header class="page-header">

            <div>

                <h2>
                     Citas
                </h2>

                <p>
                    Registra y consulta las citas de tus mascotas.
                </p>

            </div>

        </header>


        <!-- =================================================
             CITAS REGISTRADAS
        ================================================== -->

        <section class="citas-section">

            <div class="section-title">

                <div>

                    <h2>
                         Citas registradas
                    </h2>

                    <p>
                        Consulta las citas programadas para tus mascotas.
                    </p>

                </div>

            </div>


            <div class="citas-grid">

                <?php

                $sql_citas = "SELECT cita.fecha,
                                     cita.hora,
                                     cita.motivo,
                                     cita.lugar,
                                     cita.estado,
                                     mascota.nombre AS mascota
                              FROM cita
                              INNER JOIN mascota
                                  ON cita.id_mascota = mascota.id_mascota
                              WHERE mascota.id_usuario = ?
                              ORDER BY cita.fecha ASC, cita.hora ASC";

                $stmt_citas = $conexion->prepare($sql_citas);

                $stmt_citas->bind_param(
                    "i",
                    $id_usuario
                );

                $stmt_citas->execute();

                $resultado_citas =
                    $stmt_citas->get_result();


                if ($resultado_citas->num_rows > 0) {

                    while (
                        $cita =
                        $resultado_citas->fetch_assoc()
                    ) {

                ?>

                        <div class="cita-card">

                            <div class="cita-icon">
                            </div>

                            <div class="cita-info">

                                <h3>

                                    Cita de
                                    <?php

                                    echo htmlspecialchars(
                                        $cita['mascota']
                                    );

                                    ?>

                                </h3>


                                <p>

                                    <strong>
                                         Fecha:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $cita['fecha']
                                    );

                                    ?>

                                </p>


                                <p>

                                    <strong>
                                         Hora:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $cita['hora']
                                    );

                                    ?>

                                </p>


                                <p>

                                    <strong>
                                         Motivo:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $cita['motivo']
                                    );

                                    ?>

                                </p>


                                <p>

                                    <strong>
                                         Lugar:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $cita['lugar']
                                    );

                                    ?>

                                </p>


                                <span class="cita-estado">

                                    <?php

                                    echo htmlspecialchars(
                                        $cita['estado']
                                    );

                                    ?>

                                </span>

                            </div>

                        </div>

                <?php

                    }

                } else {

                ?>

                    <div class="sin-citas">

                        <div class="sin-citas-icon">
                            
                        </div>

                        <h3>
                            No hay citas registradas
                        </h3>

                        <p>
                            Cuando registres una cita aparecerá aquí.
                        </p>

                    </div>

                <?php

                }

                ?>

            </div>

        </section>


        <!-- =================================================
             REGISTRAR CITA
        ================================================== -->

        <section class="agregar-section">

            <div class="form-card">


                <div class="form-header">

                    <div>

                        <h2>
                             Registrar cita
                        </h2>

                        <p>
                            Agrega una nueva cita para una de tus mascotas.
                        </p>

                    </div>

                </div>


                <form
                    action="guardar_cita.php"
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

                            ?>

                                <option
                                    value="<?php echo $mascota['id_mascota']; ?>"
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


                    <!-- FECHA -->

                    <div class="form-group">

                        <label for="fecha">

                             Fecha de la cita

                        </label>


                        <input
                            type="date"
                            name="fecha"
                            id="fecha"
                            required
                        >

                    </div>


                    <!-- HORA -->

                    <div class="form-group">

                        <label for="hora">

                             Hora

                        </label>


                        <input
                            type="time"
                            name="hora"
                            id="hora"
                            required
                        >

                    </div>


                    <!-- MOTIVO -->

                    <div class="form-group">

                        <label for="motivo">

                             Motivo

                        </label>


                        <input
                            type="text"
                            name="motivo"
                            id="motivo"
                            placeholder="Ej. Consulta veterinaria"
                            required
                        >

                    </div>


                    <!-- LUGAR -->

                    <div class="form-group">

                        <label for="lugar">

                             Lugar

                        </label>


                        <input
                            type="text"
                            name="lugar"
                            id="lugar"
                            placeholder="Ej. Clínica veterinaria"
                        >

                    </div>


                    <!-- ESTADO -->

                    <div class="form-group">

                        <label for="estado">

                             Estado

                        </label>


                        <select
                            name="estado"
                            id="estado"
                        >

                            <option value="Pendiente">
                                Pendiente
                            </option>

                            <option value="Realizada">
                                Realizada
                            </option>

                            <option value="Cancelada">
                                Cancelada
                            </option>

                        </select>

                    </div>


                    <!-- BOTONES -->

                    <div class="form-buttons">

                        <a
                            href="inicio.php"
                            class="btn-cancelar"
                        >
                             Volver al inicio
                        </a>


                        <button
                            type="submit"
                            class="btn-guardar"
                        >
                             Registrar cita
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>


<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script src="scrip.js"></script>


</body>

</html>

<?php

$stmt_mascotas->close();

$conexion->close();

?>
