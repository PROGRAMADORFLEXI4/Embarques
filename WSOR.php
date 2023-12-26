<?php
	$file = fopen("LOG.txt", "a");
	$IMEI = $_POST['CLAVE_CHOFER'];
	$IDSMS = $_POST['ID'];
	$OBSERVACIONES = $_POST['OBS'];
	
	date_default_timezone_set('America/Mexico_City');
	if($IMEI=="358467066146829")
	{
		include("conectabdBCA.php");
		fwrite($file, date('Ymd H:i:s')." Conectando a BCA ". PHP_EOL);
	}
	else
	{
		include("conectabd.php");
		fwrite($file, date('Ymd H:i:s')." Conectando a producción ".PHP_EOL);
	}
	
	if($IMEI != NULL && $IDSMS != NULL){
		odbc_exec($conn, "UPDATE embrec SET observaciones='$OBSERVACIONES' WHERE R_E_C_N_O_='$IDSMS'") or die("Problemas eal actualizar los datos de la guia");
		echo "".$IDSMS."-".$OBSERVACIONES;
		fwrite($file, date('Ymd H:i:s')." Se actualizaron observaciones para R_E_C_N_O_ ".$IDSMS. PHP_EOL);
	}
	else{
		$registros=odbc_exec($conn,"SELECT ZZN_CODIGO, ZZN_NOMBRE FROM ZZN010 WHERE ZZN_IMEI='$IMEI'") or die("Problemas en el select");
		if($reg=odbc_fetch_array($registros)){
			$CODIGO = $reg['ZZN_CODIGO'];
			$NOMBRE = $reg['ZZN_NOMBRE'];
		}
		
		$numero_dia = date("d");
		$numero_mes = date("m");
		$numero_year = date("Y");
		
		$fecha_hora = $numero_dia."/".$numero_mes."/".$numero_year;	
		fwrite($file, date('Ymd H:i:s')." Obteniendo Otra Ruta para chofer ".$CODIGO. PHP_EOL);
		$registros_sms=odbc_exec($conn,"SELECT depto,descrip,FyHora,R_E_C_N_O_ FROM embrec WHERE Chofer='$CODIGO' AND estatus = 0") or die("Problemas en el select");
		while($reg=odbc_fetch_array($registros_sms)){
			$REGISTROS = $REGISTROS."".$NOMBRE."-".$reg['depto']."-".$reg['descrip']."-".$reg['FyHora']."-".$reg['R_E_C_N_O_'].";";
				$i++;
		}
   		echo $REGISTROS."".$i;
	}
	odbc_close($conn);
	fclose($file);
?>