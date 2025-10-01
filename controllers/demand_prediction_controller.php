<?php
session_start();
include("conx.php");
include("funciones.php");

header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

// Función para obtener datos históricos de precios y demanda
function getHistoricalPricingData($productName = null) {
    $link = conectarse();
    
    $query = "SELECT 
                p.id as product_id,
                p.nombre as product_name,
                k.precio_unitario as price,
                SUM(k.cantidad) as quantity_sold,
                COUNT(DISTINCT k.id_documento) as quote_count,
                MIN(d.fecha) as date,
                WEEK(MIN(d.fecha)) as week_number,
                MONTH(MIN(d.fecha)) as month_number,
                QUARTER(MIN(d.fecha)) as quarter_number,
                YEAR(MIN(d.fecha)) as year_number
              FROM kardex k
              JOIN documentos d ON k.id_documento = d.id_documento
              JOIN productos p ON k.producto = p.nombre
              WHERE d.id_tipo_documento = 5";
    
    if ($productName) {
        $query .= " AND p.nombre = ?";
    }
    
    $query .= " GROUP BY p.id, p.nombre, k.precio_unitario ORDER BY date ASC";
    
    $stmt = mysqli_prepare($link, $query);
    
    if ($productName) {
        mysqli_stmt_bind_param($stmt, "s", $productName);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        $error = mysqli_error($link);
        mysqli_close($link);
        return ['error' => $error];
    }
    
    $historicalData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $historicalData[] = $row;
    }
    
    mysqli_close($link);
    return $historicalData;
}

// Función para predecir demanda usando regresión lineal
function predictDemandWithLinearRegression($productName, $pricePoints) {
    $historicalData = getHistoricalPricingData($productName);
    
    if (isset($historicalData['error'])) {
        return $historicalData;
    }
    
    if (count($historicalData) < 3) {
        return ['error' => 'No hay suficientes datos históricos para este producto'];
    }
    
    // Preparar datos para la regresión
    $x = []; // Precios
    $y = []; // Cantidades vendidas
    
    foreach ($historicalData as $data) {
        $x[] = $data['price'];
        $y[] = $data['quantity_sold'];
    }
    
    // Calcular coeficientes de regresión lineal (y = ax + b)
    $n = count($x);
    $sumX = array_sum($x);
    $sumY = array_sum($y);
    $sumXY = 0;
    $sumX2 = 0;
    
    for ($i = 0; $i < $n; $i++) {
        $sumXY += $x[$i] * $y[$i];
        $sumX2 += $x[$i] * $x[$i];
    }
    
    $a = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
    $b = ($sumY - $a * $sumX) / $n;
    
    // Predecir demanda para los puntos de precio solicitados
    $predictions = [];
    foreach ($pricePoints as $price) {
        $predictedDemand = $a * $price + $b;
        $predictions[] = [
            'price' => $price,
            'predicted_demand' => max(0, round($predictedDemand)) // No permitir valores negativos
        ];
    }
    
    return [
        'method' => 'linear_regression',
        'coefficients' => ['a' => $a, 'b' => $b],
        'equation' => "y = " . round($a, 2) . "x + " . round($b, 2),
        'predictions' => $predictions,
        'historical_data' => $historicalData
    ];
}

// Función para predecir demanda usando regresión logística
function predictDemandWithLogisticRegression($productName, $pricePoints) {
    $historicalData = getHistoricalPricingData($productName);
    
    if (isset($historicalData['error'])) {
        return $historicalData;
    }
    
    if (count($historicalData) < 3) {
        return ['error' => 'No hay suficientes datos históricos para este producto'];
    }
    
    // Preparar datos para la regresión logística
    $x = []; // Precios
    $y = []; // Probabilidad de compra (normalizamos las cantidades vendidas)
    
    $maxQuantity = max(array_column($historicalData, 'quantity_sold'));
    
    foreach ($historicalData as $data) {
        $x[] = $data['price'];
        $y[] = $data['quantity_sold'] / $maxQuantity; // Normalizar entre 0 y 1
    }
    
    // Implementación simplificada de regresión logística
    $learningRate = 0.01;
    $iterations = 1000;
    $theta0 = 0;
    $theta1 = 0;
    
    for ($i = 0; $i < $iterations; $i++) {
        $gradient0 = 0;
        $gradient1 = 0;
        
        for ($j = 0; $j < count($x); $j++) {
            $prediction = 1 / (1 + exp(-($theta0 + $theta1 * $x[$j])));
            $gradient0 += ($prediction - $y[$j]);
            $gradient1 += ($prediction - $y[$j]) * $x[$j];
        }
        
        $theta0 -= $learningRate * $gradient0 / count($x);
        $theta1 -= $learningRate * $gradient1 / count($x);
    }
    
    // Predecir demanda para los puntos de precio solicitados
    $predictions = [];
    foreach ($pricePoints as $price) {
        $probability = 1 / (1 + exp(-($theta0 + $theta1 * $price)));
        $predictedDemand = $probability * $maxQuantity;
        $predictions[] = [
            'price' => $price,
            'predicted_demand' => max(0, round($predictedDemand)) // No permitir valores negativos
        ];
    }
    
    return [
        'method' => 'logistic_regression',
        'coefficients' => ['theta0' => $theta0, 'theta1' => $theta1],
        'equation' => "p = 1 / (1 + e^(-(" . round($theta0, 2) . " + " . round($theta1, 2) . "x))",
        'predictions' => $predictions,
        'historical_data' => $historicalData
    ];
}

