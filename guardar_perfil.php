<?php

session_start();


// ========================================
// VERIFICAR SESIÓN
// ========================================

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.php");

    exit();

}


// ========================================
// VERIFICAR QUE SEA POST
// ========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: perfil.php");

    exit();

}


// ========================================
// CONEXIÓN
// ========================================

include './conexion.php';


// ========================================
// ID DEL USUARIO
// ========================================

$id_usuario = $_SESSION['id_usuario'];


// ========================================
// RECIBIR DATOS
// ========================================

$nombre = trim($_POST['nombre'] ?? '');

$apellido = trim($_POST['apellido'] ?? '');

$correo = trim($_POST['correo'] ?? '');

$telefono = trim($_POST['telefono'] ?? '');


// ========================================
// VALIDAR NOMBRE
// ========================================

if ($nombre === '') {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El nombre es obligatorio.")
    );

    exit();

}


if (strlen($nombre) > 50) {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El nombre no puede superar los 50 caracteres.")
    );

    exit();

}


// ========================================
// VALIDAR APELLIDO
// ========================================

if ($apellido === '') {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El apellido es obligatorio.")
    );

    exit();

}


if (strlen($apellido) > 50) {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El apellido no puede superar los 50 caracteres.")
    );

    exit();

}


// ========================================
// VALIDAR CORREO
// ========================================

if ($correo === '') {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El correo electrónico es obligatorio.")
    );

    exit();

}


if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El correo electrónico no es válido.")
    );

    exit();

}


if (strlen($correo) > 100) {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El correo no puede superar los 100 caracteres.")
    );

    exit();

}


// ========================================
// VALIDAR TELÉFONO
// ========================================

if (strlen($telefono) > 20) {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("El teléfono no puede superar los 20 caracteres.")
    );

    exit();

}


// ========================================
// VERIFICAR QUE EL CORREO NO ESTÉ REPETIDO
// ========================================

$sql = "SELECT id_usuario
        FROM usuario
        WHERE correo = ?
        AND id_usuario <> ?";

$stmt = $conexion->prepare($sql);


if (!$stmt) {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("Error al verificar el correo.")
    );

    exit();

}


$stmt->bind_param(
    "si",
    $correo,
    $id_usuario
);


$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows > 0) {

    $stmt->close();

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("Ese correo electrónico ya está registrado por otro usuario.")
    );

    exit();

}


$stmt->close();


// ========================================
// ACTUALIZAR USUARIO
// ========================================

$sql = "UPDATE usuario
        SET nombre = ?,
            apellido = ?,
            correo = ?,
            telefono = ?
        WHERE id_usuario = ?";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("Error al preparar la actualización.")
    );

    exit();

}


$stmt->bind_param(
    "ssssi",
    $nombre,
    $apellido,
    $correo,
    $telefono,
    $id_usuario
);


// ========================================
// EJECUTAR ACTUALIZACIÓN
// ========================================

if ($stmt->execute()) {

    $stmt->close();

    $conexion->close();

    header(
        "Location: perfil.php?actualizado=1"
    );

    exit();

} else {

    $stmt->close();

    $conexion->close();

    header(
        "Location: perfil.php?editar=1&error=" .
        urlencode("No fue posible actualizar el perfil.")
    );

    exit();

}

?>