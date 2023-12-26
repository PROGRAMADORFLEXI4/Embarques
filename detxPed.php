<script>
	$("#btnCerrar").on("click", function(e){
		$("#fondoObsc").hide();
		$("#detP").text("");
		$("#detP").slideToggle();
			e.defaultprevent();
	});
</script>
<?php
	include("conectabd.php");

	function diaLab($dia,$link){
		$valor=0;
		if(date("N",$dia)==6 || date("N",$dia)==7){
			$sqlD=odbc_exec($link, "SELECT ZDF_LABORA FROM ZDF010 WHERE ZDF_DIA='".date("Ymd",$dia)."' AND D_E_L_E_T_=''")or die("Error en Dias 6,7");
			$dLab=odbc_fetch_array($sqlD);
			if($dLab["ZDF_LABORA"]=="T")
				$valor+=1;
			odbc_free_result($sqlD);
		}else{
			$sqlD=odbc_exec($link, "SELECT ZDF_LABORA FROM ZDF010 WHERE ZDF_DIA='".date("Ymd", $dia)."' AND ZDF_LABORA='F' AND D_E_L_E_T_=''")or die("Error DF");
			if(odbc_num_rows($sqlD)>0)
				$valor=0;
			else
				$valor=1;
			odbc_free_result($sqlD);
		}
		return $valor;
	}

	if(isset($_POST['num'])){
		if($_POST['num']=="fab" || $_POST['num']=="com" || $_POST['num']=="imp"){
			echo "<div id='hDet'>BackOrder: ";
			if($_POST['num']=="fab"){
				$opc="B1_TIPO BETWEEN '01' AND '02' ";
				echo "Fabricado ";
			}elseif($_POST['num']=="com"){
				$opc="B1_TIPO='04' ";
				echo "Comercializado ";
			}else{
				$opc="B1_TIPO='03'";
				echo "Importado ";
			}
			echo "</div><button id='btnCerrar'>X</button><br>";
			$sql=odbc_exec($conn, "SET LANGUAGE 'español'; SELECT C6_PRODUTO,B1_DESC,SUM(C6_QTDVEN) AS C6_QTDVEN,SUM(C6_QTDENT) AS C6_QTDENT,SUM(C6_QTDVEN-C6_QTDENT) AS pzasF,C6_PROGALM FROM SC6010 SC6 LEFT JOIN SB1010 SB1 ON C6_PRODUTO=B1_COD WHERE $opc AND C6_TES BETWEEN '501' AND '502' AND (C6_QTDVEN-C6_QTDENT)>0 AND SC6.D_E_L_E_T_='' AND SB1.D_E_L_E_T_='' AND C6_NUM IN(SELECT C6_NUM FROM SC5010 SC5 INNER JOIN SC6010 SC6 ON C5_NUM=C6_NUM WHERE CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($_POST['fec'],0,6)."01' AND '$_POST[fec]' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND C6_TES BETWEEN '501' AND '502' AND SC5.D_E_L_E_T_='' AND SC6.D_E_L_E_T_='' GROUP BY C6_NUM HAVING SUM(C6_QTDVEN-C6_QTDENT)>0 AND SUM(C6_QTDENT)>0) GROUP BY C6_PRODUTO,B1_DESC,C6_PROGALM ORDER BY C6_PRODUTO")or die("Error en las partidas xLinea");
			echo "<table class='tDatos'>
			<thead class='fixedHeader'>
				<tr><th>C&oacute;digo</th><th class='thDesc'>Descripci&oacute;n</th><th>Cant Solicitada</th><th>Cant Entegada</th><th>Pzas Faltantes</th><th>Prog x Almacen</th></tr>
			</thead>
			<tbody class='scrollContent'>";
			while($datos=odbc_fetch_array($sql))
				echo "<tr><td>".($datos['C6_QTDVEN']-$datos['C6_QTDENT']>0?"<img src='images/cancel.png'/> ":"")."$datos[C6_PRODUTO]</td><td class='tdDesc'>$datos[B1_DESC]</td><td class='tdD'>".number_format($datos['C6_QTDVEN'],0)."</td><td class='tdD'>".number_format($datos['C6_QTDENT'],0)."</td><td class='tdD'>".number_format($datos["pzasF"],0)."</td><td class='tdD'>".($datos['C6_PROGALM']=="T"?"SI":"NO")."</td></tr>";
			echo "</tbody></table>";
		}elseif($_POST['num']=="Pron"){
			echo "<div id='hDet'>Exceso de pronostico de ventas</div><button id='btnCerrar'>X</button><br>";
			$sql=odbc_exec($conn, "SET LANGUAGE 'español'; SELECT C6_PRODUTO,B1_DESC,SUM(ven) AS 'ven',SUM(ent) AS 'ent',SUM(prevV) AS 'prevV' FROM SC5010 INNER JOIN(SELECT C6_NUM,C6_PRODUTO,B1_DESC,SUM(C6_QTDVEN) AS 'ven',SUM(C6_QTDENT) AS 'ent',SUM(C4_QUANT) AS 'prevV' FROM SC6010 SC6 INNER JOIN SC4010 SC4 ON C6_PRODUTO=C4_PRODUTO INNER JOIN SB1010 SB1 ON C6_PRODUTO=B1_COD WHERE C4_DATA BETWEEN '".substr($_POST['fec'],0,6)."01' AND '$_POST[fec]' AND C6_TES BETWEEN '501' AND '502' AND SC6.D_E_L_E_T_='' AND SC4.D_E_L_E_T_='' AND SB1.D_E_L_E_T_='' GROUP BY C6_NUM,C6_PRODUTO,B1_DESC HAVING SUM(C6_QTDVEN)>SUM(C4_QUANT)) AS SC6 ON C5_NUM=C6_NUM WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($_POST['fec'],0,6)."01' AND '$_POST[fec]' AND D_E_L_E_T_='' GROUP BY C6_PRODUTO,B1_DESC")or die("Err pronV");
			echo "<table class='tDatos'>
			<thead class='fixedHeader'>
				<tr><th>C&oacute;digo</th><th class='thDesc'>Descripci&oacute;n</th><th>Cant Solicitada</th><th>Cant Entegada</th><th>Pzas Faltantes</th><th>Pronostico de Ventas</th></tr>
			</thead>
			<tbody class='scrollContent'>";
			while($datos=odbc_fetch_array($sql))
				echo "<tr><td>$datos[C6_PRODUTO]</td><td class='tdDesc'>$datos[B1_DESC]</td><td class='tdD'>".number_format($datos['ven'],0)."</td><td class='tdD'>".number_format($datos['ent'],0)."</td><td class='tdD'>".number_format($datos['ven']-$datos['ent'],0)."</td><td class='tdD'>".number_format($datos['prevV'],0)."</td></tr>";
			echo "</tbody></table>";				
		}elseif($_POST['num']=="noProg" || $_POST['num']=="programado"){
			echo "<div id='hDet'>";
			if($_POST['num']=="noProg")
				echo "Productos no programados x Almac&eacute;n";
			else
				echo "Productos programados x Almac&eacute;n";
			echo "</div><button id='btnCerrar'>X</button><br>";
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C6_PRODUTO,B1_DESC,ISNULL(SUM(ven),0) AS 'Solicitado',ISNULL(SUM(Ent),0) AS 'Ent',ISNULL(SUM(SC6.imp),0) AS 'Importe' FROM SC5010 INNER JOIN(SELECT C6_NUM,C6_PRODUTO,B1_DESC,SUM((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) AS 'imp',SUM(C6_QTDVEN) AS 'Ven',SUM(C6_QTDENT) AS 'Ent' FROM SC6010 SC6 LEFT JOIN SB1010 SB1 ON C6_PRODUTO=B1_COD WHERE C6_TES BETWEEN '501' AND '502' AND (C6_QTDVEN-C6_QTDENT)>0 AND C6_PROGALM='".($_POST['num']=="programado"?"T":"F")."' AND SC6.D_E_L_E_T_='' AND SB1.D_E_L_E_T_='' GROUP BY C6_NUM,C6_PRODUTO,B1_DESC) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE C5_NOTA<>'' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($_POST['fec'],0,6)."01' AND '$_POST[fec]' AND D_E_L_E_T_='' GROUP BY C6_PRODUTO,B1_DESC")or die("Err prodPnP");
			echo "<table class='tDatos'>
			<thead class='fixedHeader'>
				<tr><th>C&oacute;digo</th><th class='thDesc'>Descripci&oacute;n</th><th>Cant Solicitada</th><th>Cant Entegada</th><th>Pzas Faltantes</th><th>Imp no vendido</th></tr>
			</thead>
			<tbody class='scrollContent'>";
			while($datos=odbc_fetch_array($sql))
				echo "<tr><td>$datos[C6_PRODUTO]</td><td class='tdDesc'>$datos[B1_DESC]</td><td class='tdD'>".number_format($datos['Solicitado'],0)."</td><td class='tdD'>".number_format($datos['Ent'],0)."</td><td class='tdD'>".number_format($datos['Solicitado']-$datos['Ent'],0)."</td><td class='tdD'>$".number_format($datos['Importe'],2)."</td></tr>";
			echo "</tbody></table>";
			
		}else{
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT TOP 1 C5_FYHRCYC,C5_FYHSURT,CONVERT(DATETIME,C5_FYHRCYC) AS cyc,CONVERT(DATETIME,ZZM_FYHSUR) AS surt,CONVERT(DATETIME,ZZM_FECSUR) AS aud,CONVERT(DATETIME,ZZO_FECFAC) AS fac2,CONVERT(DATETIME,ZZO_FEMBAR) AS emb,CASE WHEN ZZO_FECHEN NOT LIKE '%--%' AND ZZO_FECHEN NOT LIKE 'CANCEL%' THEN CONVERT(DATETIME,ZZO_FECHEN) ELSE 0 END AS entF,ISNULL(ZVA_NUM,0) AS 'postergado' FROM SC5010 SC5 LEFT JOIN ZZM010 ZZM ON C5_NUM=ZZM_PEDIDO LEFT JOIN ZZO010 ZZO ON C5_NUM=ZZO_PEDIDO AND ZZM_FATURA=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 WHERE C5_NUM='$_POST[num]' AND SC5.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' ORDER BY CONVERT(DATETIME,ZZO_FEMBAR) DESC") or die("error al obtener los tiempos");
			echo "<div id='hDet'>Detalle del Pedido: $_POST[num]</div><button id='btnCerrar'>X</button><br>
			<table class='tDatos'><tr><th>CyC - Surtido Alm</th><th>Surtido Alm - Auditado</th><th>Auditado - Facturado</th><th>Facturado - Embarcado</th><th>Embarcado - Ent Fletera</th><th>Tiempo total</th></tr>";
			$fyhCyc="";
			$fyhLim="";
			$fyhSurt="";
			$postergado="";
			while($datos=odbc_fetch_array($sql)){
				if($datos["postergado"]<>0)
					$postergado=1;
				if($fyhCyc==""){
					$fyhCyc=$datos["cyc"];
					$fyhLim=$datos["C5_FYHSURT"];
			}
			$fyhSurt=$datos["emb"];
			echo "<tr><td id='tdDS' title='".date("d/m/Y H:i",strtotime($datos["cyc"]))." A ".date("d/m/Y H:i",strtotime($datos["surt"]))."'>";
			$tIni=strtotime($datos["cyc"]);
			$tTotal=0; $tiempo=0;
			if(substr($datos["cyc"],0,10)==substr($datos["surt"],0,10)){
				$tFin=strtotime($datos["surt"]);
				$tTotal=($tFin-$tIni);
				$tiempo=$tTotal;
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}else{
				$tAux=strtotime(substr($datos["cyc"],0,11)."18:00:00.000");
				$tTotal=$tAux-$tIni;
				$tAux+=50400;
				while($tAux<strtotime(substr($datos["surt"],0,11)."08:00:00.00")){
					if(diaLab($tAux,$conn)>0)
						$tTotal+=36000; //86400;
					$tAux+=86400;
				}
				$tTotal+=(strtotime($datos["surt"])-$tAux);
				$tiempo=$tTotal;
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}
			//Surtido a Auditado
			echo "</td><td title='".date("d/m/Y H:i",strtotime($datos["surt"]))." A ".date("d/m/Y H:i",strtotime($datos["aud"]))."' id='tdDS'>";
			$tIni=strtotime($datos["surt"]);
			$tTotal=0;
			if(substr($datos["surt"],0,10)==substr($datos["aud"],0,10)){
				$tFin=strtotime($datos["aud"]);
				$tTotal=($tFin-$tIni);
				$tiempo+=$tTotal;
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}else{
				$tAux=strtotime(substr($datos["surt"],0,11)."18:00:00.000");
				$tTotal=$tAux-$tIni;
				$tAux+=50400;
				while($tAux<strtotime(substr($datos["aud"],0,11)."08:00:00.00")){
					if(diaLab($tAux,$conn)>0)
						$tTotal+=36000;
					$tAux+=86400;
				}
				$tTotal+=(strtotime($datos["aud"])-$tAux);
				$tiempo+=$tTotal;
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}
	
			//Auditado a Facturado
			echo "</td><td title='".date("d/m/Y H:i",strtotime($datos["aud"]))." A ".date("d/m/Y H:i",strtotime($datos["fac2"]))."' id='tdDS'>";
			$tIni=strtotime($datos["aud"]);
			$tTotal=0;
			if(substr($datos["aud"],0,10)==substr($datos["fac2"],0,10)){
				$tFin=strtotime($datos["fac2"]);
				$tTotal=($tFin-$tIni);
				$tiempo+=$tTotal;
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}else{
				$tAux=strtotime(substr($datos["aud"],0,11)."18:00:00.000");
				$tTotal=$tAux-$tIni;
				$tAux+=50400;
				while($tAux<strtotime(substr($datos["fac2"],0,11)."08:00:00.00")){
					if(diaLab($tAux,$conn)>0)
						$tTotal+=36000;
					$tAux+=86400;
				}
				$tTotal+=(strtotime($datos["fac2"])-$tAux);
				$tiempo+=$tTotal;
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}
				
			//Facturado a Embarcado
			echo "</td><td title='".date("d/m/Y H:i",strtotime($datos["fac2"]))." A ".date("d/m/Y H:i",strtotime($datos["emb"]))."' id='tdDS'>";
			$tIni=strtotime($datos["fac2"]);
			$tTotal=0;
			if(substr($datos["fac2"],0,10)==substr($datos["emb"],0,10)){
				$tFin=strtotime($datos["emb"]);
				$tTotal=($tFin-$tIni);
				$tiempo+=$tTotal;
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}else{
				if($datos["emb"]=="1900-01-01 00:00:00.000")
					echo "-";
				else{
					$tAux=strtotime(substr($datos["fac2"],0,11)."18:00:00.000");
					$tTotal=$tAux-$tIni;
					$tAux+=50400;
					while($tAux<strtotime(substr($datos["emb"],0,11)."08:00:00.00")){
						if(diaLab($tAux,$conn)>0)
							$tTotal+=36000;
						$tAux+=86400;
					}
					$tTotal+=(strtotime($datos["emb"])-$tAux);
					$tiempo+=$tTotal;
					printf("%1$02d:",intval($tTotal/3600));
					$tTotal=intval($tTotal%3600);
					printf("%1$02d",intval($tTotal/60));
				}
			}
				
			//Embarcado a Entregado Fletera
			echo "</td><td title='".date("d/m/Y H:i",strtotime($datos["emb"]))." A ".date("d/m/Y H:i",strtotime($datos["entF"]))."' id='tdDS'>";
			$tIni=strtotime($datos["emb"]);
			$tTotal=0;
			if($datos["emb"]=="1900-01-01 00:00:00.000" || ($datos["entF"]=="1900-01-01 00:00:00.000"))
				echo "-";
			elseif(substr($datos["emb"],0,10)==substr($datos["entF"],0,10)){
				$tFin=strtotime($datos["entF"]);
				$tTotal=($tFin-$tIni);
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}else{
				$tAux=strtotime(substr($datos["emb"],0,11)."18:00:00.000");
				$tTotal=$tAux-$tIni;
				$tAux+=50400;
				while($tAux<strtotime(substr($datos["entF"],0,11)."08:00:00.00")){
					if(diaLab($tAux,$conn)>0)
						$tTotal+=36000;
					$tAux+=86400;
				}
				$tTotal+=(strtotime($datos["entF"])-$tAux);
				printf("%1$02d:",intval($tTotal/3600));
				$tTotal=intval($tTotal%3600);
				printf("%1$02d",intval($tTotal/60));
			}
			echo "</td><td id='tdDS'>";
			printf("%1$02d:",intval($tiempo/3600));
			$tiempo=intval($tiempo%3600);
			printf("%1$02d",intval($tiempo/60));
			echo "</td>";
		}
		echo "</table><center>";
		if($postergado<>"")
			echo "<u>PEDIDO POSTERGADO</u><br>";
		echo "Fecha aprobaci&oacute;n CyC: ".substr($fyhCyc,8,2)."/".substr($fyhCyc,5,2)."/".substr($fyhCyc,2,4).substr($fyhCyc,10,9)." | Fecha Embarque Interno: ".$fyhLim." | Fecha real de surtido: ".substr($fyhSurt,8,2)."/".substr($fyhSurt,5,2)."/".substr($fyhSurt,2,4).substr($fyhSurt,10,9)."</center>";
		odbc_free_result($sql);
		$noSurtido=0;		
		$sql=odbc_exec($conn,"SELECT C6_PRODUTO,C6_DESCRI,CASE WHEN b1_tipo BETWEEN '01' AND '02' THEN 'FABRIC' ELSE CASE WHEN B1_TIPO='03' THEN 'COMER' ELSE 'IMPORT' END END AS 'tipo',C6_QTDVEN,C6_QTDENT,(C6_QTDVEN-C6_QTDENT) AS pzasF,C6_PROGALM FROM SC6010 SC6 LEFT JOIN SB1010 SB1 ON C6_PRODUTO=B1_COD WHERE C6_NUM='$_POST[num]' AND SC6.D_E_L_E_T_='' AND SB1.D_E_L_E_T_=''")or die("Error en las partidas");
		echo "<table class='tDatos'>
			<thead class='fixedHeader'>
				<tr><th>C&oacute;digo</th><th class='thDesc'>Descripci&oacute;n</th><th>Tipo</th><th>Solicitado</th><th>Entegado</th><th>Faltantes</th><th>%Entregado</th><th>Prog x Almacen</th></tr>
			</thead>
			<tbody class='scrollContent'>";
		while($datos=odbc_fetch_array($sql)){
			echo "<tr><td>".($datos['C6_QTDVEN']-$datos['C6_QTDENT']>0?"<img src='images/cancel.png'/> ":"")."$datos[C6_PRODUTO]</td><td class='tdDesc'>$datos[C6_DESCRI]</td><td>$datos[tipo]</td><td class='tdD'>".number_format($datos['C6_QTDVEN'],0)."</td><td class='tdD'>".number_format($datos['C6_QTDENT'],0)."</td><td class='tdD'>".number_format($datos["pzasF"],0)."</td><td class='tdD'>".number_format(($datos['C6_QTDENT']/$datos['C6_QTDVEN'])*100,2)."%</td><td class='tdD'>".($datos['C6_PROGALM']=="T"?"SI":"NO")."</td></tr>";
			$noSurtido+=$datos["pzasF"];
		}
		echo "</tbody></table>";
	}
	}

	odbc_free_result($sql);
	odbc_close($conn);
?>