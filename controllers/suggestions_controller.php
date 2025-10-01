<?php
session_start();
include("conx.php");
include("funciones.php");

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

// Función para obtener productos frecuentemente cotizados juntos
function getFrequentItemsets($productIds, $minSupport = 0.2, $maxSuggestions = 5) {
    $link = conectarse();
    
    // Primero obtener los nombres/descripciones de los productos actuales
    $productNames = [];
    if (!empty($productIds)) {
        $productIdsStr = implode(",", array_map('intval', $productIds));
        $query = "SELECT nombre, descripcion FROM productos WHERE id IN ($productIdsStr)";
        $result = mysqli_query($link, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $productNames[] = mysqli_real_escape_string($link, $row['nombre']);
            $productNames[] = mysqli_real_escape_string($link, $row['descripcion']);
        }
    }
    
    if (empty($productNames)) {
        return [];
    }
    
    // Buscar documentos que contengan estos productos
    $namesStr = "'" . implode("','", $productNames) . "'";
    $query = "SELECT DISTINCT id_documento 
              FROM kardex 
              WHERE id_tipo_documento = 5 
              AND (producto IN ($namesStr) OR descripcion IN ($namesStr))";
    
    $result = mysqli_query($link, $query);
    $documentIds = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $documentIds[] = $row['id_documento'];
    }
    
    if (empty($documentIds)) {
        return [];
    }
    
    // Obtener productos sugeridos de esos documentos
    $docIdsStr = implode(",", $documentIds);
    $query = "SELECT p.id, p.nombre as producto_nombre, p.descripcion as producto_descripcion, 
                     m.nombre_marca as marca, COUNT(*) as frequency 
              FROM kardex k
              JOIN productos p ON (k.producto = p.nombre OR k.descripcion = p.descripcion)
              JOIN marcas m ON p.id_marca = m.id
              WHERE k.id_tipo_documento = 5 
              AND k.id_documento IN ($docIdsStr) 
              AND p.id NOT IN (".implode(",", $productIds).")
              GROUP BY p.id, p.nombre, p.descripcion, m.nombre_marca
              HAVING frequency >= 1 
              ORDER BY frequency DESC 
              LIMIT $maxSuggestions";
    
    $result = mysqli_query($link, $query);
    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = [
            'id' => $row['id'],
            'producto_nombre' => $row['producto_nombre'],
            'producto_descripcion' => $row['producto_descripcion'],
            'marca' => $row['marca'],
            'frequency' => $row['frequency']
        ];
    }
    
    mysqli_close($link);
    return $suggestions;
}

