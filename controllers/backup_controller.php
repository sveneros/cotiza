<?php
session_start();
include("conx.php");
include("funciones.php");
include_once("Security.php");

// Configurar encabezados
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configurar zona horaria
date_default_timezone_set('America/La_Paz');

// Configuración de directorio de backups
define('BACKUP_DIR', __DIR__ . '/../backups/');
define('MAX_BACKUP_FILES', 20);

// Crear directorio si no existe
if (!file_exists(BACKUP_DIR)) {
    if (!mkdir(BACKUP_DIR, 0755, true)) {
        die(json_encode(['success' => false, 'message' => 'No se pudo crear el directorio de backups']));
    }
}

// Función para obtener información de la base de datos
function getDatabaseInfo() {
    $link = conectarse();
    
    if (!$link) {
        return ['error' => 'No se pudo conectar a la base de datos'];
    }
    
    // Obtener nombre de la base de datos
    $result = mysqli_query($link, "SELECT DATABASE() as db_name");
    if (!$result) {
        return ['error' => 'Error al obtener nombre de la base de datos'];
    }
    
    $row = mysqli_fetch_assoc($result);
    $db_name = $row['db_name'];
    
    // Obtener información de tablas
    $tables = [];
    $result = mysqli_query($link, "SHOW TABLES");
    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            $tables[] = $row[0];
        }
    }
    
    mysqli_close($link);
    
    return [
        'name' => $db_name,
        'tables' => $tables,
        'count' => count($tables)
    ];
}

