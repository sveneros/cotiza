<?php
session_start();
include_once("Security.php");
include("conx.php");
include("funciones.php");

header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Limitar intentos de login
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

function attemptLogin($username, $password) {
    // Verificar intentos recientes
    if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_attempt_time']) < 300) {
        logs_db("Bloqueado por muchos intentos: ".$username, $_SERVER['PHP_SELF']);
        return ['status' => 'error', 'message' => 'Demasiados intentos. Espere 5 minutos.'];
    }

    $link = conectarse();
    $username = htmlspecialchars($username, ENT_QUOTES);
    
    logs_db("Intento de login, usuario: ".$username, $_SERVER['PHP_SELF']);
    
    $sql = "SELECT `id`, `nombre`, `usr`, `pwd`, `id_rol`, `estado`, `email` FROM usuarios WHERE `usr` LIKE '".$username."' and estado='V'";
    $result = mysqli_query($link, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        
        if(strcmp($row['pwd'], Security::encrypt($password)) == 0) {
            // Login exitoso - resetear contador de intentos
            $_SESSION['login_attempts'] = 0;
            
            // Crear sesión de usuario
            createUserSession($row);
            
            logs_db("Usuario: ".$username." | INGRESO AL SISTEMA", $_SERVER['PHP_SELF']);
            return ['status' => 'success', 'message' => 'Login exitoso' , 'id_rol' => $row['id_rol']];
        } else {
            // Password incorrecto
            handleFailedAttempt();
            logs_db("Password incorrecto para usuario: ".$username, $_SERVER['PHP_SELF']);
            return ['status' => 'error', 'message' => 'Credenciales incorrectas'];
        }
    } else {
        // Usuario no existe
        handleFailedAttempt();
        logs_db("Usuario no identificado: ".$username, $_SERVER['PHP_SELF']);
        return ['status' => 'error', 'message' => 'Credenciales incorrectas'];
    }
}

function createUserSession($userData) {
    $_SESSION['sml2020_svenerossys_usuario_registrado'] = $userData['usr'];
    $_SESSION['sml2020_svenerossys_id_usuario_registrado'] = $userData['id'];
    $_SESSION['sml2020_svenerossys_email_usuario_registrado'] = $userData['email'];
    $_SESSION['sml2020_svenerossys_nombre_usuario_registrado'] = $userData['nombre'];
    $_SESSION['sml2020_svenerossys_id_rol_usuario_registrado'] = $userData['id_rol'];
    if($userData['id_rol']=="3")
    $_SESSION['sml2020_svenerossys_id_cliente_usuario_registrado'] = getClientID($userData['id']);
    
    $_SESSION['sml2020_svenerossys_CREATED'] = time();
    
    // Obtener nombre del rol
    $link = conectarse();
    $rolResult = mysqli_query($link, "SELECT rol FROM roles WHERE id = ".$userData['id_rol']);
    $rolData = mysqli_fetch_assoc($rolResult);
    $_SESSION['sml2020_svenerossys_rol_usuario_registrado'] = $rolData['rol'];
    mysqli_close($link);
}

function handleFailedAttempt() {
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
}


// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
   
    
    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit;
    }
    
    $result = attemptLogin($data['username'], $data['password']);
    echo json_encode($result);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}

function getClientID($el_id_usuario) {
    $link = conectarse();
    $result = mysqli_query($link, "SELECT id FROM clientes where id_usuario='$el_id_usuario'");
    $row = mysqli_fetch_assoc($result);
    mysqli_close($link);
    //logs_db("Obtener id Cliente | usuario: ". $_SESSION['sml2020_svenerossys_usuario_registrado'], $_SERVER['PHP_SELF']);
    return $row["id"];
}
?>