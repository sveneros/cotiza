<?php session_start();
include("../controllers/conx.php");
include("../controllers/funciones.php");
logs_db("Cerrando sesion | usuario: ". $_SESSION['sml2020_svenerossys_usuario_registrado'], $_SERVER['PHP_SELF']); 
error_log("Cerrando sesion | usuario: ". $_SESSION['sml2020_svenerossys_usuario_registrado'] . " | ". date("l jS \of F, Y, h:i:s A") . "| File: " . $_SERVER['PHP_SELF'], E_USER_NOTICE); //E_USER_NOTICE, E_USER_ERROR, E_USER_WARNING
session_unset();
session_destroy();
header("location:index.php");?>
  