<?php
	session_start();
	include("conectabd.php");
	
	$num_factura=$_POST["lafactura"];
	
	//OBTIENE NUMERO DE PEDIDO Y CLIENTE
	$sql=odbc_exec($conn,"SELECT ZZO_PEDIDO,ZZO_CLTE FROM ZZO010 WHERE R_E_C_N_O_=".$num_factura)or die("Error al obtener el pedido");
	$dPed=odbc_fetch_array($sql);
	$numero_pedido=trim($dPed['ZZO_PEDIDO']);
	$codigo_cliente=trim($dPed['ZZO_CLTE']);
	odbc_free_result($sql);
	
	//OBTIENE DIRECCION DE ENTREGA
	$sql=odbc_exec($conn,"SELECT C5_DIREMB FROM SC5010 WHERE D_E_L_E_T_='' AND C5_NUM='".trim($numero_pedido)."';")or die("Error al obtener el la direccion");
	$dPed=odbc_fetch_array($sql);
	$dir_entrega=trim($dPed['C5_DIREMB']);
	odbc_free_result($sql);
	
	//BUSCA PEDIDOS QUE SEAN DEL MISMO CLIENTE Y DIRECCION DE EMBARQUE QUE VENGAN EN CAMINO
	$numeros_pedidos="";
	$sql=odbc_exec($conn,"SELECT C5_NUM FROM (SELECT C5_NUM FROM SC5010 WHERE D_E_L_E_T_='' AND C5_CLIENTE='".$codigo_cliente."' AND C5_DIREMB='".$dir_entrega."' AND C5_NUM<>'".$numero_pedido."') C5
										INNER JOIN (SELECT C9_PEDIDO FROM SC9010 WHERE D_E_L_E_T_='' AND C9_CLIENTE='".$codigo_cliente."' GROUP BY C9_PEDIDO HAVING(SUM(CASE WHEN C9_NFISCAL='' AND C9_REMITO='' THEN 1 ELSE 0 END)>0 AND SUM(CASE WHEN C9_NFISCAL<>'' OR C9_REMITO<>'' THEN 1 ELSE 0 END)=0)) C9 ON C5_NUM=C9_PEDIDO;")
			or die("Error al obtener el pedido");
	while($dPed=odbc_fetch_array($sql))
	{
		$cuantos++;
		if($numeros_pedidos!="")
			$numeros_pedidos.=", ";
		$numeros_pedidos.=trim($dPed['C5_NUM']);
	}
	odbc_free_result($sql);
	
	echo trim($numeros_pedidos);//." - ".$num_factura." - ".$numero_pedido." - ".$codigo_cliente." - ".$dir_entrega." - ".$cuantos
?>