function geneticAlgorithmSuggestions($productIds, $populationSize = 10, $generations = 5) {
    $link = conectarse();
    
    // 1. Crear población inicial de combinaciones de productos
    $population = [];
    for ($i = 0; $i < $populationSize; $i++) {
        $query = "SELECT p.id, p.nombre as producto_nombre, p.descripcion as producto_descripcion, 
                         m.nombre_marca as marca
                  FROM productos p
                  JOIN marcas m ON p.id_marca = m.id
                  WHERE p.id NOT IN (".implode(",", $productIds).")
                  ORDER BY RAND() LIMIT 3";
        
        $result = mysqli_query($link, $query);
        $suggestion = [
            'products' => [],
            'fitness' => 0
        ];
        while ($row = mysqli_fetch_assoc($result)) {
            $suggestion['products'][] = $row;
        }
        $population[] = $suggestion;
    }
    
    // 2. Evolucionar la población
    for ($gen = 0; $gen < $generations; $gen++) {
        // Evaluar fitness (qué tan frecuentes son estas combinaciones en kardex)
        foreach ($population as &$individual) {
            $productIdsInInd = array_column($individual['products'], 'id');
            
            if (!empty($productIdsInInd)) {
                $idsStr = implode(",", $productIdsInInd);
                $query = "SELECT COUNT(DISTINCT k.id_documento) as count 
                          FROM kardex k
                          JOIN productos p ON (k.producto = p.nombre OR k.descripcion = p.descripcion)
                          WHERE k.id_tipo_documento = 5 
                          AND p.id IN ($idsStr)";
                
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_assoc($result);
                $individual['fitness'] = $row['count'];
            } else {
                $individual['fitness'] = 0;
            }
        }
        unset($individual);
        
        // Ordenar por fitness
        usort($population, function($a, $b) {
            return $b['fitness'] - $a['fitness'];
        });
        
        // Seleccionar los mejores (50%)
        $best = array_slice($population, 0, ceil($populationSize / 2));
        
        // Reproducir (crossover y mutación)
        $newPopulation = $best;
        while (count($newPopulation) < $populationSize) {
            $parent1 = $best[array_rand($best)];
            $parent2 = $best[array_rand($best)];
            
            // Crossover: tomar algunos productos de cada padre
            $child = [
                'products' => [],
                'fitness' => 0
            ];
            
            // Tomar productos del padre 1
            if (!empty($parent1['products'])) {
                $takeFromParent1 = min(rand(1, 2), count($parent1['products']));
                $taken = array_slice($parent1['products'], 0, $takeFromParent1);
                $child['products'] = array_merge($child['products'], $taken);
            }
            
            // Tomar productos del padre 2 (evitando duplicados)
            if (!empty($parent2['products'])) {
                $takeFromParent2 = min(3 - count($child['products']), count($parent2['products']));
                $available = array_filter($parent2['products'], function($product) use ($child) {
                    return !in_array($product['id'], array_column($child['products'], 'id'));
                });
                
                if (!empty($available)) {
                    $taken = array_slice($available, 0, $takeFromParent2);
                    $child['products'] = array_merge($child['products'], $taken);
                }
            }
            
            // Mutación: agregar un producto aleatorio si hay espacio
            if (count($child['products']) < 3 && rand(0, 100) < 30) {
                $currentIds = array_merge($productIds, array_column($child['products'], 'id'));
                $query = "SELECT p.id, p.nombre as producto_nombre, p.descripcion as producto_descripcion, 
                                 m.nombre_marca as marca
                          FROM productos p
                          JOIN marcas m ON p.id_marca = m.id
                          WHERE p.id NOT IN (".implode(",", $currentIds).")
                          ORDER BY RAND() LIMIT 1";
                
                $result = mysqli_query($link, $query);
                if ($row = mysqli_fetch_assoc($result)) {
                    $child['products'][] = $row;
                }
            }
            
            $newPopulation[] = $child;
        }
        
        $population = $newPopulation;
    }
    
    // Devolver los productos del mejor individuo
    usort($population, function($a, $b) {
        return $b['fitness'] - $a['fitness'];
    });
    
    mysqli_close($link);
    return !empty($population[0]['products']) ? $population[0]['products'] : [];
}

// Manejar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['productIds']) && is_array($data['productIds'])) {
        $productIds = array_map('intval', $data['productIds']);
        
        // Primero intentar con itemsets frecuentes
        $suggestions = getFrequentItemsets($productIds);
        
        // Si no hay suficientes sugerencias, usar el algoritmo genético
        if (count($suggestions) < 3) {
            $gaSuggestions = geneticAlgorithmSuggestions($productIds);
            $suggestions = array_merge($suggestions, $gaSuggestions);
        }
        
        // Limitar a 5 sugerencias máximo y eliminar duplicados
        $uniqueSuggestions = [];
        $seenIds = [];
        foreach ($suggestions as $suggestion) {
            if (!in_array($suggestion['id'], $seenIds)) {
                $uniqueSuggestions[] = $suggestion;
                $seenIds[] = $suggestion['id'];
                if (count($uniqueSuggestions) >= 5) break;
            }
        }
        
        echo json_encode(['success' => true, 'suggestions' => $uniqueSuggestions]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Product IDs not provided']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}