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

// Función para convertir USD a Bs. (tasa de cambio fija para el ejemplo)
function convertToBs($amount) {
    $exchangeRate = 6.96; // Tasa de cambio USD a Bs.
    return $amount * $exchangeRate;
}

function getProductPricingData($productId) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM product_cost_factors WHERE product_id = $productId");
    $data = mysqli_fetch_assoc($result);
    
    // Convertir todos los valores monetarios a Bs.
    if ($data) {
        $data['base_cost'] = convertToBs($data['base_cost']);
        $data['shipping_cost'] = convertToBs($data['shipping_cost']);
        $data['local_transport'] = convertToBs($data['local_transport']);
        $data['storage_cost'] = convertToBs($data['storage_cost']);
        $data['insurance'] = convertToBs($data['insurance']);
        $data['customs_fees'] = convertToBs($data['customs_fees']);
    }
    
    mysqli_close($link);
    return $data;
}

function calculateSuggestedPrice($productId) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM product_pricing_view WHERE id = $productId");
    $data = mysqli_fetch_assoc($result);
    mysqli_close($link);
    
    if (!$data) {
        return null;
    }
    
    // Convertir costos a Bs.
    $baseCost = convertToBs($data['base_cost_local']);
    $totalCosts = ($baseCost * (1 + $data['import_tax'] / 100)) + 
                  convertToBs($data['shipping_cost']) + 
                  convertToBs($data['local_transport']) + 
                  convertToBs($data['storage_cost']) + 
                  convertToBs($data['insurance']) + 
                  convertToBs($data['customs_fees']);
    
    // Factores de mercado
    $marketFactor = $data['market_demand_factor'];
    $competitionFactor = $data['competition_factor'];
    
    // Margen de ganancia
    $profitMargin = $data['profit_margin'] / 100;
    
    // Cálculo básico del precio
    $basicPrice = $totalCosts * (1 + $profitMargin);
    
    // Aplicar factores de mercado con límite máximo
    $adjustmentFactor = min($marketFactor * $competitionFactor, 1.3); // Máximo 30% de ajuste
    $suggestedPrice = $basicPrice * $adjustmentFactor;
    
    // Redondear a 2 decimales
    return round($suggestedPrice, 2);
}

function updateProductCostFactors($productId, $costData) {
    $link = conectarse();
    
    // Convertir de Bs. a USD si es necesario
    $exchangeRate = 6.96;
    $baseCost = isset($costData['base_cost']) ? floatval($costData['base_cost']) / $exchangeRate : 0;
    $shippingCost = isset($costData['shipping_cost']) ? floatval($costData['shipping_cost']) / $exchangeRate : 0;
    $localTransport = isset($costData['local_transport']) ? floatval($costData['local_transport']) / $exchangeRate : 0;
    $storageCost = isset($costData['storage_cost']) ? floatval($costData['storage_cost']) / $exchangeRate : 0;
    $insurance = isset($costData['insurance']) ? floatval($costData['insurance']) / $exchangeRate : 0;
    $customsFees = isset($costData['customs_fees']) ? floatval($costData['customs_fees']) / $exchangeRate : 0;
    
    // Escapar todos los datos
    $baseCost = mysqli_real_escape_string($link, $baseCost);
    $importTax = mysqli_real_escape_string($link, $costData['import_tax']);
    $shippingCost = mysqli_real_escape_string($link, $shippingCost);
    $localTransport = mysqli_real_escape_string($link, $localTransport);
    $storageCost = mysqli_real_escape_string($link, $storageCost);
    $insurance = mysqli_real_escape_string($link, $insurance);
    $customsFees = mysqli_real_escape_string($link, $customsFees);
    $exchangeRate = mysqli_real_escape_string($link, $exchangeRate);
    $currencyCode = 'USD';
    $profitMargin = mysqli_real_escape_string($link, $costData['profit_margin']);
    $marketFactor = mysqli_real_escape_string($link, $costData['market_demand_factor']);
    $competitionFactor = mysqli_real_escape_string($link, $costData['competition_factor']);
    
    // Verificar si ya existe un registro para este producto
    $checkQuery = "SELECT id FROM product_cost_factors WHERE product_id = $productId";
    $checkResult = mysqli_query($link, $checkQuery);
    
    if(mysqli_num_rows($checkResult) > 0) {
        // Actualizar registro existente
        $query = "UPDATE product_cost_factors SET 
                  base_cost = '$baseCost',
                  import_tax = '$importTax',
                  shipping_cost = '$shippingCost',
                  local_transport = '$localTransport',
                  storage_cost = '$storageCost',
                  insurance = '$insurance',
                  customs_fees = '$customsFees',
                  currency_exchange_rate = '$exchangeRate',
                  currency_code = '$currencyCode',
                  profit_margin = '$profitMargin',
                  market_demand_factor = '$marketFactor',
                  competition_factor = '$competitionFactor'
                  WHERE product_id = $productId";
    } else {
        // Insertar nuevo registro
        $query = "INSERT INTO product_cost_factors 
                  (product_id, base_cost, import_tax, shipping_cost, local_transport, 
                   storage_cost, insurance, customs_fees, currency_exchange_rate, 
                   currency_code, profit_margin, market_demand_factor, competition_factor)
                  VALUES 
                  ($productId, '$baseCost', '$importTax', '$shippingCost', '$localTransport',
                   '$storageCost', '$insurance', '$customsFees', '$exchangeRate',
                   '$currencyCode', '$profitMargin', '$marketFactor', '$competitionFactor')";
    }
    
    $result = mysqli_query($link, $query);
    
    if ($result) {
        logs_db("Actualizados factores de costo para producto ID: $productId", $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => true];
    } else {
        $error = mysqli_error($link);
        logs_db("Error al actualizar factores de costo: ".$error, $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => false, 'error' => $error];
    }
}

