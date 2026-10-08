<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    die("No hay un usuario con sesión iniciada.");
}

$id_usuario = $_SESSION['id_usuario'];

$id_mascota = $_POST['id_mascota'];
$fecha = $_POST['fecha'];
$tipo_registro = $_POST['tipo_registro'];
$descripcion = $_POST['descripcion'];
$observaciones = $_POST['observaciones'];


// Verificar que la mascota pertenezca al usuario

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


// Guardar el historial

$sql = "INSERT INTO historial_salud
        (id_mascota, fecha, tipo_registro, descripcion, observaciones)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "issss",
    $id_mascota,
    $fecha,
    $tipo_registro,
    $descripcion,
    $observaciones
);


if ($stmt->execute()) {

    echo "Historial registrado correctamente";

} else {

    echo "Error al registrar el historial: " . $stmt->error;

}


$stmt->close();
$stmt_mascota->close();
$conexion->close();

?>
