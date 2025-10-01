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
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");

function getAllSales() {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT d.id_documento as numero, d.fecha as fecha, d.total as total, d.estado as estado, c.nombre as nombre, c.apellido1 as apellido1, c.apellido2 as apellido2, c.cel1 as telefono,c.email as email, c.cel2 as celular, c.direccion as direccion  
                              FROM documentos as d 
                              INNER JOIN clientes as c ON d.id_cliente = c.id 
                              WHERE d.id_tipo_documento = 6 
                              ORDER BY d.id_documento DESC;");
    
    $Sales = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $id_documento = $row['numero'];
        $detalle_result = mysqli_query($link, "SELECT k.producto as producto, k.descripcion as descripcion, k.cantidad as cantidad, k.precio_unitario as precio_unitario, k.precio_total as precio_total 
                                               FROM kardex as k 
                                               WHERE k.id_documento = '$id_documento' AND k.id_tipo_documento = 6 order by 1 desc;");
        
        $detalles = array();
        while ($detalle = mysqli_fetch_assoc($detalle_result)) {
            $detalles[] = $detalle;
        }
        
        $row['detalle'] = $detalles;
        $Sales[] = $row;
    }
    
    mysqli_close($link);
    return $Sales;
}

function getSaleById($id) {
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
            WHERE d.id_tipo_documento = 6 AND d.id_documento = ?";
    
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cabecera = mysqli_fetch_assoc($result);
    
    if (!$cabecera) {
        throw new Exception("Venta no encontrada");
    }
    
    // Obtener detalle
    $sql = "SELECT producto, descripcion, cantidad, precio_unitario, precio_total 
            FROM kardex 
            WHERE id_tipo_documento = 6 AND id_documento = ?";
    
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

function generateDeliveryNote($sale_id) {
    $link = conectarse();
    
    // Obtener datos de la venta
    $sale_data = getSaleById($sale_id);
    
    if (!$sale_data['success']) {
        throw new Exception("No se pudo obtener los datos de la venta");
    }
    $delivery_note_id=devuelveCorrelativoTipoDoc(7);
    $tipo_cambio=devuelve_campo("configuracion","valor","campo","tipo_cambio_dolar");
    $id_usuario= $_SESSION['sml2020_svenerossys_id_usuario_registrado'];

    // Insertar nota de entrega
    $sql = "INSERT INTO documentos (id_documento, id_tipo_documento, id_cliente, tipo_cambio, fecha, glosa, descuento,usuario, total,efectivo,cambio, estado) 
            VALUES (?, 7, ?,?, NOW(), CONCAT('Nota de entrega para venta #', ?),0,?, ?,?,0, 'ACT')";
    
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "idiiidd",
        $delivery_note_id,
        $tipo_cambio,
        $sale_data['data']['cabecera']['id_cliente'],
        $id_usuario, 
        $sale_data['data']['cabecera']['numero'],
        $sale_data['data']['cabecera']['total'],
        $sale_data['data']['cabecera']['total']
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($link);
        mysqli_close($link);
        throw new Exception("Error al insertar nota de entrega: " . $error);
    }
    
    //$delivery_note_id = mysqli_insert_id($link);
    
    // Copiar items del kardex
    foreach ($sale_data['data']['detalle'] as $item) {
        $sql = "INSERT INTO kardex (id_documento, id_tipo_documento, producto, descripcion, cantidad, precio_unitario, precio_total,descuento)
                VALUES (?, 7, ?, ?, ?, ?, ?,0)";
        
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "issddd", 
            $delivery_note_id,
            $item['producto'],
            $item['descripcion'],
            $item['cantidad'],
            $item['precio_unitario'],
            $item['precio_total']
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_error($link);
            mysqli_close($link);
            throw new Exception("Error al insertar detalle en kardex: " . $error);
        }
    }
    
    mysqli_close($link);
    
    return $delivery_note_id;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $Sale = getSaleById($id);
            echo json_encode($Sale);
        } else {
            $Sales = getAllSales();
            echo json_encode($Sales);
        }
        break;
        
    case 'POST':
        if (isset($_POST['action']) && $_POST['action'] == 'generate_delivery_note') {
            $sale_id = $_POST['sale_id'];
            try {
                $delivery_note_id = generateDeliveryNote($sale_id);
                echo json_encode(['success' => true, 'delivery_note_id' => $delivery_note_id]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        break;
    
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}