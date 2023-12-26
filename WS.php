<?php
	include("send_mail_365.php");
	$file = fopen("LOG.txt", "a");
	
	$IMEI=$_POST['CLAVE_CHOFER'];
	//$IMEI="358467066146829";
	$CODIGOFACTURA=$_POST['CF'];
	//$CODIGOFACTURA="00000000000000098625";
	$GUIA=trim($_POST['G']);
	//$GUIA="GDL 76980";
	$VALORGUIA=$_POST['VG'];
	//$VALORGUIA="894.40";
	$OBS=$_POST['OBS'];
	//$OBS="";
	$FECHAENTREGA=$_POST['FE'];
	//$FECHAENTREGA="09/03/16  13:40:58";
	$Enviar = 0;
	$GUIA=strtoupper($GUIA);
	fwrite($file, date('Ymd H:i:s').$IMEI." - ".$CODIGOFACTURA." - ".$GUIA." - ".$VALORGUIA." - ".$OBS." - ".$FECHAENTREGA. PHP_EOL);
	date_default_timezone_set('America/Mexico_City');
	if($IMEI=="358467066146829")
	{
		include("conectabdBCA.php");
		fwrite($file, date('Ymd H:i:s')." conectado a BCA ". PHP_EOL);
	}
	else{
		include("conectabd.php");
		fwrite($file, date('Ymd H:i:s')." conectado a produccion ". PHP_EOL);
	}
    if($CODIGOFACTURA != NULL && $FECHAENTREGA == NULL){
		fwrite($file, date('Ymd H:i:s')." Actualizando datos para factura ".$CODIGOFACTURA." GUIA: ".$GUIA." Valor Guia: ".$VALORGUIA." Fecha Entrega: ".$FECHAENTREGA." OBS: ".$OBS.PHP_EOL);
		odbc_exec($conn, "UPDATE ZZO010 SET ZZO_GUIA='$GUIA',ZZO_VALORG='$VALORGUIA',ZZO_OBSERV='$OBS' WHERE ZZO_FACT='$CODIGOFACTURA'") or die("Problemas eal actualizar los datos de la guia");
		$Enviar = 1;
	}else if($CODIGOFACTURA != NULL && $FECHAENTREGA != NULL){
		if($GUIA == "LOCAL" || $GUIA == "local"){
			$registros=odbc_exec($conn,"SELECT TOP 1 C6_NUM FROM SC6010 WHERE C6_NOTA='$CODIGOFACTURA'") or die("Problemas en el select");
			if($reg=odbc_fetch_array($registros)){
				$Pedido = $reg['C6_NUM'];
				$Pedidos=odbc_exec($conn,"SELECT C6_NOTA FROM SC6010 WHERE C6_NUM='$Pedido' GROUP BY C6_NOTA") or die("Problemas en el select");
				while($reg=odbc_fetch_array($Pedidos)){
					$FACTURA = $reg['C6_NOTA'];
					$FECHADIV = substr($FECHAENTREGA, 0, 10);
					//$FECHADIV = trim($FECHADIV, "/");
					$FECHADIV= substr($FECHADIV,6,4).substr($FECHADIV,3,2).substr($FECHADIV,0,2);
					fwrite($file, date('Ymd H:i:s')." Actualizando datos para factura ".$CODIGOFACTURA." GUIA: ".$GUIA." Valor Guia: ".$VALORGUIA." Fecha Entrega: ".$FECHAENTREGA." OBS: ".$OBS." --- ".$FECHADIV." - ".$FACTURA.PHP_EOL);
					odbc_exec($conn,"UPDATE ZZO010 set ZZO_GUIA='$GUIA', ZZO_VALORG='$VALORGUIA',ZZO_OBSERV='$OBS',ZZO_FECHEN='$FECHAENTREGA', ZZO_FECENT='$FECHADIV',ZZO_CAPTUR='WEB',ZZO_USER='".$IMEI."-WEB' WHERE ZZO_FACT='$FACTURA'") or die("Problemas en el select");
					$sql_p=odbc_exec($conn,"SELECT ZZO_ORDSUR FROM ZZO010 WHERE ZZO_FACT='".$FACTURA."';")or die("Error al obtener el pedido");
					$dPedi=odbc_fetch_array($sql_p);
					odbc_free_result($sql_p);
					$sql_upd_Z77 = "UPDATE Z77010 SET Z77_STATUS = 'EC', Z77_ENVFLE = '".$FECHAENTREGA."', Z77_FYHAPP = '".$FECHADIV."' WHERE Z77_ORDSUR = '".$dPedi['ZZO_ORDSUR']."' AND D_E_L_E_T_ = '';";
					odbc_exec($conn, $sql_upd_Z77);

					//AQUI VA EL CODIGO PARA GENERAR LA FECHA POSIBLE DE ENTREGA
					//SELECT C5_DIREMB FROM SC6010 C6 INNER JOIN SC5010 C5 ON C5_NUM=C6_NUM WHERE C6_NOTA='00000000000000109695' GROUP BY C5_DIREMB
					//OBTIENE EL NUMERO DE FACTURA Y FLETERA
					$sql=odbc_exec($conn,"SELECT ZZO_FACT,ZZO_CODFLE FROM ZZO010 WHERE ZZO_FACT='$FACTURA';") or die("Error al obtener factura");
					$datos=odbc_fetch_array($sql);
					$num_fac=$datos["ZZO_FACT"];
					$cod_fle=$datos["ZZO_CODFLE"];
					odbc_free_result($sql);
					//OBTIENE DIR EMBARQUE
					$sql=odbc_exec($conn,"SELECT C5_NUM,C5_DIREMB FROM SC6010 C6 INNER JOIN SC5010 C5 ON C5_NUM=C6_NUM WHERE C6_NOTA='".$num_fac."' GROUP BY C5_NUM,C5_DIREMB") or die("Error al obtener DIR EMBARQUE");
					$datos=odbc_fetch_array($sql);
					$dir_emb=trim($datos["C5_DIREMB"]);
					$num_ped=$datos["C5_NUM"];
					odbc_free_result($sql);
					//OBTIENE EL CLIENTE
					$sql=odbc_exec($conn,"SELECT F2_CLIENTE FROM SF2010 WHERE F2_DOC='".$num_fac."' AND D_E_L_E_T_='';") or die("Error al obtener DIR EMBARQUE");
					$datos=odbc_fetch_array($sql);
					$cliente_fac=trim($datos["F2_CLIENTE"]);
					odbc_free_result($sql);
					if($dir_emb!="" && $dir_emb!="FISCAL")
					{
						//BUSCAR LA DIR DE ENTREGA PARA OBTENER EL ESTADO
						$sql=odbc_exec($conn,"SELECT ZD1_EDOCVE FROM ZD1010 WHERE ZD1_CLAVE='".$dir_emb."' AND ZD1_CLIENT='".$cliente_fac."' GROUP BY ZD1_EDOCVE;") or die("Error al obtener DIR EMBARQUE");
						$datos=odbc_fetch_array($sql);
						$estado_cli=trim($datos["ZD1_EDOCVE"]);
						odbc_free_result($sql);
					}
					Else
					{
						//obtiene el estado fiscal del cliente
						$sql=odbc_exec($conn,"SELECT A1_EST FROM SA1010 WHERE A1_COD='".$cliente_fac."' AND D_E_L_E_T_='';") or die("Error al obtener DIR EMBARQUE");
						$datos=odbc_fetch_array($sql);
						$estado_cli=trim($datos["A1_EST"]);
						odbc_free_result($sql);
					}
					//obtiene los dias segun el estado y la fletera
					$sql=odbc_exec($conn,"SELECT CASE WHEN MAX(DDF_DIAS) IS NULL THEN 0 ELSE MAX(DDF_DIAS) END DDF_DIAS FROM DDF010 WHERE D_E_L_E_T_='' AND DDF_EDO='".$estado_cli."' AND DDF_COD='".$cod_fle."';");
					//POR EL MOMENTO SOLO FILTRA POR ESTADO
					//$sql=odbc_exec($conn,"SELECT CASE WHEN MAX(DDF_DIAS) IS NULL THEN 0 ELSE MAX(DDF_DIAS) END DDF_DIAS FROM DDF010 WHERE D_E_L_E_T_='' AND DDF_EDO='".$estado_cli."';");
					if($datos=odbc_fetch_array($sql))
					{
						$num_dias=$datos["DDF_DIAS"];
						$num_dias=round($num_dias);
						if($num_dias>0)
						{
							//POR EL MOMENTO SOLO SI ENCUENTRA ESTE PARAMETRO ACTUALIZA LA FECHA
							//sumar dias a hoy para sacar la fecha de posible entrega
							$fpe=strtotime(date("d-M-Y"));
							for ($r=1 ;$r<=$num_dias;$r++)
							{
								$fpe=strtotime ( '+1 day' , $fpe);
								if(date('N',$fpe)==6)
									$fpe=strtotime ( '+2 day' , $fpe);
								elseif(date('N',$fpe)==7)
									$fpe=strtotime ( '+1 day' , $fpe);
							}
							$fpe=date("Ymd", $fpe);
							//$fpe=date("Ymd", strtotime ( '+'.$num_dias.' day' , strtotime(date("d-M-Y"))));
							//ACTUALIZAR LA FECHA EN PROTHEUS
							odbc_exec($conn,"UPDATE SC5010 SET C5_FECESLL='".$fpe."'  WHERE C5_NUM='".$num_ped."' AND D_E_L_E_T_='';") or die("Problemas en el select");
							odbc_exec($conn,"UPDATE Z77010 SET Z77_ENTEST = '".$fpe."' WHERE Z77_ORDSUR = '".$dPedi['ZZO_ORDSUR']."' AND D_E_L_E_T_ = ''");
						}
					}
					else
						$num_dias=0;
					odbc_free_result($sql);
					/*
					//sumar dias a hoy para sacar la fecha de posible entrega
					$fpe=date("Ymd", strtotime ( '+18 day' , strtotime(date("d-M-Y"))));
					//ACTUALIZAR LA FECHA EN PROTHEUS
					odbc_exec($conn,"UPDATE SC5010 SET C5_FECESLL='".$fpe."'  WHERE C5_NUM='".$num_ped."' AND D_E_L_E_T_='';") or die("Problemas en el select");
					*/
				}
			}
		}
		else{
			fwrite($file, date('Ymd H:i:s')." Actualizando datos para factura ".$CODIGOFACTURA." GUIA: ".$GUIA." Valor Guia: ".$VALORGUIA." Fecha Entrega: ".$FECHAENTREGA." OBS: ".$OBS.PHP_EOL);
			odbc_exec($conn,"UPDATE ZZO010 set ZZO_GUIA='$GUIA', ZZO_VALORG='$VALORGUIA',ZZO_OBSERV='$OBS',ZZO_FECHEN='$FECHAENTREGA',ZZO_CAPTUR='WEB',ZZO_USER='".$IMEI."-WEB' WHERE ZZO_FACT='$CODIGOFACTURA'") or die("Problemas en el select");
			$sql_p=odbc_exec($conn,"SELECT ZZO_ORDSUR FROM ZZO010 WHERE ZZO_FACT='".$CODIGOFACTURA."';")or die("Error al obtener el pedido");
			$dPedi=odbc_fetch_array($sql_p);
			odbc_free_result($sql_p);
			$sql_upd_Z77 = "UPDATE Z77010 SET Z77_STATUS = 'EC', Z77_ENVFLE = '".$FECHAENTREGA."', Z77_FYHAPP = '".$FECHAENTREGA."' WHERE Z77_ORDSUR = '".$dPedi['ZZO_ORDSUR']."' AND D_E_L_E_T_ = '';";
			odbc_exec($conn, $sql_upd_Z77);

			$Enviar = 1;
			//AQUI VA EL CODIGO PARA GENERAR LA FECHA POSIBLE DE ENTREGA
			//SELECT C5_DIREMB FROM SC6010 C6 INNER JOIN SC5010 C5 ON C5_NUM=C6_NUM WHERE C6_NOTA='00000000000000109695' GROUP BY C5_DIREMB
			//OBTIENE EL NUMERO DE FACTURA Y FLETERA
			$sql=odbc_exec($conn,"SELECT ZZO_FACT,ZZO_CODFLE FROM ZZO010 WHERE ZZO_FACT='$CODIGOFACTURA';") or die("Error al obtener factura");
			$datos=odbc_fetch_array($sql);
			$num_fac=$datos["ZZO_FACT"];
			$cod_fle=$datos["ZZO_CODFLE"];
			odbc_free_result($sql);
			//OBTIENE DIR EMBARQUE
			$sql=odbc_exec($conn,"SELECT C5_NUM,C5_DIREMB FROM SC6010 C6 INNER JOIN SC5010 C5 ON C5_NUM=C6_NUM WHERE C6_NOTA='".$num_fac."' GROUP BY C5_NUM,C5_DIREMB") or die("Error al obtener DIR EMBARQUE");
			$datos=odbc_fetch_array($sql);
			$dir_emb=trim($datos["C5_DIREMB"]);
			$num_ped=$datos["C5_NUM"];
			odbc_free_result($sql);
			//OBTIENE EL CLIENTE
			$sql=odbc_exec($conn,"SELECT F2_CLIENTE FROM SF2010 WHERE F2_DOC='".$num_fac."' AND D_E_L_E_T_='';") or die("Error al obtener DIR EMBARQUE");
			$datos=odbc_fetch_array($sql);
			$cliente_fac=trim($datos["F2_CLIENTE"]);
			odbc_free_result($sql);
			if($dir_emb!="" && $dir_emb!="FISCAL")
			{
				//BUSCAR LA DIR DE ENTREGA PARA OBTENER EL ESTADO
				$sql=odbc_exec($conn,"SELECT ZD1_EDOCVE FROM ZD1010 WHERE ZD1_CLAVE='".$dir_emb."' AND ZD1_CLIENT='".$cliente_fac."' GROUP BY ZD1_EDOCVE;") or die("Error al obtener DIR EMBARQUE");
				$datos=odbc_fetch_array($sql);
				$estado_cli=trim($datos["ZD1_EDOCVE"]);
				odbc_free_result($sql);
			}
			Else
			{
				//obtiene el estado fiscal del cliente
				$sql=odbc_exec($conn,"SELECT A1_EST FROM SA1010 WHERE A1_COD='".$cliente_fac."' AND D_E_L_E_T_='';") or die("Error al obtener DIR EMBARQUE");
				$datos=odbc_fetch_array($sql);
				$estado_cli=trim($datos["A1_EST"]);
				odbc_free_result($sql);
			}
			//obtiene los dias segun el estado y la fletera
			$sql=odbc_exec($conn,"SELECT CASE WHEN MAX(DDF_DIAS) IS NULL THEN 0 ELSE MAX(DDF_DIAS) END DDF_DIAS FROM DDF010 WHERE D_E_L_E_T_='' AND DDF_COD='".$cod_fle."' AND DDF_EDO='".$estado_cli."'");
			//POR EL MOMENTO SOLO FILTRA POR ESTADO
			//$sql=odbc_exec($conn,"SELECT CASE WHEN MAX(DDF_DIAS) IS NULL THEN 0 ELSE MAX(DDF_DIAS) END DDF_DIAS FROM DDF010 WHERE D_E_L_E_T_='' AND DDF_EDO='".$estado_cli."';");
			if($datos=odbc_fetch_array($sql))
			{
				$num_dias=$datos["DDF_DIAS"];
				$num_dias=round($num_dias);
				if($num_dias>0)
				{
					//POR EL MOMENTO SOLO SI ENCUENTRA ESTE PARAMETRO ACTUALIZA LA FECHA
					//sumar dias a hoy para sacar la fecha de posible entrega
					$fpe=strtotime(date("d-M-Y"));
					for ($r=1 ;$r<=$num_dias;$r++)
					{
						$fpe=strtotime ( '+1 day' , $fpe);
						if(date('N',$fpe)==6)
							$fpe=strtotime ( '+2 day' , $fpe);
						elseif(date('N',$fpe)==7)
							$fpe=strtotime ( '+1 day' , $fpe);
					}
					$fpe=date("Ymd", $fpe);
					//$fpe=date("Ymd", strtotime ( '+'.$num_dias.' day' , strtotime(date("d-M-Y"))));
					//ACTUALIZAR LA FECHA EN PROTHEUS
					odbc_exec($conn,"UPDATE SC5010 SET C5_FECESLL='".$fpe."'  WHERE C5_NUM='".$num_ped."' AND D_E_L_E_T_='';") or die("Problemas en el select");
					odbc_exec($conn,"UPDATE Z77010 SET Z77_ENTEST = '".$fpe."' WHERE Z77_ORDSUR = '".$dPedi['ZZO_ORDSUR']."' AND D_E_L_E_T_ = ''");

				}
			}
			else
				$num_dias=0;
			odbc_free_result($sql);
			/*
			//sumar dias a hoy para sacar la fecha de posible entrega
			$fpe=date("Ymd", strtotime ( '+'.$num_dias.' day' , strtotime(date("d-M-Y"))));
			//ACTUALIZAR LA FECHA EN PROTHEUS
			odbc_exec($conn,"UPDATE SC5010 SET C5_FECESLL='".$fpe."'  WHERE C5_NUM='".$num_ped."' AND D_E_L_E_T_='';") or die("Problemas en el select");
			*/
		}
	}else{
		fwrite($file, date('Ymd H:i:s')." Obteniendo ruta para  ".$IMEI. PHP_EOL);
		$registros=odbc_exec($conn,"SELECT 
									A1_COD,CASE WHEN ZZM_PEDIDO IS NULL THEN '000000' ELSE ZZM_PEDIDO END ZZM_PEDIDO,ZZO_FECHEN,ZZN_NOMBRE,ZZO_FACT,A1_NOME,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,
									CASE WHEN ZZM_CAJAS IS NULL THEN 0 ELSE ZZM_CAJAS END ZZM_CAJAS, CASE WHEN ZZM_COSTAL IS NULL THEN 0 ELSE ZZM_COSTAL END ZZM_COSTAL,
									CASE WHEN ZZM_EXHIB IS NULL THEN 0 ELSE ZZM_EXHIB END ZZM_EXHIB,CASE WHEN ZZM_TINACO IS NULL THEN 0 ELSE ZZM_TINACO END ZZM_TINACO,ZZO_GUIA
								FROM ZZO010 ZZO 
									INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD 
									LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD 
									INNER JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO 
									LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA 
								WHERE 
									ZZN_IMEI='$IMEI' AND (ZZO_FECHEN='' OR LTRIM(RTRIM(ZZO_GUIA))='PENDIENTE') AND ZZO.R_E_C_N_O_ > 26874 
									AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND SA2.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' 
								ORDER BY ZZO_CONSEC") or die("Problemas en el select");
		while($reg=odbc_fetch_array($registros)){
			$conversion = strtoupper($reg['ZZO_GUIA']);
			if(strcasecmp($reg['ZZO_GUIA'], "PENDIENTE"))
				$fletera = "".$conversion."".$reg['A2_NOME']."".$fch;
			else
				$fletera = $reg['A2_NOME']." ".$reg['ZZO_GUIA'];
			if(trim($reg['A1_COD'])=="D00123")
			{
				/*$diremb=odbc_exec($conn,"SELECT 
													A1_COD,ZZM_PEDIDO,ZZO_FECHEN,ZZN_NOMBRE,ZZO_FACT,A1_NOME,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,
													ZZM_CAJAS,ZZM_COSTAL,ZZM_EXHIB,ZZM_TINACO,ZZO_GUIA 
												FROM ZZO010 ZZO 
													INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD 
													LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD 
													INNER JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO 
													LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA 
												WHERE 
													ZZN_IMEI='$IMEI' AND (ZZO_FECHEN='' OR LTRIM(RTRIM(ZZO_GUIA))='PENDIENTE') AND ZZO.R_E_C_N_O_ > 26874 
													AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND SA2.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' 
												ORDER BY ZZO_CONSEC") or die("Problemas en el select");
				while($reg=odbc_fetch_array($registros)){*/
				$REGISTROS = $REGISTROS."".$reg['ZZN_NOMBRE']."/".$reg['A1_NOME']."/".$reg['ZZO_FACT']."/".$fletera."/".$reg['ZZM_CAJAS']."/".$reg['ZZM_COSTAL']."/".$reg['ZZM_EXHIB']."/".$reg['ZZM_TINACO'].";";
			}
			else
				$REGISTROS = $REGISTROS."".$reg['ZZN_NOMBRE']."/".$reg['A1_NOME']."/".$reg['ZZO_FACT']."/".$fletera."/".$reg['ZZM_CAJAS']."/".$reg['ZZM_COSTAL']."/".$reg['ZZM_EXHIB']."/".$reg['ZZM_TINACO'].";";
		    $i++;
		}
		echo $REGISTROS."".$i;
	}
	if($Enviar == 1){
		$Enviar = 0;
		$registros_email=odbc_exec($conn,"SELECT A1_COD,A1_NOME,A1_EMAIL,A3_EMAIL,ZZO_FACT,ZZO_PEDIDO,A2_NOME,ZZO_GUIA,ZZO_FECHEN,ZZM_CAJAS,ZZM_COSTAL,ZZM_EXHIB,ZZM_TINACO FROM ZZO010 INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN ZZM010 ON ZZO_FACT=ZZM_FATURA LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD LEFT JOIN SA3010 SA3 ON A1_VEND=A3_COD WHERE ZZO_FACT='$CODIGOFACTURA' AND SA3.D_E_L_E_T_='' AND SA1.D_E_L_E_T_=''") or die("Problemas en el select");
		if($reg=odbc_fetch_array($registros_email)){
			$num_cliente=trim($reg['A1_COD']);
			$nombre_cliente = $reg['A1_NOME'];
			$numero_pedido = $reg['ZZO_PEDIDO'];
			$nombre_fletera = $reg['A2_NOME'];
			$numero_guia = $reg['ZZO_GUIA'];
			$fecha_entrega = $reg['ZZO_FECHEN'];
			$numero_cajas = $reg['ZZM_CAJAS'];
			$numero_costales = $reg['ZZM_COSTAL'];
			$numero_exhibidores = $reg['ZZM_EXHIB'];
			$numero_tinacos = $reg['ZZM_TINACO'];
			
			$mailtemp=$reg['A1_EMAIL'];
			$email_cliente = explode(";",$mailtemp);
			$email_vendedor = $reg['A3_EMAIL'];
						
			$nombre_dia = date("l");
			$numero_dia = date("d");
			$nombre_mes = date("F");
			$nombre_year = date("Y");
			$hora_actual = date("h:i:s a");
			
			//***** DIAS EN ESPAÑOL ***
			if(strcmp($nombre_dia, "Monday") == 0) $nombre_dia="Lunes";
			else if(strcmp($nombre_dia, "Tuesday") == 0) $nombre_dia="Martes";
			else if(strcmp($nombre_dia, "Wednesday") == 0) $nombre_dia="Miércoles";
			else if(strcmp($nombre_dia, "Thursday") == 0) $nombre_dia="Jueves";
			else if(strcmp($nombre_dia, "Friday") == 0) $nombre_dia="Viernes";
			else if(strcmp($nombre_dia, "Saturday") == 0) $nombre_dia="Sabado";
			else if(strcmp($nombre_dia, "Sunday") == 0) $nombre_dia="Domingo";
			
			//***** MESES EN ESPAÑOL ***
			
			if(strcmp($nombre_mes, "January") == 0) $nombre_mes="Enero";
			else if(strcmp($nombre_mes, "February") == 0) $nombre_mes="Febrero";
			else if(strcmp($nombre_mes, "March") == 0) $nombre_mes="Marzo";
			else if(strcmp($nombre_mes, "April") == 0) $nombre_mes="Abril";
			else if(strcmp($nombre_mes, "May") == 0) $nombre_mes="Mayo";
			else if(strcmp($nombre_mes, "June") == 0) $nombre_mes="Junio";
			else if(strcmp($nombre_mes, "July") == 0) $nombre_mes="Julio";
			else if(strcmp($nombre_mes, "August") == 0) $nombre_mes="Agosto";
			else if(strcmp($nombre_mes, "September") == 0) $nombre_mes="Septiembre";
			else if(strcmp($nombre_mes, "November") == 0) $nombre_mes="Noviembre";
			else if(strcmp($nombre_mes, "December") == 0) $nombre_mes="Diciembre";
			
			$fecha_hora = $nombre_dia.", ".$numero_dia." de ".$nombre_mes." de ".$nombre_year." ".$hora_actual;		
			require_once("class.phpmailer.php");
			require_once("class.smtp.php");
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = false;
			$datoC=odbc_exec($conn,"SELECT * FROM COR010")or die("Error en la configuracion");
			$resC=odbc_fetch_array($datoC);
			odbc_free_result($datoC);


			$mailSubject = '';
			$mailAddress = '';
			$mailCC = '';
			$mailCCO = '';
			$mailAttachment = '';
			$mailImgRoute = array();
			$mailImgAlias = array();
			$mailImgName = array();

			fwrite($file, date('Ymd H:i:s')." Enviando correo real".PHP_EOL);
			if($IMEI=="358467066146829")
			{
				$mailAddress .= "lesparza@fleximatic.com.mx;";
				fwrite($file, date('Ymd H:i:s')." Enviando con la prueba solo a sistemas". PHP_EOL);
			}
			elseif ($num_cliente == "D00123")
			{
				$mailCC = "lsantillan@fleximatic.com.mx";
			}
			else
			{
				for($pos=0;$pos<sizeof($email_cliente);$pos++)
				{
					$mailAddress .= $email_cliente[$pos].';';
				}
				$mailCC .= $email_vendedor.';';

				$mailCCO = "embarques@fleximatic.com.mx";
			}
			if ($num_cliente!="D00123")
			{
				$mailSubject = "Fleximatic: Informe de entrega del pedido ".$numero_pedido;

				$mailImgRoute[] = 'images/logo.png';
				$mailImgAlias[] = 'logo';
				$mailImgName[] = 'logo.png';

				$mailImgRoute[] = 'images/cintillo.png';
				$mailImgAlias[] = 'cintillo';
				$mailImgName[] = 'cintillo.png';
			}
			else
			{$mailSubject = "Informe de entrega del pedido ".$numero_pedido;}
			
			
			$nombre_cliente = utf8_decode($nombre_cliente);
			$nombre_fletera = utf8_decode($nombre_fletera);
			$body="";
			$body = "<head>
						<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/> 
						<style> 
							#formato1{
								font-family: Calibri;
								font-size: 8pt;
								color: #FFFFFF;
								align: 200px;
								font-weight: bold;	
							}	
							#formato2{
								font-family: Calibri;
								font-size: 8pt;
								color: #000000;
								font-weight: bold;	
							}
							#fuenteTexto1{
								font-family: Calibri;
								font-size: 10pt;
								color: #424242;
								width: 48%;
							}
							#fuenteTexto2{
								font-family: Calibri;
								font-size: 8pt;
								color: #424242;
								
							}
							#AvisoC{
							color: #1C1C1C;
							}
							.alinearTexto{
								margin-left:30%;
								text-align : justify;
							}
							#alinearTabla{
								margin-left:12%;
							}
							#alinearCelda1{
								color:black;
								width: 30.5%;
							}
							#alinearCelda2{
								width: 30.5%;
							}
							.salidatable, .salidatable td, .salidatable th { font-family:Calibri; font-size:3pt; color:#424242; }
						</style>
					</head>
					<body>
							<div id='fuenteTexto1' class='alinearTexto'>";
								if ($num_cliente!="D00123")
								{
									$body=$body. "<b> DE: </b> FLEXIMATIC SA DE CV [info@fleximatic.com.mx]";
								}
								$body=$body. "<br>
								<b> ENVIADO: </b> $fecha_hora<br>
								<b> PARA: </b> $nombre_cliente <br>
								<b> ASUNTO: </b> Notificación de Pedido <br><br>";
								if ($num_cliente!="D00123")
								{
									$body=$body. "<IMG SRC='cid:logo' WIDTH=158 HEIGHT=60 BORDER=0 ALT='FLEXIMATIC' />";
								}
								$body=$body. " <br> <br>
								<div> <b> Estimado Cliente: </b> <br><br>
											Por medio de la presente le notificamos que su pedido con No. <b> $numero_pedido </b> y No. Factura <b> $CODIGOFACTURA </b> del cliente  <b> $nombre_cliente </b> 
											ha sido enviado por paquetería: <b> $nombre_fletera </b> con No. Guía: <b> $numero_guia </b> el día: <b> $fecha_entrega </b>.
											Detallamos para usted el contenido en empaques de su pedido, le solicitamos que si al recibirlo encuentra un faltante
											de empaque lo notifique por favor inmediatamente a Fleximatic SA de CV. <br><br>
								</div>
								
								<div id='alinearTabla'> 
									<table border='0' cellpadding='0' cellspacing='0'>
									<tr bgcolor='#CDD5DD' id='formato1' HEIGHT=20 VALIGN='MIDDLE' ALIGN='CENTER'>
									<td id='alinearCelda1'>Cajas</td><td id='alinearCelda1'>Costales</td><td id='alinearCelda1'>Exhibidores</td><td id='alinearCelda1'>Tinacos</td>
									</tr>
									<tr id='formato2' HEIGHT=20 VALIGN='MIDDLE' ALIGN='CENTER'>
									<td id='alinearCelda2'>$numero_cajas</td><td id='alinearCelda2'>$numero_costales</td><td id='alinearCelda2'>$numero_exhibidores</td><td id='alinearCelda2'>$numero_tinacos</td>
									</tr>
									</table>		
								</div>
								<div> ";
								//---------------------------------------------------------------------------------------------------------------------------
								$bandera = 0;
								//Reviza si ya esta capturada la salida del pedido, si es asi Imprime la salida correspondiente
								$sql=odbc_exec($conn,"SELECT ZZS_SALIDA,ZZS_CODALM,ZZS_FYHSAL FROM ZZS010 WHERE ZZS_PEDIDO='$numero_pedido' AND D_E_L_E_T_='' ORDER BY ZZS_SALIDA ASC")
								or die("Error al obtener los datos de la salida");
								while($datos1=odbc_fetch_array($sql))
								{ 
									$bandera = 1;
									//$datos=odbc_fetch_array($sql);
									//odbc_free_result($sql);
									$alm=$datos1['ZZS_CODALM']; 
									$sal=$datos1['ZZS_SALIDA'];
									$fechaSal=$datos1['ZZS_FYHSAL'];
									//Totales Exhibidor, tindec, costales, cajas
									$impreso=0;
									if($impreso < 1)
									{
										$lineas=0;
										$exhi=0;
										$tinaco=0;
										$costal=0;
										$caja=0;
										$codIn="";
										$cantidad=0;
										$multiBox=0;
										$partida=0;
										$sql1=odbc_exec($conn,"SELECT C5_NUM,C5_EMISSAO,A1_COD,A1_NOME FROM SC5010 SC5 INNER JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE C5_NUM='$numero_pedido' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_=''")
										or die("Error al validar el pedido de Impresión en SC5");
										if(odbc_num_rows($sql1)>0)
										{
											$datos=odbc_fetch_array($sql1);
											$body=$body. "<table border='1' class='salidatable'>
												<tr>
													<td rowspan='2' align='center'>";
													if ($num_cliente!="D00123")
													{
														$body=$body. "<IMG SRC='cid:logo' BORDER=0 ALT='FLEXIMATIC' height='40' width='80'>";
													}													
													$body=$body. "
													</td>
													<td align='center' colspan='2' style='font-family:Calibri;font-size:8pt;'>";
													if ($num_cliente!="D00123")
													{
														$body=$body. "<strong>Fleximatic S.A. de C.V.</strong>"; 
													}													
													$body=$body. "
													</td>
													<td align='center' style='font-family:Calibri;font-size:8pt;'><strong>Salida: ".str_replace(".0","",$sal)."</strong></td>
												</tr>
												<tr>
													<td align='center' colspan='2'  style='font-family:Calibri;font-size:8pt;'><strong>SALIDA DE MERCANC&Iacute;A PEDIDO DEL ALMACEN DE PRODUCTO TERMINADO</strong></td>
													<td align='center'  style='font-family:Calibri;font-size:8pt;'><input type='text' name='txtStatus' value='";
													if($impreso==0)
														$body=$body. "Cliente";
													else
														$body=$body. "Cliente";
													$body=$body. "' disabled style='border:none' size='4'></td>
												</tr>
												<tr>
												  <td  style='font-family:Calibri;font-size:8pt;'><strong>CLIENTE:</strong></td>
												  <td  style='font-family:Calibri;font-size:8pt;'>".trim($datos["A1_NOME"])."</td>
												  <td  style='font-family:Calibri;font-size:8pt;'><strong>FECHA PEDIDO:</strong></td>
												  <td  style='font-family:Calibri;font-size:8pt;'>".substr($datos["C5_EMISSAO"],6)."/".substr($datos["C5_EMISSAO"],4,2)."/".substr($datos["C5_EMISSAO"],0,4)."</td>
												</tr>
												<tr>
												  <td  style='font-family:Calibri;font-size:8pt;'><strong>PEDIDO:</strong></td>
												  <td  style='font-family:Calibri;font-size:8pt;'>".$datos["C5_NUM"]."</td>
												  <td  style='font-family:Calibri;font-size:8pt;'><strong>FECHA SALIDA:</strong></td>
												  <td  style='font-family:Calibri;font-size:8pt;'>".$fechaSal."</td>
												</tr>";
												odbc_free_result($sql1);
												$sql1=odbc_exec($conn,"SELECT ZDS_QTENT,B1_COD,B1_DESC,ZDS_PEDIM,B1_TIPO,B1_CLASE,B1_BOLSA,ZDS_COSTAL,ZDS_QE,ZDS_CAJA,ZDS_PARTID FROM ZDS010 ZDS INNER JOIN SB1010 SB1 ON ZDS_PRODUC=B1_COD WHERE ZDS_SALIDA=".$sal." AND SB1.D_E_L_E_T_='' AND ZDS.D_E_L_E_T_='' ORDER BY ZDS_CAJA,ZDS_PARTID,ZDS.R_E_C_N_O_")
												or die("Error al obtener las partidas de la salida ZDS");
												if(odbc_num_rows($sql1)>0)
												{
													$body=$body. "<tr>
															<td colspan='4'>
															 <table width='100%' border='1' cellspacing='0' class='salidatable'>
															  <tr borderColor='#FFF'>
																<th  style='font-family:Calibri;font-size:8pt;'>CANT.</th>
																<th  style='font-family:Calibri;font-size:8pt;'>[PRODUCTO] DESCRIPCI&Oacute;N</th>
																<th  style='font-family:Calibri;font-size:8pt;'>PEDIMENTO</th>
																<th  style='font-family:Calibri;font-size:8pt;'>TINDEC</th>
																<th  style='font-family:Calibri;font-size:8pt;'>EXHIBIDORES</th>
																<th  style='font-family:Calibri;font-size:8pt;'>COSTALES</th>
																<th  style='font-family:Calibri;font-size:8pt;'>CAJAS</th>
															  </tr>";
													  $agrupaBox="";
													  while($datos=odbc_fetch_array($sql1))
													  {
														if($datos["ZDS_CAJA"]>0)
														{
															$cantidad=0;
															if($multiBox==0)
															{
																$body=$body. "<tr borderColor='#FFF'  style='font-family:Calibri;font-size:10pt;'><td colspan='7' align='center'>CAJAS MULTIPLES</td></tr>";
																$lineas+=1;
																$multiBox=1;
															}
														}
														else
														{
															$cantidad=intval($datos["ZDS_QTENT"]/$datos["ZDS_QE"]);
														}		
														if($cantidad==0 && $agrupaBox<>$datos["ZDS_CAJA"])
														{
															$body=$body. "<tr borderColor='#FFF'  style='font-family:Calibri;font-size:8pt;'><td colspan='7'><hr></td></tr>";
															$lineas+=1;
														}
														$body=$body. "<tr bgColor='#FFF' borderColor='#FFF'>
																<td align='right' style='border-bottom-color: #CCC;  font-family:Calibri;font-size:8pt;'; bgcolor='white'>".intval($datos["ZDS_QTENT"])."</td>
																<td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;'; bgcolor='white'>[".trim($datos["B1_COD"])."] ".trim($datos["B1_DESC"])."</td>
																<td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'>".trim($datos["ZDS_PEDIM"])."&nbsp;</td>";
														if($partida<>$datos["ZDS_PARTID"] || $partida==0)
														{
															if(trim($datos["B1_CLASE"])=="11")
															{
																$tinaco+=$cantidad;
															}
															elseif(trim($datos["B1_CLASE"])=="22" && $datos["ZDS_QE"]>0)
															{
																$exhi+=$cantidad;
																$body=$body. "<td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;'; bgcolor='white'>&nbsp;</td>";
															}
															elseif(trim($datos["B1_BOLSA"])=="T" || $datos["ZDS_COSTAL"]=="T")
															{
																$costal+=$cantidad;
																$body=$body. "<td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;'; bgcolor='white'>&nbsp;</td><td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'>&nbsp;</td>";
															}
															else
															{
																$caja+=$cantidad;
																$body=$body. "<td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;'; bgcolor='white'>&nbsp;</td><td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'>&nbsp;</td><td style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'>&nbsp;</td>";
															}
															if($cantidad==0 && $agrupaBox<>$datos["ZDS_CAJA"])
															{
																$agrupaBox=$datos["ZDS_CAJA"];
																$body=$body. "<td align='right' style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;'; bgcolor='white'>1</td></tr>";
																if(trim($datos["B1_CLASE"])=="11")
																	$tinaco+=1;
																elseif(trim($datos["B1_CLASE"])=="22" && $datos['ZDS_QE']>0)
																	$exhi+=1;
																elseif(trim($datos["B1_BOLSA"])=="T" || $datos["ZDS_COSTAL"]=="T")
																	$costal+=1;
																else
																	$caja+=1;
															}
															elseif($cantidad<>0)
																$body=$body. "<td align='right' style='border-bottom-color:#CCC; font-family:Calibri;font-size:8pt;'; bgcolor='white'>".$cantidad."</td></tr>";
															else
															{
																if(trim($datos["B1_BOLSA"])=="T")
																	$body=$body. "<td bgcolor='white' style='font-family:Calibri;font-size:8pt;'></td>";
																$body=$body. "<td bgcolor='white' style='font-family:Calibri;font-size:8pt;'></td></tr>";
															}
														}
														else
															$body=$body. "</tr>";
														$lineas+=1;
														$partida=$datos["ZDS_PARTID"];
													  }
													  if($lineas<18) //23 //25
													  {
														  while($lineas<18)
														  {
															  $body=$body. "<tr borderColor='#FFF' bgcolor='white'><td style='border:none; font-family:Calibri;font-size:8pt;'>&nbsp;</td></tr>";
															$lineas+=1;  
														  }
														  $body=$body. "<tr borderColor='#FFF' bgcolor='white'><td style='border:none; font-family:Calibri;font-size:8pt;'>&nbsp;</td></tr>";
													  }
												}
												odbc_free_result($sql1);
												$body=$body. "<tr borderColor='#FFF'>
														<strong>
														   <td colspan='2' align='right' style='font-family:Calibri;font-size:8pt;'>TOTAL: </td><td></td>
														   <td align='right' style='border-top-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'><input type='hidden' name='tinaco' value='".$tinaco."'>".$tinaco."</td>
														   <td align='right' style='border-top-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'><input type='hidden' name='exhi' value='".$exhi."'>".$exhi."</td>
														   <td align='right' style='border-top-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'><input type='hidden' name='costal' value='".$costal."'>".$costal."</td>
														   <td align='right' style='border-top-color:#CCC; font-family:Calibri;font-size:8pt;' bgcolor='white'><input type='hidden' name='caja' value='".$caja."'>".$caja."</td>
														 </strong>
													  </tr>";
													  //Nombre del Almacenista que surte el pedido cuando no ha sido facturado
													  $sql1=odbc_exec($conn,"SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 ZZN INNER JOIN ZZM010 ZZM ON ZZN_CODIGO=ZZM_CODALM WHERE ZZM_PEDIDO='$numero_pedido' AND ZZM.D_E_L_E_T_=''") or die("Error al consultar el nombre del almacenista");
													  $datos=odbc_fetch_array($sql1);
													  $body=$body.  "<tr bgcolor='#CCCCCC'>
															   <td colspan='2'>
																<table class='salidatable'>
																	<tr>
																		<th colspan='2' style='font-family:Calibri;font-size:8pt;'>ELABOR&Oacute;</th>
																	</tr>
																	<tr>
																		<td style='font-family:Calibri;font-size:8pt;'><input type='hidden' name='txtCodAlm' value='".$datos["ZZN_CODIGO"]."'><strong>Nombre:&nbsp;&nbsp;".trim($datos["ZZN_NOMBRE"])."</strong></td>
																	</tr>
																	<tr>
																		<td style='font-family:Calibri;font-size:8pt;'>ALMACENISTA DE PRODUCTO TERMINADO</td>
																	</tr>
																</table>
															   </td>
															   <td colspan='5' align='center'>
																  <table bgcolor='#CCCCCC' class='salidatable'>
																	<tr>
																	  <th colspan='2' style='font-family:Calibri;font-size:8pt;'>REVIS&Oacute;</th>
																	</tr>
																	<tr>
																	  <td style='font-family:Calibri;font-size:8pt;'><strong>Nombre:</strong></td>
																	</tr>
																	<tr>
																	  <td style='font-family:Calibri;font-size:8pt;'>AUDITOR DE PEDIDO</td>
																	</tr>
																  </table>
															   </td>
															 </tr>
														</table>          
													  </td>
													</tr>
											</table><br><br>";
										}
										if($impreso==0)
										{
											if($lineas>24)
											{
												while(($lineas%54)<>0)
												{
													$body=$body. "<br>";
													$lineas+=1;
												}
											}
										}
										$impreso+=1;
									}  
								}
								if($bandera == 0)
								{
									odbc_free_result($sql);		  
									odbc_close($conn);
									echo "No Hay datos de salida para el pedido ".$numero_pedido;
								}
								//---------------------------------------------------------------------------------------------------------------------------
								$body=$body."
								</div>
								<br>";
								if ($num_cliente!="D00123")
								{
									$body=$body. "
									<div id='fuenteTexto2'>
										<b style='color: #585858'>AVISO DE CONFIDENCIALIDAD:</b> Este mail es automático por lo que le solicitamos no responder al mismo. Para cualquier duda y/o aclaración
											de su pedido favor de comunicarse al Tel. (33) 35 40 10 50 Ext. 259 o al correo embarques@fleximatic.com.mx <br><br>
									</div>
									<IMG SRC='cid:cintillo' BORDER=0 ALT='FLEXIMATIC'> <br> <br>
									<b>AVISO DE PRIVACIDAD</b><br><br>

									FLEXIMATIC SA DE CV.,  con domicilio en Camino Real de Colima No.901-14, en Tlajomulco de Zúñiga Jalisco y C.P. <br>
									45645, Teléfono (33) 3540 1050, Fax (33) 3540 1075,es responsable de recabar sus datos personales, del uso que se le dé<br> 
									a los mismos y de su protección. Sus datos personales, incluso los  patrimoniales o financieros, que en su caso fueran <br>
									recabados, que se recaben o generados con motivo de la relación jurídica que tengamos celebrada, o que en su caso, se <br>
									celebre, se tratarán para todos los fines vinculados con dicha relación, tales como: Facturación, identificación, operación, <br>
									administración, análisis, ofrecimiento y promoción de bienes, productos y servicios, elaborar estudios y programas que son <br>
									necesarios para determinar hábitos de consumo; realizar evaluaciones periódicas de nuestros productos y servicios a efecto <br>
									de mejorar la calidad de los mismos; evaluar la calidad del servicio que brindamos, así como para cumplir las obligaciones <br>
									derivadas de tal relación y otros fines compatibles o análogos. Si requiere mayor información acerca del tratamiento y de los <br>
									derechos que puede hacer valer, usted puede acceder a nuestro aviso de privacidad completo a través de nuestro sitio web <br>
									www.fleximatic.com.mx   y/o a través de comunicados colocados en nuestras oficinas y sucursales.";
								}
						$body=$body. "
					</div>
				</body>";
			$body = utf8_decode($body);
			$body = eregi_replace("[\]",'',$body);

			if(!$enviar_correo(
				trim($resC['COR_USER']),
				trim($resC['COR_USER']),
				$body,
				$mailSubject,
				explode(';', $mailAddress),
				explode(';', $mailCC),
				explode(';', $mailCCO),
				explode(';', $mailAttachment),
				'', '', '', '',
				$mailImgRoute, $mailImgAlias, $mailImgName)
			){
				$nombre_cliente = utf8_decode($nombre_cliente);
				$nombre_fletera = utf8_decode($nombre_fletera);
				$body = "Error al enviar notificacion de pedido a ";
				for($pos=0;$pos<sizeof($email_cliente);$pos++)
				{
					$body.=$email_cliente[$pos]."; ";
				}
				$body.=$email_vendedor." Pedido: ".$numero_pedido." Factura: ".$CODIGOFACTURA." Cliente: ".$nombre_cliente." Fletera: ".$nombre_fletera;
				$body = utf8_decode($body);
				$body = eregi_replace("[\]",'',$body);

				$mailImgRoute = array();
				$mailImgAlias = array();
				$mailImgName = array();

				$mailImgRoute[] = 'images/logo.png';
				$mailImgAlias[] = 'logo';
				$mailImgName[] = 'logo.png';

				$mailImgRoute[] = 'images/cintillo.png';
				$mailImgAlias[] = 'cintillo';
				$mailImgName[] = 'cintillo.png';


				$enviar_correo(
				trim($resC['COR_USER']),
				trim($resC['COR_USER']),
				$body,
				"Fleximatic: ERROR en Informe de entrega del pedido ".$numero_pedido,
				explode(';', "lesparza@fleximatic.com.mx;creditoycobranza@fleximatic.com.mx"),
				explode(';', ''),
				explode(';', ''),
				explode(';', ''),
				'', '', '', '',
				$mailImgRoute, $mailImgAlias, $mailImgName);
			}
		}	
	}
	odbc_close($conn);
	fclose($file);
?>