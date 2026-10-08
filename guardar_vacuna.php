<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    die("No hay un usuario con sesión iniciada.");
}

$id_usuario = $_SESSION['id_usuario'];

$id_mascota = $_POST['id_mascota'];
$nombre_vacuna = $_POST['nombre_vacuna'];
$fecha_aplicacion = $_POST['fecha_aplicacion'];
$proxima_dosis = $_POST['proxima_dosis'];
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

$sql = "INSERT INTO vacuna
        (id_mascota, nombre_vacuna, fecha_aplicacion, proxima_dosis, observaciones)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "issss",
    $id_mascota,
    $nombre_vacuna,
    $fecha_aplicacion,
    $proxima_dosis,
    $observaciones
);


if ($stmt->execute()) {

    header("Location: vacunas.php");
    exit();

} else {

    echo "Error al registrar la vacuna: " . $stmt->error;

}




$stmt->close();
$conexion->close();

?>