<?
	//Marca el pedido como embarcado y asigna su chofer y la fletera
	include 'conectabd.php';
	odbc_exec($conn,"UPDATE ZZO010 SET ZZO_CODFLE='".$_GET['flet']."',ZZO_OBSERV='".trim($_GET['obs'])."',ZZO_FPAGO='".trim($_GET['pag'])."' WHERE D_E_L_E_T_='' AND R_E_C_N_O_=".$_GET['fac2']) or die("Error al actualizar los datos de embarque");
	$sql_s=odbc_exec($conn,"SELECT ZZO_ORDSUR FROM ZZO010 WHERE R_E_C_N_O_=".$_GET['fac2'])or die("Error al obtener el pedido");
	$dPedido=odbc_fetch_array($sql_s);
	odbc_free_result($sql_s);
	$sql_upd_Z77 = "UPDATE Z77010 SET Z77_STATUS = 'E', Z77_CODTRS = '".$_GET['flet']."' WHERE Z77_ORDSUR = '".$dPedido['ZZO_ORDSUR']."' AND D_E_L_E_T_ = '';";
	odbc_exec($conn, $sql_upd_Z77);
	odbc_close($conn);
	echo "<script languaje='JavaSctip'>
			window.open('embarques.php','_top');
		  </script>";
?>