// Función para exportar base de datos
function exportDatabase($backupName, $includeDrop = true, $includeData = true) {
    $db_info = getDatabaseInfo();
    
    if (isset($db_info['error'])) {
        return ['success' => false, 'message' => $db_info['error']];
    }
    
    $link = conectarse();
    if (!$link) {
        return ['success' => false, 'message' => 'Error de conexión a la base de datos'];
    }
    
    // Generar nombre de archivo con fecha y hora
    $timestamp = date('Y-m-d_His');
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $backupName) . '_' . $timestamp;
    $sql_file = BACKUP_DIR . $filename . '.sql';
    $gz_file = BACKUP_DIR . $filename . '.sql.gz';
    
    // Abrir archivo SQL
    $handle = @fopen($sql_file, 'w+');
    if (!$handle) {
        mysqli_close($link);
        return ['success' => false, 'message' => 'No se pudo crear el archivo SQL'];
    }
    
    // Escribir encabezado
    fwrite($handle, "-- --------------------------------------------------------\n");
    fwrite($handle, "-- Backup generado automáticamente por el Sistema\n");
    fwrite($handle, "-- Fecha: " . date('Y-m-d H:i:s') . "\n");
    fwrite($handle, "-- Base de Datos: " . $db_info['name'] . "\n");
    fwrite($handle, "-- Total de Tablas: " . $db_info['count'] . "\n");
    fwrite($handle, "-- --------------------------------------------------------\n\n");
    fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
    fwrite($handle, "SET AUTOCOMMIT = 0;\n");
    fwrite($handle, "START TRANSACTION;\n");
    fwrite($handle, "SET time_zone = \"+00:00\";\n\n");
    
    // Exportar cada tabla
    foreach ($db_info['tables'] as $table) {
        // Obtener estructura de la tabla
        $result = mysqli_query($link, "SHOW CREATE TABLE `$table`");
        if (!$result) {
            fwrite($handle, "-- Error al obtener estructura de la tabla `$table`\n\n");
            continue;
        }
        
        $row = mysqli_fetch_assoc($result);
        if (!$row || !isset($row['Create Table'])) {
            fwrite($handle, "-- No se pudo obtener estructura de la tabla `$table`\n\n");
            continue;
        }
        
        if ($includeDrop) {
            fwrite($handle, "--\n");
            fwrite($handle, "-- Estructura de tabla para la tabla `$table`\n");
            fwrite($handle, "--\n\n");
            fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n\n");
        }
        
        fwrite($handle, $row['Create Table'] . ";\n\n");
        
        // Exportar datos si está habilitado
        if ($includeData) {
            $result = mysqli_query($link, "SELECT * FROM `$table`");
            if (!$result) {
                fwrite($handle, "-- Error al obtener datos de la tabla `$table`\n\n");
                continue;
            }
            
            $num_rows = mysqli_num_rows($result);
            
            if ($num_rows > 0) {
                fwrite($handle, "--\n");
                fwrite($handle, "-- Volcado de datos para la tabla `$table`\n");
                fwrite($handle, "--\n\n");
                
                // Obtener información de columnas
                $fields_result = mysqli_query($link, "DESCRIBE `$table`");
                $columns = [];
                if ($fields_result) {
                    while ($field = mysqli_fetch_assoc($fields_result)) {
                        $columns[] = $field['Field'];
                    }
                } else {
                    // Si no se puede obtener la descripción, intentar obtener columnas de otra forma
                    $first_row = mysqli_fetch_assoc($result);
                    mysqli_data_seek($result, 0); // Volver al inicio
                    if ($first_row) {
                        $columns = array_keys($first_row);
                    }
                }
                
                while ($row = mysqli_fetch_assoc($result)) {
                    // Preparar valores
                    $values = [];
                    foreach ($columns as $column) {
                        if (isset($row[$column]) && $row[$column] !== null) {
                            $values[] = "'" . mysqli_real_escape_string($link, $row[$column]) . "'";
                        } else {
                            $values[] = "NULL";
                        }
                    }
                    
                    if (!empty($values)) {
                        fwrite($handle, "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n");
                    }
                }
                fwrite($handle, "\n");
            }
        }
    }
    
    // Escribir pie
    fwrite($handle, "COMMIT;\n");
    fwrite($handle, "-- --------------------------------------------------------\n");
    fwrite($handle, "-- Fin del backup\n");
    fwrite($handle, "-- --------------------------------------------------------\n");
    
    fclose($handle);
    mysqli_close($link);
    
    // Verificar que el archivo SQL se creó correctamente
    if (!file_exists($sql_file) || filesize($sql_file) == 0) {
        if (file_exists($sql_file)) {
            unlink($sql_file);
        }
        return ['success' => false, 'message' => 'El archivo SQL está vacío o no se creó'];
    }
    
    // Comprimir archivo usando PHP si gzip no está disponible
    if (function_exists('gzopen')) {
        // Usar compresión nativa de PHP
        $gz_content = gzencode(file_get_contents($sql_file), 9);
        file_put_contents($gz_file, $gz_content);
    } else {
        // Intentar usar comando del sistema
        exec("gzip -9 \"$sql_file\" 2>&1", $output, $return_var);
        
        if ($return_var !== 0) {
            // Si falla la compresión, renombrar el archivo SQL
            rename($sql_file, $gz_file);
        }
    }
    
    // Verificar que el archivo comprimido existe
    if (!file_exists($gz_file) && file_exists($sql_file)) {
        // Si no se pudo comprimir, usar el SQL directamente
        $gz_file = $sql_file;
    } else if (file_exists($sql_file)) {
        // Eliminar archivo SQL sin comprimir
        unlink($sql_file);
    }
    
    // Verificar tamaño del archivo final
    if (!file_exists($gz_file) || filesize($gz_file) == 0) {
        return ['success' => false, 'message' => 'El archivo de backup está vacío'];
    }
    
    // Registrar en logs
    logs_db("Backup generado: $filename.sql.gz", $_SERVER['PHP_SELF']);
    
    return [
        'success' => true,
        'filename' => $filename . '.sql.gz',
        'size' => formatBytes(@filesize($gz_file) ?: 0),
        'download_url' => '../controllers/backup_controller.php?action=download&file=' . urlencode($filename . '.sql.gz')
    ];
}

