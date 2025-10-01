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

function getAllRoles() {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM roles");
    $roles = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $roles[] = $row;
    }
    mysqli_close($link);
    logs_db("Obtener todas las roles | usuario: ". $_SESSION['sml2020_svenerossys_usuario_registrado'], $_SERVER['PHP_SELF']);
    return $roles;
}

function getRoleById($id) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM roles WHERE id = $id");
    $brand = mysqli_fetch_assoc($result);
    mysqli_close($link);
    logs_db("Obtener marca por id: ".$id , $_SERVER['PHP_SELF']);
    return $brand;
}

function createRole($rol) {
    $link = conectarse();
    $rol = mysqli_real_escape_string($link, $rol);
    
    $result = mysqli_query($link, "INSERT INTO roles (rol) VALUES ('$rol')");
    $newId = mysqli_insert_id($link);
    mysqli_close($link);
    logs_db("Rol Creado: ".$rol , $_SERVER['PHP_SELF']);
    return $newId;
}

function updateRole($id, $rol) {
    $link = conectarse();
    $rol = mysqli_real_escape_string($link, $rol);
    
    $result = mysqli_query($link, "UPDATE roles SET rol = '$rol' WHERE id = $id");
    mysqli_close($link);
    logs_db("Rol Editada: ".$rol , $_SERVER['PHP_SELF']);
    return $result;
}

function deleteRole($id) {
    $link = conectarse();
    $result = mysqli_query($link, "DELETE FROM roles WHERE id = $id");
    mysqli_close($link);
    logs_db("Rol Eliminado por id: ".$id , $_SERVER['PHP_SELF']);
    return $result;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $role = getRoleById($id);
            echo json_encode($role);
        } else {
            $roles = getAllRoles();
            echo json_encode($roles);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = createRole($data['descripcion']);
        $result = json_encode(['id' => $id]);
        echo json_encode(['success' => $result]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $result = updateRole($id, $data['descripcion']);
        echo json_encode(['success' => $result]);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteRole($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}