function getPricingAnalysisData() {
    $link = conectarse();
    $query = "
        SELECT 
            p.id,
            p.codigo,
            p.nombre,
            p.puntos as precio_actual,
            pcf.base_cost as base_cost_local,
            pcf.import_tax,
            pcf.shipping_cost,
            pcf.local_transport,
            pcf.storage_cost,
            pcf.insurance,
            pcf.customs_fees,
            pcf.currency_exchange_rate,
            pcf.profit_margin,
            pcf.market_demand_factor,
            pcf.competition_factor,
            ROUND(
                ((pcf.base_cost * (1 + pcf.import_tax / 100)) + 
                pcf.shipping_cost + pcf.local_transport + 
                pcf.storage_cost + pcf.insurance + pcf.customs_fees) * 
                (1 + pcf.profit_margin / 100) * 
                pcf.market_demand_factor * pcf.competition_factor, 
                2
            ) as precio_sugerido,
            ROUND(
                (((pcf.base_cost * (1 + pcf.import_tax / 100)) + 
                pcf.shipping_cost + pcf.local_transport + 
                pcf.storage_cost + pcf.insurance + pcf.customs_fees) * 
                (1 + pcf.profit_margin / 100) * 
                pcf.market_demand_factor * pcf.competition_factor) - p.puntos, 
                2
            ) as diferencia,
            ROUND(
                ((((pcf.base_cost * (1 + pcf.import_tax / 100)) + 
                pcf.shipping_cost + pcf.local_transport + 
                pcf.storage_cost + pcf.insurance + pcf.customs_fees) * 
                (1 + pcf.profit_margin / 100) * 
                pcf.market_demand_factor * pcf.competition_factor) - p.puntos) / p.puntos * 100, 
                2
            ) as porcentaje_diferencia
        FROM 
            productos p
        JOIN 
            product_cost_factors pcf ON p.id = pcf.product_id
        ORDER BY 
            ABS(
                ((((pcf.base_cost * (1 + pcf.import_tax / 100)) + 
                pcf.shipping_cost + pcf.local_transport + 
                pcf.storage_cost + pcf.insurance + pcf.customs_fees) * 
                (1 + pcf.profit_margin / 100) * 
                pcf.market_demand_factor * pcf.competition_factor) - p.puntos) / p.puntos * 100
            ) DESC
    ";
    
    $result = mysqli_query($link, $query);
    
    if (!$result) {
        logs_db("Error en consulta de análisis de precios: " . mysqli_error($link), $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return [];
    }
    
    $analysisData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Convertir todos los precios y costos a Bs.
        $row['precio_actual'] = convertToBs($row['precio_actual']);
        $row['precio_sugerido'] = convertToBs($row['precio_sugerido']);
        $row['diferencia'] = convertToBs($row['diferencia']);
        $row['base_cost_local'] = convertToBs($row['base_cost_local']);
        $row['shipping_cost'] = convertToBs($row['shipping_cost']);
        $row['local_transport'] = convertToBs($row['local_transport']);
        $row['storage_cost'] = convertToBs($row['storage_cost']);
        $row['insurance'] = convertToBs($row['insurance']);
        $row['customs_fees'] = convertToBs($row['customs_fees']);
        
        $analysisData[] = $row;
    }
    
    mysqli_close($link);
    return $analysisData;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'pricing_data':
                        if (isset($_GET['id'])) {
                            $id = intval($_GET['id']);
                            $data = getProductPricingData($id);
                            echo json_encode($data);
                        } else {
                            throw new Exception("ID de producto no proporcionado");
                        }
                        break;
                    case 'suggested_price':
                        if (isset($_GET['id'])) {
                            $id = intval($_GET['id']);
                            $price = calculateSuggestedPrice($id);
                            if ($price === null) {
                                throw new Exception("Producto no encontrado");
                            }
                            echo json_encode(['suggested_price' => $price]);
                        } else {
                            throw new Exception("ID de producto no proporcionado");
                        }
                        break;
                    case 'pricing_analysis':
                        $data = getPricingAnalysisData();
                        echo json_encode($data);
                        break;
                    default:
                        throw new Exception("Acción no válida");
                }
            } else {
                throw new Exception("Acción no especificada");
            }
            break;
            
        case 'POST':
            if (isset($_GET['action']) && $_GET['action'] == 'update_cost_factors') {
                if (!isset($data['product_id'])) {
                    throw new Exception("ID de producto no proporcionado");
                }
                $result = updateProductCostFactors(
                    intval($data['product_id']),
                    $data
                );
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