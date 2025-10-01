<?php
include("config.php");

header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");

try {
    $pdo = new PDO("mysql:host=$server;dbname=$dbname;charset=utf8mb4", $usr, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ejecutar el algoritmo genético y obtener resultados
    $resultados = ejecutarAlgoritmoGenetico($pdo);
    $productosMasSolicitados = $resultados['mejor_individuo'];
    $datosEntrenamiento = $resultados['datos_entrenamiento'];
    
    // Preparar los datos para el JSON
    $topProductos = [];
    $top = 1;
    foreach ($productosMasSolicitados as $producto => $solicitudes) {
        $topProductos[] = [
            'position' => $top,
            'producto' => $producto,
            'solicitudes' => $solicitudes
        ];
        $top++;
        if ($top > 5) break; // Mostrar solo top 5
    }
    
    $response = [
        'success' => true,
        'message' => 'Datos obtenidos correctamente',
        'data' => $topProductos,
        'training_data' => $datosEntrenamiento,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    echo json_encode($response);
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

function ejecutarAlgoritmoGenetico(PDO $pdo, int $generaciones = 30, int $tamanoPoblacion = 20): array {
    // Obtener todos los productos distintos de la base de datos
    $productos = obtenerProductos($pdo);
    
    // Si hay pocos productos, no necesitamos algoritmo genético
    if (count($productos) <= 5) {
        return [
            'mejor_individuo' => obtenerProductosMasSolicitadosDirectamente($pdo),
            'datos_entrenamiento' => []
        ];
    }
    
    // Inicializar población
    $poblacion = inicializarPoblacion($productos, $tamanoPoblacion);
    
    // Array para almacenar datos de entrenamiento
    $datosEntrenamiento = [];
    
    // Evolucionar la población
    for ($i = 0; $i < $generaciones; $i++) {
        $poblacion = evolucionarPoblacion($poblacion, $pdo);
        
        // Calcular estadísticas para esta generación
        $fitnessValues = array_map(function($individuo) use ($pdo) {
            return calcularFitness($individuo, $pdo);
        }, $poblacion);
        
        $datosEntrenamiento[] = [
            'generacion' => $i + 1,
            'fitness_promedio' => array_sum($fitnessValues) / count($fitnessValues),
            'fitness_mejor' => max($fitnessValues),
            'fitness_peor' => min($fitnessValues),
            'fitness_mediana' => calcularMediana($fitnessValues)
        ];
    }
    
    // Obtener el mejor individuo
    $mejorIndividuo = $poblacion[0];
    $mejorFitness = calcularFitness($mejorIndividuo, $pdo);
    
    foreach ($poblacion as $individuo) {
        $fitness = calcularFitness($individuo, $pdo);
        if ($fitness > $mejorFitness) {
            $mejorIndividuo = $individuo;
            $mejorFitness = $fitness;
        }
    }
    
    // Obtener los productos más solicitados del mejor individuo
    return [
        'mejor_individuo' => obtenerSolicitudesPorProducto($mejorIndividuo, $pdo),
        'datos_entrenamiento' => $datosEntrenamiento
    ];
}

// Función auxiliar para calcular la mediana
function calcularMediana(array $arr): float {
    sort($arr);
    $count = count($arr);
    $middle = floor(($count - 1) / 2);
    
    if ($count % 2) {
        return $arr[$middle];
    } else {
        return ($arr[$middle] + $arr[$middle + 1]) / 2;
    }
}

function cargarProductosDesdeCSV(string $archivo): array {
    $productos = [];
    if ($handle = fopen($archivo, "r")) {
        // Saltar la primera línea (cabecera)
        fgetcsv($handle);
        
        while ($data = fgetcsv($handle)) {
            $productos[] = $data[1]; // Tomar la columna "Product Name"
        }
        fclose($handle);
    }
    return $productos;
}

/**
 * Genera registros de prueba en las tablas documentos y kardex
 */
function generarRegistrosPrueba(PDO $pdo, array $productos, int $totalRegistros): void {
    // Verificar si ya hay datos para no duplicar
    $stmt = $pdo->query("SELECT COUNT(*) FROM documentos");
    if ($stmt->fetchColumn() > 100) {
        return;
    }
    
    // Limpiar tablas para pruebas (opcional, comentar en producción)
    $pdo->exec("TRUNCATE TABLE documentos");
    $pdo->exec("TRUNCATE TABLE kardex");
    
    // Tipos de documento (5 = cotizaciones)
    $tipoDocumento = 5;
    
    // Obtener marcas disponibles
    $stmtMarcas = $pdo->query("SELECT id FROM marcas WHERE estado = 'V'");
    $marcas = $stmtMarcas->fetchAll(PDO::FETCH_COLUMN);
    
    // Generar documentos y sus detalles (kardex)
    for ($i = 1; $i <= $totalRegistros; $i++) {
        $idDocumento = 2000 + $i;
        $idCliente = rand(1, 50);
        $fecha = date('Y-m-d H:i:s', strtotime("-".rand(1, 365)." days"));
        $total = 0;
        
        // Insertar documento
        $stmtDoc = $pdo->prepare("INSERT INTO documentos (id_documento, id_tipo_documento, id_cliente, tipo_cambio, fecha, glosa, descuento, usuario, total, efectivo, cambio, estado) 
                                VALUES (?, ?, ?, 0, ?, 'Cotización generada automáticamente', 0, 1, ?, ?, 0, 'V')");
        
        // Generar entre 1 y 5 productos por documento
        $numProductos = rand(1, 5);
        $clavesProductos = array_rand($productos, $numProductos);
        
        // Asegurarnos de que siempre sea un array
        if (!is_array($clavesProductos)) {
            $clavesProductos = [$clavesProductos];
        }
        
        foreach ($clavesProductos as $clave) {
            $producto = $productos[$clave];
            $cantidad = rand(1, 10);
            $precioUnitario = rand(50, 1000) + (rand(0, 99) / 100);
            $precioTotal = round($cantidad * $precioUnitario, 2);
            $total += $precioTotal;
            $idMarca = $marcas[array_rand($marcas)];
            $marca = devuelve_campo("marcas", "descripcion", "id", $idMarca);
            
            // Insertar en kardex con la nueva estructura
            $stmtKar = $pdo->prepare("INSERT INTO kardex 
                                    (id_documento, id_tipo_documento, producto, descripcion, id_marca, marca, cantidad, precio_unitario, precio_total, descuento) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $stmtKar->execute([
                $idDocumento, 
                $tipoDocumento, 
                $producto, 
                "Descripción de prueba para $producto",
                $idMarca,
                $marca,
                $cantidad, 
                $precioUnitario, 
                $precioTotal
            ]);
        }
        
        // Completar e insertar documento
        $efectivo = $total + rand(0, 100);
        $stmtDoc->execute([$idDocumento, $tipoDocumento, $idCliente, $fecha, $total, $efectivo]);
    }
}

/**
 * Obtiene todos los productos distintos de la base de datos
 */
function obtenerProductos(PDO $pdo): array {
    $stmt = $pdo->query("SELECT DISTINCT producto FROM kardex");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Inicializa una población aleatoria
 */
function inicializarPoblacion(array $productos, int $tamanoPoblacion): array {
    $poblacion = [];
    $numProductos = count($productos);
    
    for ($i = 0; $i < $tamanoPoblacion; $i++) {
        // Seleccionar aleatoriamente algunos productos (entre 1 y todos)
        $cantidad = rand(1, $numProductos);
        shuffle($productos);
        $individuo = array_slice($productos, 0, $cantidad);
        $poblacion[] = $individuo;
    }
    
    return $poblacion;
}

/**
 * Calcula el fitness de un individuo (suma de solicitudes de sus productos)
 */
function calcularFitness(array $individuo, PDO $pdo): float {
    $totalSolicitudes = 0;
    
    foreach ($individuo as $producto) {
        $stmt = $pdo->prepare("SELECT SUM(cantidad) FROM kardex WHERE producto = ?");
        $stmt->execute([$producto]);
        $totalSolicitudes += (float)$stmt->fetchColumn();
    }
    
    return $totalSolicitudes;
}

/**
 * Evoluciona la población mediante selección, cruce y mutación
 */
function evolucionarPoblacion(array $poblacion, PDO $pdo): array {
    // Ordenar población por fitness (mayor a menor)
    usort($poblacion, function($a, $b) use ($pdo) {
        return calcularFitness($b, $pdo) <=> calcularFitness($a, $pdo);
    });
    
    // Seleccionar los mejores (50%)
    $mejores = array_slice($poblacion, 0, (int)(count($poblacion) / 2));
    
    // Cruzar los mejores para crear nueva generación
    $nuevaGeneracion = $mejores;
    
    while (count($nuevaGeneracion) < count($poblacion)) {
        $padre1 = $mejores[array_rand($mejores)];
        $padre2 = $mejores[array_rand($mejores)];
        
        $hijo = cruzar($padre1, $padre2);
        $hijo = mutar($hijo);
        
        $nuevaGeneracion[] = $hijo;
    }
    
    return $nuevaGeneracion;
}

/**
 * Cruza dos individuos para crear un nuevo individuo
 */
function cruzar(array $padre1, array $padre2): array {
    $hijo = array_unique(array_merge($padre1, $padre2));
    return array_values($hijo); // Reindexar
}

/**
 * Aplica mutación a un individuo
 */
function mutar(array $individuo): array {
    // 20% de probabilidad de mutación
    if (rand(1, 100) <= 20) {
        // Eliminar o agregar un producto aleatorio
        if (!empty($individuo) && rand(0, 1)) {
            unset($individuo[array_rand($individuo)]);
            $individuo = array_values($individuo); // Reindexar
        } else {
            // Obtener todos los productos posibles
            global $pdo; // No es la mejor práctica, pero simplifica el ejemplo
            $todosProductos = obtenerProductos($pdo);
            $nuevoProducto = $todosProductos[array_rand($todosProductos)];
            
            if (!in_array($nuevoProducto, $individuo)) {
                $individuo[] = $nuevoProducto;
            }
        }
    }
    
    return $individuo;
}

/**
 * Obtiene las solicitudes por producto para un individuo
 */
function obtenerSolicitudesPorProducto(array $individuo, PDO $pdo): array {
    $resultado = [];
    
    foreach ($individuo as $producto) {
        $stmt = $pdo->prepare("SELECT SUM(cantidad) FROM kardex WHERE producto = ?");
        $stmt->execute([$producto]);
        $solicitudes = (int)$stmt->fetchColumn();
        $resultado[$producto] = $solicitudes;
    }
    
    // Ordenar por solicitudes (mayor a menor)
    arsort($resultado);
    
    return $resultado;
}

/**
 * Método directo para cuando hay pocos productos
 */
function obtenerProductosMasSolicitadosDirectamente(PDO $pdo): array {
    $stmt = $pdo->query("SELECT producto, SUM(cantidad) as total FROM kardex GROUP BY producto ORDER BY total DESC");
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}