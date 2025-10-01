<?php
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

function getAllLogs() {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM logs");
    $Logs = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $Logs[] = $row;
    }
    mysqli_close($link);
    return $Logs;
}

function getLogById($id) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT * FROM logs WHERE id = $id");
    $Log = mysqli_fetch_assoc($result);
    mysqli_close($link);
    return $Log;
}

function getLogByDate($date) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT usuario,mensaje,archivo,fecha_hora,estado FROM logs WHERE fecha_hora like '$date%'");
    $Log = mysqli_fetch_assoc($result);
    mysqli_close($link);
    return $Log;
}

/* function createLog($descripcion, $usuario, $route, $status) {
    $link = conectarse();
    $descripcion = mysqli_real_escape_string($link, $descripcion);
    $usuario = mysqli_real_escape_string($link, $usuario);
    $fecha_hora = date('Y-m-d H:i:s');
    $result = mysqli_query($link, "INSERT INTO logs (usuario,mensaje,archivo,fecha_hora, estado) VALUES ('$usuario','$descripcion','$route', '$fecha_hora', 'VIG')");
    $newId = mysqli_insert_id($link);
    mysqli_close($link);
    return $newId;
} */

function updateLog($id, $descripcion, $estado) {
    $link = conectarse();
    $descripcion = mysqli_real_escape_string($link, $descripcion);
    $estado = mysqli_real_escape_string($link, $estado);
    $result = mysqli_query($link, "UPDATE logs SET descripcion = '$descripcion', estado = '$estado' WHERE id = $id");
    mysqli_close($link);
    return $result;
}

function deleteLog($id) {
    $link = conectarse();
    $result = mysqli_query($link, "DELETE FROM logs WHERE id = $id");
    mysqli_close($link);
    return $result;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $Log = getLogById($id);
            echo json_encode($Log);
        } else {
            $Logs = getAllLogs();
            echo json_encode($Logs);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = createLog($data['descripcion'], $data['estado'],$data['route']);
        echo json_encode(['id' => $id]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $result = updateLog($id, $data['descripcion'], $data['estado']);
        echo json_encode(['success' => $result]);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteLog($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}