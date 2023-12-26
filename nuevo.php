<?PHP
	include("conectabd.php");

	$sql = odbc_exec($conn,"SELECT ZZM_PEDIDO FROM ZZM010 WHERE ZZM_PEDIDO='".$_POST["ped"]."' AND ZZM_FECSUR='' AND D_E_L_E_T_=''")or die("Error al validar el pedido en ZZM010");

	if(odbc_num_rows($sql) == 0){
		odbc_free_result($sql);
		//Actualiza la fecha y hora de impresión del pedido, siempre y cuando no este registrada
		$sql=odbc_exec($conn,"SELECT C5_FYHRIMP, C5_FYHSURT FROM SC5010 WHERE C5_NUM='".$_POST["ped"]."' AND D_E_L_E_T_=''")or die("Error al consultar la hora de impresion del pedido");
		$pedido=odbc_fetch_array($sql);
		odbc_free_result($sql);
		if(trim($pedido['C5_FYHRIMP'])==''){
			$sql_update_SC5 = "UPDATE SC5010 SET C5_FYHRIMP='".date("d/m/y H:i:s")."',C5_IMPRESO='T' WHERE C5_NUM='".$_POST["ped"]."' AND D_E_L_E_T_=''";
			$sql_update_Z77 = "UPDATE Z77010 SET Z77_FYHSUR='".date("d/m/y H:i:s")."', Z77_STATUS='CS' WHERE Z77_ORDSUR='".$_POST["ordsur"]."' AND Z77_STATUS = 'AC' AND D_E_L_E_T_=''";

			if (odbc_exec($conn, $sql_update_SC5)) {
				if (odbc_exec($conn, $sql_update_Z77)) {
					$sql = odbc_exec($conn,"SELECT ISNULL(MAX(R_E_C_N_O_),0)+1 AS maximo FROM ZZM010")or die("Error al obtener el registro de ZZM010");
					$consec = odbc_fetch_array($sql);

					$sql_insert_ZZM = "INSERT INTO ZZM010(ZZM_CODALM,ZZM_FATURA,ZZM_MONTO,ZZM_PEDIDO,ZZM_FECFAC,ZZM_CAJAS,ZZM_FECSUR,ZZM_CODCLI,ZZM_NOMCLI,ZZM_HORA,ZZM_FYHSUR,D_E_L_E_T_,R_E_C_N_O_, ZZM_ORDSUR)VALUES('".$_POST["alm"]."','','0','".$_POST["ped"]."','','0','','','','','".date("d/m/Y H:i:s")."','',".$consec['maximo'].", '".$_POST['ordsur']."';";
					if (odbc_exec($conn, $sql_insert_ZZM)) {
						$res = 'OK';
					}else{
						//Error al insertat ZZM
						$res = "ERROR_ZZM";
					}
				}else{
					$res = "ERROR_Z77";
				}
			}else{
				$res = "ERROR_SC5";
			}
		}else{
			//Se hace en caso de que la C5_FYHRIMP tenga un valor ya que se considera que se realizara un nuevo surtido y en caso de que si tenga un valor se realiza un update y se hace el insert en ZZM
			
			//nueva orden de surtido
			$sql_nueva_ord = "SELECT TOP 1 Z77_ORDSUR FROM Z77010 WHERE SUBSTRING(Z77_ORDSUR, 1, 6) = '".date('Ym')."' ORDER BY Z77_ORDSUR DESC;";
			$result = odbc_exec($conn, $sql_nueva_ord);
			if (odbc_num_rows($result) > 0) {
				if ($row = odbc_fetch_array($result)) {
					$siguiente = explode('-', $row["Z77_ORDSUR"]);
					if (intval($siguiente[1]) >= 9 && intval($siguiente[1]) <= 98) {
						$ordSur = date('Ym')."-000".(intval($siguiente[1])+1);
					}else if (intval($siguiente[1]) >= 99 && intval($siguiente[1]) <= 998){
						$ordSur = date('Ym')."-00".(intval($siguiente[1])+1);
					}else if (intval($siguiente[1]) >= 999 && intval($siguiente[1]) <= 9998){
						$ordSur = date('Ym')."-0".(intval($siguiente[1])+1);
					}else if (intval($siguiente[1]) >= 9999){
						$ordSur = date('Ym')."-".(intval($siguiente[1])+1);
					}else{
						$ordSur = date('Ym')."-0000".($siguiente[1]+1);
					}
				}
			}else{
				$ordSur = date('Ym')."-00001";
			}

			//limite de surtido
			$lim_surtido = $pedido['C5_FYHSURT'];
			//limite de embarque
			$fechasurt = substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5);
			$valfechasur = strtotime($fechasurt);

			if(strlen(trim($pedido['C5_FYHSURT']))==16 || strlen(trim($pedido['C5_FYHSURT']))==17){
				$iniciolab = substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5,3)." 08:00:00";
			}elseif(strlen(trim($pedido['C5_FYHSURT']))==18 || strlen(trim($pedido['C5_FYHSURT']))==19){
				$iniciolab = substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5,5)." 08:00:00";
			}
			$valiniciolab = strtotime($iniciolab);
			$sec1 = $valfechasur - $valiniciolab;
			if($sec1 >= 10800){
				//SI EL TIEMPO DISPONIBLE ES MAYOR A 3 HORAS LO RESTA
				//$valfechasur-=10800;
				$lim_embarque = date("d/m/y H:i:s",($valfechasur-10800));
			}
			else{
				$sec2 = 10800 - $sec1;
				//restar al dia anterior al cierre los minutos restantes
				$finlab = date("m/d/y",strtotime("-1 day",$valfechasur))." 18:00:00";
				$valfinlab = strtotime($finlab);
				$valfinlab -= $sec2;
				$lim_embarque = date("d/m/y H:i:s",$valfinlab);
			}

			$sql_ins_Z77 = "
				INSERT INTO Z77010 (Z77_FILIAL, Z77_ORDSUR, Z77_PEDIDO, Z77_CLIENT, Z77_STATUS, Z77_USRVTA, 
					Z77_FYHVTA, Z77_USRCYC, Z77_FYHCYC, Z77_FYHSUR, Z77_LIMSUR, Z77_FINSUR, Z77_DIREMB, Z77_CODTRS, 
					Z77_FYHEMB, Z77_LIMEMB, Z77_ENVFLE, Z77_ENTEST, Z77_ENTCLI, Z77_FYHAPP, Z77_ENTEMB, D_E_L_E_T_, R_E_C_N_O_) 
				SELECT 
					Z77_FILIAL, '".$ordSur."', Z77_PEDIDO, 
					Z77_CLIENT, 'CS', Z77_USRVTA, 
					Z77_FYHVTA, Z77_USRCYC, Z77_FYHCYC, 
					'".date('d/m/y H:i')."', '".$lim_surtido."', '', 
					'', '', '', 
					'".$lim_embarque."', '', '', 
					'', '', '', '', (SELECT (case when MAX(R_E_C_N_O_) is null then 0 else max(R_E_C_N_O_) END) maxrecno FROM Z77010) + ROW_NUMBER() OVER (ORDER BY Z77_PEDIDO)
				FROM Z77010 
				WHERE D_E_L_E_T_ = '' AND Z77_ORDSUR = '".$_POST["ordsur"]."';";
			
			$sql=odbc_exec($conn,"SELECT ISNULL(MAX(R_E_C_N_O_),0)+1 AS maximo FROM ZZM010")or die("Error al obtener el registro de ZZM010");
			$consec=odbc_fetch_array($sql);

			$sql_insert_ZZM = "INSERT INTO ZZM010(ZZM_CODALM,ZZM_FATURA,ZZM_MONTO,ZZM_PEDIDO,ZZM_FECFAC,ZZM_CAJAS,ZZM_FECSUR,ZZM_CODCLI,ZZM_NOMCLI,ZZM_HORA,ZZM_FYHSUR,D_E_L_E_T_,R_E_C_N_O_, ZZM_ORDSUR)VALUES('".$_POST["alm"]."','','0','".$_POST["ped"]."','','0','','','','','".date("d/m/Y H:i:s")."','',".$consec['maximo'].", '".$ordSur."')";

			if (odbc_exec($conn, $sql_ins_Z77)) {
				if (odbc_exec($conn, $sql_insert_ZZM)) {
					$res = "OK";
				}else{
					$res = "ERROR_ZZM";
				}
			}else {
				$res = "ERROR_Z77";
			}
		}
	}else{
		// el pedido contiene fecha en ZZM_FECSUR por lo tanto se considera un pedido que ya esta en proceso de surtido no puede comenzar nuevamente, es necesario que este campo este vacio 
		$res = 'ERROR_YA_SURTIENDO';
	}
	echo $res;
	odbc_free_result($sql);
	odbc_close($conn);
?>