// Función para importar base de datos
function importDatabase($filePath, $mode = 'normal') {
    if (!file_exists($filePath)) {
        return ['success' => false, 'message' => 'Archivo no encontrado'];
    }
    
    $link = conectarse();
    if (!$link) {
        return ['success' => false, 'message' => 'Error de conexión a la base de datos'];
    }
    
    // Verificar si es archivo comprimido
    $is_gzipped = (pathinfo($filePath, PATHINFO_EXTENSION) === 'gz');
    
    if ($is_gzipped) {
        if (function_exists('gzopen')) {
            // Descomprimir usando PHP
            $sql_content = gzdecode(file_get_contents($filePath));
            if (!$sql_content) {
                mysqli_close($link);
                return ['success' => false, 'message' => 'Error al descomprimir archivo GZIP'];
            }
        } else {
            // Intentar usar comando del sistema
            $sql_file = BACKUP_DIR . 'temp_' . time() . '.sql';
            exec("gunzip -c \"$filePath\" > \"$sql_file\" 2>&1", $output, $return_var);
            
            if ($return_var !== 0 || !file_exists($sql_file)) {
                mysqli_close($link);
                return ['success' => false, 'message' => 'Error al descomprimir archivo: ' . implode(', ', $output)];
            }
            
            $sql_content = file_get_contents($sql_file);
            unlink($sql_file);
        }
    } else {
        $sql_content = file_get_contents($filePath);
    }
    
    if (empty($sql_content)) {
        mysqli_close($link);
        return ['success' => false, 'message' => 'Archivo SQL vacío o corrupto'];
    }
    
    // Dividir en consultas
    $queries = [];
    $delimiter = ';';
    $current_query = '';
    
    // Manejar DELIMITER correctamente
    $lines = explode("\n", $sql_content);
    $in_procedure = false;
    
    foreach ($lines as $line) {
        $trimmed_line = trim($line);
        
        // Ignorar comentarios
        if (substr($trimmed_line, 0, 2) == '--' || $trimmed_line == '') {
            continue;
        }
        
        // Manejar cambio de delimitador
        if (preg_match('/^DELIMITER\s+([^\s]+)$/i', $trimmed_line, $matches)) {
            $delimiter = $matches[1];
            continue;
        }
        
        $current_query .= $line . "\n";
        
        // Verificar si termina con el delimitador
        if (substr($trimmed_line, -strlen($delimiter)) == $delimiter) {
            $queries[] = substr($current_query, 0, -strlen($delimiter));
            $current_query = '';
        }
    }
    
    // Si queda algo, agregarlo
    if (!empty(trim($current_query))) {
        $queries[] = $current_query;
    }
    
    // Ejecutar consultas
    mysqli_autocommit($link, false);
    $error = false;
    $error_message = '';
    
    foreach ($queries as $index => $query) {
        $query = trim($query);
        
        if (!empty($query)) {
            if ($mode === 'safe') {
                // En modo seguro, omitir DROP TABLE y TRUNCATE
                if (preg_match('/^(DROP\s+TABLE|TRUNCATE\s+TABLE)/i', $query)) {
                    continue;
                }
            }
            
            if (!mysqli_query($link, $query)) {
                $error = true;
                $error_message = "Error en consulta #" . ($index + 1) . ": " . mysqli_error($link) . "\nConsulta: " . substr($query, 0, 100) . "...";
                break;
            }
        }
    }
    
    if ($error) {
        mysqli_rollback($link);
        mysqli_close($link);
        return [
            'success' => false,
            'message' => $error_message
        ];
    } else {
        mysqli_commit($link);
        mysqli_close($link);
        
        // Registrar en logs
        logs_db("Backup importado: " . basename($filePath), $_SERVER['PHP_SELF']);
        
        return [
            'success' => true,
            'message' => 'Base de datos importada exitosamente. ' . count($queries) . ' consultas ejecutadas.'
        ];
    }
}

// Función para listar backups disponibles
function listBackups() {
    $backups = [];
    
    if (!file_exists(BACKUP_DIR)) {
        return ['success' => true, 'backups' => []];
    }
    
    $files = @scandir(BACKUP_DIR);
    if (!$files) {
        return ['success' => true, 'backups' => []];
    }
    
    // Filtrar solo archivos de backup
    $backup_files = [];
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && 
            (preg_match('/\.(sql|gz|gzip)$/i', $file) || 
             preg_match('/backup_.*\.sql(\.gz)?$/i', $file))) {
            $backup_files[] = $file;
        }
    }
    
    // Ordenar por fecha de modificación (más reciente primero)
    usort($backup_files, function($a, $b) {
        return filemtime(BACKUP_DIR . $b) - filemtime(BACKUP_DIR . $a);
    });
    
    foreach ($backup_files as $file) {
        $filepath = BACKUP_DIR . $file;
        if (file_exists($filepath) && is_file($filepath)) {
            $backups[] = [
                'filename' => $file,
                'name' => pathinfo($file, PATHINFO_FILENAME),
                'size' => formatBytes(@filesize($filepath) ?: 0),
                'date' => date('Y-m-d H:i:s', @filemtime($filepath) ?: time()),
                'type' => (preg_match('/\.gz(ip)?$/i', $file)) ? 'GZIP' : 'SQL'
            ];
        }
    }
    
    // Limitar número de backups
    if (count($backups) > MAX_BACKUP_FILES) {
        $excess = array_slice($backups, MAX_BACKUP_FILES);
        foreach ($excess as $old_backup) {
            @unlink(BACKUP_DIR . $old_backup['filename']);
        }
        $backups = array_slice($backups, 0, MAX_BACKUP_FILES);
    }
    
    return ['success' => true, 'backups' => $backups];
}

