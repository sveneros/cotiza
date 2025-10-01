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

function getAllExchangeRates() {
    $link = conectarse();
    $sql = "SELECT tc.id, tc.fecha, tc.tasa, tc.aprobado, tc.creado_en, tc.actualizado_en, 
                   u1.nombre as registrado_por, u2.nombre as aprobado_por
            FROM tipo_cambio tc
            LEFT JOIN usuarios u1 ON tc.registrado_por = u1.id
            LEFT JOIN usuarios u2 ON tc.aprobado_por = u2.id
            ORDER BY tc.fecha DESC";
    $result = mysqli_query($link, $sql);
    $rates = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rates[] = $row;
    }
    mysqli_close($link);
    logs_db("Obteniendo todos los tipos de cambio", $_SERVER['PHP_SELF']);
    return $rates;
}

function getApprovedExchangeRates() {
    $link = conectarse();
    $sql = "SELECT id, fecha, tasa, creado_en, actualizado_en 
            FROM tipo_cambio 
            WHERE aprobado = 1 
            ORDER BY fecha DESC";
    $result = mysqli_query($link, $sql);
    $rates = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rates[] = $row;
    }
    mysqli_close($link);
    return $rates;
}

function getExchangeRateByDate($fecha) {
    $link = conectarse();
    $sql = "SELECT id, fecha, tasa FROM tipo_cambio WHERE fecha = ? AND aprobado = 1";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $fecha);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rate = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Obteniendo tipo de cambio para fecha: ".$fecha, $_SERVER['PHP_SELF']);
    return $rate;
}

function getCurrentExchangeRate() {
    $link = conectarse();
    $sql = "SELECT id, fecha, tasa FROM tipo_cambio WHERE aprobado = 1 ORDER BY fecha DESC LIMIT 1";
    $result = mysqli_query($link, $sql);
    $rate = mysqli_fetch_assoc($result);
    mysqli_close($link);
    logs_db("Obteniendo tipo de cambio actual", $_SERVER['PHP_SELF']);
    return $rate;
}

function createExchangeRate($fecha, $tasa) {
    $link = conectarse();
    $fecha = mysqli_real_escape_string($link, $fecha);
    $tasa = mysqli_real_escape_string($link, $tasa);
    $usuario_id = $_SESSION['sml2020_svenerossys_id_usuario_registrado'] ?? 0;
    
    // Primero verificamos si ya existe un registro para esta fecha
    $check_sql = "SELECT id FROM tipo_cambio WHERE fecha = ?";
    $check_stmt = mysqli_prepare($link, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $fecha);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($link);
        return ['success' => false, 'error' => 'Ya existe un tipo de cambio para la fecha ' . $fecha];
    }
    mysqli_stmt_close($check_stmt);
    
    // Si el usuario es administrador, se aprueba automáticamente
    $esAdministrador = ($_SESSION['sml2020_svenerossys_id_rol_usuario_registrado'] ?? 0) == 1;
    $aprobado = $esAdministrador ? 1 : 0;
    $aprobado_por = $esAdministrador ? $usuario_id : null;
    $aprobado_en = $esAdministrador ? date('Y-m-d H:i:s') : null;
    
    $sql = "INSERT INTO tipo_cambio (fecha, tasa, registrado_por, aprobado, aprobado_por, aprobado_en) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sdiiss", $fecha, $tasa, $usuario_id, $aprobado, $aprobado_por, $aprobado_en);
    $result = mysqli_stmt_execute($stmt);
    $newId = mysqli_insert_id($link);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    
    if ($result) {
        $accion = $esAdministrador ? "Creando y aprobando" : "Creando pendiente de aprobación";
        logs_db("$accion tipo de cambio para fecha: ".$fecha." con tasa: ".$tasa, $_SERVER['PHP_SELF']);
        return [
            'success' => true, 
            'id' => $newId,
            'aprobado' => $aprobado,
            'message' => $esAdministrador ? 'Tipo de cambio creado y aprobado' : 'Tipo de cambio creado, pendiente de aprobación'
        ];
    } else {
        return ['success' => false, 'error' => 'Error al crear el tipo de cambio'];
    }
}

function updateExchangeRate($id, $fecha, $tasa) {
    $link = conectarse();
    $fecha = mysqli_real_escape_string($link, $fecha);
    $tasa = mysqli_real_escape_string($link, $tasa);
    
    $sql = "UPDATE tipo_cambio SET fecha = ?, tasa = ?, aprobado = 0, aprobado_por = NULL, aprobado_en = NULL WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sdi", $fecha, $tasa, $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Actualizando tipo de cambio ID: ".$id." con tasa: ".$tasa, $_SERVER['PHP_SELF']);
    return $result;
}

function approveExchangeRate($id) {
    $link = conectarse();
    $usuario_id = $_SESSION['sml2020_svenerossys_id_usuario_registrado'] ?? 0;
    
    $sql = "UPDATE tipo_cambio SET aprobado = 1, aprobado_por = ?, aprobado_en = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    
    if ($result) {
        logs_db("Aprobando tipo de cambio ID: ".$id, $_SERVER['PHP_SELF']);
        return ['success' => true, 'message' => 'Tipo de cambio aprobado correctamente'];
    } else {
        return ['success' => false, 'error' => 'Error al aprobar el tipo de cambio'];
    }
}

function deleteExchangeRate($id) {
    $link = conectarse();
    $sql = "DELETE FROM tipo_cambio WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    logs_db("Eliminando tipo de cambio con ID: ".$id, $_SERVER['PHP_SELF']);
    return $result;
}

// Handle the request based on the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['fecha'])) {
            $fecha = $_GET['fecha'];
            $rate = getExchangeRateByDate($fecha);
            echo json_encode($rate);
        } elseif (isset($_GET['current'])) {
            $rate = getCurrentExchangeRate();
            echo json_encode($rate);
        } elseif (isset($_GET['approved'])) {
            $rates = getApprovedExchangeRates();
            echo json_encode($rates);
        } else {
            $rates = getAllExchangeRates();
            echo json_encode($rates);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = createExchangeRate($data['fecha'], $data['tasa']);
        echo json_encode($result);
        break;
    case 'PUT':
        if (isset($_GET['approve'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = approveExchangeRate($data['id']);
            echo json_encode($result);
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = updateExchangeRate($data['id'], $data['fecha'], $data['tasa']);
            echo json_encode(['success' => $result]);
        }
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $result = deleteExchangeRate($id);
        echo json_encode(['success' => $result]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}