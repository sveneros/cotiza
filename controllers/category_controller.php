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

function getAllCategories() {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM categorias");
    $categories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    mysqli_close($link);
     logs_db("Obteniendo Categorias" , $_SERVER['PHP_SELF']);
    return $categories;
}

function getCategoryById($id) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM categorias WHERE id = $id order by id_marca, codigo, descripcion;");
    $category = mysqli_fetch_assoc($result);
    mysqli_close($link);
     logs_db("Obteniendo Categoria por id: ".$id , $_SERVER['PHP_SELF']);
    return $category;
}

function createCategory($id_marca,$codigo,$descripcion,$padre, $estado) {
    $link = conectarse();
    $codigo = mysqli_real_escape_string($link, $codigo);
    $descripcion = mysqli_real_escape_string($link, $descripcion);
    $estado = mysqli_real_escape_string($link, $estado);
    $result = mysqli_query($link, "INSERT INTO categorias (id_marca,codigo,descripcion, padre, estado) VALUES ('$id_marca','$codigo','$descripcion','$padre', '$estado')");
    $newId = mysqli_insert_id($link);
    mysqli_close($link);
     logs_db("Creando Categoria: ".$descripcion  , $_SERVER['PHP_SELF']);
    return $newId;
}

function updateCategory($id, $id_marca, $codigo, $descripcion,$padre, $estado) {
    $link = conectarse();
    $codigo = mysqli_real_escape_string($link, $codigo);
    $descripcion = mysqli_real_escape_string($link, $descripcion);
    $estado = mysqli_real_escape_string($link, $estado);
    $result = mysqli_query($link, "UPDATE categorias SET id_marca = '$id_marca', codigo = '$codigo', descripcion = '$descripcion',padre = '$padre', estado = '$estado' WHERE id = $id");
    mysqli_close($link);
     logs_db("Editando Categoria: ".$descripcion  , $_SERVER['PHP_SELF']);
    return $result;
}

function deleteCategory($id) {
    $link = conectarse();
    $result = mysqli_query($link, "DELETE FROM categorias WHERE id = $id");
    mysqli_close($link);
    return $result;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $category = getCategoryById($id);
            echo json_encode($category);
        } else {
            $categories = getAllCategories();
            echo json_encode($categories);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = createCategory($data['id_marca'], $data['codigo'],$data['descripcion'], $data['padre'],$data['estado']);
        $result = json_encode(['id' => $id]);
        echo json_encode(['success' => $result]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $result = updateCategory($id, $data['id_marca'],$data['codigo'], $data['descripcion'], $data['padre'], $data['estado']);
        echo json_encode(['success' => $result]);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteCategory($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}