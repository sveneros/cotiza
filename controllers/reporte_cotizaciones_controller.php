<?php
session_start();
include("conx.php");
include("funciones.php");

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');
header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$response = array(
    'success' => false,
    'message' => '',
    'error' => '',
    'data' => array()
);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'GET') {
        throw new Exception("Método no permitido");
    }
    
    $link = conectarse();
    
    // Obtener parámetros de filtro
    $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'semanal';
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
    
    // Determinar rango de fechas según el período seleccionado
    if (!$fecha_inicio || !$fecha_fin) {
        switch ($periodo) {
            case 'semanal':
                $fecha_inicio = date('Y-m-d', strtotime('-1 week'));
                $fecha_fin = date('Y-m-d');
                break;
            case 'mensual':
                $fecha_inicio = date('Y-m-d', strtotime('-1 month'));
                $fecha_fin = date('Y-m-d');
                break;
            case 'trimestral':
                $fecha_inicio = date('Y-m-d', strtotime('-3 months'));
                $fecha_fin = date('Y-m-d');
                break;
            default:
                $fecha_inicio = date('Y-m-d', strtotime('-1 week'));
                $fecha_fin = date('Y-m-d');
        }
    }
    
    // 1. Estadísticas de tiempo de aprobación
    $query = "SELECT 
                AVG(TIMESTAMPDIFF(HOUR, c1.fecha_hora, c2.fecha_hora)) as tiempo_promedio_horas,
                AVG(TIMESTAMPDIFF(DAY, c1.fecha_hora, c2.fecha_hora)) as tiempo_promedio_dias
              FROM auditoria_cotizaciones c1
              JOIN auditoria_cotizaciones c2 ON c1.id_documento = c2.id_documento 
                AND c2.estado_nuevo = 'APRO'
              WHERE c1.estado_nuevo = 'REV' 
                AND c1.accion = 'CREAR COTIZACIÓN'
                AND c1.fecha_hora BETWEEN ? AND ?";
    
    $stmt = $link->prepare($query);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $tiempo_aprobacion = $stmt->get_result()->fetch_assoc();
    
    // 2. Cotizaciones por estado
    // Consulta corregida para obtener los estados
$query = "SELECT 
            SUM(CASE WHEN ultimo_estado = 'REV' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN ultimo_estado = 'APRO' THEN 1 ELSE 0 END) as aprobadas,
            SUM(CASE WHEN ultimo_estado = 'CLI' THEN 1 ELSE 0 END) as generadas,
            COUNT(*) as total
          FROM (
            SELECT 
              a.id_documento, 
              (
                SELECT ac.estado_nuevo 
                FROM auditoria_cotizaciones ac 
                WHERE ac.id_documento = a.id_documento 
                ORDER BY ac.fecha_hora DESC 
                LIMIT 1
              ) as ultimo_estado
            FROM auditoria_cotizaciones a
            WHERE a.fecha_hora BETWEEN ? AND ?
            GROUP BY a.id_documento
          ) as ultimos_estados";

$stmt = $link->prepare($query);
$stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt->execute();
$estados = $stmt->get_result()->fetch_assoc();

// Asegurarnos de que siempre haya valores aunque sean 0
if (!$estados) {
    $estados = [
        'pendientes' => 0,
        'aprobadas' => 0,
        'generadas' => 0,
        'total' => 0
    ];
}
    
    // 3. Evolución de cotizaciones por día
    $query = "SELECT 
                DATE(fecha_hora) as fecha,
                SUM(CASE WHEN accion = 'CREAR COTIZACIÓN' THEN 1 ELSE 0 END) as creadas,
                SUM(CASE WHEN estado_nuevo = 'APRO' THEN 1 ELSE 0 END) as aprobadas
              FROM auditoria_cotizaciones
              WHERE fecha_hora BETWEEN ? AND ?
              GROUP BY DATE(fecha_hora)
              ORDER BY fecha";
    
    $stmt = $link->prepare($query);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $evolucion = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $response['success'] = true;
    $response['data'] = array(
        'tiempo_aprobacion' => $tiempo_aprobacion,
        'estados' => $estados,
        'evolucion' => $evolucion,
        'filtros' => array(
            'periodo' => $periodo,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        )
    );
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>