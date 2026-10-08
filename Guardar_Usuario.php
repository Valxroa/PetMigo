<?php

include './conexion.php';

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];
$telefono = $_POST['telefono'];

$contrasena_segura = password_hash($contrasena, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario 
        (nombre, apellido, correo, contrasena, telefono)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "sssss",
    $nombre,
    $apellido,
    $correo,
    $contrasena_segura,
    $telefono
);

if ($stmt->execute()) {

    header("Location: login.php");
    exit();

} else {

    echo "Error al registrar el usuario: " . $stmt->error;

}

$stmt->close();
$conexion->close();

?>