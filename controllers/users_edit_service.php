<?php 
include_once("Security.php");
include("conx.php");
include("funciones.php");


	$data = array(
		'results' => array(),
		'success' => false,
		'error' => '' 
	);
	
	if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['apellido1']) && isset($_POST['apellido2']) 
	&& isset($_POST['usr']) && isset($_POST['celular']) && isset($_POST['ci']) && isset($_POST['direccion']) && isset($_POST['ciudad']) && isset($_POST['email']) 
	&& isset($_POST['pwd']) && isset($_POST['rol']) && isset($_POST['estado']))
	{
		
		$link = conectarse();
		$id=$_POST['id'];
		
		$nombre=strtoupper(trim(mysqli_real_escape_string($link,utf8_decode($_POST['nombre']))));
		$apellido1=strtoupper(trim(mysqli_real_escape_string($link,utf8_decode($_POST['apellido1']))));
		$apellido2=strtoupper(trim(mysqli_real_escape_string($link,utf8_decode($_POST['apellido2']))));
		$celular=strtoupper(trim(mysqli_real_escape_string($link,utf8_decode($_POST['celular']))));
		$ci=strtoupper(trim(mysqli_real_escape_string($link,utf8_decode($_POST['ci']))));
		$direccion=strtoupper(trim(mysqli_real_escape_string($link,utf8_decode($_POST['direccion']))));
		$ciudad=strtoupper(trim(mysqli_real_escape_string($link,utf8_decode($_POST['ciudad']))));
		$email=trim(mysqli_real_escape_string($link,utf8_decode($_POST['email'])));
		$usr=trim(mysqli_real_escape_string($link,utf8_decode($_POST['usr'])));
		$pwd=trim(mysqli_real_escape_string($link,utf8_decode($_POST['pwd'])));
		
		$id_rol=$_POST['rol'];
		$estado=$_POST['estado'];
		
		if($id=="0"){
			$pwd=Security::encrypt($pwd);
			
			$result =mysqli_query($link,"INSERT INTO `usuarios`(`id`, `nombre`, `apellido1`, `apellido2`, `celular`, `ci`, `direccion`, `ciudad`, `email`, `usr`, `pwd`, `id_rol`, `estado`) 
			VALUES (NULL,'$nombre','$apellido1','$apellido2','$celular','$ci','$direccion','$ciudad','$email', '$usr','$pwd','$id_rol','$estado');");
				
				if (!$result) {
					 logs_db("Usuario". $nombre." ". $apellido1." NO CREADO" , $_SERVER['PHP_SELF']);
					$data['error'] = "ERROR AL INSERTAR " ;
				} else {
					
					 logs_db("Usuario". $nombre." ". $apellido1." CREADO" , $_SERVER['PHP_SELF']);
					$data['success'] = true;
				}

		}
		else{
			$aux_pwd=devuelve_campo("usuarios","pwd","id",$id); 
			if(strcmp ( $aux_pwd , $pwd )!=0)
			$pwd=Security::encrypt($pwd);
			$result =mysqli_query($link,"UPDATE `usuarios` SET `nombre` = '$nombre',`apellido1` = '$apellido1',`apellido2` = '$apellido2',
			`celular` = '$celular',`ci` = '$ci',`direccion` = '$direccion',`ciudad` = '$ciudad',`email` = '$email', `usr` = '$usr', `pwd` = '$pwd', `id_rol` = '$id_rol', `estado` = '$estado' WHERE `usuarios`.`id` = $id;");
				
			if (!$result) {
				 logs_db("Usuario". $nombre." ". $apellido1." NO EDITADO" , $_SERVER['PHP_SELF']);
				$data['error'] = "ERROR AL EDITAR " ;
			} else {
				 logs_db("Usuario". $nombre." ". $apellido1." EDITADO" , $_SERVER['PHP_SELF']);
				
				$data['success'] = true;
			}
		}
				
			
		
	}
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Content-Type: application/json; charset=UTF-8");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo json_encode((object)$data);
?>
