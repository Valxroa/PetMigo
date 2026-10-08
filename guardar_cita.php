<?php

session_start();

include './conexion.php';


// Verificar que exista una sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];


// Recibir datos del formulario
$id_mascota = $_POST['id_mascota'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$motivo = $_POST['motivo'] ?? '';
$lugar = $_POST['lugar'] ?? '';
$estado = $_POST['estado'] ?? 'Pendiente';


// Verificar que los campos obligatorios estén completos
if (
    empty($id_mascota) ||
    empty($fecha) ||
    empty($hora) ||
    empty($motivo)
) {
    die("Por favor, completa todos los campos obligatorios.");
}


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


// Si la mascota no pertenece al usuario
if ($resultado_mascota->num_rows == 0) {

    die("La mascota seleccionada no pertenece al usuario.");

}


// Guardar la cita
$sql = "INSERT INTO cita
        (id_mascota, fecha, hora, motivo, lugar, estado)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "isssss",
    $id_mascota,
    $fecha,
    $hora,
    $motivo,
    $lugar,
    $estado
);


// Ejecutar registro
if ($stmt->execute()) {

    // Cita guardada correctamente
    header("Location: citas.php");
    exit();

} else {

    echo "Error al registrar la cita: " . $stmt->error;

}


// Cerrar conexiones
$stmt->close();
$stmt_mascota->close();
$conexion->close();

?>