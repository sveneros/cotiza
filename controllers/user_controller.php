<?php
session_start();
include("conx.php");
include("funciones.php");
include_once("Security.php");

header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

function getAllUsers() {
    $link = conectarse();
    $sql = "SELECT u.id as id, u.usr as usr, r.rol as rol, u.id_rol as id_rol, u.nombre as nombre, u.apellido1 as apellido1, u.apellido2 as apellido2, u.celular as celular, u.ci as ci, u.direccion as direccion, u.ciudad as ciudad, u.email as email, u.estado as estado  FROM usuarios as u inner join roles as r on u.id_rol=r.id where u.id_rol != 100; ";
    $result = mysqli_query($link, $sql);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_close($link);
    logs_db("Obteniendo todos los usuarios ", $_SERVER['PHP_SELF']);
    return $users;
}

function getUserById($id) {
    $link = conectarse();
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $User = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Obteniendo Usuario con id: ".$id  , $_SERVER['PHP_SELF']);
    return $User;
}

function createUser($nombre, $apellido1, $apellido2, $celular, $ci, $direccion, $ciudad, $email, $usr, $pwd, $id_rol, $estado) {
    
    $link = conectarse();
    $nombre = mysqli_real_escape_string($link, $nombre);
    $apellido1 = mysqli_real_escape_string($link, $apellido1);
    $apellido2 = mysqli_real_escape_string($link, $apellido2);
    $celular = mysqli_real_escape_string($link, $celular);
    $ci = mysqli_real_escape_string($link, $ci);
    $direccion = mysqli_real_escape_string($link, $direccion);
    $ciudad = mysqli_real_escape_string($link, $ciudad);
    $email = mysqli_real_escape_string($link, $email);
    $usr = mysqli_real_escape_string($link, $usr);
    $pwd = mysqli_real_escape_string($link, $pwd);
    $pwd = Security::encrypt($pwd);
    $estado = mysqli_real_escape_string($link, $estado);
    $sql = "INSERT INTO usuarios (nombre, apellido1, apellido2, celular, ci, direccion, ciudad, email, usr, pwd, id_rol, estado) VALUES ( '$nombre', '$apellido1', '$apellido2', '$celular', '$ci', '$direccion', '$ciudad', '$email', '$usr','$pwd','$id_rol', '$estado')";
    $result = mysqli_query($link, $sql);
    $newId = mysqli_insert_id($link);
    mysqli_close($link);
    logs_db("Creando Usuario: ".$nombre ." ".$apellido1." ".$apellido2  , $_SERVER['PHP_SELF']);
    return $newId;
}

function updateUser($id,$nombre, $apellido1, $apellido2, $celular, $ci, $direccion, $ciudad, $email, $id_rol, $estado) {
    $link = conectarse();
    $nombre = mysqli_real_escape_string($link, $nombre);
    $apellido1 = mysqli_real_escape_string($link, $apellido1);
    $apellido2 = mysqli_real_escape_string($link, $apellido2);
    $celular = mysqli_real_escape_string($link, $celular);
    $ci = mysqli_real_escape_string($link, $ci);
    $direccion = mysqli_real_escape_string($link, $direccion);
    $email = mysqli_real_escape_string($link, $email);
    
    $estado = mysqli_real_escape_string($link, $estado);
    $sql = "UPDATE usuarios SET nombre = '$nombre', apellido1 = '$apellido1', apellido2 = '$apellido2', celular = '$celular', ci = '$ci', direccion = '$direccion', ciudad = '$ciudad', email = '$email', id_rol = '$id_rol', estado = '$estado' WHERE id = $id";
    $result = mysqli_query($link, $sql);
    logs_db("Editando con nombre: ".$nombre ." ".$apellido1." ".$apellido2  , $_SERVER['PHP_SELF']);
    mysqli_close($link);
    return $result;
}

function updatePass($id, $pwd) {
    $link = conectarse();
    $pwd = mysqli_real_escape_string($link, $pwd);
    $pwd = Security::encrypt($pwd);
    $sql = "UPDATE usuarios SET pwd = '$pwd' WHERE id = $id";
    $result = mysqli_query($link, $sql);
    logs_db("Reseteando password para usuario con id: ".$id , $_SERVER['PHP_SELF']);
    mysqli_close($link);
    return $result;
}

function deleteUser($id) {
    $link = conectarse();
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    return $result;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $User = getUserById($id);
            logs_db("Obteniendo usuarios" , $_SERVER['PHP_SELF']);
            echo json_encode($User);
        } else {
            $Users = getAllUsers();
            echo json_encode($Users);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $newId = createUser($data['nombre'], $data['apellido1'], $data['apellido2'], $data['celular'], $data['ci'], $data['direccion'], $data['ciudad'], $data['email'], $data['usr'],$data['pwd'], $data['id_rol'], $data['estado']);
        $result = json_encode(['id' => $newId]);
        echo json_encode(['success' => $result]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $result = updateUser($data['id'],$data['nombre'], $data['apellido1'], $data['apellido2'], $data['celular'], $data['ci'], $data['direccion'], $data['ciudad'], $data['email'], $data['id_rol'], $data['estado']);
        echo json_encode(['success' => $result]);
        break;
    case 'PATCH':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $result = updatePass($data['id'], $data['password']);
        echo json_encode(['success' => $result]);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteUser($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}