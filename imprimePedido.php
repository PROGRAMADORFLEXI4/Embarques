<?PHP
	include("conectabd.php");
	if(isset($_GET["apP"])){
		$ped=odbc_exec($conn,"SELECT C5_FYHRCYC FROM SC5010 WHERE C5_NUM='".$_GET['apP']."' AND C5_FYHRCYC='' AND C5_RECCLIE='SI' AND C5_APALM='F' AND C5_TIPOCLI='2' AND D_E_L_E_T_=''")or die("Error al obtener los datos");
		$datos=odbc_fetch_array($ped);
		odbc_free_result($ped);
		echo "<script language='JavaScript'>";
		if($datos['C5_FYHRCYC']<>"")
		{
			odbc_exec($conn,"UPDATE SC5010 SET C5_APALM='T',C5_UAPALM='".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' WHERE C5_NUM='".$_GET['apP']."'");
			echo "alert('Pedido aprobado');";
		}
		else
		{
			echo "alert('No es posible aprobar el pedido ya que no es pedido local o ya se encuentra en proceso de surtido');";
		}
		echo "window.open('index.php','_self');</script>";	
	}
	else{
		//cambio backorder CHT 
		// buscar el numero de pedido por el numero de factura 
		if (isset($_GET['fac'])) {
			// code...
			$sql_buscar_zzm = "SELECT * FROM ZZM010 WHERE ZZM_FATURA like '%".$_GET['fac']."' AND D_E_L_E_T_ = '';";
		}else{
			$sql_buscar_zzm = "SELECT * FROM ZZM010 WHERE ZZM_PEDIDO = '".$_GET['ped']. "' AND D_E_L_E_T_ = '' AND ZZM_FATURA = '';";
		}

		$busca_ZZM = odbc_exec($conn, $sql_buscar_zzm);
		if ($data_busca = odbc_fetch_array($busca_ZZM)) {
			$pedido_zzm = trim($data_busca["ZZM_PEDIDO"]);
			$orden_surtido = trim($data_busca["ZZM_ORDSUR"]);
		}

		$ped=odbc_exec($conn,"SELECT A1_TRANSP,A1_EST,C5_CLIENTE,C5_APFLETE,A1_NOME,C5_EMISSAO,C5_OBSVTA,A3_NOME,C5_LOF AS est,C5_FYHRVTA,C5_FYHRCYC,C5_FYHSURT,C5_FYHRIMP,C5_DIREMB,A1_END,A1_BAIRRO,A1_MUN+', '+A1_EST AS poblacion,C5_EMBCPED FROM SC5010 SC5 INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE LEFT JOIN SA3010 ON A3_COD=A1_VEND WHERE C5_LOJAENT=A1_LOJA AND SC5.D_E_L_E_T_<>'*' AND SA1.D_E_L_E_T_='' AND C5_NUM='".$pedido_zzm."'")
		or die("Error al ejecutar la consulta del pedido");
		$pedido=odbc_fetch_array($ped);
		/* $sql=odbc_exec($conn,"SELECT C6_ITEM,C6_PRODUTO,B1_DESC,C6_QTDVEN,C6_PRCVEN C6_PRUNIT,C6_VALOR,C6_PEDIM,C6_TES,B1_QE,B1_CANTBOL, C6_LOCAL FROM SC6010 SC6 INNER JOIN SB1010 SB1 ON B1_COD=C6_PRODUTO WHERE C6_NUM='".$pedido_zzm."' AND C6_BLQ='' AND SC6.D_E_L_E_T_<>'*' AND SB1.D_E_L_E_T_<>'*' ORDER BY C6_PRODUTO") or die("Error al ejecutar la consulta de partidas del pedido");	 */
		$sql = odbc_exec($conn,
		"SELECT 
				C6_ITEM,C6_PRODUTO,B1_DESC, SUM(C6_QTDVEN-C6_QTDENT) AS C6_QTDVEN,
				C6_PRCVEN C6_PRUNIT,(SUM(C6_QTDVEN-C6_QTDENT) * C6_PRUNIT ) AS C6_VALOR,C6_PEDIM,
				C6_TES,B1_QE,B1_CANTBOL, C6_LOCAL 
			FROM 
				SC6010 SC6 
				INNER JOIN SB1010 SB1 ON B1_COD=C6_PRODUTO
				LEFT JOIN (SELECT 
					Z46_CODPRO
				FROM Z46010 
				WHERE 
					Z46_PEDIDO = '" . $pedido_zzm . "' AND Z46_ORDSUR = '". $orden_surtido .
			"' AND D_E_L_E_T_='' 
					AND Z46_VALBKO = 'F'
				GROUP BY Z46_CODPRO) Z46 ON Z46_CODPRO = C6_PRODUTO
			WHERE C6_NUM='" . $pedido_zzm . "' AND C6_BLQ='' AND SC6.D_E_L_E_T_ = '' AND SB1.D_E_L_E_T_ = '' 
			GROUP BY 
				C6_TES,C6_ITEM,C6_PRODUTO,B1_DESC,C6_PRUNIT,C6_PEDIM,C6_PRCVEN,B1_QE,B1_CANTBOL,C6_LOCAL
			ORDER BY C6_PRODUTO") or die("Error al ejecutar la consulta de partidas del pedido");

		 echo "<html>
				 <head>
					<link href='css/styles.css' rel='stylesheet' type='text/css'>
					<title>Impresión de Pedido</title>
				 </head>
			 	 <body><div ><!-- class='page' -->
					<img src='images/cancel.png' id='clsSM' title='Cerrar ventana' onClick='window.close();'/> &nbsp;&nbsp; <img src='images/printer.png' id='prntSA' title='Imprimir' onClick='window.print();'/>
				 <table><tr><td colspan='7'><h1>Pedido de Venta</h1></td></tr>
				 <tr><td colspan='7'><hr></td></tr>
				 <tr><th colspan='7' style='text-align:left'>Pedido: ".$pedido_zzm." Orden Surtido: ".$orden_surtido." Cliente: ".$pedido["C5_CLIENTE"]." ".$pedido["A1_NOME"].", Tipo de Pedido: ".$pedido["est"].", Vendedor: ".$pedido["A3_NOME"]."</tr>
				 <tr class='trEnc'>
				 	<td>Item</td><td>Producto</td><td>Descripci&oacute;n</td><td>TES</td><td>Almac&eacute;n</td><td>P. Unit</td><td>Cantidad</td><td>Cajas</td><td>Valor Total</td></tr><tr><td colspan='7'><hr></td></tr>";
					$valor=0;
						 while($datos=odbc_fetch_array($sql)){
						 	 $almacen = $datos["C6_LOCAL"];
							 $valor+=$datos["C6_VALOR"];
							 $cajas=floor($datos["C6_QTDVEN"]/$datos["B1_QE"]);
							 $bolsas=floor(($datos["C6_QTDVEN"]-($datos["B1_QE"]*$cajas))/$datos["B1_CANTBOL"]);
							 $resto=$datos["C6_QTDVEN"]-($datos["B1_QE"]*$cajas)-($datos["B1_CANTBOL"]*$bolsas);
							 //echo "<tr><td>$datos[C6_ITEM]</td><td>$datos[C6_PRODUTO]</td><td>$datos[B1_DESC]</td><td style='text-align:center;'>$datos[C6_TES]</td><td class='tdD'>$datos[C6_LOCAL]</td><td class='tdD'>$".number_format($datos["C6_PRUNIT"],2)."</td><td class='tdD'>".number_format($datos["C6_QTDVEN"],2)."</td><td class='tdD'>".($cajas>0?$cajas."C ":"").($bolsas>0?$bolsas."B ":"").($resto>0?$resto."R ":"")."</td><td class='tdD'>$".number_format($datos["C6_VALOR"],2)."</td></tr>";
							 echo "<tr><td>$datos[C6_ITEM]</td><td>$datos[C6_PRODUTO]</td><td>$datos[B1_DESC]</td><td style='text-align:center;'>$datos[C6_TES]</td><td class='tdD'>$datos[C6_LOCAL]</td><td class='tdD'>$".number_format($datos["C6_PRUNIT"],2)."</td><td class='tdD'>".number_format($datos["C6_QTDVEN"],2)."</td><td class='tdD'></td><td class='tdD'>$".number_format($datos["C6_VALOR"],2)."</td></tr>";
						 }
						 odbc_free_result($sql);
						 //cambio backo orders CHT
						 /*$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE FROM ZZN010 ZZN INNER JOIN ZZM010 ZZM ON ZZN_CODIGO=ZZM_CODALM WHERE ZZM_PEDIDO='".$pedido_zzm."' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_=''");*/
						 //buscar por el numero de factura 
						 $sql=odbc_exec($conn,"SELECT ZZN_NOMBRE FROM ZZN010 ZZN INNER JOIN ZZM010 ZZM ON ZZN_CODIGO=ZZM_CODALM WHERE ZZM_FATURA = '".$_GET["fac"]."' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_=''");
						 $datos=odbc_fetch_array($sql);
						 odbc_free_result($sql);
						 echo "<tr><td colspan='6'>Total del pedido:</td><td class='tdD'><hr>$".number_format($valor,2)."</td></tr></table><strong>
								<hr>Observaciones: ".$pedido['C5_OBSVTA']."<br><br><hr> &nbsp;***Enviar a: ";
							if(trim($pedido['C5_DIREMB'])=="FISCAL")
							{
								echo "[FISCAL] ".trim($pedido['A1_END']).", Col. ".trim($pedido['A1_BAIRRO']).", Mun. ".trim($pedido['poblacion']);
								$flet=odbc_exec($conn,"SELECT A2_NREDUZ,Z14_NREDUZ FROM Z15010 Z15 INNER JOIN Z14010 Z14 ON Z14.D_E_L_E_T_='' AND Z14_COD=Z15_CODZON INNER JOIN SA2010 A2 ON A2.D_E_L_E_T_='' AND A2_COD=Z15_CODFLE WHERE Z15_CODFLE='".$pedido['A1_TRANSP']."' AND Z15.D_E_L_E_T_='';") or die("Error al ejecutar la zona");
									if($fletera=odbc_fetch_array($flet))
									{
										$zonaflet=$fletera['Z14_NREDUZ'];
										$nomflet=$fletera['A2_NREDUZ'];
									}
									else
									{
										$zonaflet="N/A";
										$nomflet="N/A";
									}
									odbc_free_result($flet);
							}
							else{
								$sqlD=odbc_exec($conn,"SELECT * FROM ZD1010 WHERE ZD1_CLAVE='$pedido[C5_DIREMB]' AND ZD1_CLIENT='$pedido[C5_CLIENTE]' AND D_E_L_E_T_=''")or die("Error DirEmb");
								$dEmb=odbc_fetch_array($sqlD);
								odbc_free_result($sqlD);
								echo trim($dEmb['ZD1_DIRECC']).", Col. ".trim($dEmb['ZD1_COLON']).", Mun. ".trim($dEmb['ZD1_POBLAC']);
								$flet=odbc_exec($conn,"SELECT A2_NREDUZ,Z14_NREDUZ FROM Z15010 Z15 INNER JOIN Z14010 Z14 ON Z14.D_E_L_E_T_='' AND Z14_COD=Z15_CODZON INNER JOIN SA2010 A2 ON A2.D_E_L_E_T_='' AND A2_COD=Z15_CODFLE WHERE Z15_CODFLE='".$dEmb['ZD1_FLETE']."' AND Z15.D_E_L_E_T_='';") or die("Error al ejecutar la zona");
								if($fletera=odbc_fetch_array($flet))
								{
									$zonaflet=$fletera['Z14_NREDUZ'];
									$nomflet=$fletera['A2_NREDUZ'];
								}
								else
								{
									$zonaflet="";
									$nomflet="";
								}
								odbc_free_result($flet);
							}
							$fechasurt=substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5);
							//OBTIENE LA FECHA DE SURTIDO EN FORMATO M-D-Y
							$fechasurt=substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5);
							//CALCULA LA FECHA DE SURTIDO MENOS 3HR
							$valfechasur=strtotime($fechasurt);
							if(strlen(trim($pedido['C5_FYHSURT']))==16 || strlen(trim($pedido['C5_FYHSURT']))==17)
							{
								$iniciolab=substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5,3)." 08:00:00";
							}
							elseif(strlen(trim($pedido['C5_FYHSURT']))==18 || strlen(trim($pedido['C5_FYHSURT']))==19)
							{
								$iniciolab=substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5,5)." 08:00:00";
							}
							$valiniciolab=strtotime($iniciolab);
							$sec1=$valfechasur-$valiniciolab;
							if($sec1>=10800) //SI EL TIEMPO DISPONIBLE ES MAYOR A 3 HORAS LO RESTA
							{
								//$valfechasur-=10800;
								$surtalm=date("d/m/y H:i:s",($valfechasur-10800));
							}
							else
							{
								$sec2=10800-$sec1;
								//restar al dia anterior al cierre los minutos restantes
								$finlab=date("m/d/y",strtotime("-1 day",$valfechasur))." 18:00:00";
								$valfinlab=strtotime($finlab);
								$valfinlab-=$sec2;
								$surtalm=date("d/m/y H:i:s",$valfinlab);
							}
						echo "***<hr>Fletera: ".(trim($nomflet)==""?"----- ":trim($nomflet))." Zona: <strong>".(trim($zonaflet)==""?"-----":trim($zonaflet))."</strong>";
						//echo"<hr>Almacen No: ".trim($almacen)."<br>";
						echo"<hr>Almacenista: ".trim($datos['ZZN_NOMBRE'])."<br>
								<table class='tdRes'><tr><td>Emisi&oacute;n</td><td>Aprobaci&oacute;n Ventas</td><td>Aprobaci&oacute;n CyC</td><td>Limite Almac&eacute;n</td><td>Limite Surtido</td><td>Impresi&oacute;n</td></tr>
								<tr><td>".substr($pedido["C5_EMISSAO"],6)."/".substr($pedido["C5_EMISSAO"],4,2)."/".substr($pedido["C5_EMISSAO"],0,4)."</td><td>$pedido[C5_FYHRVTA]</td><td>$pedido[C5_FYHRCYC]</td><td>".$surtalm."</td><td>$pedido[C5_FYHSURT]</td><td>$pedido[C5_FYHRIMP]</td></tr></table></strong><br><br><center><font face='cb' size='+3'>*".$pedido_zzm."*</font><br>[".$pedido_zzm."]</center>";
							if($pedido['C5_APFLETE']=='T')
								echo "<span class='text2'>Flete pagado autorizado por Fleximatic S.A. de C.V.</span>";
							If(trim($pedido['C5_EMBCPED'])!="")
								echo "<br><br><span class='text2'>ESTE PEDIDO ESTA ASOCIADO CON EL PEDIDO ".trim($pedido['C5_EMBCPED']).".</span>";
						echo "<br><br>En la columna Cajas:&nbsp;&nbsp;&nbsp;C=Cajas&nbsp;&nbsp;&nbsp;B=Bolsas/Inner&nbsp;&nbsp;&nbsp;R=Resto
								</div>
							</body>
						</html>";
	}
	odbc_close($conn);	
?>