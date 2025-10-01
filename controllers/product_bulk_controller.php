<?php
session_start();

// Establecer headers UTF-8 inmediatamente
header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

// Configurar PHP para UTF-8
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');

include("conx.php");
include("funciones.php");

// Función mejorada para conexión con UTF-8
function conectarse_utf8() {
    $link = conectarse();
    
    // Establecer charset UTF-8 en la conexión
    if ($link) {
        mysqli_set_charset($link, "utf8mb4");
        mysqli_query($link, "SET NAMES 'utf8mb4'");
        mysqli_query($link, "SET CHARACTER SET utf8mb4");
        mysqli_query($link, "SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
    }
    
    return $link;
}

function procesarArchivoExcel($archivoTemp, $usuarioId) {
    $link = conectarse_utf8();
    
    try {
        if (!file_exists($archivoTemp)) {
            throw new Exception("El archivo no existe");
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
        
        if ($fileExtension === 'csv') {
            $rows = parseCSVFile($archivoTemp);
        } else {
            throw new Exception("Solo se permiten archivos CSV. Convierta su archivo Excel a CSV.");
        }
        
        // Validar que hay datos
        if (empty($rows) || count($rows) < 2) {
            throw new Exception("El archivo está vacío o no contiene datos válidos");
        }
        
        // Validar encabezados con flexibilidad para UTF-8
        $encabezadosEsperados = ['Código', 'Nombre', 'Descripción', 'Marca', 'Categoría', 'Precio', 'Stock Actual', 'Stock Mínimo'];
        $encabezadosArchivo = array_slice($rows[0], 0, 8);
        
        $encabezadosValidos = true;
        foreach ($encabezadosEsperados as $index => $encabezado) {
            if (!isset($encabezadosArchivo[$index])) {
                $encabezadosValidos = false;
                break;
            }
            
            $encabezadoArchivo = normalizarTexto($encabezadosArchivo[$index]);
            $encabezadoEsperado = normalizarTexto($encabezado);
            
            if (stripos($encabezadoArchivo, $encabezadoEsperado) === false) {
                $encabezadosValidos = false;
                break;
            }
        }
        
        if (!$encabezadosValidos) {
            throw new Exception("El formato del archivo no es válido. Los encabezados deben ser: " . implode(', ', $encabezadosEsperados));
        }
        
        // Crear registro de carga temporal
        $nombreArchivo = mysqli_real_escape_string($link, basename($_FILES['archivo']['name']));
        mysqli_query($link, "INSERT INTO cargas_temporales (usuario_id, nombre_archivo) VALUES ($usuarioId, '$nombreArchivo')");
        $cargaId = mysqli_insert_id($link);
        
        $productosCorrectos = 0;
        $productosError = 0;
        
        // Procesar filas
        for ($i = 1; $i < count($rows); $i++) {
            $fila = $rows[$i];
            
            // Saltar filas vacías
            if (empty(trim(implode('', $fila)))) {
                continue;
            }
            
            // Obtener y limpiar datos
            $datosFila = array_pad($fila, 8, '');
            
            $codigo = limpiarUTF8(trim($datosFila[0] ?? ''));
            $nombre = limpiarUTF8(trim($datosFila[1] ?? ''));
            $descripcion = limpiarUTF8(trim($datosFila[2] ?? ''));
            $marcaNombre = limpiarUTF8(trim($datosFila[3] ?? ''));
            $categoriaNombre = limpiarUTF8(trim($datosFila[4] ?? ''));
            $puntos = floatval(str_replace(',', '.', $datosFila[5] ?? 0));
            $stockActual = intval($datosFila[6] ?? 0);
            $stockMinimo = intval($datosFila[7] ?? 5);
            
            $errores = [];
            
            // Validaciones
            if (empty($codigo)) {
                $errores[] = "Código es requerido";
            } elseif (strlen($codigo) > 50) {
                $errores[] = "Código demasiado largo (máx. 50 caracteres)";
            }
            
            if (empty($nombre)) {
                $errores[] = "Nombre es requerido";
            } elseif (strlen($nombre) > 250) {
                $errores[] = "Nombre demasiado largo (máx. 250 caracteres)";
            }
            
            if (strlen($descripcion) > 65535) {
                $errores[] = "Descripción demasiado larga";
            }
            
            if (empty($marcaNombre)) {
                $errores[] = "Marca es requerida";
            } elseif (strlen($marcaNombre) > 150) {
                $errores[] = "Marca demasiado larga (máx. 150 caracteres)";
            }
            
            if (empty($categoriaNombre)) {
                $errores[] = "Categoría es requerida";
            } elseif (strlen($categoriaNombre) > 250) {
                $errores[] = "Categoría demasiado larga (máx. 250 caracteres)";
            }
            
            if ($puntos <= 0) $errores[] = "Precio debe ser mayor a 0";
            if ($stockActual < 0) $errores[] = "Stock actual no puede ser negativo";
            if ($stockMinimo < 0) $errores[] = "Stock mínimo no puede ser negativo";
            
            // Procesar marca y categoría si no hay errores
            $idMarca = null;
            $idCategoria = null;
            
            if (empty($errores)) {
                $idMarca = buscarOCrearMarca($marcaNombre, $link);
                if (!$idMarca) {
                    $errores[] = "Error al procesar la marca";
                } else {
                    $idCategoria = buscarOCrearCategoria($categoriaNombre, $idMarca, $link);
                    if (!$idCategoria) {
                        $errores[] = "Error al procesar la categoría";
                    }
                }
                
                // Verificar código único
                if (empty($errores)) {
                    $codigoSafe = mysqli_real_escape_string($link, $codigo);
                    $result = mysqli_query($link, "SELECT id FROM productos WHERE codigo = '$codigoSafe'");
                    if (mysqli_num_rows($result) > 0) {
                        $errores[] = "El código ya existe en la base de datos";
                    }
                }
            }
            
            // Preparar datos para inserción
            $estado = empty($errores) ? 'correcto' : 'error';
            $erroresStr = empty($errores) ? null : implode('; ', $errores);
            
            // Limpiar y escapar datos para UTF-8
            $codigoSafe = mysqli_real_escape_string($link, substr($codigo, 0, 50));
            $nombreSafe = mysqli_real_escape_string($link, substr($nombre, 0, 250));
            $descripcionSafe = mysqli_real_escape_string($link, substr($descripcion, 0, 65535));
            $marcaNombreSafe = mysqli_real_escape_string($link, substr($marcaNombre, 0, 150));
            $categoriaNombreSafe = mysqli_real_escape_string($link, substr($categoriaNombre, 0, 250));
            $erroresSafe = $erroresStr ? "'" . mysqli_real_escape_string($link, substr($erroresStr, 0, 500)) . "'" : "NULL";
            $idMarcaStr = $idMarca ? "'$idMarca'" : "NULL";
            $idCategoriaStr = $idCategoria ? "'$idCategoria'" : "NULL";
            
            $query = "INSERT INTO productos_temporales 
                     (carga_id, codigo, nombre, descripcion, marca_nombre, categoria_nombre, puntos, stock_actual, stock_minimo, estado, errores, id_marca, id_categoria) 
                     VALUES ($cargaId, '$codigoSafe', '$nombreSafe', '$descripcionSafe', '$marcaNombreSafe', '$categoriaNombreSafe', $puntos, $stockActual, $stockMinimo, '$estado', $erroresSafe, $idMarcaStr, $idCategoriaStr)";
            
            if (mysqli_query($link, $query)) {
                if ($estado === 'correcto') {
                    $productosCorrectos++;
                } else {
                    $productosError++;
                }
            } else {
                $productosError++;
                error_log("Error insertando producto temporal: " . mysqli_error($link));
            }
        }
        
        // Actualizar estadísticas
        mysqli_query($link, "UPDATE cargas_temporales SET productos_cargados = $productosCorrectos, productos_error = $productosError WHERE id = $cargaId");
        
        mysqli_close($link);
        
        return [
            'success' => true,
            'carga_id' => $cargaId,
            'productos_correctos' => $productosCorrectos,
            'productos_error' => $productosError
        ];
        
    } catch (Exception $e) {
        if (isset($link)) mysqli_close($link);
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// FUNCIONES DE APROBACIÓN - ESTAS SON LAS QUE FALTABAN

function obtenerCargasTemporales($estado = null) {
    $link = conectarse_utf8();
    
    $where = "";
    if ($estado) {
        $estadoSafe = mysqli_real_escape_string($link, $estado);
        $where = "WHERE c.estado = '$estadoSafe'";
    }
    
    // Nota: Asumiendo que tienes una tabla 'usuarios'. Si no, ajusta esta consulta.
    $query = "SELECT c.*, u.nombre as usuario_nombre, u2.nombre as usuario_aprueba_nombre
              FROM cargas_temporales c
              LEFT JOIN usuarios u ON c.usuario_id = u.id
              LEFT JOIN usuarios u2 ON c.usuario_aprueba = u2.id
              $where
              ORDER BY c.fecha_carga DESC";
    
    // Si no tienes tabla usuarios, usa esta consulta alternativa:
    /*
    $query = "SELECT c.*, 
                     CONCAT('Usuario ', c.usuario_id) as usuario_nombre,
                     CONCAT('Usuario ', c.usuario_aprueba) as usuario_aprueba_nombre
              FROM cargas_temporales c
              $where
              ORDER BY c.fecha_carga DESC";
    */
    
    $result = mysqli_query($link, $query);
    $cargas = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $cargas[] = $row;
    }
    
    mysqli_close($link);
    return $cargas;
}

function obtenerProductosTemporales($cargaId) {
    $link = conectarse_utf8();
    
    $cargaId = intval($cargaId);
    $query = "SELECT pt.*, c.nombre_archivo, c.fecha_carga, c.usuario_id, c.estado as carga_estado
              FROM productos_temporales pt
              LEFT JOIN cargas_temporales c ON pt.carga_id = c.id
              WHERE pt.carga_id = $cargaId 
              ORDER BY pt.estado, pt.id";
    
    $result = mysqli_query($link, $query);
    $productos = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
    
    mysqli_close($link);
    return $productos;
}

function aprobarCarga($cargaId, $usuarioId) {
    $link = conectarse_utf8();
    
    try {
        mysqli_autocommit($link, false);
        
        // Obtener productos correctos de la carga
        $productos = obtenerProductosTemporales($cargaId);
        $productosAprobados = 0;
        $errores = [];
        
        foreach ($productos as $producto) {
            if ($producto['estado'] === 'correcto') {
                // Insertar en productos
                $codigo = mysqli_real_escape_string($link, $producto['codigo']);
                $nombre = mysqli_real_escape_string($link, $producto['nombre']);
                $descripcion = mysqli_real_escape_string($link, $producto['descripcion']);
                
                $query = "INSERT INTO productos (codigo, nombre, descripcion, id_marca, id_categoria, puntos, estado, stock_actual, stock_minimo) 
                         VALUES ('$codigo', '$nombre', '$descripcion', {$producto['id_marca']}, {$producto['id_categoria']}, {$producto['puntos']}, 'V', {$producto['stock_actual']}, {$producto['stock_minimo']})";
                
                if (mysqli_query($link, $query)) {
                    $productoId = mysqli_insert_id($link);
                    $productosAprobados++;
                    
                    // Registrar en auditoría si la tabla existe
                    $datosNuevos = json_encode([
                        'codigo' => $producto['codigo'],
                        'nombre' => $producto['nombre'],
                        'marca' => $producto['marca_nombre'],
                        'categoria' => $producto['categoria_nombre'],
                        'puntos' => $producto['puntos'],
                        'stock_actual' => $producto['stock_actual'],
                        'stock_minimo' => $producto['stock_minimo']
                    ]);
                    
                    // Intentar insertar en auditoría (si la tabla existe)
                    $auditoriaQuery = "INSERT INTO auditoria_productos (producto_id, usuario_id, accion, datos_nuevos, carga_id) 
                                     VALUES ($productoId, $usuarioId, 'carga_masiva', '$datosNuevos', $cargaId)";
                    @mysqli_query($link, $auditoriaQuery); // Usar @ para evitar errores si no existe la tabla
                    
                } else {
                    $errores[] = "Error al insertar producto {$producto['codigo']}: " . mysqli_error($link);
                }
            }
        }
        
        if (empty($errores)) {
            // Actualizar estado de la carga
            mysqli_query($link, "UPDATE cargas_temporales SET estado = 'aprobada', usuario_aprueba = $usuarioId, fecha_aprobacion = NOW() WHERE id = $cargaId");
            mysqli_commit($link);
            
            logs_db("Carga masiva aprobada: ID $cargaId, Productos: $productosAprobados", $_SERVER['PHP_SELF']);
            
            mysqli_close($link);
            
            return [
                'success' => true,
                'productos_aprobados' => $productosAprobados
            ];
        } else {
            mysqli_rollback($link);
            mysqli_close($link);
            
            return [
                'success' => false,
                'error' => implode('; ', $errores)
            ];
        }
        
    } catch (Exception $e) {
        mysqli_rollback($link);
        if (isset($link)) mysqli_close($link);
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function rechazarCarga($cargaId, $usuarioId, $observaciones = '') {
    $link = conectarse_utf8();
    
    $observacionesSafe = mysqli_real_escape_string($link, $observaciones);
    $query = "UPDATE cargas_temporales SET estado = 'rechazada', usuario_aprueba = $usuarioId, fecha_aprobacion = NOW(), observaciones = '$observacionesSafe' WHERE id = $cargaId";
    
    if (mysqli_query($link, $query)) {
        logs_db("Carga masiva rechazada: ID $cargaId", $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => true];
    } else {
        $error = mysqli_error($link);
        mysqli_close($link);
        return ['success' => false, 'error' => $error];
    }
}

// FUNCIONES AUXILIARES

function limpiarUTF8($texto) {
    if (empty($texto)) return $texto;
    
    // Eliminar BOM si existe
    $texto = preg_replace('/^\xEF\xBB\xBF/', '', $texto);
    
    // Convertir a UTF-8 si no lo está
    if (!mb_check_encoding($texto, 'UTF-8')) {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
    }
    
    // Limpiar caracteres inválidos
    $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
    
    return trim($texto);
}

function normalizarTexto($texto) {
    $texto = limpiarUTF8($texto);
    $texto = mb_strtolower($texto, 'UTF-8');
    
    return $texto;
}

function parseCSVFile($filePath) {
    $rows = [];
    
    // Detectar encoding del archivo
    $fileContent = file_get_contents($filePath);
    $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
    
    if ($encoding !== 'UTF-8') {
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
        file_put_contents($filePath, $fileContent);
    }
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        // Leer primera línea para detectar delimitador
        $firstLine = fgets($handle);
        rewind($handle);
        
        $delimiter = detectDelimiter($firstLine);
        
        while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
            // Limpiar cada valor
            $cleanedData = array_map('limpiarUTF8', $data);
            $rows[] = $cleanedData;
        }
        fclose($handle);
    }
    
    return $rows;
}

function detectDelimiter($firstLine) {
    $delimiters = [',', ';', "\t", '|'];
    $counts = [];
    
    foreach ($delimiters as $delimiter) {
        $counts[$delimiter] = count(str_getcsv($firstLine, $delimiter));
    }
    
    arsort($counts);
    foreach ($counts as $delimiter => $count) {
        if ($count > 1) {
            return $delimiter;
        }
    }
    
    return ',';
}

function buscarOCrearMarca($nombreMarca, $link) {
    $nombreMarcaSafe = mysqli_real_escape_string($link, limpiarUTF8($nombreMarca));
    $result = mysqli_query($link, "SELECT id FROM marcas WHERE nombre_marca = '$nombreMarcaSafe' AND estado = 'V'");
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }
    
    // Crear nueva marca
    $codigo = generarCodigoMarca($link);
    $query = "INSERT INTO marcas (codigo, nombre_marca, estado, fecha_registro) 
              VALUES ('$codigo', '$nombreMarcaSafe', 'V', CURDATE())";
    
    if (mysqli_query($link, $query)) {
        return mysqli_insert_id($link);
    }
    
    return false;
}

function buscarOCrearCategoria($nombreCategoria, $idMarca, $link) {
    $nombreCategoriaSafe = mysqli_real_escape_string($link, limpiarUTF8($nombreCategoria));
    $result = mysqli_query($link, "SELECT id FROM categorias WHERE descripcion = '$nombreCategoriaSafe' AND id_marca = $idMarca AND estado = 'V'");
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }
    
    // Crear nueva categoría
    $codigo = generarCodigoCategoria($link, $idMarca);
    $query = "INSERT INTO categorias (id_marca, codigo, descripcion, padre, estado) 
              VALUES ($idMarca, '$codigo', '$nombreCategoriaSafe', 0, 'V')";
    
    if (mysqli_query($link, $query)) {
        return mysqli_insert_id($link);
    }
    
    return false;
}

function generarCodigoMarca($link) {
    $result = mysqli_query($link, "SELECT MAX(CAST(SUBSTRING(codigo, 3) AS UNSIGNED)) as max_num FROM marcas WHERE codigo REGEXP '^MG[0-9]+$'");
    $row = mysqli_fetch_assoc($result);
    $nextNum = ($row['max_num'] ?? 0) + 1;
    return 'MG' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
}

function generarCodigoCategoria($link, $idMarca) {
    $result = mysqli_query($link, "SELECT MAX(CAST(SUBSTRING(codigo, 3) AS UNSIGNED)) as max_num FROM categorias WHERE codigo REGEXP '^CT[0-9]+$' AND id_marca = $idMarca");
    $row = mysqli_fetch_assoc($result);
    $nextNum = ($row['max_num'] ?? 0) + 1;
    return 'CT' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
}

// MANEJO DE LAS SOLICITUDES

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (isset($_FILES['archivo'])) {
        // Procesar archivo Excel/CSV
        $usuarioId = $_SESSION['usuario_id'] ?? 1;
        $result = procesarArchivoExcel($_FILES['archivo']['tmp_name'], $usuarioId);
        echo json_encode($result);
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['accion'])) {
            $usuarioId = $_SESSION['usuario_id'] ?? 1;
            
            if ($data['accion'] === 'aprobar') {
                $result = aprobarCarga($data['carga_id'], $usuarioId);
                echo json_encode($result);
            } elseif ($data['accion'] === 'rechazar') {
                $result = rechazarCarga($data['carga_id'], $usuarioId, $data['observaciones'] ?? '');
                echo json_encode($result);
            }
        }
    }
} elseif ($method == 'GET') {
    if (isset($_GET['carga_id'])) {
        $productos = obtenerProductosTemporales($_GET['carga_id']);
        echo json_encode($productos);
    } else {
        $estado = $_GET['estado'] ?? null;
        $cargas = obtenerCargasTemporales($estado);
        echo json_encode($cargas);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>