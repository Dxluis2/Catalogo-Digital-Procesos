<?php
require_once __DIR__ . '/conexion.php';if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id = $_POST['id'] ?? null;
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$media_tipo = 'imagen'; // Solo imagenes permitidas

if (!$id || !$categoria) {
    die("Datos incompletos.");
}

$categoria = $conexion->real_escape_string($categoria);
$titulo = $conexion->real_escape_string($titulo);
$descripcion = $conexion->real_escape_string($descripcion);
$id = intval($id);

$media_urls = [];

// Obtener media_url actual para borrar archivos si cambia
$sqlOld = "SELECT media_url FROM `$categoria` WHERE id = $id";
$resultOld = $conexion->query($sqlOld);
$media_url_old = null;
if ($resultOld && $filaOld = $resultOld->fetch_assoc()) {
    $media_url_old = $filaOld['media_url'];
}

if (isset($_FILES['media_url'])) {
    $directorio = "media/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }
    foreach ($_FILES['media_url']['tmp_name'] as $index => $tmp_name) {
        if ($_FILES['media_url']['error'][$index] === UPLOAD_ERR_OK) {
            $nombre_archivo = basename($_FILES['media_url']['name'][$index]);
            $ruta_archivo = $directorio . time() . "_" . $index . "_" . $nombre_archivo;

            $tipo = mime_content_type($tmp_name);
            if (strpos($tipo, 'image') !== 0) {
                continue; // Saltar si no es imagen
            }

            if (move_uploaded_file($tmp_name, $ruta_archivo)) {
                $media_urls[] = $ruta_archivo;
            }
        }
    }
}

$media_url = $media_url_old;
if (count($media_urls) > 0) {
    // Eliminar archivos viejos
    if ($media_url_old) {
        $urls = explode(',', $media_url_old);
        foreach ($urls as $url) {
            if (file_exists($url)) {
                unlink($url);
            }
        }
    }
    $media_url = implode(',', $media_urls);
}

$stmt = $conexion->prepare("UPDATE `$categoria` SET titulo=?, descripcion=?, media_tipo=?, media_url=? WHERE id=?");
$stmt->bind_param("ssssi", $titulo, $descripcion, $media_tipo, $media_url, $id);

if ($stmt->execute()) {
    echo "<script>alert('Proceso actualizado exitosamente.'); window.location.href='../procesos.html';</script>";
} else {
    echo "<script>alert('Error al actualizar el proceso.'); window.history.back();</script>";
}

$stmt->close();
$conexion->close();
?>
