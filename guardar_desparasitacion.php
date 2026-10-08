<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    die("No hay un usuario con sesión iniciada.");
}

$id_usuario = $_SESSION['id_usuario'];

$id_mascota = $_POST['id_mascota'];
$producto = $_POST['producto'];
$fecha_aplicacion = $_POST['fecha_aplicacion'];
$proxima_aplicacion = $_POST['proxima_aplicacion'];
$observaciones = $_POST['observaciones'];

$sql_mascota = "SELECT id_mascota
                FROM mascota
                WHERE id_mascota = ?
                AND id_usuario = ?";

$stmt_mascota = $conexion->prepare($sql_mascota);

$stmt_mascota->bind_param(
    "ii",
    $id_mascota,
    $id_usuario
);

$stmt_mascota->execute();

$resultado_mascota = $stmt_mascota->get_result();

if ($resultado_mascota->num_rows == 0) {
    die("La mascota no pertenece al usuario.");
}

$sql = "INSERT INTO desparasitacion
        (id_mascota, producto, fecha_aplicacion, proxima_aplicacion, observaciones)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "issss",
    $id_mascota,
    $producto,
    $fecha_aplicacion,
    $proxima_aplicacion,
    $observaciones
);

if ($stmt->execute()) {

    echo "Desparasitación registrada correctamente";

} else {

    echo "Error al registrar la desparasitación: "
         . $stmt->error;

}

$stmt->close();
$stmt_mascota->close();
$conexion->close();

?>