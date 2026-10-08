<?php

include './conexion.php';


// ========================================
// VERIFICAR QUE SEA POST
// ========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: recuperar_contrasena.php");

    exit();

}


// ========================================
// RECIBIR DATOS
// ========================================

$correo = trim($_POST['correo'] ?? '');

$contrasena = $_POST['contrasena'] ?? '';

$confirmar_contrasena =
    $_POST['confirmar_contrasena'] ?? '';


// ========================================
// VALIDAR CORREO
// ========================================

if ($correo === '') {

    echo "
        <script>
            alert('El correo electrónico es obligatorio.');
            window.location.href =
                'recuperar_contrasena.php';
        </script>
    ";

    exit();

}


if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    echo "
        <script>
            alert('El correo electrónico no es válido.');
            window.location.href =
                'recuperar_contrasena.php';
        </script>
    ";

    exit();

}


// ========================================
// VALIDAR CONTRASEÑA
// ========================================

if ($contrasena === '') {

    echo "
        <script>
            alert('La nueva contraseña es obligatoria.');
            window.location.href =
                'recuperar_contrasena.php';
        </script>
    ";

    exit();

}


if (strlen($contrasena) < 6) {

    echo "
        <script>
            alert('La contraseña debe tener mínimo 6 caracteres.');
            window.location.href =
                'recuperar_contrasena.php';
        </script>
    ";

    exit();

}


// ========================================
// CONFIRMAR CONTRASEÑA
// ========================================

if ($contrasena !== $confirmar_contrasena) {

    echo "
        <script>
            alert('Las contraseñas no coinciden.');
            window.location.href =
                'recuperar_contrasena.php';
        </script>
    ";

    exit();

}


// ========================================
// BUSCAR USUARIO
// ========================================

$sql = "SELECT id_usuario
        FROM usuario
        WHERE correo = ?";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    die("Error al preparar la consulta.");

}


$stmt->bind_param(
    "s",
    $correo
);


$stmt->execute();


$resultado = $stmt->get_result();


// ========================================
// VERIFICAR USUARIO
// ========================================

if ($resultado->num_rows == 0) {

    $stmt->close();

    $conexion->close();

    echo "
        <script>
            alert('El correo electrónico no está registrado.');
            window.location.href =
                'recuperar_contrasena.php';
        </script>
    ";

    exit();

}


$usuario = $resultado->fetch_assoc();

$id_usuario = $usuario['id_usuario'];


$stmt->close();


// ========================================
// ENCRIPTAR NUEVA CONTRASEÑA
// ========================================

$contrasena_hash =
    password_hash(
        $contrasena,
        PASSWORD_DEFAULT
    );


// ========================================
// ACTUALIZAR CONTRASEÑA
// ========================================

$sql = "UPDATE usuario
        SET contrasena = ?
        WHERE id_usuario = ?";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    die("Error al preparar la actualización.");

}


$stmt->bind_param(
    "si",
    $contrasena_hash,
    $id_usuario
);


// ========================================
// GUARDAR
// ========================================

if ($stmt->execute()) {

    $stmt->close();

    $conexion->close();

    echo "
        <script>

            alert(
                'Contraseña actualizada correctamente. Ahora puedes iniciar sesión.'
            );

            window.location.href =
                'login.php';

        </script>
    ";

    exit();

} else {

    $stmt->close();

    $conexion->close();

    echo "
        <script>

            alert(
                'No fue posible actualizar la contraseña.'
            );

            window.location.href =
                'recuperar_contrasena.php';

        </script>
    ";

    exit();

}

?>