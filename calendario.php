<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

$id_usuario = $_SESSION['id_usuario'];

/* =========================================================
   FECHA ACTUAL Y MES MOSTRADO
========================================================= */

$mes = isset($_GET['mes']) ? (int) $_GET['mes'] : (int) date('n');
$anio = isset($_GET['anio']) ? (int) $_GET['anio'] : (int) date('Y');

if ($mes < 1 || $mes > 12) {
    $mes = (int) date('n');
}

if ($anio < 2000 || $anio > 2100) {
    $anio = (int) date('Y');
}

/* =========================================================
   DÍAS DEL MES
========================================================= */

$primer_dia = mktime(0, 0, 0, $mes, 1, $anio);
$dias_mes = date('t', $primer_dia);

/*
   date('N'):
   1 = lunes
   2 = martes
   ...
   7 = domingo
*/
$dia_semana_inicio = (int) date('N', $primer_dia);

/* =========================================================
   NOMBRES DE LOS MESES
========================================================= */

$nombres_meses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];

$nombre_mes = $nombres_meses[$mes];

/* =========================================================
   FECHA SELECCIONADA
========================================================= */

$fecha_seleccionada = isset($_GET['fecha'])
    ? $_GET['fecha']
    : date('Y-m-d');

/*
   Si la fecha seleccionada no pertenece al mes mostrado,
   seleccionamos el primer día del mes.
*/
if (
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_seleccionada)
    ||
    date('Y-m', strtotime($fecha_seleccionada)) !== sprintf('%04d-%02d', $anio, $mes)
) {
    $fecha_seleccionada = sprintf(
        '%04d-%02d-01',
        $anio,
        $mes
    );
}

/* =========================================================
   FECHA DE HOY
========================================================= */

$fecha_hoy = date('Y-m-d');

/* =========================================================
   CONSULTAR CITAS DEL MES
========================================================= */

$citas_por_fecha = [];

$sql_citas = "SELECT
                  c.id_cita,
                  c.fecha,
                  c.hora,
                  c.motivo,
                  c.lugar,
                  c.estado,
                  m.nombre AS mascota
              FROM cita c
              INNER JOIN mascota m
                  ON c.id_mascota = m.id_mascota
              WHERE m.id_usuario = ?
              AND MONTH(c.fecha) = ?
              AND YEAR(c.fecha) = ?
              ORDER BY c.fecha ASC, c.hora ASC";

$stmt_citas = $conexion->prepare($sql_citas);

if ($stmt_citas) {

    $stmt_citas->bind_param(
        "iii",
        $id_usuario,
        $mes,
        $anio
    );

    $stmt_citas->execute();

    $resultado_citas = $stmt_citas->get_result();

    while ($cita = $resultado_citas->fetch_assoc()) {

        $fecha = $cita['fecha'];

        if (!isset($citas_por_fecha[$fecha])) {
            $citas_por_fecha[$fecha] = [];
        }

        $citas_por_fecha[$fecha][] = $cita;
    }

    $stmt_citas->close();
}

/* =========================================================
   CONSULTAR RECORDATORIOS DEL MES
========================================================= */

$recordatorios_por_fecha = [];

$sql_recordatorios = "SELECT
                           r.id_recordatorio,
                           r.fecha,
                           r.hora,
                           r.titulo,
                           r.descripcion,
                           r.estado,
                           m.nombre AS mascota
                       FROM recordatorio r
                       INNER JOIN mascota m
                           ON r.id_mascota = m.id_mascota
                       WHERE m.id_usuario = ?
                       AND MONTH(r.fecha) = ?
                       AND YEAR(r.fecha) = ?
                       ORDER BY r.fecha ASC, r.hora ASC";

$stmt_recordatorios = $conexion->prepare($sql_recordatorios);

if ($stmt_recordatorios) {

    $stmt_recordatorios->bind_param(
        "iii",
        $id_usuario,
        $mes,
        $anio
    );

    $stmt_recordatorios->execute();

    $resultado_recordatorios = $stmt_recordatorios->get_result();

    while ($recordatorio = $resultado_recordatorios->fetch_assoc()) {

        $fecha = $recordatorio['fecha'];

        if (!isset($recordatorios_por_fecha[$fecha])) {
            $recordatorios_por_fecha[$fecha] = [];
        }

        $recordatorios_por_fecha[$fecha][] = $recordatorio;
    }

    $stmt_recordatorios->close();
}

/* =========================================================
   ACTIVIDADES DEL DÍA SELECCIONADO
========================================================= */

