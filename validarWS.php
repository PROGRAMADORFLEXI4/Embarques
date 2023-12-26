<?php
	$IMEI = $_POST['CLAVE_CHOFER'];
	$file = fopen("LOG.txt", "a");
	fwrite($file, date('Ymd H:i:s')." Intento de conexión desde ".$IMEI. PHP_EOL);
	
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
	$registros=odbc_exec($conn, "SELECT ZZN_NOMBRE FROM ZZN010 WHERE ZZN_IMEI='$IMEI'") or die("Problemas en el select:");
	while($reg=odbc_fetch_array($registros)){
		echo $reg['ZZN_NOMBRE'];
		fwrite($file, date('Ymd H:i:s')." Usuario Autenticado ".$reg['ZZN_NOMBRE']. PHP_EOL);
	}
	odbc_free_result($registros);
	odbc_close($conn);
	fclose($file);
?>	