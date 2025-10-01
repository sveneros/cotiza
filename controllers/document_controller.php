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
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Content-Type: application/json');

function getDocumentsByClient($id_cliente) {
    $link = conectarse();
    $sql = "SELECT * FROM documentos_clientes WHERE id_cliente = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_cliente);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $documents = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $documents[] = $row;
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Obteniendo documentos del cliente: ".$id_cliente, $_SERVER['PHP_SELF']);
    return $documents;
}

function uploadDocument($id_cliente, $tipo_documento, $nombre_archivo, $ruta_archivo) {
    $link = conectarse();
    $sql = "INSERT INTO documentos_clientes (id_cliente, tipo_documento, nombre_archivo, ruta_archivo, fecha_subida) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $id_cliente, $tipo_documento, $nombre_archivo, $ruta_archivo);
    $result = mysqli_stmt_execute($stmt);
    $newId = mysqli_insert_id($link);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Subiendo documento para cliente: ".$id_cliente, $_SERVER['PHP_SELF']);
    return $newId;
}

function deleteDocument($id) {
    $link = conectarse();
    // Primero obtenemos la ruta del archivo para eliminarlo físicamente
    $sql = "SELECT ruta_archivo FROM documentos_clientes WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $document = mysqli_fetch_assoc($result);
    
    if ($document && file_exists($document['ruta_archivo'])) {
        unlink($document['ruta_archivo']);
    }
    
    // Ahora eliminamos el registro de la base de datos
    $sql = "DELETE FROM documentos_clientes WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Eliminando documento: ".$id, $_SERVER['PHP_SELF']);
    return $result;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id_cliente'])) {
            $id_cliente = $_GET['id_cliente'];
            $documents = getDocumentsByClient($id_cliente);
            echo json_encode($documents);
        }
        break;
    case 'POST':
        // Procesar subida de archivo
        if (isset($_FILES['documento']) && isset($_POST['id_cliente']) && isset($_POST['tipo_documento'])) {
            $id_cliente = $_POST['id_cliente'];
            $tipo_documento = $_POST['tipo_documento'];
            $uploadDir = '../uploads/clientes/' . $id_cliente . '/';
            
            // Crear directorio si no existe
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = basename($_FILES['documento']['name']);
            $filePath = $uploadDir . uniqid() . '_' . $fileName;
            
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $filePath)) {
                $newId = uploadDocument($id_cliente, $tipo_documento, $fileName, $filePath);
                echo json_encode(['success' => true, 'id' => $newId]);
            } else {
                echo json_encode(['error' => 'Error al subir el archivo']);
            }
        }
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteDocument($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}
?>