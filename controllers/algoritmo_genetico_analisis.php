<?php
include("config.php");
include("conx.php");
include("funciones.php");
header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    $pdo = new PDO("mysql:host=$server;dbname=$dbname;charset=utf8mb4", $usr, $pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ejecutar todos los algoritmos genéticos
    $response['data'] = [
        'cliente_mas_cotizaciones' => ejecutarAlgoritmoClienteMasCotizaciones($pdo),
        'cliente_menos_cotizaciones' => ejecutarAlgoritmoClienteMenosCotizaciones($pdo),
        'marca_mas_cotizada' => ejecutarAlgoritmoMarcaMasCotizada($pdo),
        'marca_menos_cotizada' => ejecutarAlgoritmoMarcaMenosCotizada($pdo),
    ];
    
    $response['success'] = true;
    $response['message'] = 'Análisis completado correctamente';
    
} catch (PDOException $e) {
    $response['message'] = "Error de conexión: " . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);

/**
 * Algoritmo genético para encontrar el cliente con más cotizaciones
 */
// Reemplaza las llamadas a devuelve_campo con esta función PDO
function obtenerNombreCliente(PDO $pdo, int $idCliente): string {
    $stmt = $pdo->prepare("SELECT nombre, apellido1, apellido2 FROM clientes WHERE id = ?");
    $stmt->execute([$idCliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cliente) {
        return trim($cliente['nombre'] . " " . $cliente['apellido1'] . " " . $cliente['apellido2']);
    }
    
    return '';
}

function obtenerNombreMarca(PDO $pdo, int $idMarca): string {
    $stmt = $pdo->prepare("SELECT nombre_marca FROM marcas WHERE id = ?");
    $stmt->execute([$idMarca]);
    $marca = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $marca ? $marca['nombre_marca'] : '';
}

function ejecutarAlgoritmoClienteMasCotizaciones(PDO $pdo, int $generaciones = 30, int $tamanoPoblacion = 15): array {
    $clientes = obtenerClientesUnicos($pdo);
    
    if (count($clientes) <= 5) {
        return obtenerClienteMasCotizacionesDirectamente($pdo);
    }
    
    $poblacion = inicializarPoblacionClientes($clientes, $tamanoPoblacion);
    
    for ($i = 0; $i < $generaciones; $i++) {
        $poblacion = evolucionarPoblacionClientes($poblacion, $pdo, 'max');
    }
    
    $nombreCompleto = obtenerNombreCliente($pdo, $poblacion[0]);
    return ['cliente' => $nombreCompleto, 'cotizaciones' => contarCotizacionesCliente($poblacion[0], $pdo)];
}

/**
 * Algoritmo genético para encontrar el cliente con menos cotizaciones
 */
function ejecutarAlgoritmoClienteMenosCotizaciones(PDO $pdo, int $generaciones = 30, int $tamanoPoblacion = 15): array {
    $clientes = obtenerClientesUnicos($pdo);
    
    if (count($clientes) <= 5) {
        return obtenerClienteMenosCotizacionesDirectamente($pdo);
    }
    
    $poblacion = inicializarPoblacionClientes($clientes, $tamanoPoblacion);
    
    for ($i = 0; $i < $generaciones; $i++) {
        $poblacion = evolucionarPoblacionClientes($poblacion, $pdo, 'min');
    }
    
    $nombreCompleto = obtenerNombreCliente($pdo, $poblacion[0]);
    return ['cliente' => $nombreCompleto, 'cotizaciones' => contarCotizacionesCliente($poblacion[0], $pdo)];
}

/**
 * Algoritmo genético para encontrar la marca más cotizada
 */
function ejecutarAlgoritmoMarcaMasCotizada(PDO $pdo, int $generaciones = 30, int $tamanoPoblacion = 15): array {
    $marcas = obtenerMarcasUnicas($pdo);
    
    if (count($marcas) <= 5) {
        return obtenerMarcaMasCotizadaDirectamente($pdo);
    }
    
    $poblacion = inicializarPoblacionMarcas($marcas, $tamanoPoblacion);
    
    for ($i = 0; $i < $generaciones; $i++) {
        $poblacion = evolucionarPoblacionMarcas($poblacion, $pdo, 'max');
    }
    
    $descripcion = obtenerNombreMarca($pdo, $poblacion[0]);
    return ['marca' => $descripcion, 'cotizaciones' => contarCotizacionesMarca($poblacion[0], $pdo)];
}

/**
 * Algoritmo genético para encontrar la marca menos cotizada
 */
function ejecutarAlgoritmoMarcaMenosCotizada(PDO $pdo, int $generaciones = 30, int $tamanoPoblacion = 15): array {
    $marcas = obtenerMarcasUnicas($pdo);
    
    if (count($marcas) <= 5) {
        return obtenerMarcaMenosCotizadaDirectamente($pdo);
    }
    
    $poblacion = inicializarPoblacionMarcas($marcas, $tamanoPoblacion);
    
    for ($i = 0; $i < $generaciones; $i++) {
        $poblacion = evolucionarPoblacionMarcas($poblacion, $pdo, 'min');
    }
    
     $descripcion = obtenerNombreMarca($pdo, $poblacion[0]);
    return ['marca' => $descripcion, 'cotizaciones' => contarCotizacionesMarca($poblacion[0], $pdo)];
}

/* FUNCIONES AUXILIARES COMUNES */
function obtenerClientesUnicos(PDO $pdo): array {
    $stmt = $pdo->query("SELECT DISTINCT id_cliente FROM documentos");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function obtenerMarcasUnicas(PDO $pdo): array {
    $stmt = $pdo->query("SELECT DISTINCT id_marca FROM kardex WHERE id_marca > 0");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function contarCotizacionesCliente(int $idCliente, PDO $pdo): int {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM documentos WHERE id_cliente = ?");
    $stmt->execute([$idCliente]);
    return (int)$stmt->fetchColumn();
}

function contarCotizacionesMarca(int $idMarca, PDO $pdo): int {
    $stmt = $pdo->prepare("SELECT COUNT(k.id) 
                          FROM kardex k
                          JOIN documentos d ON k.id_documento = d.id_documento
                          WHERE k.id_marca = ?");
    $stmt->execute([$idMarca]);
    return (int)$stmt->fetchColumn();
}

/* FUNCIONES DE INICIALIZACIÓN Y EVOLUCIÓN */
function inicializarPoblacionClientes(array $clientes, int $tamanoPoblacion): array {
    $poblacion = [];
    for ($i = 0; $i < $tamanoPoblacion; $i++) {
        $poblacion[] = $clientes[array_rand($clientes)];
    }
    return $poblacion;
}

function inicializarPoblacionMarcas(array $marcas, int $tamanoPoblacion): array {
    $poblacion = [];
    for ($i = 0; $i < $tamanoPoblacion; $i++) {
        $poblacion[] = $marcas[array_rand($marcas)];
    }
    return $poblacion;
}

function evolucionarPoblacionClientes(array $poblacion, PDO $pdo, string $tipo = 'max'): array {
    usort($poblacion, function($a, $b) use ($pdo, $tipo) {
        $cotizacionesA = contarCotizacionesCliente($a, $pdo);
        $cotizacionesB = contarCotizacionesCliente($b, $pdo);
        return ($tipo == 'max') ? ($cotizacionesB <=> $cotizacionesA) : ($cotizacionesA <=> $cotizacionesB);
    });
    
    $mejores = array_slice($poblacion, 0, (int)(count($poblacion) / 2));
    
    $nuevaGeneracion = $mejores;
    $todosClientes = obtenerClientesUnicos($pdo);
    
    while (count($nuevaGeneracion) < count($poblacion)) {
        if (rand(1, 100) <= 80 && count($mejores) >= 2) {
            $padre1 = $mejores[array_rand($mejores)];
            $padre2 = $mejores[array_rand($mejores)];
            $hijo = (rand(0, 1)) ? $padre1 : $padre2;
        } else {
            $hijo = $todosClientes[array_rand($todosClientes)];
        }
        
        if (rand(1, 100) <= 10) {
            $hijo = $todosClientes[array_rand($todosClientes)];
        }
        
        $nuevaGeneracion[] = $hijo;
    }
    
    return $nuevaGeneracion;
}

function evolucionarPoblacionMarcas(array $poblacion, PDO $pdo, string $tipo = 'max'): array {
    usort($poblacion, function($a, $b) use ($pdo, $tipo) {
        $cotizacionesA = contarCotizacionesMarca($a, $pdo);
        $cotizacionesB = contarCotizacionesMarca($b, $pdo);
        return ($tipo == 'max') ? ($cotizacionesB <=> $cotizacionesA) : ($cotizacionesA <=> $cotizacionesB);
    });
    
    $mejores = array_slice($poblacion, 0, (int)(count($poblacion) / 2));
    
    $nuevaGeneracion = $mejores;
    $todasMarcas = obtenerMarcasUnicas($pdo);
    
    while (count($nuevaGeneracion) < count($poblacion)) {
        if (rand(1, 100) <= 80 && count($mejores) >= 2) {
            $padre1 = $mejores[array_rand($mejores)];
            $padre2 = $mejores[array_rand($mejores)];
            $hijo = (rand(0, 1)) ? $padre1 : $padre2;
        } else {
            $hijo = $todasMarcas[array_rand($todasMarcas)];
        }
        
        if (rand(1, 100) <= 15) {
            $hijo = $todasMarcas[array_rand($todasMarcas)];
        }
        
        $nuevaGeneracion[] = $hijo;
    }
    
    if (count(array_unique($nuevaGeneracion)) < 2 && count($todasMarcas) > 1) {
        $nuevaGeneracion[array_rand($nuevaGeneracion)] = $todasMarcas[array_rand($todasMarcas)];
    }
    
    return $nuevaGeneracion;
}

/* FUNCIONES DIRECTAS PARA CASOS CON POCOS DATOS */
function obtenerClienteMasCotizacionesDirectamente(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id_cliente, COUNT(*) as cotizaciones 
                        FROM documentos 
                        GROUP BY id_cliente 
                        ORDER BY cotizaciones DESC 
                        LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $nombre = devuelve_campo("clientes","nombre","id",$result['id_cliente']);
        $apellido1 = devuelve_campo("clientes","apellido1","id",$result['id_cliente']);
        $apellido2 = devuelve_campo("clientes","apellido2","id",$result['id_cliente']);
        return ['cliente' => $nombre . " ". $apellido1. " ". $apellido2, 'cotizaciones' => $result['cotizaciones']];
    }
    return ['cliente' => '', 'cotizaciones' => 0];
}

function obtenerClienteMenosCotizacionesDirectamente(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id_cliente, COUNT(*) as cotizaciones 
                        FROM documentos 
                        GROUP BY id_cliente 
                        ORDER BY cotizaciones ASC 
                        LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $nombre = devuelve_campo("clientes","nombre","id",$result['id_cliente']);
        $apellido1 = devuelve_campo("clientes","apellido1","id",$result['id_cliente']);
        $apellido2 = devuelve_campo("clientes","apellido2","id",$result['id_cliente']);
        return ['cliente' => $nombre . " ". $apellido1. " ". $apellido2, 'cotizaciones' => $result['cotizaciones']];
    }
    return ['cliente' => '', 'cotizaciones' => 0];
}

function obtenerMarcaMasCotizadaDirectamente(PDO $pdo): array {
    $stmt = $pdo->query("SELECT k.id_marca, m.nombre_marca as marca, COUNT(*) as cotizaciones
                         FROM kardex k
                         JOIN marcas m ON k.id_marca = m.id
                         WHERE k.id_marca > 0
                         GROUP BY k.id_marca, m.nombre_marca
                         ORDER BY cotizaciones DESC
                         LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['marca' => '', 'cotizaciones' => 0];
}

function obtenerMarcaMenosCotizadaDirectamente(PDO $pdo): array {
    $stmt = $pdo->query("SELECT k.id_marca, m.nombre_marca as marca, COUNT(*) as cotizaciones
                         FROM kardex k
                         JOIN marcas m ON k.id_marca = m.id
                         WHERE k.id_marca > 0
                         GROUP BY k.id_marca, m.nombre_marca
                         ORDER BY cotizaciones ASC
                         LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['marca' => '', 'cotizaciones' => 0];
}