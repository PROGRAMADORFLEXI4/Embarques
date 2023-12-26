<?PHP
	include("conectabd.php");
	$ped=odbc_exec($conn,"SELECT A1_TRANSP,A1_EST,C5_CLIENTE,C5_APFLETE,A1_NOME,C5_EMISSAO,C5_OBSVTA,A3_NOME,C5_LOF AS est,C5_FYHRVTA,C5_FYHRCYC,C5_FYHSURT,C5_FYHRIMP,C5_DIREMB,A1_END,A1_BAIRRO,A1_MUN+', '+A1_EST AS poblacion,C5_EMBCPED FROM SC5010 SC5 INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE LEFT JOIN SA3010 ON A3_COD=A1_VEND WHERE C5_LOJAENT=A1_LOJA AND SC5.D_E_L_E_T_<>'*' AND SA1.D_E_L_E_T_='' AND C5_NUM='".$_GET["pedido"]."'") or die("Error al ejecutar la consulta del pedido");
	$pedido=odbc_fetch_array($ped);

	$sql_inicio=odbc_exec($conn,"SELECT ZZS_SALIDA,ZZS_CODALM,ZZS_FYHSAL FROM ZZS010 WHERE ZZS_PEDIDO='".$_GET['Ped']."' AND D_E_L_E_T_='' ORDER BY ZZS_SALIDA DESC") or die("Error al obtener los datos de la salida");
	$datos_inicio=odbc_fetch_array($sql_inicio);
	odbc_free_result($sql);
	$sal =$datos_inicio['ZZS_SALIDA'];

	/*$sql=odbc_exec($conn,"SELECT  ZDS_PRODUC, B1_DESC, ZDS_PEDIM, SUM(C6_QTDVEN) CANTPED, ZDS_QTENT CANTENT, SUM(ZDS_QE) QE, B1_CANTBOL
		FROM ZDS010 ZDS 
		INNER JOIN SB1010 SB1 ON ZDS_PRODUC=B1_COD 
		LEFT JOIN SC6010 SC6 ON ZDS_PRODUC=C6_PRODUTO 
		WHERE ZDS_SALIDA='".$_GET["salida"]."' AND ZDS.D_E_L_E_T_='' AND C6_BLQ='' AND SC6.D_E_L_E_T_<>'*' AND C6_NUM='".$_GET["pedido"]."'
		GROUP BY ZDS_PRODUC, B1_DESC, ZDS_PEDIM, B1_CANTBOL, ZDS_QTENT;") or die("Error al ejecutar la consulta de partidas del pedido");*/	
		$sql=odbc_exec($conn,"SELECT ZDS_PRODUC, B1_DESC, ZDS_PEDIM, C6_QTDVEN CANTPED, SUM(ZDS_QTENT) CANTENT, SUM(ZDS_QE) QE, B1_CANTBOL, C6_LOCAL
		FROM ZDS010 ZDS 
		INNER JOIN SB1010 SB1 ON ZDS_PRODUC=B1_COD AND SB1.D_E_L_E_T_=''
		LEFT JOIN (SELECT SUM(C6_QTDVEN) C6_QTDVEN, C6_PRODUTO, C6_LOCAL FROM SC6010 WHERE  C6_BLQ='' AND D_E_L_E_T_<>'*' AND C6_NUM='".$_GET["pedido"]."' GROUP BY C6_PRODUTO, C6_LOCAL) SC6 ON ZDS_PRODUC=C6_PRODUTO 
		WHERE ZDS_SALIDA='".$_GET["salida"]."' AND ZDS.D_E_L_E_T_=''
		GROUP BY ZDS_PRODUC, B1_DESC, ZDS_PEDIM, B1_CANTBOL, C6_QTDVEN, C6_LOCAL;") or die("Error al ejecutar la consulta de partidas del pedido");
	//Reviza si ya esta capturada la salida del pedido, si es asi Imprime la salida correspondiente
	 echo "<html>
		 <head>
		<link href='css/styles.css' rel='stylesheet' type='text/css'>
		<title>Acuse de surtido</title>
	</head>
 	<body onload=print_pedido_salida('".$_GET["pedido"]."')><div ><!-- class='page' -->
	<table>
	<tr>
		<td colspan='7'><h1>Acuse de surtido</h1></td>
	</tr>
	<tr>
		<td colspan='7'><hr></td>
	</tr>
	<tr>
		<th colspan='7' style='text-align:left'>Pedido: ".$_GET["pedido"]." Cliente: ".$pedido["C5_CLIENTE"]." ".$pedido["A1_NOME"].", Tipo de Pedido: ".$pedido["est"].", Vendedor: ".$pedido["A3_NOME"]."</th>
	</tr>
	<tr class='trEnc'>
		<td>Producto</td>
		<td>Descripci&oacute;n</td>
		<td>Almac&eacute;n</td>
		<td>CantPed</td>
		<td>CantSurt</td>
		<td>Cajas</td>
		<td>BKO</td>
	</tr>
	 <tr>
	 	<td colspan='7'><hr></td>
	 </tr>";
	$valor=0;
	while($datos=odbc_fetch_array($sql)){
		$almacen = $datos["C6_LOCAL"];
		$valor+=$datos["C6_VALOR"];
		$cajas=floor($datos["CANTENT"]/$datos["B1_CANTBOL"]);
		$bolsas=floor(($datos["CANTENT"]-($datos["QE"]*$cajas))/$datos["B1_CANTBOL"]);
		$resto=$datos["CANTENT"]-($datos["QE"]*$cajas)-($datos["B1_CANTBOL"]*$bolsas);
		echo "<tr>
			<td>$datos[ZDS_PRODUC]</td>
			<td>$datos[B1_DESC]</td>
			<td class='tdD'>$datos[C6_LOCAL]</td>
			<td class='tdD'>".number_format($datos["CANTPED"])."</td>
			<td class='tdD'>".number_format($datos["CANTENT"],0)."</td>
			<td class='tdD'>".($cajas>0?$cajas."C ":"").($bolsas>0?$bolsas."B ":"").($resto>0?$resto."R ":"")."</td>
			<td class='tdD'>".(intval($datos['CANTPED']) - intval($datos["CANTENT"]))."</td>
		</tr>";
	}
	$sql_totales = "SELECT ZZM_TINACO, ZZM_EXHIB, ZZM_COSTAL, ZZM_CAJAS FROM ZZM010 WHERE ZZM_PEDIDO = '".$_GET["pedido"]."' AND D_E_L_E_T_ = '';";
	$totales= odbc_exec($conn, $sql_totales) or die("Error en consulta del totales");

	if($total=odbc_fetch_array($totales)){
		$tinacos = trim($total["ZZM_TINACO"]);
		$exhibidores = trim($total["ZZM_EXHIB"]);
		$costales = trim($total["ZZM_COSTAL"]);
		$cajas = trim($total["ZZM_CAJAS"]);
	}
	echo "<tr>
	 	<td colspan='7'><hr></td>
	 </tr>
	 <tr>
		<td>T&nbsp;O&nbsp;T&nbsp;A&nbsp;L&nbsp;E&nbsp;S:</td>
		<td></td>
		<td>Tinacos: ".round($tinacos, 0)."</td>
		<td>Exhibidores: ".round($exhibidores, 0) ."</td>
		<td>Costales: ".round($costales, 0)."</td>
		<td>Cajas: ".round($cajas, 0)."</td>
		<td></td>
	</tr>";
	odbc_free_result($sql);
	$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE FROM ZZN010 ZZN INNER JOIN ZZM010 ZZM ON ZZN_CODIGO=ZZM_CODALM WHERE ZZM_PEDIDO='".$_GET["pedido"]."' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_=''");
	$datos=odbc_fetch_array($sql);
	odbc_free_result($sql);
	echo "</table><strong>
			<hr>Observaciones: ".$pedido['C5_OBSVTA']."<br><br><hr> &nbsp;***Enviar a: ";
		if(trim($pedido['C5_DIREMB'])=="FISCAL"){
			echo "[FISCAL] ".trim($pedido['A1_END']).", Col. ".trim($pedido['A1_BAIRRO']).", Mun. ".trim($pedido['poblacion']);
			$flet=odbc_exec($conn,"SELECT A2_NREDUZ,Z14_NREDUZ FROM Z15010 Z15 INNER JOIN Z14010 Z14 ON Z14.D_E_L_E_T_='' AND Z14_COD=Z15_CODZON INNER JOIN SA2010 A2 ON A2.D_E_L_E_T_='' AND A2_COD=Z15_CODFLE WHERE Z15_CODFLE='".$pedido['A1_TRANSP']."' AND Z15.D_E_L_E_T_='';") or die("Error al ejecutar la zona");
				if($fletera=odbc_fetch_array($flet)){
					$zonaflet=$fletera['Z14_NREDUZ'];
					$nomflet=$fletera['A2_NREDUZ'];
				}
				else{
					$zonaflet="N/A";
					$nomflet="N/A";
				}
				odbc_free_result($flet);
		}else{
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
				<tr><td>".substr($pedido["C5_EMISSAO"],6)."/".substr($pedido["C5_EMISSAO"],4,2)."/".substr($pedido["C5_EMISSAO"],0,4)."</td><td>$pedido[C5_FYHRVTA]</td><td>$pedido[C5_FYHRCYC]</td><td>".$surtalm."</td><td>$pedido[C5_FYHSURT]</td><td>$pedido[C5_FYHRIMP]</td></tr></table></strong><br><br><center><font face='cb' size='+3'>*".$_GET["pedido"]."*</font><br>[".$_GET["pedido"]."]</center>";
			if($pedido['C5_APFLETE']=='T')
				echo "<span class='text2'>Flete pagado autorizado por Fleximatic S.A. de C.V.</span>";
			If(trim($pedido['C5_EMBCPED'])!="")
				echo "<br><br><span class='text2'>ESTE PEDIDO ESTA ASOCIADO CON EL PEDIDO ".trim($pedido['C5_EMBCPED']).".</span>";
		echo "<br><br>En la columna Cajas:&nbsp;&nbsp;&nbsp;C=Cajas&nbsp;&nbsp;&nbsp;B=Bolsas/Inner&nbsp;&nbsp;&nbsp;R=Resto
				</div>
					<script language='javascript'>
						setTimeout(function() {
							window.print();
							alert('Imprimiendo acuse..');
							window.open('index.php', '_self');
						}, 3000);
					</script>
			</body>
		</html>";
	odbc_close($conn);	
?>