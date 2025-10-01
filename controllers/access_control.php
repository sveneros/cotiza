<?php

function checkMenuAccess($menu) {
    // Verificar si la sesión está iniciada y si el usuario tiene un rol asignado
    if (!isset($_SESSION['sml2020_svenerossys_id_rol_usuario_registrado'])) {
        return false;
    }

    $id_rol = $_SESSION['sml2020_svenerossys_id_rol_usuario_registrado'];

    $link = conectarse();


    $id_menu = devuelve_campo("menu","id","enlace",$menu);
    
    $sql = "SELECT 1 FROM rol_menu WHERE id_rol = ? AND id_menu = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_rol, $id_menu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $hasAccess = mysqli_num_rows($result) > 0;
    
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    
    return $hasAccess;
}

function validateAccessOrDie($id_menu) {
    if (!checkMenuAccess($id_menu)) {
        if($_SESSION['sml2020_svenerossys_id_rol_usuario_registrado']=="3"){
            // Mostrar mensaje de acceso restringido
            echo '<div class="container-fluid">';
            echo '    <div class="row justify-content-center mt-5">';
            echo '        <div class="col-md-8 text-center">';
            echo '            <img src="../assets/images/access-denied.png" alt="Acceso denegado" class="img-fluid mb-4" style="max-height: 300px;">';
            echo '            <h2 class="mb-3">Acceso Restringido</h2>';
            echo '            <p class="lead">No tienes permisos para acceder a esta sección.</p>';
            echo '            <a href="tienda.php" class="btn btn-primary mt-3">Volver al inicio</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';
            include('../layout/footer.php');
            exit();
        }
        else{
            // Mostrar mensaje de acceso restringido
            echo '<div class="container-fluid">';
            echo '    <div class="row justify-content-center mt-5">';
            echo '        <div class="col-md-8 text-center">';
            echo '            <img src="../assets/images/access-denied.png" alt="Acceso denegado" class="img-fluid mb-4" style="max-height: 300px;">';
            echo '            <h2 class="mb-3">Acceso Restringido</h2>';
            echo '            <p class="lead">No tienes permisos para acceder a esta sección.</p>';
            echo '            <a href="index.php" class="btn btn-primary mt-3">Volver al inicio</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';
            include('../layout/footer.php');
            exit();
        }
        
    }
}
?>