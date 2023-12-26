<?php

	include("conectabd.php");
	//para GDL-NET
/* 	require_once("class.phpmailer.php"); 
	require_once("class.smtp.php"); 
*/
	
	include("send_mail_365.php");

    $fletePedido="";
    $fleteNueva="";
	$result ="";
	
	$cvincul =str_replace("!","'", $_POST['pedidosVin']); 

    $rstPedid = odbc_exec($conn,"select A2_NOME from sa2010 where d_e_l_e_t_='' and a2_cod='".$_POST['flePedi']."'; ") or die("Error en consulta de provedores");
	if($pedi=odbc_fetch_array($rstPedid)){
        $fletePedido=trim($pedi['A2_NOME']);
    }

    $rstNueva = odbc_exec($conn,"select A2_NOME from sa2010 where d_e_l_e_t_='' and a2_cod='".$_POST['fletera']."'; ") or die("Error en consulta de provedores");
	if($nuev=odbc_fetch_array($rstNueva)){
        $fleteNueva=trim($nuev['A2_NOME']);
    }


	$rst_actualizaZ72 = "INSERT INTO Z72010 (Z72_PEDIDO,Z72_VALPED,Z72_CP,Z72_CODFLE,Z72_COSTFL,Z72_PORFLE,Z72_BULTOS,Z72_PESO,Z72_MCUBIC,Z72_ORIGEN,Z72_FYH,Z72_USER,R_E_C_N_O_,Z72_OBS)
	SELECT SC5.C5_NUM,VALPED,CP,CODFLE,COSTFL,PORFLE,BULTOS,PESO,MCUBIC,ORIGEN,FYH,USER_, RECNO + ROW_NUMBER() OVER(ORDER BY SC5.C5_NUM) AS RECNO_,OBS FROM
	(SELECT '' AS 'FILIAL',".$_POST['valPed']." AS 'VALPED','".$_POST['cp']."' AS 'CP','".$_POST['fletera']."' AS 'CODFLE',".$_POST['costo']." AS 'COSTFL',".$_POST['porcentaje']." AS 'PORFLE',".$_POST['totBult']." AS 'BULTOS',".$_POST['totPeso']." AS 'PESO',".$_POST['mcubic']." AS 'MCUBIC','EMBARQUES' AS 'ORIGEN',CONVERT(varchar,GETDATE(),20) AS 'FYH','".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' AS 'USER_', ISNULL(MAX(R_E_C_N_O_),0) AS 'RECNO', '".$_POST['msj']."' AS 'OBS'  FROM Z72010) Z72
	LEFT JOIN 
	(SELECT '' AS 'FILIAL', C5_NUM FROM SC5010 WHERE D_E_L_E_T_='' AND C5_NUM IN (".$cvincul.") ) SC5 ON Z72.FILIAL=SC5.FILIAL";

	if (odbc_exec($conn, $rst_actualizaZ72)) {

		$rst_actualiza ="update sc5010 set c5_transp='".$_POST['fletera']."', c5_costfl='".$_POST['costo']."', c5_porfle='".$_POST['porcentaje']."' where d_e_l_e_t_='' and c5_num in (".$cvincul.") ";
	
		if (odbc_exec($conn, $rst_actualiza)) {
	
			$complemento="
			Embarques cambio la fletera recomendada por sistema del pedido: <strong>".$_POST['pedido']."</strong>, el cliente: <strong>".$_POST['cliente']."</strong> tenia la fletera: <strong>".$fletePedido."</strong> y se cambio por <strong>".$fleteNueva."</strong>.<br><br>
	
			Tenga presente que esta cuenta no es monitoreada, así que no responda a este correo.";
	
			  
			if (enviar_correo(
				trim('noreply@fleximatic.com.mx'),
				trim('Flexinet'),
				$complemento,
				"Cambio de fletera en el pedido: ".$_POST['pedido'],
				explode(';', 'gerentecomercial@fleximatic.com.mx'),
				'', '', '', '', '', '', '')	) {
	
				$res = "CORRECTO";
				
			}else{
	
				$res = "ERRORCORREO";
				
			} 
	 
		}else{
			$res = "ERROR";
		}
	}else{
        $res = "ERROR";
    }


    $res=$res;


    echo $res;

?>