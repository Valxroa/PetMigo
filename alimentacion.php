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
        Alimentación - PetMigo
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

            <a href="citas.php">
                 Citas
            </a>

            <a href="calendario.php">
                 Calendario
            </a>

            <a
                href="alimentacion.php"
                class="menu-activo"
            >
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

    <main class="main-content alimentacion-page">


        <!-- ENCABEZADO -->

        <header class="page-header">

            <div>

                <h2>
                     Alimentación
                </h2>

                <p>
                    Registra y consulta la alimentación de tus mascotas.
                </p>

            </div>

        </header>


        <!-- =================================================
             ALIMENTACIONES REGISTRADAS
        ================================================== -->

        <section class="alimentacion-section">

            <div class="section-title">

                <div>

                    <h2>
                         Alimentaciones registradas
                    </h2>

                    <p>
                        Consulta la alimentación de tus mascotas.
                    </p>

                </div>

            </div>


            <div class="alimentacion-grid">

                <?php

                $sql_alimentacion = "SELECT alimentacion.tipo_alimento,
                                            alimentacion.cantidad,
                                            alimentacion.frecuencia,
                                            alimentacion.observaciones,
                                            mascota.nombre AS mascota
                                     FROM alimentacion
                                     INNER JOIN mascota
                                         ON alimentacion.id_mascota = mascota.id_mascota
                                     WHERE mascota.id_usuario = ?
                                     ORDER BY mascota.nombre";


                $stmt_alimentacion =
                    $conexion->prepare(
                        $sql_alimentacion
                    );


                $stmt_alimentacion->bind_param(
                    "i",
                    $id_usuario
                );


                $stmt_alimentacion->execute();


                $resultado_alimentacion =
                    $stmt_alimentacion->get_result();


                if (
                    $resultado_alimentacion->num_rows > 0
                ) {

                    while (
                        $alimentacion =
                        $resultado_alimentacion->fetch_assoc()
                    ) {

                ?>

                        <div class="alimentacion-card">

                            <div class="alimentacion-icon">
                                
                            </div>


                            <div class="alimentacion-info">

                                <h3>

                                    Alimentación de

                                    <?php

                                    echo htmlspecialchars(
                                        $alimentacion['mascota']
                                    );

                                    ?>

                                </h3>


                                <p>

                                    <strong>
                                         Tipo de alimento:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $alimentacion['tipo_alimento']
                                    );

                                    ?>

                                </p>


                                <p>

                                    <strong>
                                         Cantidad:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $alimentacion['cantidad']
                                    );

                                    ?>

                                </p>


                                <p>

                                    <strong>
                                         Frecuencia:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $alimentacion['frecuencia']
                                    );

                                    ?>

                                </p>


                                <p>

                                    <strong>
                                         Observaciones:
                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $alimentacion['observaciones']
                                    );

                                    ?>

                                </p>

                            </div>

                        </div>

                <?php

                    }

                } else {

                ?>

                    <div class="sin-alimentacion">

                        <div class="sin-alimentacion-icon">
                            
                        </div>

                        <h3>
                            No hay alimentaciones registradas
                        </h3>

                        <p>
                            Cuando registres una alimentación
                            aparecerá aquí.
                        </p>

                    </div>

                <?php

                }

                ?>

            </div>

        </section>


        <!-- =================================================
             REGISTRAR ALIMENTACIÓN
        ================================================== -->

        <section class="agregar-section">

            <div class="form-card">


                <div class="form-header">

                    <div>

                        <h2>
                            Registrar alimentación
                        </h2>

                        <p>
                            Agrega la información de alimentación
                            de una de tus mascotas.
                        </p>

                    </div>

                </div>


                <form
                    action="guardar_alimentacion.php"
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


                    <!-- TIPO DE ALIMENTO -->

                    <div class="form-group">

                        <label for="tipo_alimento">

                             Tipo de alimento

                        </label>


                        <input
                            type="text"
                            name="tipo_alimento"
                            id="tipo_alimento"
                            placeholder="Ej. Concentrado para perros"
                            required
                        >

                    </div>


                    <!-- CANTIDAD -->

                    <div class="form-group">

                        <label for="cantidad">

                             Cantidad

                        </label>


                        <input
                            type="text"
                            name="cantidad"
                            id="cantidad"
                            placeholder="Ej. 200 gramos"
                        >

                    </div>


                    <!-- FRECUENCIA -->

                    <div class="form-group">

                        <label for="frecuencia">

                             Frecuencia

                        </label>


                        <input
                            type="text"
                            name="frecuencia"
                            id="frecuencia"
                            placeholder="Ej. 2 veces al día"
                        >

                    </div>


                    <!-- OBSERVACIONES -->

                    <div class="form-group">

                        <label for="observaciones">

                             Observaciones

                        </label>


                        <textarea
                            name="observaciones"
                            id="observaciones"
                            placeholder="Escribe alguna observación..."
                        ></textarea>

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
                             Registrar alimentación
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>


<script src="scrip.js"></script>


</body>

</html>

<?php

$stmt_mascotas->close();

$conexion->close();

?>
