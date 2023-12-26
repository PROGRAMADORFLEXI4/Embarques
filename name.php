<?php
	echo $_SERVER['REMOTE_ADDR'];
	echo "<br>";
	echo gethostbyaddr($_SERVER['REMOTE_ADDR']);
	echo "<br>";
	/*echo gethostname();
	echo "<br>";*/
	echo php_uname('n');
	echo "<br>";
	echo 'Usuario Servidor: ' . get_current_user();
	echo "<br>";
	echo "Usuario Local: '" . $_SERVER['REMOTE_USER']."'";
	echo "<br>";
	//$consulta"psloggedon -l -x \\".$_SERVER['REMOTE_ADDR'];
	//$salida = shell_exec($consulta);
	//echo $salida;
	//echo "<br>";
	echo "Fin";
?>