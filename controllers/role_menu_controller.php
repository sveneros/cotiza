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

function getAllRolesWithMenus() {
    $link = conectarse();
    
    // Obtener todos los roles
    $roles = [];
    $sql = "SELECT id, rol FROM roles ORDER BY id";
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $roles[] = $row;
    }
    
    // Obtener todos los permisos (rol_menu)
    $permissions = [];
    $sql = "SELECT rm.id_rol, rm.id_menu FROM rol_menu rm ORDER BY rm.id_rol, rm.id_menu";
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $roleId = $row['id_rol'];
        if (!isset($permissions[$roleId])) {
            $permissions[$roleId] = [];
        }
        $permissions[$roleId][] = $row['id_menu'];
    }
    
    mysqli_close($link);
    
    return [
        'roles' => $roles,
        'permissions' => $permissions
    ];
}

function getDataForEdit($roleId) {
    $link = conectarse();
    
    // Obtener todos los menús disponibles
    $menus = [];
    $sql = "SELECT id, texto, enlace, padre as categoria FROM menu ORDER BY padre, texto";
    $result = mysqli_query($link, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $menus[] = $row;
    }
    
    // Obtener los menús asignados a este rol
    $assignedMenus = [];
    $sql = "SELECT id_menu FROM rol_menu WHERE id_rol = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $roleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $assignedMenus[] = $row['id_menu'];
    }
    mysqli_stmt_close($stmt);
    
    mysqli_close($link);
    
    return [
        'menus' => $menus,
        'assignedMenus' => $assignedMenus
    ];
}

function updateRolePermissions($roleId, $menus) {
    $link = conectarse();
    
    mysqli_begin_transaction($link);
    
    try {
        // Eliminar todos los permisos actuales del rol
        $sql = "DELETE FROM rol_menu WHERE id_rol = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $roleId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Insertar los nuevos permisos
        if (!empty($menus)) {
            $values = [];
            $params = [];
            $types = '';
            
            foreach ($menus as $menuId) {
                $values[] = "(?, ?)";
                $params[] = $roleId;
                $params[] = $menuId;
                $types .= "ii";
            }
            
            $sql = "INSERT INTO rol_menu (id_rol, id_menu) VALUES " . implode(", ", $values);
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        mysqli_commit($link);
        mysqli_close($link);
        
        logs_db("Permisos actualizados para rol ID: $roleId", $_SERVER['PHP_SELF']);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($link);
        mysqli_close($link);
        logs_db("Error al actualizar permisos: " . $e->getMessage(), $_SERVER['PHP_SELF']);
        return false;
    }
}

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

switch ($action) {
    case 'getAllWithMenus':
        $data = getAllRolesWithMenus();
        echo json_encode($data);
        break;
        
    case 'getForEdit':
        $roleId = isset($_GET['roleId']) ? (int)$_GET['roleId'] : 0;
        if ($roleId > 0) {
            $data = getDataForEdit($roleId);
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'ID de rol inválido']);
        }
        break;
        
    case 'update':
        $roleId = isset($_POST['roleId']) ? (int)$_POST['roleId'] : 0;
        $menus = isset($_POST['menus']) ? $_POST['menus'] : [];
        
        if ($roleId > 0) {
            $success = updateRolePermissions($roleId, $menus);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ID de rol inválido']);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
        break;
}