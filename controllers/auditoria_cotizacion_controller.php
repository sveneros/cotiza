<?php
include("conx.php");
include("funciones.php");

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");

$response = array(
    'success' => false,
    'message' => '',
    'error' => '',
    'historial' => array()
);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $link = conectarse();
    
    // Obtener historial de una cotización
    if ($method === 'GET' && isset($_GET['id_cotizacion'])) {
        $id_cotizacion = $_GET['id_cotizacion'];
        
        $query = "SELECT a.*, u.nombre as nombre_usuario 
                  FROM auditoria_cotizaciones a
                  LEFT JOIN usuarios u ON a.id_usuario = u.id
                  WHERE a.id_documento = ? AND a.id_tipo_documento = 5
                  ORDER BY a.fecha_hora DESC";
        $stmt = $link->prepare($query);
        $stmt->bind_param("i", $id_cotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $response['success'] = true;
        $response['historial'] = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Registrar una acción de auditoría (generalmente llamado desde otros controllers)
    elseif ($method === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data || !isset($data['id_documento']) || !isset($data['accion'])) {
            throw new Exception("Datos JSON inválidos o faltan campos requeridos");
        }
        
        $query = "INSERT INTO auditoria_cotizaciones (
                    id_documento, id_tipo_documento, accion, 
                    estado_anterior, estado_nuevo, detalles, 
                    id_usuario, ip_origen
                  ) VALUES (?, 5, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $link->prepare($query);
        $stmt->bind_param("issssis", 
            $data['id_documento'],
            $data['accion'],
            $data['estado_anterior'] ?? null,
            $data['estado_nuevo'] ?? null,
            $data['detalles'] ?? null,
            $data['id_usuario'],
            $_SERVER['REMOTE_ADDR']
        );
        $stmt->execute();
        
        $response['success'] = true;
        $response['message'] = 'Registro de auditoría guardado';
    }
    else {
        $response['error'] = 'Método no permitido';
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>