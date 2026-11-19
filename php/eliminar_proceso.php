<?php
require_once __DIR__ . '/conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id = $_POST['id'] ?? null;
$categoria = $_POST['categoria'] ?? '';

if (!$id || !$categoria) {
    die("Datos incompletos.");
}

$categoria = $conexion->real_escape_string($categoria);
$id = intval($id);

// Eliminar archivos media si existen
$sqlMedia = "SELECT media_url FROM `$categoria` WHERE id = $id";
$result = $conexion->query($sqlMedia);
if ($result && $fila = $result->fetch_assoc()) {
    if ($fila['media_url']) {
        $urls = explode(',', $fila['media_url']);
        foreach ($urls as $url) {
            if (file_exists($url)) {
                unlink($url);
            }
        }
    }
}

// Eliminar registro
$sql = "DELETE FROM `$categoria` WHERE id = $id";
if ($conexion->query($sql)) {
    echo "Proceso eliminado correctamente.";
} else {
    echo "Error al eliminar el proceso.";
}

$conexion->close();
?>
