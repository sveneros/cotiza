<?php 
session_start();
include("conx.php");
include("funciones.php");


	$data = array(
		'results' => array(),
		'success' => false,
		'error' => '' 
	);
	
	if (isset($_POST['id_producto']) && isset($_POST['id_usuario']) && isset($_POST['fecha_vencimiento']) && isset($_POST['costo']) && isset($_POST['precio'])&& isset($_POST['cantidad'])&& isset($_POST['ubicacion']) ) {
		
		$id_producto=$_POST['id_producto'];
		$nombre=devuelve_campo("productos","nombre","id",$id_producto);
		$descripcion=devuelve_campo("productos","descripcion","id",$id_producto);
		$id_marca=devuelve_campo("productos","id_marca","id",$id_producto);
		$marca=devuelve_campo("marcas","descripcion","id",$id_marca);
		$id_almacen_origen=$_POST['id_almacen_origen'];
		$id_cliente=0;
		$id_almacen_destino=0;
		$tipo_cambio=devuelve_campo("configuracion","valor","campo","tipo_cambio_dolar");
		$numero_lote=utf8_decode(strtoupper(trim($_POST['numero_lote'])));
		$fecha_fabricacion=$_POST['fecha_fabricacion'];
		$fecha_vencimiento=$_POST['fecha_vencimiento'];
		$costo=number_format($_POST['costo'], 2);
		$precio=number_format($_POST['precio'], 2);
		$cantidad=$_POST['cantidad'];
		$ubicacion=utf8_decode(trim($_POST['ubicacion']));
		$numero_documento=devuelveCorrelativoTipoDoc(5);
	    $id_tipo_documento=7;
		$glosa="Inventario Inicial";
		$usuario=utf8_decode($_POST['id_usuario']);
		//$fecha=$_POST['fecha'];
		$fecha=date('Y-m-d H:i:s');
		$descuento=0;
		$total=0;
		$precio_total=number_format($precio*$costo, 2);
		//CreaDocumento($id_documento,$id_tipo_documento,$id_almacen_origen,$id_almacen_destino,$id_cliente,$tipo_cambio,$fecha,$descuento,$usuario,$total,$efectivo,$cambio,$estado)
		CreaDocumento($numero_documento,$id_tipo_documento,$id_almacen_origen,$id_almacen_destino,$id_cliente,$tipo_cambio,$fecha,$glosa,$descuento,$usuario,$precio_total,$precio_total,0,'V');
		logs("Se creo el documento: ".$numero_documento ." tipo: ".$id_tipo_documento." | usuario: ". $_SESSION['sml2020_svenerossys_usuario_registrado'], $_SERVER['PHP_SELF']);
		//CreaKardex($id_documento,$id_tipo_documento,$producto,$cantidad,$precio_unitario,$precio_total,$descuento
		CreaKardex($numero_documento,$id_tipo_documento,$nombre,$descripcion,$id_marca, $marca,$cantidad,$precio,$precio_total,0);
		logs("Se creo el Kardex: ".$numero_documento ." tipo: ".$id_tipo_documento." | usuario: ". $_SESSION['sml2020_svenerossys_usuario_registrado'], $_SERVER['PHP_SELF']);
		
		
		if (!isset($numero_documento)) {
			
			$data['error'] = "Could not query database for search results, MYSQL ERROR: " ;
		} else {
			
			   $data['results'][] = array(
				        
					'inventario' => $numero_documento
				);
			
			  $data['success'] = true;
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
