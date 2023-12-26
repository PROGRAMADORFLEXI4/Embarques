<?php
	include("conectabd.php");
	
	$sql=odbc_exec($conn,"SELECT ZPP_ADMIN FROM ZPP010 WHERE ZPP_NOMPC='".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' AND ZPP_PAGINA='SAMP' AND D_E_L_E_T_=''")
	or die("Error al validar el equipo");
	if(odbc_num_rows($sql)>0){
		echo "<section>
				<table class='tSAL' direction='rtl'>
				<caption>MUESTRAS PENDIENTES POR SURTIR.</caption>
		    	<thead class='fixedHeader' direction='rtl'><tr><th class='thChec'></th><th class='thCod'>N&uacute;m. SA</th><th>Solicitante</th><th>Aprobaci&oacute;n</th><th>Vencimiento</th></tr></thead><tbody class='scrollContent'>";
/*        $sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT CP_NUM,CP_SOLICIT,CP_FYHAP,CP_FYHLIM,DATEDIFF(hh,CP_FYHAP,CP_FYHLIM) AS 'tt',DATEDIFF(hh,CP_FYHLIM,GETDATE()) AS 'horas',CP_SALIDA FROM SCP010 WHERE CP_TIPO=0 AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_='' GROUP BY CP_NUM,CP_SOLICIT,CP_FYHAP,CP_FYHLIM,CP_SALIDA ORDER BY CONVERT(DATETIME,CP_FYHAP)")or die("ErrorSA");*/
		  $sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT CP_NUM,CP_SOLICIT,CP_FYHAP,CP_FYHLIM,DATEDIFF(hh,CP_FYHAP,CP_FYHLIM) AS 'tt',DATEDIFF(hh,CP_FYHLIM,GETDATE()) AS 'horas',CP_SALIDA FROM SCP010 WHERE CP_STATUS='' AND CP_QUANT-CP_QUJE>0 AND D_E_L_E_T_='' AND CP_STATSA<>'B' AND CP_PREREQU='' AND CP_OK='' AND (CP_TIPO=0 OR CP_TIPO=2) AND (CP_SALIDA='0' or CP_SALIDA='' or CP_SALIDA='-1') GROUP BY CP_NUM,CP_SOLICIT,CP_FYHAP,CP_FYHLIM,CP_SALIDA ORDER BY CONVERT(DATETIME,CP_FYHAP)") or die("ErrorSA");
        while($datos=odbc_fetch_array($sql)){
        	echo "<tr class='";
			if(trim($datos['CP_SALIDA'])<>"0")
				echo "trImpr";
			else
				echo "trPart";
			echo "' id='tr$datos[CP_NUM]'><td class='tdChec'></td><td class='tdPart' id='td$datos[CP_NUM]'><img src='images/";
	        if($datos["horas"]>=0)
            	$img="late";
            elseif(($datos["horas"]/2)>=($datos["tt"]*-1))
            	$img="medium";
            else
            	$img="good";
            echo $img.".png'>$datos[CP_NUM]&nbsp;</td><td class='mSol' id='td$datos[CP_NUM]'>$datos[CP_SOLICIT]</td><td class='mSol' id='td$datos[CP_NUM]'>".substr($datos['CP_FYHAP'],0,14)."</td><td class='mSol' id='td$datos[CP_NUM]'>".substr($datos['CP_FYHLIM'],0,14)."</td></tr>";
        }
        odbc_free_result($sql);
        odbc_close($conn);		
		echo "</tbody></table>
			</section>
				<article id='detM'>
				</article>";
	}
?>