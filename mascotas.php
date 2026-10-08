<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include './conexion.php';

$id_usuario = $_SESSION['id_usuario'];

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
        WHERE mascota.id_usuario = ?";

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

    <title>Mis mascotas - PetMigo</title>

    <link
        rel="stylesheet"
        href="estilos.css"
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

            <a
                href="mascotas.php"
                class="menu-activo"
            >
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

    <main class="main-content mascotas-page">


        <!-- ENCABEZADO -->

        <header class="page-header">

            <div>

                <h2>
                     Mis mascotas
                </h2>

                <p>
                    Administra la información y el cuidado
                    de tus mascotas.
                </p>

            </div>


            <a
                href="#agregar-mascota"
                class="btn-agregar"
            >
                 Agregar mascota
            </a>

        </header>


        <!-- =================================================
             MASCOTAS REGISTRADAS
        ================================================== -->

        <section class="mascotas-section">

            <h2>
                Mis mascotas
            </h2>


            <div class="mascotas-grid">


                <?php

                if ($resultado->num_rows > 0) {

                    while (
                        $mascota = $resultado->fetch_assoc()
                    ) {

                ?>

                    <div class="mascota-card">


                        <!-- FOTO / ICONO -->

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


                        <!-- INFORMACIÓN -->

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
                                    Fecha de nacimiento:
                                </strong>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['fecha_nacimiento']
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


                            <p>

                                <strong>
                                    Peso:
                                </strong>

                                <?php

                                echo htmlspecialchars(
                                    $mascota['peso']
                                );

                                ?>

                                kg

                            </p>


                            <!-- BOTÓN VER PERFIL -->

                            <a
                                href="Perfil_Mascota.php?id=<?php echo $mascota['id_mascota']; ?>"
                                class="btn-perfil"
                            >
                                Ver perfil
                            </a>

                        </div>

                    </div>


                <?php

                    }

                } else {

                ?>


                    <!-- SIN MASCOTAS -->

                    <div class="sin-mascotas">

                        <div>
                        </div>

                        <h3>
                            Aún no tienes mascotas registradas
                        </h3>

                        <p>
                            Agrega tu primera mascota para comenzar
                            a llevar el control de sus cuidados.
                        </p>

                        <a
                            href="#agregar-mascota"
                            class="btn-agregar"
                        >
                            + Agregar mascota
                        </a>

                    </div>


                <?php

                }

                ?>


                <!-- TARJETA AGREGAR -->

                <a
                    href="#agregar-mascota"
                    class="agregar-mascota-card"
                >

                    <div class="plus-icon">
                        +
                    </div>

                    <h3>
                        Agregar mascota
                    </h3>

                    <p>
                        Registra una nueva mascota
                    </p>

                </a>


            </div>

        </section>


        <!-- =================================================
             FORMULARIO AGREGAR MASCOTA
        ================================================== -->

        <section
            class="agregar-section"
            id="agregar-mascota"
        >

            <div class="form-card">


                <div class="form-header">

                    <div>

                        <h2>
                             Agregar mascota
                        </h2>

                        <p>
                            Completa la información de tu mascota.
                        </p>

                    </div>

                </div>


                <form
                    action="guardar_mascota.php"
                    method="POST"
                    class="mascota-form"
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
                            placeholder="Nombre de tu mascota"
                            required
                        >

                    </div>


                    <!-- FECHA DE NACIMIENTO -->

                    <div class="form-group">

                        <label for="fecha_nacimiento">
                            Fecha de nacimiento
                        </label>

                        <input
                            type="date"
                            id="fecha_nacimiento"
                            name="fecha_nacimiento"
                        >

                    </div>


                    <!-- ESPECIE -->

                    <div class="form-group">

                        <label for="especie">
                            Especie
                        </label>

                        <select
                            name="especie"
                            id="especie"
                            required
                        >

                            <option value="">
                                Seleccione una especie
                            </option>


                            <?php

                            $sql_especies =
                                "SELECT nombre
                                 FROM especie
                                 ORDER BY nombre";

                            $resultado_especies =
                                $conexion->query(
                                    $sql_especies
                                );


                            while (
                                $especie =
                                $resultado_especies->fetch_assoc()
                            ) {

                            ?>

                                <option
                                    value="<?php echo htmlspecialchars($especie['nombre']); ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $especie['nombre']
                                    );

                                    ?>

                                </option>

                            <?php

                            }

                            ?>

                        </select>

                    </div>


                    <!-- RAZA -->

                    <div class="form-group">

                        <label for="raza">
                            Raza
                        </label>

                        <select
                            name="raza"
                            id="raza"
                            required
                        >

                            <option value="">
                                Seleccione primero una especie
                            </option>

                        </select>

                    </div>


                    <!-- SEXO -->

                    <div class="form-group">

                        <label for="sexo">
                            Sexo
                        </label>

                        <select
                            name="sexo"
                            id="sexo"
                        >

                            <option value="">
                                Seleccione
                            </option>

                            <option value="Macho">
                                Macho
                            </option>

                            <option value="Hembra">
                                Hembra
                            </option>

                        </select>

                    </div>


                    <!-- PESO -->

                    <div class="form-group">

                        <label for="peso">
                            Peso (kg)
                        </label>

                        <input
                            type="number"
                            id="peso"
                            name="peso"
                            step="0.01"
                            min="0.01"
                            placeholder="Ej. 12"
                        >

                    </div>


                    <!-- BOTONES -->

                    <div class="form-buttons">

                        <a
                            href="inicio.php"
                            class="btn-cancelar"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn-guardar"
                        >
                            Guardar mascota
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>


<!-- =====================================================
     SCRIPT PARA LAS RAZAS
====================================================== -->

<script>

const especie =
    document.getElementById('especie');

const raza =
    document.getElementById('raza');


especie.addEventListener(
    'change',
    function () {

        const especieSeleccionada =
            this.value;

        raza.innerHTML =
            '<option value="">Seleccione una raza</option>';


        if (
            especieSeleccionada === 'Perro'
        ) {

            raza.innerHTML += `

                <option value="Labrador">
                    Labrador
                </option>

                <option value="Pastor Alemán">
                    Pastor Alemán
                </option>

                <option value="Bulldog">
                    Bulldog
                </option>

            `;

        }


        else if (
            especieSeleccionada === 'Gato'
        ) {

            raza.innerHTML += `

                <option value="Siamés">
                    Siamés
                </option>

                <option value="Persa">
                    Persa
                </option>

            `;

        }


        else if (
            especieSeleccionada === 'Ave'
        ) {

            raza.innerHTML += `

                <option value="Canario">
                    Canario
                </option>

            `;

        }

    }
);

</script>


</body>

</html>
