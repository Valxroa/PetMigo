<?php

session_start();

include './conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    die("No hay un usuario con sesión iniciada.");
}

$id_usuario = $_SESSION['id_usuario'];

$id_mascota = $_POST['id_mascota'];
$tipo_alimento = $_POST['tipo_alimento'];
$cantidad = $_POST['cantidad'];
$frecuencia = $_POST['frecuencia'];
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


// Guardar la alimentación
$sql = "INSERT INTO alimentacion
        (id_mascota, tipo_alimento, cantidad, frecuencia, observaciones)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "issss",
    $id_mascota,
    $tipo_alimento,
    $cantidad,
    $frecuencia,
    $observaciones
);


if ($stmt->execute()) {

    echo "Alimentación registrada correctamente";

} else {

    echo "Error al registrar la alimentación: " . $stmt->error;

}


$stmt->close();
$stmt_mascota->close();
$conexion->close();

?>