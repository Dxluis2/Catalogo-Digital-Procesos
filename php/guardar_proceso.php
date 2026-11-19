<?php
require_once __DIR__ . '/conexion.php';
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Recibir datos POST
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$media_tipo = 'ninguno'; // por defecto

// Sanitizar inputs
$titulo = $conexion->real_escape_string($titulo);
$descripcion = $conexion->real_escape_string($descripcion);
$categoria = strtolower($conexion->real_escape_string($categoria));

// Validar campos obligatorios
if (!$titulo || !$descripcion || !$categoria) {
    die("Faltan datos obligatorios.");
}

// Directorio correcto para guardar imágenes (en raíz, no en /php/)
$directorio = "../media/";
$media_url = null;

// Verificar si se subió un archivo
if (isset($_FILES['media_url']) && $_FILES['media_url']['error'] !== UPLOAD_ERR_NO_FILE) {
    $archivo = $_FILES['media_url'];

    // Validar error de carga
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        die("Error al subir el archivo. Código error: " . $archivo['error']);
    }

    // Validar tipo MIME permitido (solo imágenes)
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($archivo['type'], $tiposPermitidos)) {
        die("Solo se permiten imágenes JPG, PNG, GIF o WEBP.");
    }

    // Generar nombre único para archivo
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;

    $rutaDestino = $directorio . $nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        die("Error al mover el archivo subido.");
    }

    // Guardar ruta relativa a la raíz (para mostrar correctamente en HTML)
    $media_url = $conexion->real_escape_string("media/" . $nombreArchivo);
    $media_tipo = 'imagen';
}

// Insertar en la tabla correspondiente
$sql = "INSERT INTO `$categoria` (titulo, descripcion, media_tipo, media_url) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta SQL: " . $conexion->error);
}

$stmt->bind_param("ssss", $titulo, $descripcion, $media_tipo, $media_url);

if ($stmt->execute()) {
    echo "<script>alert('Proceso agregado exitosamente.'); window.location.href='../procesos.html';</script>";
} else {
    echo "<script>alert('Error al guardar el proceso: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conexion->close();
?>
