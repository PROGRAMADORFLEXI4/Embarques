<?php
	try{
		include("conectabd.php");
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
				require("class.phpmailer.php");
				require_once("class.smtp.php");
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth = false;
				$datoC=odbc_exec($conn,"SELECT * FROM COR010")or die("Error en la configuracion");
				$resC=odbc_fetch_array($datoC);
				odbc_free_result($datoC);
				$mail->Host = $resC['COR_SMTP'];
				$mail->Port = $resC['COR_PUERTO'];
				$mail->From = $resC['COR_USER'];
				$mail->FromName = "Almacen";
				$mail->AddAddress($resC['COR_PRESPU']);
				$mail->AddCC("mercadotecnia@fleximatic.com.mx");
				$mail->Subject = "Notificacion de entrega de mercancia";
				$body = "<html><head>
						</head>
						<body>
							Buen d&iacute;a, el presente correo es para notificarle que el departamento de almac&eacute;n est&aacute; surtiendo una solicitud de muestra con n&uacute;mero: $_POST[sm], la cual va a estar lista para que la recoja el d&iacute;a: <b>$datos[CP_FYHLIM]</b>.<br>Las muestras al almac&eacute;n las solilcito el usuario: $datos[CP_SOLICIT] con las siguientes observaciones: $datos[CP_OBS].<br><br>
							<u>Tenga presente que esta cuenta no es monitoreada. Para cualquier duda o aclaraci&oacute;n favor de comunicarse con el departamento de almac&eacute;n.</ul>
					</body></html>";
				$body = utf8_decode($body);
				$body = eregi_replace("[\]",'',$body);
				$mail->MsgHTML($body);
				$mail->Send();	
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
	$sql=odbc_exec($conn,"SELECT CP_FYHAP,CP_FYHLIM,CP_SOLICIT FROM SCP010 WHERE CP_NUM='".substr($_POST['np'],2)."' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_=''  GROUP BY CP_FYHAP,CP_FYHLIM,CP_SOLICIT")or die("Error SA");
	$datos=odbc_fetch_array($sql);
	odbc_free_result($sql);
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="images/icono.ico"/>
 	<link rel="stylesheet" href="css/styles.css"/>
</head>
<img src="images/cancel.png" id="clsSM" title="Cerrar ventana" onClick="window.close();"/> &nbsp;&nbsp; <img src="images/printer.png" id="prntSA" title="Imprimir solicitud de muestra" onClick="window.open('sm.php?actM=<?php echo substr($_POST['np'],2); ?>','_self'); window.print(); window.close();"/>
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
			$sql=odbc_exec($conn,"SELECT CP_PRODUTO,CP_DESCRI,CP_QUANT,CP_QUJE FROM SCP010 WHERE CP_NUM='".substr($_POST['np'],2)."' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_='' ORDER BY CP_PRODUTO")or die("Erro MP");
			while($datos=odbc_fetch_array($sql)){
				$cant+=$datos['CP_QUANT'];
				echo "<tr><td class='tdIt'>[$cont]&nbsp;&nbsp;</td><td align='left'>".trim($datos['CP_PRODUTO'])." ".utf8_encode(trim($datos['CP_DESCRI']))."</td><td class='tdMP'>".number_format($datos['CP_QUANT'],0)."</td><td class='tdMP'>".number_format($datos['CP_QUJE'],0)."</td><td class='tdMP'>".number_format($datos['CP_QUANT']-$datos['CP_QUJE'],0)."</td></tr>";
				$cont++;
			}
			odbc_free_result($sql);
			odbc_close($conn);
		}
		echo "<tr><td colspan='2' class='tdD'>Total piezas solicitadas:</td><td class='tdTotal'>".number_format($cant,0)."</td></tr>";
	}catch(Exception $mt){
		echo "Error".$mt->getMessage();
	}
?>
</tbody></table>
</html>