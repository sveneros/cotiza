<?php
session_start();
include("conx.php");
include("funciones.php");

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, PUT, PATCH, POST, DELETE, OPTIONS');
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
    'documento' => null,
    'detalle' => array()
);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $link = conectarse();
    // Obtener cotización por ID
    if ($method === 'GET' && isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Obtener documento
        $query = "SELECT d.*, c.nombre, c.apellido1, c.apellido2, c.direccion, c.cel1, c.cel2 
                  FROM documentos d
                  JOIN clientes c ON d.id_cliente = c.id
                  WHERE d.id_documento = ? AND d.id_tipo_documento = 5";
        $stmt = $link->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $documento = $result->fetch_assoc();
            
            // Obtener detalle del kardex
            $query = "SELECT k.* FROM kardex k 
                      WHERE k.id_documento = ? AND k.id_tipo_documento = 5";
            $stmt = $link->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $detalle = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            $response['success'] = true;
            $response['documento'] = $documento;
            $response['detalle'] = $detalle;
        } else {
            $response['error'] = 'Cotización no encontrada';
        }
    }

    elseif ($method === 'PATCH') {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data || !isset($data['id_documento'])) {
            throw new Exception("Datos JSON inválidos o falta ID de documento");
        }
        
        $link->autocommit(false); // Iniciar transacción
        
        try {
            $id_documento = $data['id_documento'];
            $estado = 'APRO'; // Forzamos el estado APRO
            
            // 1. Actualizar solo el estado del documento
            $query = "UPDATE documentos SET 
                    estado = ?
                    WHERE id_documento = ? AND id_tipo_documento = 5";
            $stmt = $link->prepare($query);
            $stmt->bind_param("si", $estado, $id_documento);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception("No se pudo aprobar la cotización (documento no encontrado o ya estaba aprobado)");
            }

           
            
            $link->commit();
            $link2 = conectarse();
             // Registrar en auditoría
             $auditData = [
                'id_documento' => $id_documento,
                'accion' => 'ACTUALIZAR',
                'estado_anterior' => 'CLI', 
                'estado_nuevo' => 'APRO',
                'detalles' => 'Actualización de cotización. Aprobado por administrador',
                'id_usuario' => $_SESSION['sml2020_svenerossys_id_usuario_registrado']
            ];
            if (!registrar_auditoria_cotizacion($link2, $auditData)) {
                throw new Exception("Error al registrar auditoría");
            }
            
            $response['success'] = true;
            $response['message'] = 'Cotización aprobada correctamente';
            
            // Registrar en logs
            logs_db("Cotización aprobada: $id_documento", "quote_controller.php");
            
        } catch (Exception $e) {
            $link->rollback();
            throw $e;
        }
    }
        
    // Actualizar cotización
    elseif ($method === 'PUT') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data) {
            throw new Exception("Datos JSON inválidos");
        }
        
        $link->autocommit(false); // Iniciar transacción
        
        try {
            $id_documento = $data['id_documento'];
            $estado = $data['estado'];
            $total = $data['total'];
            
            // 1. Actualizar documento
            $query = "UPDATE documentos SET 
                      total = ?, 
                      estado = ?,
                      glosa = ?
                      WHERE id_documento = ? AND id_tipo_documento = 5";
            $stmt = $link->prepare($query);
            $stmt->bind_param("dssi", $total, $estado, $data['notas'], $id_documento);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception("No se pudo actualizar el documento");
            }
            
            // 2. Actualizar información del cliente
            /* $query = "UPDATE clientes SET 
                      nombre = ?,
                      apellido1 = ?,
                      apellido2 = ?,
                      direccion = ?,
                      cel1 = ?,
                      cel2 = ?
                      WHERE id = (SELECT id_cliente FROM documentos WHERE id_documento = ?)";
            $stmt = $link->prepare($query);
            $stmt->bind_param("ssssssi", 
                $data['nombre'],
                $data['apellido1'],
                $data['apellido2'],
                $data['direccion'],
                $data['telefono'],
                $data['celular'],
                $id_documento
            );
            $stmt->execute(); */
            
            // 3. Eliminar detalle antiguo del kardex
            $query = "DELETE FROM kardex WHERE id_documento = ? AND id_tipo_documento = 5";
            $stmt = $link->prepare($query);
            $stmt->bind_param("i", $id_documento);
            $stmt->execute();
            
            // 4. Insertar nuevo detalle
            foreach ($data['detalle'] as $item) {
                $query = "INSERT INTO kardex (
                    id_documento, id_tipo_documento, producto, cantidad, 
                    precio_unitario, precio_total, descuento, descripcion, id_marca, marca
                ) VALUES (?, 5, ?, ?, ?, ?, 0, ?, ?, ?)";
                
                $stmt = $link->prepare($query);
                $stmt->bind_param("isiddsis", 
                    $id_documento,
                    $item['producto'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['precio_total'],
                    $item['descripcion'],
                    $item['id_marca'],
                    $item['marca']
                );
                $stmt->execute();
            }
            
            $link->commit();
            
            $response['success'] = true;
            $response['message'] = $estado === 'APR' ? 
                'Cotización aprobada correctamente' : 'Cotización actualizada correctamente';
            
            // Registrar en logs
            logs_db("Cotización actualizada: $id_documento", "quote_controller.php");
            
        } catch (Exception $e) {
            $link->rollback();
            throw $e;
        }
    }
    else {
        $response['error'] = 'Método no permitido';
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>