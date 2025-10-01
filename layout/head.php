<?php session_start();
 
if (!isset($_SESSION['sml2020_svenerossys_CREATED'])) {$_SESSION['sml2020_svenerossys_CREATED'] = time();} else if (time() - $_SESSION['sml2020_svenerossys_CREATED'] > 3600) { header("location:logout.php");}else {$_SESSION['sml2020_svenerossys_CREATED'] = time();} $basename = substr(strtolower(basename($_SERVER['PHP_SELF'])),0,strlen(basename($_SERVER['PHP_SELF']))-4);if(!isset($_SESSION['sml2020_svenerossys_usuario_registrado']) && $basename != 'login')header("location:login.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
          content="Aplitec, cotizaciones">
    <meta name="keywords"
          content="Aplitec, cotizaciones">
    <meta name="author" content="la-themes">
    <link rel="icon" href="../assets/images/logo/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../assets/images/logo/favicon.png" type="image/x-icon">
    <title>APLITEC - cotizaciones</title>
</head>