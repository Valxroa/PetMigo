<?php

session_start();

include './conexion.php';

$nombre = $_POST['nombre'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$especie = trim($_POST['especie']);
$raza = trim($_POST['raza']);
$sexo = $_POST['sexo'];
$peso = $_POST['peso'];


// Buscar la especie
$sql_especie = "SELECT id_especie
                FROM especie
                WHERE TRIM(nombre) = ?";

$stmt_especie = $conexion->prepare($sql_especie);
$stmt_especie->bind_param("s", $especie);
$stmt_especie->execute();

$resultado_especie = $stmt_especie->get_result();

if ($resultado_especie->num_rows == 0) {
    die("La especie no existe en la base de datos.");
}

$datos_especie = $resultado_especie->fetch_assoc();
$id_especie = $datos_especie['id_especie'];


// Buscar la raza perteneciente a esa especie
$sql_raza = "SELECT id_raza
             FROM raza
             WHERE id_especie = ?
             AND TRIM(nombre) = ?";

$stmt_raza = $conexion->prepare($sql_raza);
$stmt_raza->bind_param("is", $id_especie, $raza);
$stmt_raza->execute();

$resultado_raza = $stmt_raza->get_result();

if ($resultado_raza->num_rows == 0) {
    die("La raza no existe para la especie seleccionada.");
}

$datos_raza = $resultado_raza->fetch_assoc();
$id_raza = $datos_raza['id_raza'];


// Obtener el usuario que inició sesión
if (!isset($_SESSION['id_usuario'])) {
    die("No hay un usuario con sesión iniciada.");
}

$id_usuario = $_SESSION['id_usuario'];


// Guardar la mascota
$sql = "INSERT INTO mascota
        (id_usuario, id_raza, nombre, fecha_nacimiento, sexo, peso)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "iisssd",
    $id_usuario,
    $id_raza,
    $nombre,
    $fecha_nacimiento,
    $sexo,
    $peso
);



if ($stmt->execute()) {

    header("Location: mascotas.php");
    exit();

} else {

    echo "Error al registrar la mascota: " . $stmt->error;

}



$stmt->close();
$stmt_raza->close();
$stmt_especie->close();
$conexion->close();

?>