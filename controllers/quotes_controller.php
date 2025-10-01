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

function getAllQuotes() {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT d.id_documento as numero, d.fecha as fecha, d.total as total, d.estado as estado, c.nombre as nombre, c.apellido1 as apellido1, c.apellido2 as apellido2, c.cel1 as telefono,c.email as email, c.cel2 as celular, c.direccion as direccion  
                              FROM documentos as d 
                              INNER JOIN clientes as c ON d.id_cliente = c.id 
                              WHERE d.id_tipo_documento = 5 
                              ORDER BY d.id_documento DESC;");
    
    $Quotes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $id_documento = $row['numero'];
        $detalle_result = mysqli_query($link, "SELECT k.producto as producto, k.descripcion as descripcion, k.cantidad as cantidad, k.precio_unitario as precio_unitario, k.precio_total as precio_total 
                                               FROM kardex as k 
                                               WHERE k.id_documento = '$id_documento' order by 1 desc;");
        
        $detalles = array();
        while ($detalle = mysqli_fetch_assoc($detalle_result)) {
            $detalles[] = $detalle;
        }
        
        $row['detalle'] = $detalles;
        $Quotes[] = $row;
    }
    
    mysqli_close($link);
    return $Quotes;
}

function getAllQuotesByClientId($client_id){
    $link = conectarse();
    $result = mysqli_query($link, "SELECT d.id_documento as numero, d.fecha as fecha, d.total as total, d.estado as estado, c.nombre as nombre, c.apellido1 as apellido1, c.apellido2 as apellido2, c.cel1 as telefono,c.email as email, c.cel2 as celular, c.direccion as direccion  
                              FROM documentos as d 
                              INNER JOIN clientes as c ON d.id_cliente = c.id 
                              WHERE d.id_tipo_documento = 5 and d.id_cliente = $client_id
                              ORDER BY d.id_documento DESC;");
    
    $Quotes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $id_documento = $row['numero'];
        $detalle_result = mysqli_query($link, "SELECT k.producto as producto, k.descripcion as descripcion, k.cantidad as cantidad, k.precio_unitario as precio_unitario, k.precio_total as precio_total 
                                               FROM kardex as k 
                                               WHERE k.id_documento = '$id_documento' order by 1 desc;");
        
        $detalles = array();
        while ($detalle = mysqli_fetch_assoc($detalle_result)) {
            $detalles[] = $detalle;
        }
        
        $row['detalle'] = $detalles;
        $Quotes[] = $row;
    }
    
    mysqli_close($link);
    return $Quotes;
}

function getQuoteById($id) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM documentos WHERE id_documento = $id");
    $Log = mysqli_fetch_assoc($result);
    mysqli_close($link);
    return $Log;
}

function getQuoteDetail($id) {
    global $data;
    $link = conectarse();
    
    // Obtener cabecera
    $sql = "SELECT d.id_documento as numero, 
                   CONCAT(c.nombre, ' ', c.apellido1, ' ', IFNULL(c.apellido2, '')) as nombre_cliente,
                   d.fecha, 
                   d.total,
                   d.estado,
                   d.glosa,
                   c.id as id_cliente,
                   c.nit,
                   c.razon_social,
                   c.email,
                   c.cel1 as telefono,
                   c.cel2 as celular,
                   c.direccion
            FROM documentos d
            JOIN clientes c ON d.id_cliente = c.id
            WHERE d.id_tipo_documento = 5 AND d.id_documento = ?";
    
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cabecera = mysqli_fetch_assoc($result);
    
    if (!$cabecera) {
        throw new Exception("Cotización no encontrada");
    }
    
    // Obtener detalle
    $sql = "SELECT producto, cantidad, precio_unitario, precio_total 
            FROM kardex 
            WHERE id_tipo_documento = 5 AND id_documento = ?";
    
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $detalle = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $detalle[] = $row;
    }
    
    mysqli_close($link);
    
    $data['success'] = true;
    $data['data'] = array(
        'cabecera' => $cabecera,
        'detalle' => $detalle
    );
    
    return $data;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $Log = getQuoteDetail($id);
            echo json_encode($Log);
        }
        else if (isset($_GET['client_id'])) {
            $id = $_GET['client_id'];
            $Log = getAllQuotesByClientId($id);
            echo json_encode($Log);
        } else {
            $Logs = getAllQuotes();
            echo json_encode($Logs);
        }
        break;
    
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}