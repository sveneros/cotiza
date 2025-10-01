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

function getAllClients() {
    $link = conectarse();
    $sql = "SELECT c.*, u.usr as usuario FROM clientes c LEFT JOIN usuarios u ON c.id_usuario = u.id";
    $result = mysqli_query($link, $sql);
    $clients = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $clients[] = $row;
    }
    mysqli_close($link);
    logs_db("Obteniendo todos los clientes ", $_SERVER['PHP_SELF']);
    return $clients;
}

function getClientById($id) {
    $link = conectarse();
    $sql = "SELECT c.*, u.usr as usuario FROM clientes c LEFT JOIN usuarios u ON c.id_usuario = u.id WHERE c.id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $client = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Obteniendo Cliente con id: ".$id, $_SERVER['PHP_SELF']);
    return $client;
}

function createClient($nombre, $apellido1, $apellido2, $cel1, $cel2, $direccion, $email, $nit, $razon_social, $estado, $cargo, $avalado_por, $rubro, $web) {
    $link = conectarse();
    
    // Escapar datos
    $nombre = mysqli_real_escape_string($link, $nombre);
    $apellido1 = mysqli_real_escape_string($link, $apellido1);
    $apellido2 = mysqli_real_escape_string($link, $apellido2);
    $cel1 = mysqli_real_escape_string($link, $cel1);
    $cel2 = mysqli_real_escape_string($link, $cel2);
    $direccion = mysqli_real_escape_string($link, $direccion);
    $email = mysqli_real_escape_string($link, $email);
    $nit = mysqli_real_escape_string($link, $nit);
    $razon_social = mysqli_real_escape_string($link, $razon_social);
    $estado = mysqli_real_escape_string($link, $estado);
    $cargo = mysqli_real_escape_string($link, $cargo);
    $avalado_por = mysqli_real_escape_string($link, $avalado_por);
    $rubro = mysqli_real_escape_string($link, $rubro);
    $web = mysqli_real_escape_string($link, $web);
    
    // Crear usuario para el cliente
    $username = strtolower(substr($nombre, 0, 1) . $apellido1);
    $password = bin2hex(random_bytes(4)); // Genera una contraseña aleatoria de 8 caracteres
    $password_encrypted = Security::encrypt($password);
    
    $sql_user = "INSERT INTO usuarios (nombre, apellido1, apellido2, celular,ci,direccion,ciudad, email, usr, pwd, id_rol, estado) 
                VALUES ('$nombre', '$apellido1', '$apellido2', '$cel1','$nit','$direccion',null, '$email', '$username', '$password_encrypted', 3, 'V')";
    mysqli_query($link, $sql_user);
    $id_usuario = mysqli_insert_id($link);
    
    // Crear cliente
    $sql = "INSERT INTO clientes (nombre, apellido1, apellido2, cel1, cel2, direccion, email, nit, razon_social, estado, cargo, avalado_por, fecha_registro, rubro, web, id_usuario) 
            VALUES ('$nombre', '$apellido1', '$apellido2', '$cel1', '$cel2', '$direccion', '$email', '$nit', '$razon_social', '$estado', '$cargo', '$avalado_por', NOW(), '$rubro', '$web', '$id_usuario')";
    $result = mysqli_query($link, $sql);
    $newId = mysqli_insert_id($link);
    
    logs_db("Creando Cliente: ".$nombre." ".$apellido1." ".$apellido2." con usuario: ".$username." y password: ".$password, $_SERVER['PHP_SELF']);
    
    mysqli_close($link);
    return ['id' => $newId, 'username' => $username, 'password' => $password];
}

function updateClient($id, $nombre, $apellido1, $apellido2, $cel1, $cel2, $direccion, $email, $nit, $razon_social, $estado, $cargo, $avalado_por, $rubro, $web) {
    $link = conectarse();
    
    // Escapar datos
    $nombre = mysqli_real_escape_string($link, $nombre);
    $apellido1 = mysqli_real_escape_string($link, $apellido1);
    $apellido2 = mysqli_real_escape_string($link, $apellido2);
    $cel1 = mysqli_real_escape_string($link, $cel1);
    $cel2 = mysqli_real_escape_string($link, $cel2);
    $direccion = mysqli_real_escape_string($link, $direccion);
    $email = mysqli_real_escape_string($link, $email);
    $nit = mysqli_real_escape_string($link, $nit);
    $razon_social = mysqli_real_escape_string($link, $razon_social);
    $estado = mysqli_real_escape_string($link, $estado);
    $cargo = mysqli_real_escape_string($link, $cargo);
    $avalado_por = mysqli_real_escape_string($link, $avalado_por);
    $rubro = mysqli_real_escape_string($link, $rubro);
    $web = mysqli_real_escape_string($link, $web);
    
    $sql = "UPDATE clientes SET 
            nombre = '$nombre', 
            apellido1 = '$apellido1', 
            apellido2 = '$apellido2', 
            cel1 = '$cel1', 
            cel2 = '$cel2', 
            direccion = '$direccion', 
            email = '$email', 
            nit = '$nit', 
            razon_social = '$razon_social', 
            estado = '$estado',
            cargo = '$cargo',
            avalado_por = '$avalado_por',
            rubro = '$rubro',
            web = '$web'
            WHERE id = $id";
    
    $result = mysqli_query($link, $sql);
    logs_db("Editando Cliente: ".$nombre." ".$apellido1." ".$apellido2, $_SERVER['PHP_SELF']);
    mysqli_close($link);
    return $result;
}

function deleteClient($id) {
    $link = conectarse();
    
    // Primero obtenemos el id_usuario para eliminar el usuario asociado
    $sql = "SELECT id_usuario FROM clientes WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $client = mysqli_fetch_assoc($result);
    
    if ($client && $client['id_usuario']) {
        $sql_delete_user = "DELETE FROM usuarios WHERE id = ?";
        $stmt_delete = mysqli_prepare($link, $sql_delete_user);
        mysqli_stmt_bind_param($stmt_delete, "i", $client['id_usuario']);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
    }
    
    // Ahora eliminamos el cliente
    $sql = "DELETE FROM clientes WHERE id = ?";
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
            $client = getClientById($id);
            logs_db("Obteniendo Cliente con id: ".$id, $_SERVER['PHP_SELF']);
            echo json_encode($client);
        } else {
            $clients = getAllClients();
            echo json_encode($clients);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = createClient(
            $data['nombre'], 
            $data['apellido1'], 
            $data['apellido2'], 
            $data['cel1'], 
            $data['cel2'], 
            $data['direccion'], 
            $data['email'], 
            $data['nit'], 
            $data['razon_social'], 
            $data['estado'],
            $data['cargo'],
            $data['avalado_por'],
            $data['rubro'],
            $data['web']
        );
        echo json_encode(['success' => true, 'data' => $result]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $result = updateClient(
            $id, 
            $data['nombre'], 
            $data['apellido1'], 
            $data['apellido2'], 
            $data['cel1'], 
            $data['cel2'], 
            $data['direccion'], 
            $data['email'], 
            $data['nit'], 
            $data['razon_social'], 
            $data['estado'],
            $data['cargo'],
            $data['avalado_por'],
            $data['rubro'],
            $data['web']
        );
        echo json_encode(['success' => $result]);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteClient($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}
?>