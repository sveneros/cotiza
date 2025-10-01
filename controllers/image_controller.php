<?php
session_start();
include("conx.php");
include("funciones.php");

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");

// Configuración de directorios
$uploadDirs = [
    'producto' => '../uploads/productos/',
    'usuario' => '../uploads/usuarios/',
    'categoria' => '../uploads/categorias/',
    'marca' => '../uploads/marcas/'
];

// Crear directorios si no existen
foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

function obtenerTodasImagenes() {
    $link = conectarse();
    if (!$link) {
        return ['success' => false, 'error' => 'Error de conexión a la base de datos'];
    }

    $sql = "SELECT * FROM imagenes ORDER BY fecha_subida DESC";
    $result = mysqli_query($link, $sql);
    $imagenes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['ruta'] = str_replace('../', '', $row['ruta']);
        $imagenes[] = $row;
    }
    
    mysqli_close($link);
    return $imagenes;
}

function subirImagen() {
    global $uploadDirs;
    
    $link = conectarse();
    if (!$link) {
        return ['success' => false, 'error' => 'Error de conexión a la base de datos'];
    }

    // Obtener datos del formulario
    $entidadTipo = mysqli_real_escape_string($link, $_POST['entidad_tipo'] ?? '');
    $entidadId = intval($_POST['entidad_id'] ?? 0);
    
    if (!array_key_exists($entidadTipo, $uploadDirs)) {
        mysqli_close($link);
        return ['success' => false, 'error' => 'Tipo de entidad no válido'];
    }
    
    // Verificar si se recibieron archivos
    if (empty($_FILES) || !isset($_FILES['files'])) {
        mysqli_close($link);
        return ['success' => false, 'error' => 'No se recibieron archivos'];
    }
    
    // Procesar cada archivo
    $resultados = [];
    $fileCount = count($_FILES['files']['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        $name = $_FILES['files']['name'][$i];
        $tmp_name = $_FILES['files']['tmp_name'][$i];
        $error = $_FILES['files']['error'][$i];
        $size = $_FILES['files']['size'][$i];
        
        // Verificar errores de subida
        if ($error !== UPLOAD_ERR_OK) {
            $resultados[] = [
                'success' => false,
                'error' => 'Error en la subida: ' . obtenerMensajeError($error)
            ];
            continue;
        }
        
        // Validar que sea una imagen
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp_name);
        finfo_close($finfo);
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowedTypes)) {
            $resultados[] = [
                'success' => false,
                'error' => 'Tipo de archivo no permitido: ' . $mime
            ];
            continue;
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . strtolower($extension);
        $uploadDir = $uploadDirs[$entidadTipo];
        $uploadPath = $uploadDir . $fileName;
        
        // Mover el archivo
        if (move_uploaded_file($tmp_name, $uploadPath)) {
            // Guardar en base de datos
            $sql = "INSERT INTO imagenes (entidad_tipo, entidad_id, nombre_archivo, ruta, tamano, mime_type) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "sissss", $entidadTipo, $entidadId, $fileName, $uploadPath, $size, $mime);
            
            if (mysqli_stmt_execute($stmt)) {
                $id = mysqli_insert_id($link);
                $resultados[] = [
                    'success' => true,
                    'id' => $id,
                    'nombre' => $fileName,
                    'ruta' => str_replace('../', '', $uploadPath),
                    'tamano' => $size,
                    'mime_type' => $mime,
                    'entidad_tipo' => $entidadTipo,
                    'entidad_id' => $entidadId,
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
            } else {
                $resultados[] = [
                    'success' => false,
                    'error' => 'Error al guardar en base de datos: ' . mysqli_error($link)
                ];
                // Eliminar el archivo subido si falló la DB
                unlink($uploadPath);
            }
            mysqli_stmt_close($stmt);
        } else {
            $resultados[] = [
                'success' => false,
                'error' => 'Error al mover el archivo subido'
            ];
        }
    }
    
    mysqli_close($link);
    return count($resultados) === 1 ? $resultados[0] : $resultados;
}

function obtenerMensajeError($codigo) {
    $errores = [
        UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño permitido',
        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño permitido por el formulario',
        UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
        UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
        UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
        UPLOAD_ERR_EXTENSION => 'Subida detenida por extensión'
    ];
    return $errores[$codigo] ?? 'Error desconocido (Código: ' . $codigo . ')';
}

function eliminarImagen($id) {
    $link = conectarse();
    if (!$link) {
        return ['success' => false, 'error' => 'Error de conexión a la base de datos'];
    }

    // Obtener información de la imagen
    $sql = "SELECT ruta FROM imagenes WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $imagen = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($imagen) {
        // Eliminar archivo físico
        $success = true;
        if (file_exists($imagen['ruta'])) {
            $success = unlink($imagen['ruta']);
        }
        
        // Eliminar registro de la base de datos
        $sql = "DELETE FROM imagenes WHERE id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $dbSuccess = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        mysqli_close($link);
        return ['success' => $success && $dbSuccess];
    }
    
    mysqli_close($link);
    return ['success' => false, 'error' => 'Imagen no encontrada'];
}

function obtenerImagenes($entidadTipo, $entidadId) {
    $link = conectarse();
    if (!$link) {
        return ['success' => false, 'error' => 'Error de conexión a la base de datos'];
    }

    $entidadTipo = mysqli_real_escape_string($link, $entidadTipo);
    $entidadId = intval($entidadId);
    
    $sql = "SELECT * FROM imagenes WHERE entidad_tipo = ? AND entidad_id = ? ORDER BY fecha_subida DESC";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "si", $entidadTipo, $entidadId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $imagenes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['ruta'] = str_replace('../', '', $row['ruta']);
        $imagenes[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    return $imagenes;
}

// Manejo de solicitudes
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['entidad_tipo']) && isset($_GET['entidad_id'])) {
            $imagenes = obtenerImagenes($_GET['entidad_tipo'], $_GET['entidad_id']);
            echo json_encode($imagenes);
        } else {
            // Si no hay parámetros, devolver todas las imágenes
            $imagenes = obtenerTodasImagenes();
            echo json_encode($imagenes);
        }
        break;
        
    case 'POST':
        $response = subirImagen();
        echo json_encode($response);
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id'])) {
            $response = eliminarImagen($data['id']);
            echo json_encode($response);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}