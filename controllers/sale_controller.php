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

$data = array(
    'success' => false,
    'error' => '',
    'data' => array(),
    'numero_venta' => ''
);

try {
    if (!isset($_SESSION['sml2020_svenerossys_id_usuario_registrado'])) {
        throw new Exception("Usuario no autenticado");
    }
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method == 'POST' && $action == 'convert_to_sale' && isset($_POST['id_cotizacion'])) {
        // Lógica para convertir cotización a venta
        $id_cotizacion = $_POST['id_cotizacion'];
        $usuario = $_SESSION['sml2020_svenerossys_id_usuario_registrado'];
        
        $link = conectarse();
        mysqli_autocommit($link, false);
        
        try {
            // 1. Obtener datos de la cotización
            $sql = "SELECT d.*, 
                           CONCAT(c.nombre, ' ', c.apellido1, ' ', IFNULL(c.apellido2, '')) as nombre_cliente,
                           c.nit,
                           c.razon_social,
                           c.direccion
                    FROM documentos d
                    JOIN clientes c ON d.id_cliente = c.id
                    WHERE d.id_tipo_documento = 5 AND d.id_documento = ? AND d.estado = 'APRO'";
            
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_cotizacion);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $cotizacion = mysqli_fetch_assoc($result);
            
            if (!$cotizacion) {
                throw new Exception("Cotización no encontrada o no está aprobada");
            }
            
            // 2. Actualizar estado de cotización a VEN (vendido)
            $sql = "UPDATE documentos SET estado = 'VEN' 
                    WHERE id_tipo_documento = 5 AND id_documento = ?";
            
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_cotizacion);
            mysqli_stmt_execute($stmt);
            
            // 3. Crear nueva venta (tipo documento 6)
            $numero_venta = devuelveCorrelativoTipoDoc(6);
            $glosa = "Venta a cliente " . $cotizacion['nombre_cliente'] . " (Cotización #$id_cotizacion)";
            
            CreaDocumento(
                $numero_venta,
                6, // Tipo documento para ventas
                $cotizacion['id_cliente'],
                $cotizacion['tipo_cambio'],
                date('Y-m-d H:i:s'),
                $glosa,
                $cotizacion['descuento'],
                $usuario,
                $cotizacion['total'],
                $cotizacion['total'],
                0,
                'V' // Estado V = Vendido
            );
            
            // 4. Copiar productos del kardex de cotización a venta
            $sql = "INSERT INTO kardex (id_documento, id_tipo_documento, producto, descripcion, cantidad, precio_unitario, precio_total, descuento)
                    SELECT ?, 6, producto, descripcion, cantidad, precio_unitario, precio_total, descuento
                    FROM kardex
                    WHERE id_tipo_documento = 5 AND id_documento = ?";
            
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $numero_venta, $id_cotizacion);
            mysqli_stmt_execute($stmt);
            
            // 5. Registrar relación entre cotización y venta
            $sql = "INSERT INTO relacion_cotizacion_venta (id_cotizacion, id_venta, fecha_relacion)
                    VALUES (?, ?, NOW())";
            
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $id_cotizacion, $numero_venta);
            mysqli_stmt_execute($stmt);
            
            // Registrar auditoría
            $auditData = [
                'id_documento' => $numero_venta,
                'accion' => 'CREAR VENTA',
                'estado_anterior' => '', 
                'estado_nuevo' => 'V',
                'detalles' => "Venta generada desde cotización #$id_cotizacion",
                'id_usuario' => $usuario
            ];
            
            if (!registrar_auditoria_cotizacion($link, $auditData)) {
                throw new Exception("Error al registrar auditoría");
            }
            
            mysqli_commit($link);
            
            $data['success'] = true;
            $data['numero_venta'] = $numero_venta;
            
            logs_db("Venta #$numero_venta creada desde cotización #$id_cotizacion", $_SERVER['PHP_SELF']);
        } catch (Exception $e) {
            mysqli_rollback($link);
            throw $e;
        } finally {
            mysqli_close($link);
        }
    } elseif ($method == 'GET' && $action == 'get_sale_detail' && isset($_GET['id'])) {
        // Lógica para obtener detalles de una venta
        $id_venta = $_GET['id'];
        $link = conectarse();
        
        try {
            // Obtener cabecera de venta
            $sql = "SELECT d.id_documento as numero, 
                           d.fecha,
                           d.total,
                           d.glosa,
                           d.estado,
                           c.id as id_cliente,
                           CONCAT(c.nombre, ' ', c.apellido1, ' ', IFNULL(c.apellido2, '')) as nombre_cliente,
                           c.nit,
                           c.razon_social,
                           c.direccion,
                           c.email,
                           c.cel1 as telefono,
                           c.cel2 as celular,
                           rcv.id_cotizacion as id_cotizacion_origen
                    FROM documentos d
                    JOIN clientes c ON d.id_cliente = c.id
                    LEFT JOIN relacion_cotizacion_venta rcv ON d.id_documento = rcv.id_venta
                    WHERE d.id_tipo_documento = 6 AND d.id_documento = ?";
            
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_venta);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $venta = mysqli_fetch_assoc($result);
            
            if (!$venta) {
                throw new Exception("Venta no encontrada");
            }
            
            // Obtener detalle de productos
            $sql = "SELECT producto, cantidad, precio_unitario, precio_total 
                    FROM kardex 
                    WHERE id_tipo_documento = 6 AND id_documento = ?";
            
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_venta);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $detalle = array();
            
            while ($row = mysqli_fetch_assoc($result)) {
                $detalle[] = $row;
            }
            
            $data['success'] = true;
            $data['data'] = array(
                'cabecera' => $venta,
                'detalle' => $detalle
            );
            
            logs_db("Consultando detalle de venta #$id_venta", $_SERVER['PHP_SELF']);
        } catch (Exception $e) {
            throw $e;
        } finally {
            mysqli_close($link);
        }
    } else {
        throw new Exception("Acción no válida o datos incompletos");
    }
} catch (Exception $e) {
    $data['error'] = $e->getMessage();
}

echo json_encode($data);
?>