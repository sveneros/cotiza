<?php
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

function getQuoteStats() {
    $link = conectarse();
    
    // Estadísticas generales de cotizaciones
    $stats = array();
    
    // 1. Total de cotizaciones por estado
    $result = mysqli_query($link, "SELECT estado, COUNT(*) as total FROM documentos WHERE id_tipo_documento = 5 GROUP BY estado");
    $statusStats = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $statusStats[] = $row;
    }
    $stats['status'] = $statusStats;
    
    // 2. Productos más cotizados
    $result = mysqli_query($link, "SELECT producto, COUNT(*) as total FROM kardex WHERE id_tipo_documento = 5 GROUP BY producto ORDER BY total DESC LIMIT 10");
    $topProducts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $topProducts[] = $row;
    }
    $stats['top_products'] = $topProducts;
    
    // 3. Productos frecuentemente cotizados juntos (para recomendaciones)
    $result = mysqli_query($link, "SELECT k1.producto as producto1, k2.producto as producto2, COUNT(*) as frecuencia 
                                 FROM kardex k1 
                                 JOIN kardex k2 ON k1.id_documento = k2.id_documento AND k1.producto < k2.producto 
                                 WHERE k1.id_tipo_documento = 5 AND k2.id_tipo_documento = 5
                                 GROUP BY k1.producto, k2.producto 
                                 ORDER BY frecuencia DESC 
                                 LIMIT 20");
    $comboProducts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $comboProducts[] = $row;
    }
    $stats['combo_products'] = $comboProducts;
    
    // 4. Cotizaciones por mes
    $result = mysqli_query($link, "SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as total 
                                 FROM documentos 
                                 WHERE id_tipo_documento = 5 
                                 GROUP BY mes 
                                 ORDER BY mes DESC 
                                 LIMIT 12");
    $monthlyStats = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $monthlyStats[] = $row;
    }
    $stats['monthly'] = $monthlyStats;
    
    mysqli_close($link);
    return $stats;
}

function getProductRecommendations($product_name) {
    $link = conectarse();
    $recommendations = array();
    
    // Algoritmo genético simplificado para recomendaciones
    // 1. Obtener productos frecuentemente cotizados juntos
    $stmt = mysqli_prepare($link, "SELECT k2.producto as recommended_product, 
                                  COUNT(*) as frequency,
                                  COUNT(*)/(SELECT COUNT(DISTINCT id_documento) FROM kardex WHERE producto = ? AND id_tipo_documento = 5) as probability
                                  FROM kardex k1
                                  JOIN kardex k2 ON k1.id_documento = k2.id_documento AND k1.producto != k2.producto
                                  WHERE k1.producto = ? AND k1.id_tipo_documento = 5 AND k2.id_tipo_documento = 5
                                  GROUP BY k2.producto
                                  ORDER BY probability DESC
                                  LIMIT 3");
    mysqli_stmt_bind_param($stmt, "ss", $product_name, $product_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['probability'] = round($row['probability'] * 100, 0);
        $recommendations[] = $row;
    }
    
    // 2. Obtener marcas relacionadas (preferencia de marca)
    $stmt = mysqli_prepare($link, "SELECT marca, COUNT(*) as frequency 
                                  FROM kardex 
                                  WHERE producto != ? AND id_marca IN 
                                      (SELECT DISTINCT id_marca FROM kardex WHERE producto = ? AND id_marca > 0)
                                  AND id_tipo_documento = 5
                                  GROUP BY marca
                                  ORDER BY frequency DESC
                                  LIMIT 2");
    mysqli_stmt_bind_param($stmt, "ss", $product_name, $product_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $brand_recommendations = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $brand_recommendations[] = $row;
    }
    if (!empty($brand_recommendations)) {
        $recommendations['brands'] = $brand_recommendations;
    }
    
    mysqli_close($link);
    return $recommendations;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['product'])) {
            $product = $_GET['product'];
            $recommendations = getProductRecommendations($product);
            echo json_encode($recommendations);
        } else {
            $stats = getQuoteStats();
            echo json_encode($stats);
        }
        break;
    
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}