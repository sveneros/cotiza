<?php
session_start();
include("conx.php");
include("funciones.php");

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$data = array(
    'results' => array(),
    'success' => false,
    'error' => '',
    'document_number' => '',
    'client_name' => ''
);

try {
    if (isset($_POST['productos']) && isset($_POST['id_cliente']) && isset($_POST['fecha']) && isset($_POST['tipo'])) {
        $id_cliente = $_POST['id_cliente'];
        $productsArr = json_decode($_POST['productos'], true);
        $fecha = $_POST['fecha'];
        if($_POST['tipo']=="cliente")
        $estado = "CLI";
        else
        $estado = "REV";
        
        // Validar datos
        if (empty($id_cliente)) {
            throw new Exception("Cliente no especificado");
        }
        
        if (empty($productsArr)) {
            throw new Exception("No hay productos en la cotización");
        }
        
        // Obtener información del cliente
        $clientInfo = obtenerClientePorId($id_cliente);
        $data['client_name'] = $clientInfo['nombre'] . ' ' . $clientInfo['apellido1'];
        
        // Generar número de documento
        $numero_documento = devuelveCorrelativoTipoDoc(5);
        $data['document_number'] = $numero_documento;
        $id_tipo_documento = 5;
        $glosa = "Cotización para " . $data['client_name'];
        
        // Calcular totales
        $total = 0;
        foreach($productsArr as $product) {
            $total += $product[4]; // Precio total del producto
        }
        
        $descuento = 0;
        $usuario = 1; // ID del usuario que realiza la cotización
        
        // Crear documento
        CreaDocumento(
            $numero_documento,
            $id_tipo_documento,
            $id_cliente,
            "0",
            $fecha,
            $glosa,
            $descuento,
            $usuario,
            $total,
            $total,
            0,
            $estado
        );
        
        logs_db("Se agregó el documento: $numero_documento de tipo: $id_tipo_documento", $_SERVER['PHP_SELF']);
        $link2 = conectarse();
             // Registrar en auditoría
        $auditData = [
                'id_documento' => $numero_documento,
                'accion' => 'CREAR COTIZACIÓN',
                'estado_anterior' => '', 
                'estado_nuevo' => $estado,
                'detalles' => 'Creación de cotización desde sistema',
                'id_usuario' => $_SESSION['sml2020_svenerossys_id_usuario_registrado']
            ];
        if (!registrar_auditoria_cotizacion($link2, $auditData)) {
            throw new Exception("Error al registrar auditoría");
        }
        
        // Crear kardex para cada producto
        foreach ($productsArr as $product) {
            $productId = $product[0];
            $nombre = devuelve_campo("productos", "nombre", "id", $productId);
            $descripcion = devuelve_campo("productos", "descripcion", "id", $productId);
            $id_marca = devuelve_campo("productos", "id_marca", "id", $productId);
            $marca = devuelve_campo("marcas", "nombre_marca", "id", $id_marca);
            
            CreaKardex(
                $numero_documento,
                $id_tipo_documento,
                $nombre,
                $descripcion,
                $id_marca,
                $marca,
                $product[2], // cantidad
                $product[3], // precio unitario
                $product[4], // precio total
                0
            );
            
            logs_db("Se agregó el Kardex para el producto: $productId", $_SERVER['PHP_SELF']);
            
             
        }
        
        $data['success'] = true;
        $data['results'][] = array('documento' => $numero_documento);
    } else {
        throw new Exception("Datos incompletos para generar la cotización");
    }
} catch (Exception $e) {
    $data['error'] = $e->getMessage();
}

echo json_encode($data);

function obtenerClientePorId($id) {
    $link = conectarse();
    
    $query = "SELECT nombre, apellido1, apellido2 FROM clientes WHERE id = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        throw new Exception("Cliente no encontrado");
    }
}
?>