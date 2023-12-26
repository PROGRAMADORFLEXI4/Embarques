<?php
	include("conectabd.php");
	If(isset($_GET["regist"]) && isset($_GET["chof"]))
	{
		odbc_exec($conn,"UPDATE ZZO010 SET ZZO_CHOFER='".$_GET['chof']."' WHERE R_E_C_N_O_=".$_GET["regist"]) or die("Error al actualizar el estatus");
		odbc_close($conn);	
	}
?>