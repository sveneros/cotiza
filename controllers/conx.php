<?php function conectarse(){include("config.php");
$link = mysqli_connect($server, $usr, $pwd, $dbname);
    return $link;}
    function keylogin(){return "SelLeoVenerosMam";}
    ?>