// Función para formatear bytes a tamaño legible
function formatBytes($bytes, $precision = 2) {
    if ($bytes <= 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Manejar solicitudes
try {
    $action = $_GET['action'] ?? ($_POST['action'] ?? '');
    
    switch ($action) {
        case 'export':
            $backupName = trim($_POST['name'] ?? 'backup');
            if (empty($backupName)) {
                $backupName = 'backup';
            }
            
            $includeDrop = isset($_POST['include_drop']) && $_POST['include_drop'] == 'true';
            $includeData = isset($_POST['include_data']) && $_POST['include_data'] == 'true';
            
            $result = exportDatabase($backupName, $includeDrop, $includeData);
            echo json_encode($result);
            break;
            
        case 'import':
            if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == UPLOAD_ERR_OK) {
                $uploadedFile = $_FILES['backup_file'];
                $mode = $_POST['mode'] ?? 'normal';
                
                // Validar archivo
                $allowedExtensions = ['sql', 'gz', 'gzip'];
                $extension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
                
                if (!in_array($extension, $allowedExtensions)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Formato de archivo no válido. Use .sql, .gz o .gzip'
                    ]);
                    break;
                }
                
                // Validar tamaño máximo (50MB)
                $maxSize = 50 * 1024 * 1024;
                if ($uploadedFile['size'] > $maxSize) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'El archivo es demasiado grande (máximo 50MB)'
                    ]);
                    break;
                }
                
                // Mover archivo a directorio temporal
                $tempFile = BACKUP_DIR . 'temp_import_' . time() . '_' . basename($uploadedFile['name']);
                if (!move_uploaded_file($uploadedFile['tmp_name'], $tempFile)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al subir el archivo'
                    ]);
                    break;
                }
                
                // Importar base de datos
                $result = importDatabase($tempFile, $mode);
                
                // Eliminar archivo temporal
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
                
                echo json_encode($result);
            } else {
                $uploadError = $_FILES['backup_file']['error'] ?? UPLOAD_ERR_NO_FILE;
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
                    UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
                    UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
                    UPLOAD_ERR_NO_TMP_DIR => 'No existe el directorio temporal',
                    UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco',
                    UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo'
                ];
                
                echo json_encode([
                    'success' => false,
                    'message' => $errorMessages[$uploadError] ?? 'Error desconocido al subir el archivo'
                ]);
            }
            break;
            
        case 'list':
            $result = listBackups();
            echo json_encode($result);
            break;
            
        case 'download':
            $filename = $_GET['file'] ?? '';
            $filepath = BACKUP_DIR . $filename;
            
            // Validar que el archivo esté en el directorio de backups
            $real_path = realpath($filepath);
            $real_backup_dir = realpath(BACKUP_DIR);
            
            if (!$real_path || strpos($real_path, $real_backup_dir) !== 0) {
                header('HTTP/1.0 403 Forbidden');
                echo 'Acceso denegado';
                exit;
            }
            
            if (file_exists($filepath) && is_file($filepath)) {
                $filesize = filesize($filepath);
                
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . $filesize);
                
                // Limpiar buffer de salida
                if (ob_get_level()) {
                    ob_end_clean();
                }
                
                readfile($filepath);
                exit;
            } else {
                header('HTTP/1.0 404 Not Found');
                echo 'Archivo no encontrado: ' . htmlspecialchars($filename);
            }
            break;
            
        case 'delete':
            $filename = $_POST['filename'] ?? '';
            $filepath = BACKUP_DIR . $filename;
            
            // Validar seguridad
            $real_path = realpath($filepath);
            $real_backup_dir = realpath(BACKUP_DIR);
            
            if (!$real_path || strpos($real_path, $real_backup_dir) !== 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Acceso denegado'
                ]);
                break;
            }
            
            if (file_exists($filepath) && is_file($filepath)) {
                if (@unlink($filepath)) {
                    logs_db("Backup eliminado: $filename", $_SERVER['PHP_SELF']);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Backup eliminado exitosamente'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No se pudo eliminar el archivo'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Archivo no encontrado'
                ]);
            }
            break;
            
        case 'test':
            // Función para pruebas
            echo json_encode([
                'success' => true,
                'backup_dir' => BACKUP_DIR,
                'backup_dir_exists' => file_exists(BACKUP_DIR),
                'backup_dir_writable' => is_writable(BACKUP_DIR),
                'gzip_available' => function_exists('gzopen'),
                'timestamp' => date('Y-m-d_His')
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida: ' . htmlspecialchars($action)
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log('Error en backup_controller: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>