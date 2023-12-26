<?php
	try{
		include("conectabd.php");
		
		$sql=odbc_exec($conn,"SELECT CP_FYHAP,CP_FYHLIM,CP_SOLICIT,CP_CTEEMB,CP_DIREMB FROM SCP010 WHERE CP_NUM='".trim(substr($_POST['np'],2))."' AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_=''  GROUP BY CP_FYHAP,CP_FYHLIM,CP_SOLICIT,CP_CTEEMB,CP_DIREMB")or die("Error SA");
		$datos=odbc_fetch_array($sql);
		odbc_free_result($sql);
		
		/*if(trim($datos['CP_CTEEMB'])=="")
		{
			echo "<Script>window.close();</Script>";
			exit;
		}*/
		
		$sql=odbc_exec($conn,"SELECT A1_NOME FROM SA1010 WHERE D_E_L_E_T_='' AND A1_COD='".trim($datos['CP_CTEEMB'])."'")or die("Error SA");
		$DtosCte=odbc_fetch_array($sql);
		odbc_free_result($sql);
		
		/*$sql=odbc_exec($conn,"SELECT * FROM ZD1010 WHERE D_E_L_E_T_='' AND ZD1_CLAVE='".trim($datos['CP_DIREMB'])."' AND ZD1_CLIENT='".trim($datos['CP_CTEEMB'])."'")or die("Error SA");
		$DtosDir=odbc_fetch_array($sql);
		odbc_free_result($sql);*/
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
			odbc_free_result($sql);
		}
		
	}catch(Exception $ex){
		echo "Error".$ex->getMessage();
	}
	
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="images/icono.ico"/>
 	<link rel="stylesheet" href="css/styles.css"/>
</head>
<div style='position:relative;width:100%;'>
<!--<img src="images/cancel.png" id="clsSM" title="Cerrar ventana" onClick="window.close();"/> &nbsp;&nbsp; <img src="images/printer.png" id="prntSA" title="Imprimir solicitud de muestra <?php echo substr($_POST['np'],2); ?>" onClick="window.open('sm.php?actM=<?php echo substr($_POST['np'],2); ?>','_self'); window.print(); window.close();"/>-->
<img src="images/cancel.png" id="clsSM" title="Cerrar ventana" onClick="window.close();"/> &nbsp;&nbsp; <img src="images/printer.png" id="prntSA" title="Imprimir solicitud de muestra <?php echo substr($_POST['np'],2); ?>" onClick="window.print(); window.close();"/>
</div>
<div style='position:relative;height:550px;width:1000px;font-size:40px;'>
		<font style='font-weight:700;'>
			REMITENTE:<br>
			FLEXIMATIC, SA DE CV<br>
			CAMINO REAL DE COLIMA #901-14 <br>
			COL. SANTA ANITA <br>
			C.P. 45645<br>
			TLAJOMULCO DE ZUÑIGA, JAL.<br>
			A´TN  <?php echo $datos['CP_SOLICIT'];?>
		</font>
</div>
<div style='position:relative;height:550px;width:1500px;font-size:40px;text-align:left;'>
	<?php 
			echo "
			<font style='font-weight:700;'>
			DESTINATARIO:<br>
			".$DtosCte['A1_NOME']."<br>
			".$DtosDir['ZD1_DIRECC']."<br>
			".$DtosDir['ZD1_COLON']."<br>
			CP ".variant_fix($DtosDir['ZD1_CP'])."<br>
			".$DtosDir['ZD1_POBLAC'].", ".$DtosDir['ZD1_EDO'].", ".$DtosDir['ZD1_PAIS']."<br>
			TEL.- ".$DtosDir['ZD1_TEL']."</font>";
		?>
</div>
</html>