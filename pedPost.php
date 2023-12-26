<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<h1>PEDIDOS POSTERGADOS EN ESPERA DE APROBACI&Oacute;N.</h1>
		<label class="lblPost">N&uacute;mero de pedido:</label>
		<input type="text" size="10" maxlength="6" id="txtPed" placeholder="#####" required>
		<!-- Cambio CHT -->
		<label class="lblPost">Orden de Surtido:</label>
		<input type="text" size="10" maxlength="14" id="txtordSur" placeholder="#####" required>

		<button id="btnGPP">Guardar</button> 
		<button id="btnCancPP">Cancelar</button><br>
		<label class="lblPost">Observaciones:</label>
		<input class="pedPost" type="text" id="txtOb" maxlength="150" size="70" placeholder="Observaciones"><br>
		<label class="lblPost">Recolecci&oacute;n:</label>
		<select class="pedPost" type="text" id="txtOb1" style="width: 250px" onchange="morro()" >
			<option value=0 >NINGUNA</option>
			<option value="RECOLECTA 3 GUERRAS">RECOLECTA 3 GUERRAS</option>
			<option value="RECOLECTA JR">RECOLECTA JR</option>
			<option value="RECOLECTA VILLARREAL">RECOLECTA VILLARREAL</option>
			<option value="RECOLECTA EURO">RECOLECTA EURO</option>
			<option value="RECOLECTA PAQMEX">RECOLECTA PAQMEX</option>
			<option value="RECOLECTA GDL-MER">RECOLECTA GDL-MER</option>
			<option value="RECOLECTA EL DUERO">RECOLECTA EL DUERO</option>
			<option value="RECOLECTA POTOSINOS">RECOLECTA POTOSINOS</option>
			<option value="RECOLECTA VIAGGI">RECOLECTA VIAGGI</option>
			<option value="RECOLECTA JULIAN DE OBREGON">RECOLECTA JULIAN DE OBREGON</option>
			<option value="FLETES DE REGRESO">FLETES DE REGRESO</option>
			<option value="ESPERA DE MERCANCIA">ESPERA DE MERCANCIA</option>
			<!--<option value="CLIENTE CON CITA">CLIENTE CON CITA</option>
			<option value="CLIENTE RECOGE">CLIENTE RECOGE</option>
			<option value="PUBLICIDAD E/FLEXI">PUBLICIDAD E/FLEXI</option>
			<option value="VENTA AL PUBLICO">VENTA AL PUBLICO</option>
			<option value="ACLARACIONES">ACLARACIONES</option>-->
		</select><br>
		
		<table>
 		<tr>	
 			<th>Pedido</th>
 			<th>Orden Surtido</th>
 			<th>Observaciones</th>
 			<th>Usuario</th>
 			<th>Fecha</th>
 		</tr>
 <script>
	function morro() {
	  var x = document.getElementById("txtOb1").value;
	  document.getElementById("txtOb").value = x;
	}
