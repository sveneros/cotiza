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

function getAllProducts() {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT p.id, p.nombre as producto_nombre, p.codigo as producto_codigo, 
       p.descripcion as producto_descripcion, p.id_categoria, 
       p.id_marca, m.nombre_marca as marca, c.descripcion as categoria, 
       puntos, p.estado, p.stock_actual as stock_actual, p.stock_minimo as stock_minimo,
       (
           SELECT JSON_ARRAYAGG(JSON_OBJECT('ruta', i.ruta, 'id', i.id))
           FROM (
               SELECT ruta, id 
               FROM imagenes 
               WHERE entidad_tipo = 'producto' AND entidad_id = p.id
               LIMIT 5
           ) i
       ) as imagenes
FROM productos as p 
INNER JOIN marcas as m ON p.id_marca = m.id 
INNER JOIN categorias as c ON p.id_categoria = c.id");
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Procesar imágenes
        $row['imagenes'] = [];
        if (!empty($row['imagenes'])) {
            $imagePairs = explode(',', $row['imagenes']);
            foreach ($imagePairs as $pair) {
                $parts = explode('|||', $pair);
                if (count($parts) === 2) {
                    $row['imagenes'][] = [
                        'ruta' => str_replace('../', '', $parts[0]),
                        'id' => $parts[1]
                    ];
                }
            }
        }
        $products[] = $row;
    }
    mysqli_close($link);
    logs_db("Obtener todos los productos", $_SERVER['PHP_SELF']);
    return $products;
}

function getProductById($id) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT p.id, p.nombre as producto_nombre, p.codigo as producto_codigo, 
                                  p.descripcion as producto_descripcion, p.nombre, p.id_categoria, 
                                  p.id_marca, m.descripcion as marca, c.descripcion as categoria, 
                                  puntos, p.estado, p.stock_actual as stock_actual, p.stock_minimo as stock_minimo 
                                  FROM productos as p 
                                  INNER JOIN marcas as m ON p.id_marca = m.id 
                                  INNER JOIN categorias as c ON p.id_categoria = c.id 
                                  WHERE p.id = $id");
    $product = mysqli_fetch_assoc($result);
    mysqli_close($link);
    logs_db("Obtener producto por id: ".$id , $_SERVER['PHP_SELF']);
    return $product;
}

function createProduct($codigo, $nombre, $descripcion, $id_marca, $id_categoria, $puntos, $estado, $stock_actual, $stock_minimo) {
    $link = conectarse();
    $codigo = mysqli_real_escape_string($link, $codigo);
    $nombre = mysqli_real_escape_string($link, $nombre);
    $descripcion = mysqli_real_escape_string($link, $descripcion);
    $estado = mysqli_real_escape_string($link, $estado);
    $stock_actual = (int) $stock_actual;
    $stock_minimo = (int) $stock_minimo;

    $result = mysqli_query($link, "INSERT INTO productos (codigo, nombre, descripcion, id_marca, id_categoria, puntos, estado, stock_actual, stock_minimo) 
                                  VALUES ('$codigo', '$nombre', '$descripcion', '$id_marca', '$id_categoria', '$puntos', '$estado', '$stock_actual', '$stock_minimo')");
    
    if ($result) {
        $newId = mysqli_insert_id($link);
        logs_db("Se agregó el producto: cod:".$codigo." descr: ".$nombre , $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => true, 'id' => $newId];
    } else {
        $error = mysqli_error($link);
        logs_db("Error al agregar producto: ".$error, $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => false, 'error' => $error];
    }
}

function updateProduct($id, $codigo, $nombre, $descripcion, $id_marca, $id_categoria, $puntos, $estado, $stock_actual, $stock_minimo) {
    $link = conectarse();
    $codigo = mysqli_real_escape_string($link, $codigo);
    $nombre = mysqli_real_escape_string($link, $nombre);
    $descripcion = mysqli_real_escape_string($link, $descripcion);
    $estado = mysqli_real_escape_string($link, $estado);
    $stock_actual = (int) $stock_actual;
    $stock_minimo = (int) $stock_minimo;
    
    $result = mysqli_query($link, "UPDATE productos SET 
                                  codigo = '$codigo', 
                                  nombre = '$nombre', 
                                  descripcion = '$descripcion', 
                                  id_marca = '$id_marca', 
                                  id_categoria = '$id_categoria', 
                                  puntos = $puntos, 
                                  estado = '$estado',
                                stock_actual = '$stock_actual',
                                stock_minimo = '$stock_minimo'
                                  WHERE id = $id");
    
    if ($result) {
        logs_db("Se editó el producto: cod:".$codigo." descr: ".$nombre , $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => true];
    } else {
        $error = mysqli_error($link);
        logs_db("Error al editar producto: ".$error, $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => false, 'error' => $error];
    }
}

function deleteProduct($id) {
    $link = conectarse();
    $result = mysqli_query($link, "DELETE FROM productos WHERE id = $id");
    
    if ($result) {
        logs_db("Se eliminó el producto: id:". $id, $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => true];
    } else {
        $error = mysqli_error($link);
        logs_db("Error al eliminar producto: ".$error, $_SERVER['PHP_SELF']);
        mysqli_close($link);
        return ['success' => false, 'error' => $error];
    }
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $product = getProductById($id);
            echo json_encode($product);
        } else {
            $products = getAllProducts();
            echo json_encode($products);
        }
        break;
    case 'POST':
        $id = createProduct(
            $data['codigo'],
            $data['nombre'], 
            $data['descripcion'], 
            $data['id_marca'], 
            $data['id_categoria'], 
            $data['puntos'], 
            $data['estado'],
            stock_actual: $data['stock_actual'],
            stock_minimo: $data['stock_minimo']
            
        );
        echo json_encode($id);
        break;
    case 'PUT':
        $result = updateProduct(
            $data['id'],
            $data['codigo'],
            $data['nombre'], 
            $data['descripcion'], 
            $data['id_marca'], 
            $data['id_categoria'], 
            $data['puntos'], 
            $data['estado'],
            stock_actual: $data['stock_actual'],
            stock_minimo: $data['stock_minimo']
        );
        echo json_encode($result);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteProduct($id);
        echo json_encode($result);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}