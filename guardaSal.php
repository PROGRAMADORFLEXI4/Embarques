<?php
	include("conectabd.php");
	/*odbc_exec($conn,"UPDATE ZZM010 SET ZZM_CAJAS='$_GET[Caj]',ZZM_COSTAL='$_GET[Cos]',ZZM_EXHIB='$_GET[Exh]',ZZM_TINACO='$_GET[Tin]' WHERE ZZM_PEDIDO='$_GET[Ped]' AND ZZM_FECSUR='' AND D_E_L_E_T_=''")or die("Error al actualizar las cajas");
	odbc_close($conn);
	echo "<h1>Salida Impresa</h1>";*/

	/*CAMBIAR POR ORDEN DE SURTIDO*/
	$sql_ZZM_resumen = "UPDATE ZZM010 SET ZZM_CAJAS='$_POST[cajas]',ZZM_COSTAL='$_POST[costales]',ZZM_EXHIB='$_POST[exhi]',ZZM_TINACO='$_POST[tindec]' WHERE ZZM_ORDSUR='$_POST[ordsur]' AND ZZM_FECSUR='' AND D_E_L_E_T_='';";
	$sql_Z77_salida = "UPDATE Z77010 SET Z77_FYHSUR='".date("d/m/y H:i:s")."', Z77_STATUS='S' WHERE Z77_ORDSUR='".$_POST["ordsur"]."' AND Z77_STATUS = 'CS' AND D_E_L_E_T_='';";
	if(odbc_exec($conn, $sql_ZZM_resumen)) {
		if(odbc_exec($conn, $sql_Z77_salida)) {
			$resul = "GOOD";
		}else{
			$resul = "ERROR_SAL";
		}
	}else{
		$resul = "ERROR";
	}
	odbc_close($conn);
	echo trim($resul);
?>