</script>
<?php
	include("conectabd.php");
	require_once("class.phpmailer.php");
	require_once("class.smtp.php");
	
	include("send_mail_365.php");
	if(isset($_POST['delP']))
	{
		odbc_exec($conn,"DELETE FROM ZVA010 WHERE ZVA_NUM='$_POST[delP]'")or die("Err Del");
		odbc_close($conn);
		exit;
	}
	elseif(isset($_POST['ped']))
	{
		if(!isset($_POST['alm']))
		{
			/*$sql=odbc_exec($conn, "SELECT ZZO_PEDIDO FROM ZZO010 ZZO INNER JOIN (SELECT C6_NUM FROM SC6010 WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522')  AND C6_BLQ='' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON ZZO_PEDIDO=SC6.C6_NUM WHERE ZZO_PEDIDO='$_POST[ped]' AND ZZO_FEMBAR='' AND ZZO.D_E_L_E_T_=''")or die("Error en ZVA");*/
			/*$sql=odbc_exec($conn, "SELECT ZZO_PEDIDO, ZZO_ORDSUR FROM ZZO010 ZZO INNER JOIN (SELECT C6_NUM FROM SC6010 WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522')  AND C6_BLQ='' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON ZZO_PEDIDO=SC6.C6_NUM WHERE ZZO_PEDIDO='$_POST[ped]' AND ZZO_ORDSUR = '$_POST[ordSur]' AND ZZO_FEMBAR='' AND ZZO.D_E_L_E_T_=''")or die("Error en ZVA");*/
			//cambio backorder CHT
			$sql=odbc_exec($conn, "SELECT ZZO_PEDIDO, ZZO_ORDSUR, ZZO_FEMBAR
			FROM ZZO010 ZZO 
			INNER JOIN (
				SELECT C6_NUM 
				FROM SC6010 
				WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522' OR C6_TES='523' OR C6_TES='535')  AND C6_BLQ='' AND D_E_L_E_T_='' 
				GROUP BY C6_NUM) AS SC6 ON ZZO_PEDIDO=SC6.C6_NUM 
			WHERE ZZO_PEDIDO='".$_POST["ped"]."' AND ZZO_ORDSUR = '".$_POST["ordSur"]."' AND ZZO_FEMBAR='' AND ZZO.D_E_L_E_T_='';")or die("Error en ZVA");
			
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			if($datos['ZZO_PEDIDO']=="")
			{
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
			/*
			$sqlx=odbc_exec($conn,"SELECT COUNT(*) CUANTOS FROM ZZO010 WHERE ZZO_PEDIDO='$_POST[ped]' AND ZZO_CODFLE='000014' AND ZZO_CODFLE='000014' AND ZZO_CODFLE='000651' AND ZZO_CODFLE='001341' AND ZZO_CODFLE='000011' AND ZZO_CODFLE='001548' AND D_E_L_E_T_=''")or die("ErrPP");
			If($datosx=odbc_fetch_array($sqlx))
			{
				if($datosx["CUANTOS"]>0)
				{
					$autopost=0;
				}
			}
			*/
			if($_POST['obs1']=='RECOLECTA 3 GUERRAS')
			{
				$autopost=1;
			}
			else if($_POST['obs1']=='RECOLECTA JR')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA VILLARREAL')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA EURO')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA PAQMEX')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA GDL-MER')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA EL DUERO')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA POTOSINOS')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='FLETES DE REGRESO')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA VIAGGI')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='RECOLECTA JULIAN DE OBREGON')
			{
				$autopost=1;	
			}
			else if($_POST['obs1']=='ESPERA DE MERCANCIA')
			{
				$autopost=1;	
			}
			else
			{
				$autopost=0;
			}

            $consulta=odbc_exec($conn,"SELECT count(zva_num) zeta  FROM ZVA010 where zva_num='".$_POST['ped']."';")or die("ErrMax "."SELECT count(zva_num) zeta  FROM ZVA010 where zva_num='".$_POST['ped']."'");
            $result=odbc_fetch_array($consulta);
            if($result["zeta"]>=1){
           		echo "-2";
				odbc_close($conn);
				exit;
           	}
           	else{             
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
				/*odbc_exec($conn,"UPDATE ZZO010 SET ZZO_OBSERV='".$_POST['obs']."' WHERE ZZO_PEDIDO='$_POST[ped]';")or die("Error Actualiza ZZO");*/
				odbc_exec($conn,"UPDATE ZZO010 SET ZZO_OBSERV='".$_POST['obs']."' WHERE ZZO_PEDIDO='$_POST[ped]' AND ZZO_ORDSUR = '$_POST[ordSur]';")or die("Error Actualiza ZZO");
				$datoC=odbc_exec($conn,"SELECT * FROM COR010 WHERE D_E_L_E_T_='';")or die("Error en la configuracion");
				$resC=odbc_fetch_array($datoC);
				odbc_free_result($datoC);
				//Envia Notificación
				$enviar_correo(
					trim($resC['COR_USER']),
					trim($resC['COR_USER']),
					"El pedido:$_POST[ped] ha sido postergado para su aprobación por el usuario:".substr($nUsu,0,strpos($nUsu,".")).", con la observación: $_POST[obs]",
					"Pedido postergado",
					explode(';', "asistentegercom@fleximatic.com.mx"),
					explode(';', "embarques@fleximatic.com.mx;facturacion@fleximatic.com.mx;administrativogc@fleximatic.com.mx"),
					'','','', '','','','','', '');
			}				
	    }
		else
		{
			$consulta=odbc_exec($conn,"SELECT count(zva_num) zeta  FROM ZVA010 where zva_num='".$_POST['ped']."';")or die("ErrMax "."SELECT count(zva_num) zeta  FROM ZVA010 where zva_num='".$_POST['ped']."'");
            $result=odbc_fetch_array($consulta);
            if($result["zeta"]>=1){           		
           		echo "-2";
				odbc_close($conn);
				exit;
           	}
           	else{

			odbc_exec($conn,"UPDATE ZVA010 SET ZVA_OBSERV=(ZVA_OBSERV+'$_POST[obs]'),ZVA_STATUS=0 WHERE ZVA_NUM='$_POST[ped]';")or die("Error Actualiza ZZO");
			$datoC=odbc_exec($conn,"SELECT * FROM COR010")or die("Error en la configuracion");
			$resC=odbc_fetch_array($datoC);
			odbc_free_result($datoC);
			//Envia Notificación
			$enviar_correo(
				trim($resC['COR_USER']),
				trim($resC['COR_USER']),
				"El pedido:$_POST[ped] ha sido postergado para su aprobación por el usuario:".substr($nUsu,0,strpos($nUsu,".")).", con la observación: $_POST[obs]",
				"Pedido postergado",
				explode(';', "asistentegercom@fleximatic.com.mx"),
				explode(';', "administrativogc@fleximatic.com.mx;embarques@fleximatic.com.mx"),
				'','','', '','','','','', '');
		}
	}
		odbc_free_result($sql);									 
	}
	else
	{
		//Cambio CHT Backorders
		//$sql=odbc_exec($conn,"SELECT ZVA_NUM,ZVA_OBSCAP,ZVA_USUCAP,ZVA_FYHCAP FROM ZVA010 WHERE ZVA_STATUS=0 AND D_E_L_E_T_=''")or die("Error de pedidos postergados");
		$sql=odbc_exec($conn,"SELECT 
			ZVA_NUM,
			ISNULL(ZZM_ORDSUR,'') ZZM_ORDSUR,
			ZVA_OBSCAP,ZVA_USUCAP,ZVA_FYHCAP 
			FROM ZVA010
			LEFT JOIN (SELECT ZZM_PEDIDO, ZZM_ORDSUR FROM ZZM010 WHERE D_E_L_E_T_ = '') AS ZZM ON ZVA_NUM=ZZM_PEDIDO 
			WHERE ZVA_STATUS=0 AND D_E_L_E_T_='';")or die("Error de pedidos postergados");

		while($datos=odbc_fetch_array($sql))
			echo "<tr>
					<td><a id='cancPP' name='$datos[ZVA_NUM]' title='Cancelar pedido postergado'>$datos[ZVA_NUM]</a></td>
					<td>$datos[ZZM_ORDSUR]</td>
					<td>$datos[ZVA_OBSCAP]</td>
					<td>".substr($datos["ZVA_USUCAP"],0,strpos($datos["ZVA_USUCAP"],"."))."</td>
					<td>$datos[ZVA_FYHCAP]</td>
				 </tr>";
		odbc_free_result($sql);
		odbc_close($conn);
	}
?>
</table><br>
</body>