$citas_dia = $citas_por_fecha[$fecha_seleccionada] ?? [];

$recordatorios_dia = $recordatorios_por_fecha[$fecha_seleccionada] ?? [];

/* =========================================================
   NOMBRE DEL DÍA SELECCIONADO
========================================================= */

$dias_semana = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];

$numero_dia_seleccionado = (int) date(
    'j',
    strtotime($fecha_seleccionada)
);

$numero_dia_semana = (int) date(
    'N',
    strtotime($fecha_seleccionada)
);

$nombre_dia_seleccionado = $dias_semana[$numero_dia_semana];

/* =========================================================
   FORMATEAR HORA
========================================================= */

function formatear_hora($hora)
{
    if (!$hora) {
        return '';
    }

    return date(
        'g:i A',
        strtotime($hora)
    );
}

/* =========================================================
   NAVEGACIÓN ENTRE MESES
========================================================= */

$mes_anterior = $mes - 1;
$anio_anterior = $anio;

if ($mes_anterior < 1) {
    $mes_anterior = 12;
    $anio_anterior--;
}

$mes_siguiente = $mes + 1;
$anio_siguiente = $anio;

if ($mes_siguiente > 12) {
    $mes_siguiente = 1;
    $anio_siguiente++;
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

    <title>Calendario - PetMigo</title>

    <link rel="stylesheet" href="estilos.css">

</head>

<body>

    <!-- =====================================================
         BARRA LATERAL
    ====================================================== -->

    <aside class="sidebar">

        <div class="sidebar-logo">
            <h1>PetMigo </h1>
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

            <a href="calendario.php" class="menu-activo">
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

    <main class="main-content calendario-page">

        <!-- =================================================
             ENCABEZADO
        ================================================== -->

        <div class="page-header">

            <div>

                <h1>
                    Calendario
                </h1>

                <p>
                    Selecciona una fecha para ver tus actividades.
                </p>

            </div>

        </div>


        <!-- =================================================
             CALENDARIO
        ================================================== -->

        <section class="calendario-section">

            <div class="calendario-card">

                <!-- ENCABEZADO DEL MES -->

                <div class="calendario-header">

                    <a
                        href="calendario.php?mes=<?php echo $mes_anterior; ?>&anio=<?php echo $anio_anterior; ?>"
                        class="calendario-navegacion"
                        title="Mes anterior"
                    >
                        ‹
                    </a>


                    <h2>
                        <?php echo $nombre_mes . ' ' . $anio; ?>
                    </h2>


                    <a
                        href="calendario.php?mes=<?php echo $mes_siguiente; ?>&anio=<?php echo $anio_siguiente; ?>"
                        class="calendario-navegacion"
                        title="Mes siguiente"
                    >
                        ›
                    </a>

                </div>


                <!-- DÍAS DE LA SEMANA -->

                <div class="calendario-semana">

                    <div>L</div>
                    <div>M</div>
                    <div>M</div>
                    <div>J</div>
                    <div>V</div>
                    <div>S</div>
                    <div>D</div>

                </div>


                <!-- DÍAS -->

                <div class="calendario-dias">

                    <?php

                    /*
                       Espacios antes del primer día
                    */

                    for (
                        $i = 1;
                        $i < $dia_semana_inicio;
                        $i++
                    ):

                    ?>

                        <div class="dia-calendario dia-vacio"></div>

                    <?php

                    endfor;


                    /*
                       Días del mes
                    */

                    for (
                        $dia = 1;
                        $dia <= $dias_mes;
                        $dia++
                    ):

                        $fecha_dia = sprintf(
                            '%04d-%02d-%02d',
                            $anio,
                            $mes,
                            $dia
                        );

                        $tiene_citas =
                            isset($citas_por_fecha[$fecha_dia])
                            &&
                            count($citas_por_fecha[$fecha_dia]) > 0;

                        $tiene_recordatorios =
                            isset($recordatorios_por_fecha[$fecha_dia])
                            &&
                            count($recordatorios_por_fecha[$fecha_dia]) > 0;

                        $tiene_eventos =
                            $tiene_citas || $tiene_recordatorios;

                        $es_hoy =
                            $fecha_dia === $fecha_hoy;

                        $es_seleccionado =
                            $fecha_dia === $fecha_seleccionada;

                        $clases_dia = 'dia-calendario';

                        if ($es_hoy) {
                            $clases_dia .= ' dia-hoy';
                        }

                        if ($tiene_eventos) {
                            $clases_dia .= ' dia-con-eventos';
                        }

                        if ($es_seleccionado) {
                            $clases_dia .= ' dia-seleccionado';
                        }

                    ?>

                        <a
                            href="calendario.php?mes=<?php echo $mes; ?>&anio=<?php echo $anio; ?>&fecha=<?php echo $fecha_dia; ?>"
                            class="<?php echo $clases_dia; ?>"
                        >

                            <span class="numero-dia">
                                <?php echo $dia; ?>
                            </span>


                            <div class="indicadores-dia">

                                <?php if ($tiene_citas): ?>

                                    <span
                                        class="indicador-cita"
                                        title="Tiene citas"
                                    >
                                    </span>

                                <?php endif; ?>


                                <?php if ($tiene_recordatorios): ?>

                                    <span
                                        class="indicador-recordatorio"
                                        title="Tiene recordatorios"
                                    >
                                    </span>

                                <?php endif; ?>

                            </div>

                        </a>

                    <?php

                    endfor;

                    ?>

                </div>


                <!-- =================================================
                     REFERENCIAS
                ================================================== -->

                <div class="calendario-referencias">

                    <div>

                        <span></span>

                        <span>
                            Cita
                        </span>

                    </div>


                    <div>

                        <span></span>

                        <span>
                            Recordatorio
                        </span>

                    </div>

                </div>

            </div>

        </section>


        <!-- =================================================
             ACTIVIDADES DE LA FECHA SELECCIONADA
        ================================================== -->

        <section class="calendario-actividades">

            <div class="section-title">

                <h2>

                    <?php
                    echo $nombre_dia_seleccionado
                        . ' '
                        . $numero_dia_seleccionado
                        . ' de '
                        . strtolower($nombre_mes);
                    ?>

                </h2>

                <p>
                    Actividades programadas para este día.
                </p>

            </div>


            <div class="calendario-lista">

                <!-- =================================================
                     CITAS
                ================================================== -->

                <?php foreach ($citas_dia as $cita): ?>

                    <div class="calendario-actividad cita-actividad">

                        <div class="actividad-tipo-icono">
                        </div>


                        <div class="actividad-detalle">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $cita['motivo']
                                );
                                ?>
                            </h3>

                            <p>
                                <?php
                                echo htmlspecialchars(
                                    $cita['mascota']
                                );
                                ?>
                            </p>


                            <div class="actividad-datos">

                                <?php if (!empty($cita['hora'])): ?>

                                    <span>
                                        <?php
                                        echo formatear_hora(
                                            $cita['hora']
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>


                                <?php if (!empty($cita['lugar'])): ?>

                                    <span>
                                        <?php
                                        echo htmlspecialchars(
                                            $cita['lugar']
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>


                        <span class="actividad-estado">

                            <?php
                            echo htmlspecialchars(
                                $cita['estado']
                            );
                            ?>

                        </span>

                    </div>

                <?php endforeach; ?>


                <!-- =================================================
                     RECORDATORIOS
                ================================================== -->

                <?php foreach ($recordatorios_dia as $recordatorio): ?>

                    <div class="calendario-actividad recordatorio-actividad">

                        <div class="actividad-tipo-icono">
                        </div>


                        <div class="actividad-detalle">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $recordatorio['titulo']
                                );
                                ?>
                            </h3>


                            <p>
                                🐾
                                <?php
                                echo htmlspecialchars(
                                    $recordatorio['mascota']
                                );
                                ?>
                            </p>


                            <?php if (!empty($recordatorio['descripcion'])): ?>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $recordatorio['descripcion']
                                    );
                                    ?>
                                </p>

                            <?php endif; ?>


                            <div class="actividad-datos">

                                <?php if (!empty($recordatorio['hora'])): ?>

                                    <span>
                                        <?php
                                        echo formatear_hora(
                                            $recordatorio['hora']
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>


                        <span class="actividad-estado pendiente">

                            <?php
                            echo htmlspecialchars(
                                $recordatorio['estado']
                            );
                            ?>

                        </span>

                    </div>

                <?php endforeach; ?>


                <!-- =================================================
                     SI NO HAY ACTIVIDADES
                ================================================== -->

                <?php if (
                    count($citas_dia) === 0
                    &&
                    count($recordatorios_dia) === 0
                ): ?>

                    <div class="actividad-vacia">

                        <span>
                        </span>

                        <p>
                            No hay actividades programadas para este día.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>


    <script src="scrip.js"></script>

</body>

</html>

<?php

$conexion->close();

?>