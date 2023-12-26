<?PHP
	session_start();
	$cods=$_GET["Codigos"];
	if ($cods=='sesion')
	{
		$cods=$_SESSION['codigos'];
	}
	include 'conectabd.php';
	//Valida si la salida ya esta guardada y solo actualiza las cantidades y codigos
	/*$sql=odbc_exec($conn,"SELECT ZZS_SALIDA AS max FROM ZZS010 WHERE ZZS_PEDIDO='".$_GET["Ped"]."' AND ZZS_CODALM='".$_GET["Alm"]."' AND ZZS_FAC2='F' AND D_E_L_E_T_=''") or die("Error al consultar si se encuentra la salida registrada");*/
	$sql=odbc_exec($conn,"SELECT ZZS_SALIDA AS max FROM ZZS010 WHERE ZZS_ORDSUR='".base64_decode($_GET["ordsur"])."' AND ZZS_CODALM='".$_GET["Alm"]."' AND ZZS_FAC2='F' AND D_E_L_E_T_=''") or die("Error al consultar si se encuentra la salida registrada");
	if(odbc_num_rows($sql)>0)
	{
		/*$sql_ord=odbc_exec($conn,"SELECT Z77_ORDSUR FROM Z77010 WHERE Z77_ORDSUR='".$_GET["ordsur"]."' AND Z77_STATUS='S' AND D_E_L_E_T_ = '';")or die("Error al obtener Orden de Surtido");
		$ordn_sur=odbc_fetch_array($sql_ord);*/
		$salida=odbc_fetch_array($sql);
		odbc_exec($conn,"UPDATE ZZS010 SET ZZS_FECHA='".date("Ymd")."',ZZS_FYHSAL='".date("d/m/Y H:i:s",time())."', ZZS_ORDSUR = '".base64_decode($_GET["ordsur"])."' WHERE ZZS_SALIDA=".intval($salida["max"]))
		or die("Error al actualizar la salida");
		odbc_free_result($sql);
		//Elimina todas las partidas de la salida
		odbc_exec($conn,"DELETE FROM ZDS010 WHERE ZDS_SALIDA=".intval($salida["max"]))
		or die("Error al eliminar las paridas de la salida");
	}
	else
	{
		odbc_free_result($sql);
		$sql=odbc_exec($conn,"SELECT ISNULL(MAX(R_E_C_N_O_),0)+1 AS max FROM ZZS010")
		or die("Error al obtener el consecutivo de la Salida");
		$salida=odbc_fetch_array($sql);	
		odbc_free_result($sql);
		/*$sql_ord=odbc_exec($conn,"SELECT Z77_ORDSUR FROM Z77010 WHERE Z77_PEDIDO='".$_GET["Ped"]."' AND Z77_STATUS='CS' AND D_E_L_E_T_ = '';")or die("Error al obtener Orden de Surtido");
		$ordn_sur=odbc_fetch_array($sql_ord);*/
		odbc_exec($conn,"INSERT INTO ZZS010(ZZS_FILIAL,ZZS_SALIDA,ZZS_PEDIDO,ZZS_FECHA,ZZS_CLIENT,ZZS_CODALM,ZZS_FYHSAL,D_E_L_E_T_,R_E_C_N_O_,ZZS_FAC2, ZZS_ORDSUR) VALUES('',".intval($salida["max"]).",'".$_GET["Ped"]."','".date("Ymd")."','".$_GET["Cli"]."','".$_GET["Alm"]."','".date("d/m/Y H:i:s",time())."','',".intval($salida["max"]).",'F', '".base64_decode($_GET["ordsur"])."')")
		or die("Error al insertar la Salida.");
	}
	while($cods<>"")
	{
		$sql=odbc_exec($conn,"SELECT ISNULL(MAX(R_E_C_N_O_),0)+1 AS max FROM ZDS010")
		or die("Error al obtener el consecutivo del detalle de la salida");
		$maxds=odbc_fetch_array($sql);
		odbc_free_result($sql);
		$prod=substr($cods,2,strpos($cods," | ")-2);
		$sql=odbc_exec($conn,"SELECT Producto FROM tempo WHERE id='".$prod."' AND Pedido='".$_GET["Ped"]."'")
		or die("Erro al obtener el codigo del Producto");
		$prod=odbc_fetch_array($sql);
		odbc_free_result($sql);
		$cods=substr($cods,strpos($cods," | ")+2);
		$sol=substr($cods,0,strpos($cods," | "));
		$cods=substr($cods,strpos($cods," | ")+2);
		$ent=substr($cods,0,strpos($cods," | "));
		$cods=substr($cods,strpos($cods," | ")+2);
		$pedimento=substr($cods,1,strpos($cods," | "));
		$cods=substr($cods,strpos($cods," | ")+2);
		$caja=substr($cods,0,strpos($cods," | "));
		$cods=substr($cods,strpos($cods," | ")+2);
		$qe=substr($cods,0,strpos($cods," | "));
		$cods=substr($cods,strpos($cods," | ")+2);
		$coc=substr($cods,0,strpos($cods," | "));
		$cods=substr($cods,strpos($cods,"|")+2);
		$parti=substr($cods,0,strpos($cods,"|"));
		$cods=substr($cods,strpos($cods,"|]")+2);
		odbc_exec($conn,"INSERT INTO ZDS010(ZDS_FILIAL,ZDS_SALIDA,ZDS_PRODUC,ZDS_PEDIM,ZDS_CANSOL,ZDS_QTENT,ZDS_CAJA,ZDS_PARTID,ZDS_QE,ZDS_COSTAL,D_E_L_E_T_,R_E_C_N_O_) VALUES('',".intval($salida["max"]).",'".$prod["Producto"]."','".$pedimento."',".$sol.",".$ent.",".$caja.",".$parti.",".$qe.",'".ucfirst(substr($coc,1,1))."','',".intval($maxds["max"]).")") or die("Error al insertar el detalle de la salida".$prod["Producto"]);
	}
	odbc_close($conn);
	echo "<script languaje='JavaScript'>
			location.href='impSal.php?Ped=".$_GET["Ped"]."&Alm=".$_GET["Alm"]."&Sal=".intval($salida["max"])."&ordsur=".base64_decode($_GET["ordsur"])."';
		  </script>";
?>