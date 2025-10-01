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

function getAllBrands() {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM marcas order by codigo, nombre_marca");
    $brands = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $brands[] = $row;
    }
    mysqli_close($link);
    logs_db("Obtener todas las marcas | usuario: ". $_SESSION['sml2020_svenerossys_usuario_registrado'], $_SERVER['PHP_SELF']);
    return $brands;
}

function getBrandById($id) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM marcas WHERE id = $id");
    $brand = mysqli_fetch_assoc($result);
    mysqli_close($link);
    logs_db("Obtener marca por id: ".$id , $_SERVER['PHP_SELF']);
    return $brand;
}

function createBrand($data) {
    $link = conectarse();
    
    // Escape all input data
    $codigo = mysqli_real_escape_string($link, $data['codigo']);
    $nombre_marca = mysqli_real_escape_string($link, $data['nombre_marca']);
    $estado = mysqli_real_escape_string($link, $data['estado']);
    $pais_origen = mysqli_real_escape_string($link, $data['pais_origen']);
    $contacto_nombre = mysqli_real_escape_string($link, $data['contacto_nombre']);
    $contacto_cargo = mysqli_real_escape_string($link, $data['contacto_cargo']);
    $contacto_direccion = mysqli_real_escape_string($link, $data['contacto_direccion']);
    $contacto_telefono = mysqli_real_escape_string($link, $data['contacto_telefono']);
    $contacto_email = mysqli_real_escape_string($link, $data['contacto_email']);
    $fecha_registro = mysqli_real_escape_string($link, $data['fecha_registro']);
    $pagina_web = mysqli_real_escape_string($link, $data['pagina_web']);
    
    $query = "INSERT INTO marcas (
        codigo, nombre_marca, estado, pais_origen, 
        contacto_nombre, contacto_cargo, contacto_direccion, 
        contacto_telefono, contacto_email, fecha_registro, pagina_web
    ) VALUES (
        '$codigo', '$nombre_marca', '$estado', '$pais_origen',
        '$contacto_nombre', '$contacto_cargo', '$contacto_direccion',
        '$contacto_telefono', '$contacto_email', '$fecha_registro', '$pagina_web'
    )";
    
    $result = mysqli_query($link, $query);
    $newId = mysqli_insert_id($link);
    mysqli_close($link);
    logs_db("Marca Creada: ".$nombre_marca , $_SERVER['PHP_SELF']);
    return $newId;
}

function updateBrand($data) {
    $link = conectarse();
    
    // Escape all input data
    $id = mysqli_real_escape_string($link, $data['id']);
    $codigo = mysqli_real_escape_string($link, $data['codigo']);
    $nombre_marca = mysqli_real_escape_string($link, $data['nombre_marca']);
    $estado = mysqli_real_escape_string($link, $data['estado']);
    $pais_origen = mysqli_real_escape_string($link, $data['pais_origen']);
    $contacto_nombre = mysqli_real_escape_string($link, $data['contacto_nombre']);
    $contacto_cargo = mysqli_real_escape_string($link, $data['contacto_cargo']);
    $contacto_direccion = mysqli_real_escape_string($link, $data['contacto_direccion']);
    $contacto_telefono = mysqli_real_escape_string($link, $data['contacto_telefono']);
    $contacto_email = mysqli_real_escape_string($link, $data['contacto_email']);
    $fecha_registro = mysqli_real_escape_string($link, $data['fecha_registro']);
    $pagina_web = mysqli_real_escape_string($link, $data['pagina_web']);
    
    $query = "UPDATE marcas SET 
        codigo = '$codigo',
        nombre_marca = '$nombre_marca', 
        estado = '$estado',
        pais_origen = '$pais_origen',
        contacto_nombre = '$contacto_nombre',
        contacto_cargo = '$contacto_cargo',
        contacto_direccion = '$contacto_direccion',
        contacto_telefono = '$contacto_telefono',
        contacto_email = '$contacto_email',
        fecha_registro = '$fecha_registro',
        pagina_web = '$pagina_web'
        WHERE id = $id";
    
    $result = mysqli_query($link, $query);
    mysqli_close($link);
    logs_db("Marca Editada: ".$nombre_marca , $_SERVER['PHP_SELF']);
    return $result;
}

function deleteBrand($id) {
    $link = conectarse();
    $result = mysqli_query($link, "DELETE FROM marcas WHERE id = $id");
    mysqli_close($link);
    logs_db("Marca Eliminada por id: ".$id , $_SERVER['PHP_SELF']);
    return $result;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $brand = getBrandById($id);
            echo json_encode($brand);
        } else {
            $brands = getAllBrands();
            echo json_encode($brands);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = createBrand($data);
        $result = json_encode(['id' => $id]);
        echo json_encode(['success' => $result]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = updateBrand($data);
        echo json_encode(['success' => $result]);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteBrand($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}