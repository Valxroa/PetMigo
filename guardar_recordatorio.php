<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    die("No hay un usuario con sesión iniciada.");
}

$id_usuario = $_SESSION['id_usuario'];

$id_mascota = $_POST['id_mascota'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$estado = $_POST['estado'];


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


// Guardar el recordatorio

$sql = "INSERT INTO recordatorio
        (id_mascota, titulo, descripcion, fecha, hora, estado)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "isssss",
    $id_mascota,
    $titulo,
    $descripcion,
    $fecha,
    $hora,
    $estado
);


if ($stmt->execute()) {

    echo "Recordatorio registrado correctamente";

} else {

    echo "Error al registrar el recordatorio: " . $stmt->error;

}


$stmt->close();
$stmt_mascota->close();
$conexion->close();

?>
