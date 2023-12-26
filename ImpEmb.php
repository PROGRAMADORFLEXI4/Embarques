<?php
	include("conectabd.php");
	$cancelar="";

	if (isset($_GET['opc']))
	{
		$cancelar=$_GET['opc'];
	}
	//Si opc=e Cancela y envia a Facturas por embarcar
	if($cancelar=="e"){
		odbc_exec($conn,"UPDATE ZZO010 SET ZZO_CODFLE='',ZZO_OBSERV='',ZZO_FPAGO='',ZZO_DEST='' WHERE D_E_L_E_T_='' AND R_E_C_N_O_=".$_GET['fac2'])or die("ERRORZZO");
		$sql_s=odbc_exec($conn,"SELECT ZZO_ORDSUR FROM ZZO010 WHERE R_E_C_N_O_= $_GET[fac2]")or die("ERRORZZO");
		$dPedido=odbc_fetch_array($sql_s);
		odbc_free_result($sql_s);
		$sql_upd_Z77 = "UPDATE Z77010 SET Z77_CODTRS = '', Z77_STATUS = 'F' WHERE Z77_ORDSUR = '".$dPedido['ZZO_ORDSUR']."' AND D_E_L_E_T_ = '';";
		if(odbc_exec($conn, $sql_upd_Z77)){
			echo "OK";
		}else{
			echo "ERRORZ77";
		}
	}
	//Reviza si se va a imprimir
	elseif(trim(isset($_GET['fac2']))<>"")
	{
		$fEnt="";
		if(isset($_GET['client'])<>"")
		{
			if(trim($_GET['client'])=="V00018")
				$fEnt=",ZZO_FECENT='".date("ymd")."',ZZO_FEMBAR='".date("d/m/y H:i:s")."' ";
		}
		//cambios por CHT para reactivacion de backorder 26/08/2021
		$sql=odbc_exec($conn,"SELECT ZZO_PEDIDO, ZZO_CLTE, ZZO_FACT, ZZO_ORDSUR FROM ZZO010 WHERE R_E_C_N_O_= $_GET[fac2]")or die("Error al obtener el pedido");
		$dPed=odbc_fetch_array($sql);
		odbc_free_result($sql);

		if(trim($_GET['suc'])=="FISCAL"){
			$sql=odbc_exec($conn,"SELECT A1_MUN AS 'destino' FROM SA1010 WHERE A1_COD='$dPed[ZZO_CLTE]' AND D_E_L_E_T_=''");
			$sql_Z77_emba = "UPDATE Z77010 SET Z77_STATUS = 'E', Z77_DIREMB = '".$_GET['suc']."', Z77_CODTRS = '".$_GET['flet']."', Z77_FYHEMB = '".date("d/m/y H:i:s")."',  Z77_ENTEMB = '".date("d/m/y H:i:s")."' WHERE Z77_ORDSUR = '".trim($dPed['ZZO_ORDSUR'])."' AND D_E_L_E_T_ = '';";
		}else{
			$sql=odbc_exec($conn,"SELECT ZD1_POBLAC AS 'destino' FROM ZD1010 WHERE ZD1_CLIENT='$dPed[ZZO_CLTE]' AND ZD1_CLAVE='$_GET[suc]' AND D_E_L_E_T_=''");
			$sql_Z77_emba = "UPDATE Z77010 SET Z77_STATUS = 'E', Z77_DIREMB = '".$_GET['suc']."', Z77_CODTRS = '".$_GET['flet']."' WHERE Z77_ORDSUR = '".trim($dPed['ZZO_ORDSUR'])."' AND D_E_L_E_T_ = '';";
		}
		odbc_exec($conn, $sql_Z77_emba) or die('ERROR '.$sql_Z77_emba);
		$dPed=odbc_fetch_array($sql);
		odbc_free_result($sql);

		odbc_exec($conn,"UPDATE ZZO010 SET ZZO_CODFLE='".$_GET['flet']."',ZZO_FLETOR='".$_GET['fletor']."',ZZO_OBSERV='".trim($_GET['obs'])."',ZZO_FPAGO='".trim($_GET['pag'])."'".$fEnt.",ZZO_DEST='$dPed[destino]' WHERE D_E_L_E_T_='' AND R_E_C_N_O_=".$_GET['fac2'])or die("Error al actualizar los datos de embarque");

		if($_GET['flet']=="EXPORT" || $_GET['flet']=="ACLARA")
			exit;
		else
		{
			//$sql=odbc_exec($conn, "SELECT C5_FYHSURT,ZZO_CODFLE,ZZO_FECFAC,ZZO_FECFAC,ZZO_FACT,ZZO_PEDIDO,A1_COD,A2_NOME,A1_NOME,A1_END,A1_BAIRRO,A1_CEP,A1_MUN+', '+A1_EST AS 'poblacion','('+A1_DDD+')'+A1_TEL AS 'telefono',A1_CGC,SUM(ZZM_CAJAS) AS 'cajas',SUM(ZZM_COSTAL) AS 'costales',SUM(ZZM_EXHIB) AS 'exhibidores',SUM(
			$sql=odbc_exec($conn, "SELECT C5_FYHSURT,ZZO_CODFLE,ZZO_FECFAC,ZZO_FECFAC,ZZO_FACT,ZZO_PEDIDO,A1_COD,A2_NOME,A1_NOME,A1_END,A1_BAIRRO,A1_CEP,ltrim(rtrim(A1_MUN))+', '+LTRIM(RTRIM(X5_DESCSPA)) AS 'poblacion','('+A1_DDD+')'+A1_TEL AS 'telefono',A1_CGC,SUM(ZZM_CAJAS) AS 'cajas',SUM(ZZM_COSTAL) AS 'costales',SUM(ZZM_EXHIB) AS 'exhibidores',SUM(ZZM_TINACO) AS 'tinacos',C5_RECCLIE FROM ZZO010 ZZO LEFT JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD 
				LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA 
				LEFT JOIN SC5010 C5  ON ZZO_PEDIDO=C5_NUM and C5.D_E_L_E_T_ = ''
				INNER JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD INNER JOIN SX5010 SX5 ON X5_TABELA='12' AND X5_CHAVE=A1_EST AND SX5.D_E_L_E_T_='' WHERE ZZO.R_E_C_N_O_=".$_GET['fac2']." AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' AND SA2.D_E_L_E_T_='' GROUP BY ZZO_CODFLE,ZZO_FECFAC,A1_NOME,A1_END,A1_BAIRRO,A1_CEP,A1_MUN,X5_DESCSPA,A1_DDD,A1_TEL,A1_CGC,A2_NOME,ZZO_PEDIDO,A1_COD,C5_FYHSURT,ZZO_FACT,C5_RECCLIE")
			or die("Error al obtener los datos de la factura del cliente");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);	
			if(trim($datos['A1_COD'])=="D00123")
			{
				$separa=explode(",",$_GET['suc']);
				$cod_cliente=$separa[0];
				$sql=odbc_exec($conn, "SELECT A1_NOME FROM SA1010 WHERE A1_COD='".$cod_cliente."' AND D_E_L_E_T_='';")
				or die("Error al obtener Nombre del cliente");
				$nombre=odbc_fetch_array($sql);
				odbc_free_result($sql);	
				$nombre_cliente=$nombre["A1_NOME"];
			}
			else
			{
				$cod_cliente=$datos['A1_COD'];
				$nombre_cliente=$datos['A1_NOME'];
			}
			echo "
			<!DOCTYPE html><html>
				<head>
					<link href='css/styles.css' rel='stylesheet' type='text/css'>
				</head>
				<body class='body'>
					<div class='page1'>
						<div class='sobre'>";
							if($_GET['pag']=="Al Regreso")
							echo "
							<div id='ojo'>OJO<br>Favor de anexar a su gu&iacute;a original, la copia de la factura de Fleximatic con firma de recibido del cliente.<br>Gracias.</div>
							<center class='lth'> Fleximatic S.A. de C.V.&nbsp;&nbsp;&nbsp;&nbsp;Camino Real de Colima #901-14&nbsp;&nbsp;&nbsp;&nbsp;Tlajomulco de Zuñiga, Jal. Mex.<br>   
								 	RFC: FLE980113E95&nbsp;&nbsp;&nbsp;&nbsp;CP: 45645&nbsp;&nbsp;&nbsp;&nbsp;Tel:(33)3540-1050</center>

							</div>";
							echo "
							
						</div>
						
						<div class='sCpo'>
						
						
							<label class='lblTit'>Nombre:</label>
							<label class='lblTit'>Dirección:</label>
							<label class='lblTit'>Colonia:</label>
							<label class='lblTit'>C.P.</label>
							<label class='lblTit'>Población:</label>
							<label class='lblTit'>Teléfono:</label>
							<label class='lblTit'>RFC:</label>
							<label class='lblInfo'>$nombre_cliente</label>";
							/*echo "
							<label class='lblInfo'>$datos[A1_NOME]</label>";*/
							if(trim($_GET['suc'])=="FISCAL")
								echo "
							    
								<label class='lblInfo'>$datos[A1_END]</label>
								<label class='lblInfo'>$datos[A1_BAIRRO]</label>
								<label class='lblInfo'>$datos[A1_CEP]</label>
								<label class='lblInfo'>$datos[poblacion]</label>
								<label class='lblInfo'>$datos[telefono]</label>";
							else
							{
								$sql=odbc_exec($conn,"SELECT * FROM ZD1010 WHERE ZD1_CLAVE='$_GET[suc]' AND ZD1_CLIENT='$datos[A1_COD]' AND D_E_L_E_T_=''") or die("Error en la direccion de Embarque");
								$info=odbc_fetch_array($sql);
								odbc_free_result($sql);
								echo "
								<label class='lblInfo'>$info[ZD1_DIRECC]</label>
								<label class='lblInfo'>$info[ZD1_COLON]</label>
								<label class='lblInfo'>".substr($info['ZD1_CP'],0,5)."</label>
								<label class='lblInfo'>$info[ZD1_POBLAC]</label>
								<label class='lblInfo'>$info[ZD1_TEL]</label>";
							}
							//CHUBB DE MEXICO SA DE CV. NO. POLIZA : TR 43202153-2
							//AXA Seguros S.A. de C.V. NO. POLIZA : CNA298680000
							//AXA Seguros S.A. de C.V. NO. POLIZA : CNA298680200
							echo "
							<label class='lblInfo'>$datos[A1_CGC]</label>
							
						</div>";
						//if($datos['C5_RECCLIE']=="SI"){
							echo "
								<div>
									
								</div>
							";
						//}
						echo "
						<div class='pieP'>
							AXA Seguros S.A. de C.V. NO. POLIZA : CNA298680401
						   <label class='lblTit'>Cajas:</label>
						   <label class='lblTit'>Costales:</label>
						   <label class='lblTit'>Exhibidores:</label>
						   <label class='lblTit'>Tindec:</label>
						   <br>
						   <label class='lblTit'>Transporte:</label>
						   <label class='lblTit'>Zona:</label>
						   <label class='lblTit'>Cobro:</label>
						  
						   
						   <label class='lblInfo'>".number_format($datos['cajas'],0)."</label>
						   <label class='lblInfo'>".number_format($datos['costales'],0)."</label>
						   <label class='lblInfo'>".number_format($datos['exhibidores'],0)."</label>
						   <label class='lblInfo'>".number_format($datos['tinacos'],0)."</label>

						   <br>
						   ";
						   $flet=odbc_exec($conn,"SELECT A2_NREDUZ,Z14_NREDUZ FROM Z15010 Z15 INNER JOIN Z14010 Z14 ON Z14.D_E_L_E_T_='' AND Z14_COD=Z15_CODZON INNER JOIN SA2010 A2 ON A2.D_E_L_E_T_='' AND A2_COD=Z15_CODFLE WHERE Z15_CODFLE='".$datos['ZZO_CODFLE']."' AND Z15.D_E_L_E_T_='';") or die("Error al ejecutar la zona");
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
						   echo "
						   	<label class='lblInfo'>".trim($datos['A2_NOME'])."</label>
						   	<label class='lblInfo'>".$zonaflet."</label>
						   	<label class='lblInfo'>".$_GET['pag']."</label>
						   	<br>
						   	<label class='lblInfo' style='margin-left: -87px'>No Factura:".$datos['ZZO_FACT']."</label>
						   	<label class='lblInfo' style='margin-left: -87px'>No pedido:#".substr($datos['ZZO_PEDIDO'],4,2)."</label>
						    <label class='lblInfo' style='margin-left: -87px'>Fecha y hora de Factura:".$datos['ZZO_FECFAC']."</label>
						   	<label class='lblInfo'  style='margin-left: -87px'>Fecha limite de surtido:".$datos['C5_FYHSURT']."</label>
							<br>
						   	<label class='lblInfo' style='margin-left: -87px'><h2>".($datos["C5_RECCLIE"]=="SI"?"RECOGE CLIENTE":"")."</h2></label>
						</div>
						<div class='pP'>";
						   if(trim($_GET['obs'])<>'')
							echo "Observaciones: &nbsp;".trim(substr($_GET['obs'],0,25))."<br>";
						   echo "<br>PARA ACLARACIONES O COMENTARIOS CON RELACI&Oacute;N A ESTE ENVI&Oacute;, FAVOR DE REPORTARNOS CUALQUIER ANOMAL&Iacute;A DETECTADA ANTES DE 24 HORAS<br>DE RECIBIDA LA MERCANC&Iacute;A, DE LO CONTRARIO NO NOS HACEMOS RESPONSABLES.
					   </div>
					   <div class='pP'>
						   <img src='images/banner2.png' height='65' width='1000'>
					   </div>
					</div>
				</body>
			</html>";
		}
	}
	elseif(isset($_GET["Act"])<>"")
	{
		/* $registros=$_GET["Act"];
		while($registros<>"")
		{
			$chofer=substr($registros,0,strpos($registros,","));
			$registros=substr($registros,strpos($registros,",")+1);
			$auxchofer=substr($registros,0,strpos($registros,","));
			$registros=substr($registros,strpos($registros,",")+1); */
			/*$reg=substr($registros,0,strpos($registros,"|"));
			$registros=substr($registros,strpos($registros,"|")+1);*/
			/* $reg=substr($registros,0,strpos($registros,","));
			$registros=substr($registros,strpos($registros,",")+1);
			$nomcho=substr($registros,0,strpos($registros,"|"));
			$registros=substr($registros,strpos($registros,"|")+1);
			$sql=odbc_exec($conn,"SELECT ISNULL(MAX(ZZO_CONSEC),0)+1 AS 'consec' FROM ZZO010 WHERE ZZO_CHOFER='$chofer' AND ZZO_FECHEN=''") or die("Error al obtener el consecutivo");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			//SE AGREGO FEMBAR PARA QUE GUARDE LA FECHA AL MOMENTO DE HACER LA RUTA
			odbc_exec($conn,"UPDATE ZZO010 SET ZZO_CHOFER='$chofer',ZZO_AUXCHO='$auxchofer',ZZO_NOMCHO='$nomcho',ZZO_CONSEC=".$datos['consec'].",ZZO_FEMBAR='".date("d/m/y H:i:s")."' WHERE R_E_C_N_O_=$reg")or die("Error al actualizar los registros de embarque");

			$sql_s=odbc_exec($conn,"SELECT ZZO_ORDSUR FROM ZZO010 WHERE R_E_C_N_O_=$reg")or die("Error al obtener el pedido");
			$dPedido=odbc_fetch_array($sql_s);
			odbc_free_result($sql_s);
			$sql_Z77_emba = "UPDATE Z77010 SET Z77_STATUS = 'EV', Z77_FYHEMB = '".date("d/m/y H:i:s")."' WHERE Z77_ORDSUR = '".$dPedido['ZZO_ORDSUR']."' AND D_E_L_E_T_ = '';";
			odbc_exec($conn, $sql_Z77_emba) or die('ERROR '. $sql_Z77_emba);
		} */
	}
	else
	{
		odbc_close($conn);
		include("conectabd.php");
		//Cancela factura de ruta
		$rst_fcp = odbc_exec($conn, "SELECT * FROM
			(SELECT R_E_C_N_O_,ZZO_FACT FROM ZZO010 WHERE D_E_L_E_T_='') ZZO
			INNER JOIN
				(SELECT Z82_FACT FROM Z82010 WHERE D_E_L_E_T_='') Z82 ON ZZO_FACT=Z82_FACT
			WHERE
				ZZO.R_E_C_N_O_=" . $_GET['reg'] . "	;") or die("Error al consultar factura CP");

		if ($data = odbc_fetch_array($rst_fcp)) {
			echo "EXIST_CP";
		} else {
			//Restablece el registro
			odbc_exec($conn, "UPDATE ZZO010 SET ZZO_AUXCHO='',ZZO_CHOFER='',ZZO_NOMCHO='',ZZO_CONSEC=0 WHERE R_E_C_N_O_=" . $_GET['reg'] . ";") or die("Error al actualizar los registros de embarque");
			$sql_s = odbc_exec($conn, "SELECT ZZO_ORDSUR FROM ZZO010 WHERE R_E_C_N_O_=" . $_GET['reg'] . ";") or die("Error al obtener el pedido");
			$dPedido = odbc_fetch_array($sql_s);
			odbc_free_result($sql_s);
			$sql_Z77_emba = "UPDATE Z77010 SET Z77_STATUS = 'E', Z77_FYHEMB = '' WHERE Z77_ORDSUR = '" . $dPedido['ZZO_ORDSUR'] . "' AND D_E_L_E_T_ = '';";
			odbc_exec($conn, $sql_Z77_emba) or die('ERROR ' . $sql_Z77_emba);
			echo "Factura elimiada de ruta";
		}
		odbc_free_result($rst_fcp);
	}
	odbc_close($conn);

?>