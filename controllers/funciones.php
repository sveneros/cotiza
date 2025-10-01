<?php date_default_timezone_set('America/La_Paz');

function devuelve_campo($x13,$x14,$x15,$x16) {
	$x17='';
	$x0d = conectarse();
	$x0e = "SELECT * FROM `$x13` WHERE (`$x15` = '$x16')";
	$x0f= mysqli_query($x0d, $x0e);
	$x18 = mysqli_fetch_array($x0f,MYSQLI_ASSOC);
	$x17=$x18[$x14];
	return $x17;
} 
/* function devuelve_campo($tabla, $campo, $campo_busqueda, $valor_busqueda) {
    $resultado = '';
    $conexion = conectarse();
    
    // Usar consultas preparadas para evitar inyección SQL
    $query = "SELECT `$campo` FROM `$tabla` WHERE `$campo_busqueda` = ?";
    $stmt = mysqli_prepare($conexion, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $valor_busqueda);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $valor);
        
        if (mysqli_stmt_fetch($stmt)) {
            $resultado = $valor;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conexion);
    return $resultado;
} */
function existe_campo($x13,$x15,$x16) { $x21 = false;$x0d = conectarse();$x0e = "SELECT * FROM `$x13` WHERE (`$x15` = '$x16');";$x0f = mysqli_query($x0d,$x0e) ;$x22 = mysqli_num_rows($x0f) ;if ($x22>=1){$x21 = true;}else{$x21 = false;}return $x21;}function moneda($x5b) {return number_format((float)$x5b, 2, '.', '');}
function cargarComboRolDesh($x27) {$x0d = conectarse();$x0e = "SELECT `id`, `rol` FROM `roles`;";$x0f = mysqli_query($x0d, $x0e); echo ("<select name='id_rol' id='id_rol' class='form-control' readonly>");while($x18 = mysqli_fetch_array($x0f,MYSQLI_ASSOC)){ if ($x27 == $x18["id"]) {printf ("<option value='%s' selected>%s</option>",$x18["id"], $x18["rol"]); } else {printf ("<option value='%s'>%s</option>",$x18["id"], $x18["rol"]);} }printf("</select>"); } 
function creaEditaCliente($nit,$razon_social){$fecha_registro=date("Y-m-d H:i:s");$link=conectarse();if(existe_campo('nits',"nit",$nit)){mysqli_query($link,"
				UPDATE `nits` SET `razon` = '$razon_social', `fecha_registro` = '$fecha_registro' WHERE `nit` = '$nit' LIMIT 1 ;
			");}else{mysqli_query($link,"
	INSERT INTO `nits` (`id`, `nit`, `razon`, `fecha_visita`) VALUES (NULL, '$nit', '$razon_social', '$fecha_registro');
");}}
function devuelveCorrelativoTipoDoc($x1d) { $x0d = conectarse();$x0e = "SELECT `correlativo` FROM `tipo_documento` WHERE `id` ='$x1d';";$x0f= mysqli_query($x0d, $x0e);$x18 = mysqli_fetch_array($x0f,MYSQLI_ASSOC);$x1e=intval($x18['correlativo'])+1; mysqli_query($x0d,"UPDATE tipo_documento SET correlativo='$x1e' WHERE id='$x1d' LIMIT 1 ;");return ($x1e);}

function CreaDocumento($id_documento,$id_tipo_documento,$id_cliente,$tipo_cambio,$fecha,$glosa,$descuento,$usuario,$total,$efectivo,$cambio,$estado){
	$link = conectarse();
	
	mysqli_query($link,"INSERT INTO `documentos` (`id_documento`, `id_tipo_documento`, `id_cliente`, `tipo_cambio`, `fecha`, `glosa`, `descuento`, `usuario`, `total`, `efectivo`, `cambio`, `estado`) 
	VALUES ('$id_documento', '$id_tipo_documento', '$id_cliente', '$tipo_cambio', '$fecha','$glosa', '$descuento', '$usuario', '$total', '$efectivo', '$cambio', '$estado');");
}

function CreaKardex($id_documento,$id_tipo_documento,$nombre,$descripcion, $id_marca, $marca,$cantidad,$precio_unitario,$precio_total,$descuento){
	$link = conectarse();
	mysqli_query($link,"INSERT INTO `kardex` (`id`, `id_documento`,`id_tipo_documento`, `producto`, `descripcion`,`id_marca`,`marca`, `cantidad`, `precio_unitario`, `precio_total`, `descuento`) VALUES (NULL, '$id_documento', '$id_tipo_documento', '$nombre', '$descripcion','$id_marca','$marca', '$cantidad', '$precio_unitario','$precio_total', '$descuento');");}

function CreaPago($id_documento,$id_tipo_documento,$id_almacen,$id_usuario,$fecha,$monto,$estado){
	$link = conectarse();
	mysqli_query($link,"INSERT INTO `pagos` (`id`, `id_documento`, `id_tipo_documento`,`id_almacen`,`id_usuario`,`fecha`, `monto`, `estado`) VALUES (NULL, '$id_documento', '$id_tipo_documento', '$id_almacen', '$id_usuario', '$fecha', '$monto', '$estado');");
	VerificaPagos($id_documento);
}
function VerificaPagos($id_documento){
	$pagos=DevuelveTotalCreditosPagados($id_documento);
	$total=DevuelveTotalCredito($id_documento);
	if($pagos>=$total){
		EditaDocumento($id_documento,"PAG");
	}
	
}

function EditaDocumento($id_documento,$estado){$link = conectarse();
	mysqli_query($link,"UPDATE `documentos` SET estado='$estado' WHERE id_documento='$id_documento' and id_tipo_documento=3;");
}
	
function DevuelveTotalCredito($id_documento) {$x17='';$x0d = conectarse();
	$x0e = "SELECT `total` FROM `documentos` WHERE `id_documento`='$id_documento' AND `id_tipo_documento`=3 AND estado ='V'";
	$suma=0;
	$x0f= mysqli_query($x0d, $x0e);
	while($x33 = mysqli_fetch_array($x0f,MYSQLI_ASSOC))
	 {$suma=$suma+$x33["total"];}return $suma;	
}
	function DevuelveTotalCreditosPagados($id_documento) {$x17='';$x0d = conectarse();
		$x0e = "SELECT `monto` FROM `pagos` WHERE `id_documento`='$id_documento' AND `id_tipo_documento`=3 AND estado ='V'";
		$suma=0;
		$x0f= mysqli_query($x0d, $x0e);
		while($x33 = mysqli_fetch_array($x0f,MYSQLI_ASSOC))
		 {$suma=$suma+$x33["monto"];}return $suma;	
	}
function creaPuntosUsuarios($id_usuario,$puntos) {
 
	$x0d = conectarse();
	mysqli_query($x0d,"INSERT INTO `puntosusuario` (`id`, `id_usuario`, `puntos`) VALUES (NULL, '$id_usuario', '$puntos');");
	}

function descuentaPuntos($id_usuario,$cantidad) { 
	$exi=DevuelvePuntosUsuario($id_usuario);
	$nueva_exi=$exi-$cantidad;
	$x0d = conectarse();
	mysqli_query($x0d,"UPDATE `puntosusuario` SET `puntos` = '$nueva_exi' WHERE `id_usuario` = '$id_usuario'" ); 	
}
	
function incrementaPuntos($id_usuario,$cantidad) { 
	
	if(existe_campo('puntosusuario',"id_usuario",$id_usuario)){
		$exi=DevuelvePuntosUsuario($id_usuario);
		$nueva_exi=$exi+$cantidad;
		$x0d = conectarse();
		mysqli_query($x0d,"UPDATE `puntosusuario` SET `puntos` = '$nueva_exi' WHERE `id_usuario` = '$id_usuario';"); 
	}else{
		creaPuntosUsuarios($id_usuario,$cantidad);
	}
	
}

function DevuelvePuntosUsuario($id_usuario) {
	$x0d = conectarse();$x83=0;$x32=mysqli_query($x0d, "SELECT puntos FROM puntosusuario WHERE id_usuario='".$id_usuario."' limit 1;");
	while($x33 = mysqli_fetch_array($x32,MYSQLI_ASSOC)) {
	$x83=$x33["puntos"];}return $x83; }

function DevuelveExistenciaProductoPorAlmacen($id_producto) {
		$x0d = conectarse();$x83=0;$x32=mysqli_query($x0d, "SELECT stock FROM existencias WHERE id_producto='".$id_producto."';");
	 while($x33 = mysqli_fetch_array($x32,MYSQLI_ASSOC)) {
		$x83=$x83+$x33["stock"];}return $x83; }

function existe_campo_relacional($x13,$x15,$x16,$x19,$x1a) { $x21=false;$x0d = conectarse();$x0e = "SELECT `$x15`,`$x19` FROM `$x13` WHERE `$x15` = '$x16' AND `$x19` = '$x1a';";$x0f = mysqli_query($x0d, $x0e);$x22 = mysqli_num_rows($x0f) ;if ($x22>=1)$x21 = true;else $x21 = false;return $x21;  } function devuelve_campo_relacional($x13,$x14,$x15,$x16,$x19,$x1a) {$x0d = conectarse();$x0e = "SELECT $x14	FROM `$x13` WHERE (`$x15` = '$x16') AND (`$x19` = '$x1a')";$x0f= mysqli_query($x0d, $x0e);$x18 = mysqli_fetch_array($x0f,MYSQLI_ASSOC);return $x18[$x14];}function almacenPrincipal() {$x0d = conectarse();$x0e = "SELECT valor FROM `configuracion` where `campo`='almacenPrincipal'";$x0f= mysqli_query($x0d, $x0e);$x18 = mysqli_fetch_array($x0f,MYSQLI_ASSOC);return $x18['valor']; }


function tieneAcceso($x10){$x11=devuelve_campo("menu","id","enlace",$x10);$x12=false;
	$id_rol=$_SESSION['sml2020_svenerossys_id_rol_usuario_registrado'];
if(existe_campo_relacional("rol_menu","id_rol",$id_rol,"id_menu",$x11)) $x12=true; else $x12=false; return $x12; }
function tieneAccesoAlMenu($x10){$x12=false;
	if(existe_campo_relacional("rol_menu","id_rol",$_SESSION['sml2020_svenerossys_id_rol_usuario_registrado'],"id_menu",$x10))
	 $x12=true; else $x12=false; return $x12; }
function CreaOrdenTrabajo($numero_documento,$numero,$nombre_cliente,$direccion,$email,$telefono,$nombre_ofta,$tipo,$od_esf,$cil_d,$eje_d,$oi_esf,$cil_i,$eje_i,$dp,$codigo,$marca,$material,$valor_total,$fecha,$hora,$usuario,$id_almacen,$estado){$link = conectarse();
	$fecha_orden=date('Y-m-d H:i:s');
mysqli_query($link,"INSERT INTO `orden_trabajo` (`id`,`numero`, `nombre_cliente`, `direccion`, `email`, `telefono`, `nombre_ofta`, `vision`, `od_esf`, `oi_esf`, `cil_d`, `cil_i`, `eje_d`, `eje_i`, `dp`, `montura`, `marca`, `material`, `valor_total`, `fecha_entrega`, `hora`, `usuario`, `id_almacen`, `fecha_orden`, `estado`) VALUES ('$numero_documento','$numero','$nombre_cliente','$direccion','$email','$telefono','$nombre_ofta','$tipo','$od_esf','$cil_d','$eje_d','$oi_esf','$cil_i','$eje_i','$dp','$codigo','$marca','$material','$valor_total','$fecha','$hora','$usuario','$id_almacen','$fecha_orden','$estado');");
} function GuardarPago($numero_orden,$monto,$id_almacen,$usuario){$link = conectarse();$fecha=date('Y-m-d H:i:s');mysqli_query($link,"INSERT INTO `pagos`(`id`, `id_orden_trabajo`, `monto`, `fecha`, `id_almacen`, `usuario`, `estado`) VALUES (NULL,'$numero_orden','$monto','$fecha','$id_almacen','$usuario','V');");
}
function ExisteNumero($x13) { $x21 = false;$x0d = conectarse();$x0e = "SELECT `numero` FROM `orden_trabajo` WHERE (`numero` = '$x13');";$x0f = mysqli_query($x0d,$x0e) ;$x22 = mysqli_num_rows($x0f) ;if ($x22>=1){$x21 = true;}else{$x21 = false;}return $x21;}
function TotalSucursal($fech,$suc){
	$x0d = conectarse();$x83=0;$x32=mysqli_query($x0d, "SELECT  `monto` FROM `pagos` WHERE fecha like '".$fech."%' AND id_almacen='".$suc."' AND estado='V';"); while($x33 = mysqli_fetch_array($x32,MYSQLI_ASSOC)) {if($x33["monto"]!=null && $x33["monto"]!='' )$x83+=$x33["monto"];}return $x83; 
}
//facturacion
function devuelveCorrelativoFactura($id_almacen){$link=conectarse();$sql="SELECT `numero_factura`  FROM `sin` WHERE `id_almacen`='$id_almacen' AND `estado`='V' limit 1;";
$result=mysqli_query($link,$sql);
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
$corr=$row['numero_factura']+1;
mysqli_query($link,"UPDATE sin SET numero_factura='$corr' WHERE `id_almacen`='$id_almacen' AND `estado`='V' limit 1;");return ($corr);}

function devuelveLlaveActiva($id_almacen){$link=conectarse();$sql="SELECT `id`  FROM `sin`  WHERE `id_almacen`='$id_almacen'AND `estado`='V' limit 1;";$result=mysqli_query($link,$sql);$row=mysqli_fetch_array($result,MYSQLI_ASSOC);return $row['id'];}

function creaLibroVentas($id_documento,$id_almacen,$nit,$razon_social,$nro_factura,$nro_autorizacion,$llave,$codigo_control,$total_factura,$importe_base,$descuento){$fecha=date("Y-m-d H:i:s");$link=conectarse();mysqli_query($link,"INSERT INTO `libro_ventas` (`id`,`id_documento`, `id_almacen`, `fecha`, `nit`, `razon_social`, `nro_factura`, `nro_autorizacion`, `llave`, `codigo_control`, `total_factura`, `importe_base`, `descuento`, `estado`) VALUES (null, '$id_documento', '$id_almacen', '$fecha', '$nit', '$razon_social', '$nro_factura', '$nro_autorizacion', '$llave', '$codigo_control', '$total_factura', '$importe_base', '$descuento', 'V');");}

function creaLibroVentasManual($id_documento,$id_almacen,$nit,$razon_social,$nro_factura,$nro_autorizacion,$llave,$codigo_control,$total_factura,$importe_base,$descuento){$fecha=date("Y-m-d H:i:s");$link=conectarse();mysqli_query($link,"INSERT INTO `libro_ventas_manual` (`id`,`id_documento`, `id_almacen`, `fecha`, `nit`, `razon_social`, `nro_factura`, `nro_autorizacion`, `llave`, `codigo_control`, `total_factura`, `importe_base`, `descuento`, `estado`) VALUES (null, '$id_documento', '$id_almacen', '$fecha', '$nit', '$razon_social', '$nro_factura', '$nro_autorizacion', '$llave', '$codigo_control', '$total_factura', '$importe_base', '$descuento', 'V');");}


function literal($num,$fem=false,$dec=false){$matuni[2]="dos";$matuni[3]="tres";$matuni[4]="cuatro";$matuni[5]="cinco";$matuni[6]="seis";$matuni[7]="siete";$matuni[8]="ocho";$matuni[9]="nueve";$matuni[10]="diez";$matuni[11]="once";$matuni[12]="doce";$matuni[13]="trece";$matuni[14]="catorce";$matuni[15]="quince";$matuni[16]="dieciseis";$matuni[17]="diecisiete";$matuni[18]="dieciocho";$matuni[19]="diecinueve";$matuni[20]="veinte";$matunisub[2]="dos";$matunisub[3]="tres";$matunisub[4]="cuatro";$matunisub[5]="quin";$matunisub[6]="seis";$matunisub[7]="sete";$matunisub[8]="ocho";$matunisub[9]="nove";$matdec[2]="veint";$matdec[3]="treinta";$matdec[4]="cuarenta";$matdec[5]="cincuenta";$matdec[6]="sesenta";$matdec[7]="setenta";$matdec[8]="ochenta";$matdec[9]="noventa";$matsub[3]='mill';$matsub[5]='bill';$matsub[7]='mill';$matsub[9]='trill';$matsub[11]='mill';$matsub[13]='bill';$matsub[15]='mill';$matmil[4]='millones';$matmil[6]='billones';$matmil[7]='de billones';$matmil[8]='millones de billones';$matmil[10]='trillones';$matmil[11]='de trillones';$matmil[12]='millones de trillones';$matmil[13]='de trillones';$matmil[14]='billones de trillones';$matmil[15]='de billones de trillones';$matmil[16]='millones de billones de trillones';$num=trim((string)@$num);if($num[0]=='-'){$neg='menos ';$num=substr($num,1);}else $neg='';while($num[0]=='0')$num=substr($num,1);if($num[0]<'1' or $num[0]>9)$num='0'.$num;$zeros=true;$punt=false;$ent='';$fra='';for($c=0;$c<strlen($num);$c++){$n=$num[$c];if(!(strpos(".,'''",$n)===false)){if($punt)break;else{$punt=true;continue;}}elseif(!(strpos('0123456789',$n)===false)){if($punt){if($n!='0')$zeros=false;$fra.=$n;}else $ent.=$n;}else break;}$ent='     '.$ent;if($dec and $fra and !$zeros){$fin=' coma';for($n=0;$n<strlen($fra);$n++){if(($s=$fra[$n])=='0')$fin.=' cero';elseif($s=='1')$fin.=$fem?' una':' un';else $fin.=' '.$matuni[$s];}}else $fin='';if((int)$ent===0)return 'Cero '.$fin;$tex='';$sub=0;$mils=0;$neutro=false;while(($num=substr($ent,-3))!='   '){$ent=substr($ent,0,-3);if(++$sub<3 and $fem){$matuni[1]='una';$subcent='as';}else{$matuni[1]=$neutro?'un':'uno';$subcent='os';}$t='';$n2=substr($num,1);if($n2=='00'){}elseif($n2<21)$t=' '.$matuni[(int)$n2];elseif($n2<30){$n3=$num[2];if($n3!=0)$t='i'.$matuni[$n3];$n2=$num[1];$t=' '.$matdec[$n2].$t;}else{$n3=$num[2];if($n3!=0)$t=' y '.$matuni[$n3];$n2=$num[1];$t=' '.$matdec[$n2].$t;}$n=$num[0];if($n==1){$t=' ciento'.$t;}elseif($n==5){$t=' '.$matunisub[$n].'ient'.$subcent.$t;}elseif($n!=0){$t=' '.$matunisub[$n].'cient'.$subcent.$t;}if($sub==1){}elseif(!isset($matsub[$sub])){if($num==1){$t=' mil';}elseif($num>1){$t.=' mil';}}elseif($num==1){$t.=' '.$matsub[$sub].'?n';}elseif($num>1){$t.=' '.$matsub[$sub].'ones';}if($num=='000')$mils++;elseif($mils!=0){if(isset($matmil[$sub]))$t.=' '.$matmil[$sub];$mils=0;}$neutro=true;$tex=$t.$tex;}$tex=$neg.substr($tex,1).$fin;return strtoupper(ucfirst($tex));}

	function fecha($fecha){$dia=substr($fecha,8,2);$mes=substr($fecha,5,2);$ani=substr($fecha,0,4);$nf=$dia."/".$mes."/".$ani;return $nf;}
	
	function decimales($numero){$numero=number_format((float)$numero,2,'.','');return substr($numero,-2);}

	function DosificacionActiva(){
		$val=false;
		$id_llave=devuelveLlaveActiva($_SESSION['sml2020_svenerossys_id_almacen']);
		$exp_date=devuelve_campo("sin","fecha_limite","id",$id_llave);
		$todays_date=date("Y-m-d");
		$today=strtotime($todays_date);
		$expiration_date=strtotime($exp_date);
		if($expiration_date>$today){
			$val=true;
		}else {$val=false;
		}
		return $val;
	}


function actualizaPrecioDolaresABolivianos() {
		$link=conectarse();
		
		$x32=mysqli_query($link, "SELECT id,precio2 FROM productos;");
	 while($x33 = mysqli_fetch_array($x32,MYSQLI_ASSOC)) {
	 	$elprecio2=$x33["precio2"];
	 	$elid=$x33["id"];
	 	ActualizaPrecioProducto($elid,$elprecio2 );
	 	
	}
 }
 function ActualizaPrecioProducto($elid,$elprecio2 ){
 $link2=conectarse();
$tipo_cambio=floatval(devuelve_campo("configuracion","valor","campo","tipo_cambio_dolar"));
$precio_bs=Moneda($elprecio2*$tipo_cambio);
mysqli_query($link2,"UPDATE productos set precio='".$precio_bs."' where id=".$elid." limit 1;");
 }

 function CambiaEstadoDocumentoVenta($id_doc){$link=conectarse();
 	mysqli_query($link,"UPDATE `documentos` SET `estado` = 'C' WHERE `id_documento` =".$id_doc." and id_tipo_documento=2;");}
 function CambiaEstadoFactura($id_doc){$link=conectarse();
 	mysqli_query($link,"UPDATE `libro_ventas` SET `estado` = 'A' WHERE `libro_ventas`.`id_documento` =".$id_doc.";");}

 function HayUnArqueoAbierto($fecha,$id_usuario,$id_almacen){
	$link=conectarse();$valor=false;
	$sql="SELECT `id`, `caja_inicial`, `fecha`, `total_ventas`, `total_gastos`, `saldo`, `id_usuario`, `id_almacen`, `estado` FROM `arqueos` WHERE id_almacen=$id_almacen and id_usuario=$id_usuario and fecha like '$fecha' and estado='A';";
	$result=mysqli_query($link,$sql);
	$numero_filas = mysqli_num_rows($result);
	if($numero_filas>0){$valor=true;}
	return $valor;
 }
 function imagenMin($cod) {$img="<img src='../img/products/no_photo.jpg' alt='Sin imagen' class='img-responsive radius' style='width:80px;height:auto;margin-right : auto;margin-left : auto;min-width:80px;max-width:560px;'>";if(file_exists("../img/products/".$cod.".jpg"))$img="<img src='../img/products/".$cod.".jpg' alt='".$cod."' class='img-responsive radius' style='width:80px;height:auto;margin-right : auto;margin-left : auto;min-width:80px;max-width:560px;'>"; return $img; }
 function imagenMax($cod) {$img="<img src='../img/products/no_photo.jpg' alt='Sin imagen' class='img-responsive radius' style='width:560px;height:auto;margin-right : auto;margin-left : auto;min-width:80px;max-width:560px;'>";if(file_exists("../img/products/".$cod.".jpg"))$img="<img src='../img/products/".$cod.".jpg' alt='".$cod."' class='img-responsive radius' style='width:560px;height:auto;margin-right : auto;margin-left : auto;min-width:80px;max-width:560px;'>"; return $img; }
 //cuenta items vendidos:
 function devuelve_suma_campo_relacional3($tabla,$campo_buscado,$campo_id,$valor_id,$campo_id2,$valor_id2,$campo_id3,$valor_id3) 
{$link = conectarse();
$sql = "SELECT SUM($campo_buscado) as total
FROM `$tabla`WHERE (`$campo_id` = '$valor_id') AND (`$campo_id2` = '$valor_id2') AND (`$campo_id3` = '$valor_id3');";
$result = mysqli_query($link,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
return $row["total"];}

 function ActualizaProductosVendidosMes($fecha_ini,$fecha_fin){
	$link = conectarse();
	mysqli_query($link,"DROP TEMPORARY TABLE IF EXISTS `productos_vendidos_mes`;") ;
	mysqli_query($link,"CREATE TEMPORARY TABLE IF NOT EXISTS `productos_vendidos_mes` (
	  `id_producto` int(11) NOT NULL,
	  `id_almacen` int(11) NOT NULL,
	  `cantidad` int(11) NOT NULL,
	  `total` double NOT NULL,
	  PRIMARY KEY (`id_producto`,`id_almacen`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;") ;
	//fin crea tabla
	$query= mysqli_query($link,"SELECT id
	FROM `productos`
	WHERE 1 ORDER BY `descripcion` ASC") ;
	while($array = mysqli_fetch_array($query,MYSQLI_ASSOC)) {
	$query2= mysqli_query($link,"SELECT `documentos`.`fecha`,
	`documentos`.`id_documento`,
	`documentos`.`id_tipo_documento`
	FROM `documentos` WHERE `id_documento` in
	(SELECT `kardex`.`id_documento` FROM `kardex` WHERE `kardex`.`id_producto` = ".$array["id"].")
	AND  documentos.estado =  'V' AND  documentos.id_tipo_documento =  2 AND documentos.fecha >= '".$fecha_ini."' AND documentos.fecha <= '".$fecha_fin."' ORDER BY documentos.fecha ASC;
	") ;
	
	$cant=0;
	$prec=0;
	while($array2 = mysqli_fetch_array($query2,MYSQLI_ASSOC)) {
	$cant=$cant + devuelve_suma_campo_relacional3("kardex","cantidad","id_documento",$array2["id_documento"],"id_tipo_documento",$array2["id_tipo_documento"],"id_producto",$array["id"]);
	//$prec=$prec + devuelve_suma_campo_relacional3("kardex","precio_total","id_documento",$array2["id_documento"],"id_tipo_documento",$array2["id_tipo_documento"],"id_producto",$array["id"]);
	 }//end while DOCUMENTOS
	if( $cant>0)
	{	 mysqli_query($link,"
		INSERT INTO `productos_vendidos_mes` (`id_producto` ,`id_almacen` ,`cantidad`,`total`)VALUES ('".$array["id"]."', '".almacenPrincipal()."','".$cant."', '0');") ;
		}
	
	}//END WHILE PRODUCTOS
	
	$query3= mysqli_query($link,"SELECT *
	FROM `productos_vendidos_mes`
	 order by cantidad desc") ;
	
	echo '<table class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%" data-page-size="10">';
	
	echo "<thead><tr><th>PRODUCTO </th><th>CANTIDAD</th></tr></thead><tbody>";
	$totales=0;
		while($array3 = mysqli_fetch_array($query3,MYSQLI_ASSOC)) {
			$totales+=$array3["total"];
		printf("<tr><td> %s </td><td>%s</td></tr>",devuelve_campo("productos","descripcion","id",$array3["id_producto"]),$array3["cantidad"]);
		}
	echo "</tbody></table>";
	
	}

function CreaRecoleccion($id_usuario,$totalPuntos,$fecha){
		$link = conectarse();
	mysqli_query($link,"INSERT INTO `recolecciones` (`id`, `id_usuario`, `totalPuntos`, `fecha`) 
	VALUES (NULL, '$id_usuario', '$totalPuntos', '$fecha');");
	$last_id = mysqli_insert_id($link);
	return $last_id;
	}
	
function CreaDetalleRecoleccion($id_recoleccion,$id_producto,$cantidad,$puntos){
		$link = conectarse();
		mysqli_query($link,"INSERT INTO `detallerecoleccion` (`id`, `id_recoleccion`,`id_producto`, `cantidad`, `puntos`) VALUES (NULL, '$id_recoleccion', '$id_producto', $cantidad, $puntos);");
	}

function logs($mensaje, $route){
	error_log($mensaje . " | ". date("l jS \of F, Y, h:i:s A") . "| File: " . $route, E_USER_NOTICE); //E_USER_NOTICE, E_USER_ERROR, E_USER_WARNING
}

function createLog($descripcion, $usuario, $route) {
    $link = conectarse();
    $descripcion = mysqli_real_escape_string($link, $descripcion);
    $usuario = mysqli_real_escape_string($link, $usuario);
    $fecha_hora = date('Y-m-d H:i:s');
    $result = mysqli_query($link, "INSERT INTO logs (usuario,mensaje,archivo,fecha_hora, estado) VALUES ('$usuario','$descripcion','$route', '$fecha_hora', 'VIG')");
    $newId = mysqli_insert_id($link);
    mysqli_close($link);
    return $newId;
}

function logs_db($mensaje, $route){
	$usuario = isset($_SESSION['sml2020_svenerossys_usuario_registrado'])?$_SESSION['sml2020_svenerossys_usuario_registrado']:"no registrado";
	createLog($mensaje, $usuario, $route);
}

// Función para registrar auditoría - VERSIÓN CORREGIDA
function registrar_auditoria_cotizacion($link, $data) {
    $query = "INSERT INTO auditoria_cotizaciones (
                id_documento, id_tipo_documento, accion, 
                estado_anterior, estado_nuevo, detalles, 
                id_usuario, ip_origen
              ) VALUES (?, 5, ?, ?, ?, ?, ?, ?)";
    
    // Asignar valores por defecto antes de bind_param
    $estado_anterior = $data['estado_anterior'] ?? null;
    $estado_nuevo = $data['estado_nuevo'] ?? null;
    $detalles = $data['detalles'] ?? null;
    $id_usuario = $data['id_usuario'] ?? 0;
    $ip_origen = $_SERVER['REMOTE_ADDR'] ?? '';
    
    $stmt = $link->prepare($query);
    $stmt->bind_param("issssis", 
        $data['id_documento'],
        $data['accion'],
        $estado_anterior,
        $estado_nuevo,
        $detalles,
        $id_usuario,
        $ip_origen
    );
    return $stmt->execute();
}
?>