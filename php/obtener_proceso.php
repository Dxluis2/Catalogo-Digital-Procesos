<?php
require_once __DIR__ . '/conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id = $_GET['id'] ?? null;
$categoria = $_GET['categoria'] ?? '';

if (!$id || !$categoria) {
    echo json_encode(null);
    exit;
}

$categoria = $conexion->real_escape_string($categoria);
$id = intval($id);

$sql = "SELECT * FROM `$categoria` WHERE id = $id LIMIT 1";
$result = $conexion->query($sql);

if ($result && $fila = $result->fetch_assoc()) {
    header('Content-Type: application/json');
    echo json_encode($fila);
} else {
    echo json_encode(null);
}

$conexion->close();
?>
