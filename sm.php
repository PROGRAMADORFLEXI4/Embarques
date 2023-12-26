<?php
	try{
		include("conectabd.php");
		include("send_mail_365.php");

		if(isset($_GET['actM'])){
			$sql=odbc_exec($conn,"SELECT CP_SALIDA FROM SCP010 WHERE CP_NUM='$_GET[actM]' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_='' AND CP_SALIDA='0' GROUP BY CP_SALIDA")or die("Error actM");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			if(trim($datos['CP_SALIDA'])=='0')
				odbc_exec($conn, "UPDATE SCP010 SET CP_SALIDA='-1' WHERE CP_NUM='$_GET[actM]' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_='' AND CP_SALIDA='0'");
			odbc_close($conn);
/*			echo "<script>window.close();</script>";*/
			exit;
		}
		elseif(isset($_POST['sm'])){
			odbc_exec($conn,"UPDATE SCP010 SET CP_TIPO=1,CP_FYHALM='".date("d/m/y H:i:s",time())."',CP_SALIDA='$_POST[salida]' WHERE CP_NUM='$_POST[sm]'");
			if(substr(strtoupper($_POST['sm']),0,1)=="V" || (substr(strtoupper($_POST['sm']),0,1)=="P" && substr(strtoupper($_POST['sm']),0,2)<>"PT")){
				$sql=odbc_exec($conn,"SELECT CP_SOLICIT,CP_OBS,CP_FYHLIM,CP_PRODUTO,CP_DESCRI,CP_QUANT-CP_QUJE AS 'xEntr' FROM SCP010 WHERE CP_NUM='$_POST[sm]' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_='' ORDER BY CP_PRODUTO")or die("Error smDet");
				$datos=odbc_fetch_array($sql);
				odbc_free_result($sql);
				date_default_timezone_set('America/Mexico_City');
				$datoC=odbc_exec($conn,"SELECT * FROM COR010")or die("Error en la configuracion");
				$resC=odbc_fetch_array($datoC);
				odbc_free_result($datoC);

				$body = "<html><head>
						</head>
						<body>
							Buen d&iacute;a, el presente correo es para notificarle que el departamento de almac&eacute;n est&aacute; surtiendo una solicitud de muestra con n&uacute;mero: $_POST[sm], la cual va a estar lista para que la recoja el d&iacute;a: <b>$datos[CP_FYHLIM]</b>.<br>Las muestras al almac&eacute;n las solilcito el usuario: $datos[CP_SOLICIT] con las siguientes observaciones: $datos[CP_OBS].<br><br>
							<u>Tenga presente que esta cuenta no es monitoreada. Para cualquier duda o aclaraci&oacute;n favor de comunicarse con el departamento de almac&eacute;n.</ul>
					</body></html>";
				$body = utf8_decode($body);
				$body = eregi_replace("[\]",'',$body);

				$enviar_correo(
					trim($resC['COR_USER']),
					trim("Almacen"),
					$body,
					"Notificacion de entrega de mercancia",
					explode(';', $resC['COR_PRESPU']),
					explode(';', "mercadotecnia@fleximatic.com.mx"),
					'','','','', '', '','', '', '');
			}			
			odbc_close($conn);
			exit;
		}
		elseif(isset($_POST['cm'])){
			odbc_exec($conn,"UPDATE SCP010 SET CP_TIPO=2,CP_FYHENTR='".date("d/m/y H:i:s",time())."',CP_ENTREGO='".trim($_POST['usu'])."' WHERE CP_NUM='$_POST[cm]'");
			odbc_close($conn);
			exit;
		}
	}catch(Exception $ex){
		echo "Error".$ex->getMessage();
	}
	
	$sql=odbc_exec($conn,"SELECT CP_FYHAP,CP_FYHLIM,CP_SOLICIT,CP_CTEEMB,CP_DIREMB FROM SCP010 WHERE CP_NUM='".substr($_POST['np'],2)."' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_=''  GROUP BY CP_FYHAP,CP_FYHLIM,CP_SOLICIT,CP_CTEEMB,CP_DIREMB")or die("Error SA");
	$datos=odbc_fetch_array($sql);
	odbc_free_result($sql);
	
	$sql=odbc_exec($conn,"SELECT A1_NOME FROM SA1010 WHERE D_E_L_E_T_='' AND A1_COD='".trim($datos['CP_CTEEMB'])."'")or die("Error SA");
	$DtosCte=odbc_fetch_array($sql);
	odbc_free_result($sql);
	
	if(strtoupper(trim($datos['CP_DIREMB']))=='FISCAL')
	{
		$sql=odbc_exec($conn,"SELECT A1_END AS ZD1_DIRECC,A1_NR_END AS NOEXT, A1_BAIRRO AS ZD1_COLON, A1_MUN AS ZD1_POBLAC, A1_EST AS ZD1_EDO,A1_CEP AS ZD1_CP,YA_DESCR AS ZD1_PAIS, A1_TEL AS TELEFONO FROM SA1010 A1 INNER JOIN SYA010 YA ON A1_PAIS=YA_CODGI AND YA.D_E_L_E_T_='' WHERE A1_COD='".trim($datos['CP_CTEEMB'])."'")or die("Error SA");
		$DtosDir=odbc_fetch_array($sql);
		odbc_free_result($sql);
	}
	else
	{
		$sql=odbc_exec($conn,"SELECT ZD1_DIRECC,ZD1_COLON,ZD1_POBLAC,ZD1_EDO,ZD1_CP,ZD1_TEL,YA_DESCR AS ZD1_PAIS FROM ZD1010 ZD1 INNER JOIN SA1010 A1 ON ZD1_CLIENT=A1_COD AND A1.D_E_L_E_T_='' INNER JOIN SYA010 YA ON A1_PAIS=YA_CODGI AND YA.D_E_L_E_T_='' WHERE ZD1.D_E_L_E_T_='' AND ZD1_CLAVE='".trim($datos['CP_DIREMB'])."' AND ZD1_CLIENT='".trim($datos['CP_CTEEMB'])."'")or die("Error SA");
		$DtosDir=odbc_fetch_array($sql);
		/*if($DtosDir=odbc_fetch_array($sql))
		{$variablebasura=0;}
		else
		{echo "No hay direccion de entrega";exit;}*/
		odbc_free_result($sql);
	}
	
	if(trim($datos['CP_CTEEMB'])!="" && trim($datos['CP_DIREMB'])=="")
	{
		echo "No hay direccion de entrega";exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="images/icono.ico"/>
 	<link rel="stylesheet" href="css/styles.css"/>
</head>
<img src="images/cancel.png" id="clsSM" title="Cerrar ventana" onClick="window.close();"/> &nbsp;&nbsp; <img src="images/printer.png" id="prntSA" title="Imprimir solicitud de muestra <?php echo substr($_POST['np'],2); ?>" onClick="window.open('sm.php?actM=<?php echo substr($_POST['np'],2); ?>','_self'); window.print(); window.close();"/>
<table class="tMP">
	<thead>
	<tr>
		<td rowspan="2"><img src="images/logo.png" width="80px"></td>
		<td align="center" colspan="2"><h1>FLEXIMATIC S.A. DE C.V.</h1></td>
        <td align="right" colspan="2">MUESTRAS PENDIENTES POR SURTIR</td>
    </tr>
    <tr>
        <td colspan="5">N&uacute;m. SA: <?php echo substr($_POST['np'],2); ?> &nbsp;&nbsp; Fecha de aprobaci&oacute;n: <?php echo $datos["CP_FYHAP"]?> &nbsp; Fecha limite de surtido: <?php echo $datos["CP_FYHLIM"]?> &nbsp;&nbsp; Solicitante: <?php echo trim($datos['CP_SOLICIT']); ?></td>
    </tr>
	<?php
		if(trim($datos['CP_CTEEMB'])!="")
		{
			echo "
	<tr><td colspan='5'><hr></td></tr>
	<tr><td colspan='5'>DIRECCION DE EMBARQUE</td></tr>
	<tr><td colspan='5'>Cliente: ".$datos['CP_CTEEMB']."&nbsp;&nbsp;&nbsp;&nbsp;".$DtosCte['A1_NOME']."</td></tr>
	<tr><td colspan='5'>".$DtosDir['ZD1_DIRECC'].", ".$DtosDir['ZD1_COLON']."</td></tr>
	<tr><td colspan='5'>".$DtosDir['ZD1_POBLAC'].", ".$DtosDir['ZD1_EDO'].", ".$DtosDir['ZD1_PAIS'].", CP ".variant_fix($DtosDir['ZD1_CP'])."</td></tr>
	<tr><td colspan='5'>TELEFONO ".$DtosDir['ZD1_TEL']."</td></tr>";
		}
		//else
		//	echo $datos['CP_CTEEMB'];
	?>
    <tr><td colspan="5"><hr></td></tr>
    <tr>
    	<th>Item</th>
    	<th>Producto</th>
        <th>Solicitado</th>
        <th>Surtido</th>
        <th>Saldo</th>
    </tr>
    </thead>
    <tbody>
<?php
	try{
		if(isset($_POST['np'])){
			$cont=1;
			$cant=0;
			//$sql=odbc_exec($conn,"SELECT CP_PRODUTO,CP_DESCRI,CP_QUANT,CP_QUJE FROM SCP010 WHERE CP_NUM='".substr($_POST['np'],2)."' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_='' ORDER BY CP_PRODUTO")or die("Erro MP");
			$sql=odbc_exec($conn,"SELECT CP_PRODUTO,CP_DESCRI,CP_QUANT,CP_QUJE,B1_UM,B1_SEGUM,B1_CONV,CASE WHEN B1_CONV<>0 THEN CP_QUANT*B1_CONV ELSE 0 END CP_QUANT2,CASE WHEN B1_CONV<>0 THEN CP_QUJE*B1_CONV ELSE 0 END CP_QUJE2 FROM SCP010 CP INNER JOIN SB1010 B1 ON CP_PRODUTO=B1_COD AND B1.D_E_L_E_T_='' WHERE CP_NUM='".substr($_POST['np'],2)."' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND CP.D_E_L_E_T_='' ORDER BY CP_PRODUTO")or die("Erro MP");
			while($datos=odbc_fetch_array($sql)){
				$cant+=$datos['CP_QUANT'];
				echo "<tr><td class='tdIt'>[$cont]&nbsp;&nbsp;</td><td align='left'>".trim($datos['CP_PRODUTO'])." ".utf8_encode(trim($datos['CP_DESCRI']))."</td><td class='tdMP'>".number_format($datos['CP_QUANT'],2)." ".$datos['B1_UM']."</td><td class='tdMP'>".number_format($datos['CP_QUJE'],2)." ".$datos['B1_UM']."</td><td class='tdMP'>".number_format($datos['CP_QUANT']-$datos['CP_QUJE'],2)." ".$datos['B1_UM']."</td></tr>";
				If ($datos['B1_CONV']!=0)
					echo "<tr style='border-bottom:solid 1px gray;'><td class='tdIt'>&nbsp;&nbsp;</td><td align='right'>Segunda UM</td><td class='tdMP'>".number_format($datos['CP_QUANT2'],2)." ".$datos['B1_SEGUM']."</td><td class='tdMP'>".number_format($datos['CP_QUJE2'],2)." ".$datos['B1_SEGUM']."</td><td class='tdMP'>".number_format($datos['CP_QUANT2']-$datos['CP_QUJE2'],2)." ".$datos['B1_SEGUM']."</td></tr>";
				$cont++;
			}
			odbc_free_result($sql);
			odbc_close($conn);
		}
		echo "<tr><td colspan='2' class='tdD'>Total piezas solicitadas:</td><td class='tdTotal'>".number_format($cant,2)."</td></tr>";
	}catch(Exception $mt){
		echo "Error".$mt->getMessage();
	}
?>
</tbody></table>
</html>