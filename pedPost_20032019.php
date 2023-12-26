<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1>PEDIDOS POSTERGADOS EN ESPERA DE APROBACI&Oacute;N.</h1>
	<label class="lblPost">N&uacute;mero de pedido:</label>
	<input type="text" size="10" maxlength="6" id="txtPed" placeholder="#####" required> 
	<button id="btnGPP">Guardar</button> 
	<button id="btnCancPP">Cancelar</button><br>
	<label class="lblPost">Observaciones:</label>
	<input class="pedPost" type="text" id="txtOb" maxlength="150" size="70" placeholder="Observaciones"><br>
<table>
 <tr><th>Pedido</th><th>Observaciones</th><th>Usuario</th><th>Fecha</th></tr>
<?php
	include("conectabd.php");
	require_once("class.phpmailer.php");
	require_once("class.smtp.php");
	if(isset($_POST['delP'])){
		odbc_exec($conn,"DELETE FROM ZVA010 WHERE ZVA_NUM='$_POST[delP]'")or die("Err Del");
		odbc_close($conn);
	exit;
	}elseif(isset($_POST['ped'])){
		if(!isset($_POST['alm'])){
			$sql=odbc_exec($conn, "SELECT ZZO_PEDIDO FROM ZZO010 ZZO INNER JOIN (SELECT C6_NUM FROM SC6010 WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522')  AND C6_BLQ='' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON ZZO_PEDIDO=SC6.C6_NUM WHERE ZZO_PEDIDO='$_POST[ped]' AND ZZO_FEMBAR='' AND ZZO.D_E_L_E_T_=''")or die("Error en ZVA");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			if($datos['ZZO_PEDIDO']==""){
				echo "-1";
				odbc_close($conn);
				exit;
			}
		}
		$sql=odbc_exec($conn, "SELECT ZVA_STATUS FROM ZVA010 WHERE ZVA_NUM='$_POST[ped]' AND D_E_L_E_T_=''")or die("Err status");
		$datos=odbc_fetch_array($sql);
		if(odbc_num_rows($sql)==0 || $datos['ZVA_STATUS']==0)
		{
			odbc_free_result($sql);
			$nUsu=gethostbyaddr($_SERVER['REMOTE_ADDR']);
			$entra="";
			$autopost=0;
			$sqlx=odbc_exec($conn,"SELECT COUNT(*) CUANTOS FROM ZZO010 WHERE ZZO_PEDIDO='$_POST[ped]' AND ZZO_CODFLE='000014' AND D_E_L_E_T_=''")or die("ErrPP");
			If($datosx=odbc_fetch_array($sqlx))
			{
				if($datosx["CUANTOS"]>0)
				{
					$autopost=1;
				}
			}
			if(strpos($_POST['obs'],"RECOLECTA 3 GUERRAS")===false)
			{
				$autopost=0;
			}
			else
			{
				$autopost=1;
			}
			$sql=odbc_exec($conn,"SELECT ISNULL(MAX(R_E_C_N_O_),0)+1 AS 'max' FROM ZVA010")or die("ErrMax");
			$datos=odbc_fetch_array($sql);
			If($autopost==1)
			{
				odbc_exec($conn,"INSERT INTO ZVA010(ZVA_FILIAL,ZVA_NUM,ZVA_USUVTA,ZVA_STATUS,ZVA_OBSERV,ZVA_USUCAP,ZVA_OBSCAP,D_E_L_E_T_,R_E_C_N_O_,ZVA_FYHCAP,ZVA_FYHAP) VALUES('','$_POST[ped]','Gerente Comercial',1,'','".gethostbyaddr($_SERVER['REMOTE_ADDR'])."','".$_POST['obs']."','',$datos[max],'".date("d/m/y H:i:s")."','".date("d/m/y H:i:s")."')")or die("Error Insert ZVA");
			}
			Else
			{
				odbc_exec($conn,"INSERT INTO ZVA010(ZVA_FILIAL,ZVA_NUM,ZVA_USUVTA,ZVA_STATUS,ZVA_OBSERV,ZVA_USUCAP,ZVA_OBSCAP,D_E_L_E_T_,R_E_C_N_O_,ZVA_FYHCAP,ZVA_FYHAP) VALUES('','$_POST[ped]','',0,'','".gethostbyaddr($_SERVER['REMOTE_ADDR'])."','".$_POST['obs']."','',$datos[max],'".date("d/m/y H:i:s")."','')")or die("Error Insert ZVA");
			}
			odbc_exec($conn,"UPDATE ZZO010 SET ZZO_OBSERV='".$_POST['obs']."' WHERE ZZO_PEDIDO='$_POST[ped]';")or die("Error Actualiza ZZO");
			//Envia Notificación
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = false;
			$datoC=odbc_exec($conn,"SELECT * FROM COR010 WHERE D_E_L_E_T_='';")or die("Error en la configuracion");
			$resC=odbc_fetch_array($datoC);
			odbc_free_result($datoC);
			$mail->Host = trim($resC['COR_SMTP']);
			$mail->Port = trim($resC['COR_PUERTO']);
			$mail->From = trim($resC['COR_USER']);
			$mail->AddAddress("asistentegercom@fleximatic.com.mx");
			$mail->AddCC("embarques@fleximatic.com.mx");
			$mail->AddCC("facturacion@fleximatic.com.mx");
			$mail->Subject = "Pedido postergado";
			$mail->Body="El pedido:$_POST[ped] ha sido postergado para su aprobación por el usuario:".substr($nUsu,0,strpos($nUsu,".")).", con la observación: $_POST[obs]";
			$mail->Send();			
		}
		else
		{
			odbc_exec($conn,"UPDATE ZVA010 SET ZVA_OBSERV=(ZVA_OBSERV+'$_POST[obs]'),ZVA_STATUS=0 WHERE ZVA_NUM='$_POST[ped]';")or die("Error Actualiza ZZO");
			//Envia Notificación
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = false;
			$datoC=odbc_exec($conn,"SELECT * FROM COR010")or die("Error en la configuracion");
			$resC=odbc_fetch_array($datoC);
			odbc_free_result($datoC);
			$mail->Host = trim($resC['COR_SMTP']);
			$mail->Port = trim($resC['COR_PUERTO']);
			$mail->From = trim($resC['COR_USER']);
			$mail->AddAddress("asistentegercom@fleximatic.com.mx");
			$mail->AddCC("embarques@fleximatic.com.mx");
			$mail->Subject = "Pedido postergado";
			$mail->Body="El pedido:$_POST[ped] ha sido postergado para su aprobación por el usuario:".substr($nUsu,0,strpos($nUsu,".")).", con la observación: $_POST[obs]";
			$mail->Send();	
		}
		odbc_free_result($sql);									 
	}
	else{
		$sql=odbc_exec($conn,"SELECT ZVA_NUM,ZVA_OBSCAP,ZVA_USUCAP,ZVA_FYHCAP FROM ZVA010 WHERE ZVA_STATUS=0 AND D_E_L_E_T_=''")or die("Error de pedidos postergados");
		while($datos=odbc_fetch_array($sql))
			echo "<tr><td><a id='cancPP' name='$datos[ZVA_NUM]' title='Cancelar pedido postergado'>$datos[ZVA_NUM]</a></td><td>$datos[ZVA_OBSCAP]</td><td>".substr($datos["ZVA_USUCAP"],0,strpos($datos["ZVA_USUCAP"],"."))."</td><td>$datos[ZVA_FYHCAP]</td></tr>";
		odbc_free_result($sql);
		odbc_close($conn);
	}
?>
</table><br>
</body>
</html>