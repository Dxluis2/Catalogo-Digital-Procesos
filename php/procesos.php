<?php
require_once __DIR__ . '/conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$categoria = $_GET['equipo'] ?? '';
$categoria = $conexion->real_escape_string($categoria);

if (!$categoria) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, titulo, descripcion, media_tipo, media_url FROM `$categoria` ORDER BY id DESC";
$resultado = $conexion->query($sql);

$procesos = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $procesos[] = $fila;
    }
}

header('Content-Type: application/json');
echo json_encode($procesos);

$conexion->close();
?>