// Función para comparar métodos de predicción
function comparePredictionMethods($productName, $pricePoints) {
    $linearResults = predictDemandWithLinearRegression($productName, $pricePoints);
    $logisticResults = predictDemandWithLogisticRegression($productName, $pricePoints);
    $geneticResults = getGeneticAlgorithmPrediction($productName, $pricePoints);
    
    return [
        'product_name' => $productName,
        'price_points' => $pricePoints,
        'linear_regression' => $linearResults,
        'logistic_regression' => $logisticResults,
        'genetic_algorithm' => $geneticResults
    ];
}

// Función para obtener predicción del algoritmo genético (simulada)
function getGeneticAlgorithmPrediction($productName, $pricePoints) {
    $historicalData = getHistoricalPricingData($productName);
    
    if (isset($historicalData['error'])) {
        return $historicalData;
    }
    
    if (count($historicalData) < 3) {
        return ['error' => 'No hay suficientes datos históricos para este producto'];
    }
    
    // Simulamos una curva de demanda optimizada por algoritmo genético
    $maxPrice = max(array_column($historicalData, 'price'));
    $minPrice = min(array_column($historicalData, 'price'));
    $priceRange = $maxPrice - $minPrice;
    
    $predictions = [];
    foreach ($pricePoints as $price) {
        // Simulamos una curva de demanda que maximiza ganancias
        $normalizedPrice = ($price - $minPrice) / $priceRange;
        $predictedDemand = (1 - $normalizedPrice) * array_sum(array_column($historicalData, 'quantity_sold')) / count($historicalData) * 1.5;
        
        $predictions[] = [
            'price' => $price,
            'predicted_demand' => max(0, round($predictedDemand))
        ];
    }
    
    return [
        'method' => 'genetic_algorithm',
        'predictions' => $predictions,
        'historical_data' => $historicalData
    ];
}

// Manejar la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// Manejar la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'historical_data':
                        $productName = isset($_GET['product_name']) ? $_GET['product_name'] : null;
                        $historicalData = getHistoricalPricingData($productName);
                        echo json_encode($historicalData);
                        break;
                        
                    case 'predict_demand':
                        if (!isset($_GET['product_name']) || !isset($_GET['price_points'])) {
                            throw new Exception("Se requieren product_name y price_points");
                        }
                        
                        $productName = $_GET['product_name'];
                        $pricePoints = json_decode($_GET['price_points'], true);
                        
                        if (!is_array($pricePoints)) {
                            throw new Exception("price_points debe ser un array JSON");
                        }
                        
                        $method = isset($_GET['method']) ? $_GET['method'] : 'linear_regression';
                        
                        switch ($method) {
                            case 'linear_regression':
                                $result = predictDemandWithLinearRegression($productName, $pricePoints);
                                break;
                            case 'logistic_regression':
                                $result = predictDemandWithLogisticRegression($productName, $pricePoints);
                                break;
                            case 'genetic_algorithm':
                                $result = getGeneticAlgorithmPrediction($productName, $pricePoints);
                                break;
                            case 'compare':
                                $result = comparePredictionMethods($productName, $pricePoints);
                                break;
                            default:
                                throw new Exception("Método de predicción no válido");
                        }
                        
                        echo json_encode($result);
                        break;
                        
                    default:
                        throw new Exception("Acción no válida");
                }
            } else {
                throw new Exception("Acción no especificada");
            }
            break;
            
        case 'POST':
            if (isset($_GET['action']) && $_GET['action'] == 'predict_demand_batch') {
                if (!isset($data['product_name']) || !isset($data['price_points'])) {
                    throw new Exception("Se requieren product_name y price_points");
                }
                
                $productName = $data['product_name'];
                $pricePoints = $data['price_points'];
                $method = isset($data['method']) ? $data['method'] : 'linear_regression';
                
                switch ($method) {
                    case 'linear_regression':
                        $result = predictDemandWithLinearRegression($productName, $pricePoints);
                        break;
                    case 'logistic_regression':
                        $result = predictDemandWithLogisticRegression($productName, $pricePoints);
                        break;
                    case 'genetic_algorithm':
                        $result = getGeneticAlgorithmPrediction($productName, $pricePoints);
                        break;
                    case 'compare':
                        $result = comparePredictionMethods($productName, $pricePoints);
                        break;
                    default:
                        throw new Exception("Método de predicción no válido");
                }
                
                echo json_encode($result);
            } else {
                throw new Exception("Acción no válida");
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>