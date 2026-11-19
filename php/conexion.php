
<?php
$host = "localhost";
$usuario = getenv("DB_USER") ?: "root";
$contrasena = getenv("DB_PASSWORD");

if ($contrasena === false) {
    $contrasena = "";
}

$baseDatos = "calidad";

$conexion = new mysqli(
    $host,
    $usuario,
    $contrasena,
    $baseDatos
);

if ($conexion->connect_error) {
    error_log("Error de conexion a MySQL");
    http_response_code(500);
    exit("No se pudo conectar a la base de datos.");
}

$conexion->set_charset("utf8mb4");
?>
