<script>
	$(".fRow").on("click", function(e){
		$("#fondoObsc").show();
		var dFec="";
		if($(this).attr("name")!=null)
			dFec=$(this).attr("name");
		$.post("detxPed.php",{num:$(this).attr("id"),fec:dFec}, function(data){$("#detP").html(data);});
		$("#detP").slideToggle();
		e.preventdefault();
	});
</script>
<?php
	include("conectabd.php");
	$fecha=$_POST['an'];
	if(($_POST['m'])<10)
		$fecha.="0";
	$fecha.=($_POST['m']).cal_days_in_month(CAL_GREGORIAN,$_POST['m'],$_POST['an']);
	if(isset($_POST['tp'])){
		if(substr($_POST['tp'],0,4)=="totP"){
            $meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT Substring(C5_FYHSURT,4,5) AS 'fecha',COUNT(*) AS 'peds' FROM SC5010 WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHRCYC,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND D_E_L_E_T_='' GROUP BY Substring(C5_FYHSURT,4,5)")or die("Error detP");
			echo "<table class='tDatos'><tr><th>Pedidos por surtir</th><th>Periodo</th></tr>";
			$m=0;
			while($datos=odbc_fetch_array($sql)){
				if(substr($datos['fecha'],0,1)==0)
					$m=substr($datos['fecha'],1,1);
				else
					$m=substr($datos['fecha'],0,2);
				echo "<tr><td class='tdD'>".number_format($datos['peds'],0)."</td><td class='tdD'>".$meses[$m-1]."/".substr($datos['fecha'],3)."</td></tr>";
			}
			echo "</table>";
			odbc_free_result($sql);
		}elseif($_POST['tp']=="NoSurt"){
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT ISNULL(SUM(SC6.boF),0) AS 'Fab',ISNULL(SUM(SC6.boI),0) AS 'Imp',ISNULL(SUM(SC6.boC),0) AS 'Com' FROM SC5010 INNER JOIN(SELECT C6_NUM,SUM(CASE WHEN B1_TIPO BETWEEN '01' AND '02' THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'boF',SUM(CASE WHEN B1_TIPO='03' THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'boI',SUM(CASE WHEN B1_TIPO='04' THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'boC' FROM SC6010 SC6 INNER JOIN SB1010 SB1 ON C6_PRODUTO=B1_COD WHERE C6_TES BETWEEN '501' AND '502' AND (C6_QTDVEN-C6_QTDENT)>0 AND SC6.D_E_L_E_T_='' AND SB1.D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE C5_NOTA<>'' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND D_E_L_E_T_=''")or die("Err DetNV");
			$datos=odbc_fetch_array($sql);
		  	odbc_free_result($sql);
			echo "<table class='tDatos'><tr><caption>BackOrder por l&iacute;nea</caption><th>L&iacute;nea</th><th>Importe</th></tr><tr class='fRow' id='fab' name='$fecha'><td>Fabricado:</td><td class='tdD'>$".number_format($datos['Fab'],2)."</td></tr><tr class='fRow' id='imp' name='$fecha'><td>Importado:</td><td class='tdD'>$".number_format($datos['Imp'],2)."</td></tr><tr class='fRow' id='com' name='$fecha'><td>Comercializado:</td><td class='tdD'>$".number_format($datos['Com'],2)."</td></tr></table>
			<hr><table class='tDatos'><tr><caption>Faltantes</caption><th>Tipo</th><th>Importe</th></tr><tr class='fRow' id='Pron' name='$fecha'><td>Exceso en pronostico de ventas:</td><td class='tdD'>$";
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT SUM(SC6.importe) AS importe FROM SC5010 INNER JOIN(SELECT C6_NUM,C6_PRODUTO,SUM((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) AS importe FROM SC6010 SC6 INNER JOIN SC4010 SC4 ON C6_PRODUTO=C4_PRODUTO WHERE C4_DATA BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND C6_TES BETWEEN '501' AND '502' AND SC6.D_E_L_E_T_='' AND SC4.D_E_L_E_T_='' GROUP BY C6_NUM,C6_PRODUTO HAVING SUM(C6_QTDVEN)>SUM(C4_QUANT)) AS SC6 ON C5_NUM=C6_NUM WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND D_E_L_E_T_=''")or die("Error pVen");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			echo number_format($datos['importe'],2)."</td></tr><tr class='fRow' id='noProg' name='$fecha'><td>Productos no programados x Almac&eacute;n:</td><td class='tdD'>$";
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT ISNULL(SUM(SC6.impPa),0) AS 'Programado',ISNULL(SUM(SC6.impNoP),0) AS 'NoProg' FROM SC5010 INNER JOIN(SELECT C6_NUM,SUM(CASE WHEN C6_PROGALM='T' THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'impPa',SUM(CASE WHEN C6_PROGALM='F' THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'impNoP' FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND (C6_QTDVEN-C6_QTDENT)>0 AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE C5_NOTA<>'' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND D_E_L_E_T_=''")or die("Error proxAlm");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			echo number_format($datos['NoProg'],2)."</td></tr><tr class='fRow' id='programado' name='$fecha'><td>Productos programados x Almac&eacute;n:</td><td class='tdD'>$".number_format($datos['Programado'],2)."</td></tr></table>";
			
		}elseif($_POST['tp']=="ev"){
			$sql=odbc_exec($conn,"SELECT C4_PRODUTO,B1_DESC,SUM(C4_QUANT) AS 'previsto',ISNULL(SUM(SC6.vend),0) AS 'vendido' FROM SC4010 SC4 INNER JOIN SB1010 SB1 ON C4_PRODUTO=B1_COD LEFT JOIN (SELECT C6_PRODUTO,SUM(C6_QTDENT) AS vend FROM SC6010 WHERE C6_ENTREG BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_PRODUTO) AS SC6 ON C4_PRODUTO=SC6.C6_PRODUTO WHERE C4_DATA BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC4.D_E_L_E_T_='' AND SB1.D_E_L_E_T_='' GROUP BY C4_PRODUTO,B1_DESC ORDER BY C4_PRODUTO")or die("Erro PV");
			echo "<table class='tInfo'>
					<thead class='fixedHeader'>
					  <tr><th>C&oacute;digo</th><th class='thDesc1'>Descripci&oacute;n</th><th>Pzas. Previstas</th><th>Pzas. Vendidas</th><th>Prod Vendido</th></tr>
				    </thead><tbody class='scrollContent'>";
			while($datos=odbc_fetch_array($sql))
				echo "<tr><td>$datos[C4_PRODUTO]</td><td class='tdDesc1'>".substr($datos['B1_DESC'],0,40)."</td><td class='tdD'>".number_format($datos["previsto"],0)."</td><td class='tdD'>".number_format($datos["vendido"],0)."</td><td class='tdD'>".number_format(($datos['vendido']/$datos['previsto'])*100,2)."%</tr>";
			echo "</tbody></table>";
			odbc_free_result($sql);
		}else{
			if($_POST['tp']=="stA"){
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(noVend) AS 'nVend' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MIN(C6_NOTA) AS fac2,SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP',SUM(CASE WHEN (C6_QTDVEN-C6_QTDENT)>0 THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'noVend' FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND (CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN CASE WHEN C5_TIPOCLI<>4 AND C5_FPROG=0 AND ISNULL(DATEDIFF(mi,C5_FYHRCYC,ZZO_FECFAC),0)<((DATEDIFF(mi,C5_FYHRCYC,C5_FYHSURT)*(CASE WHEN C5_TIPOCLI=1 THEN 4 ELSE CASE WHEN C5_TIPOCLI=2 THEN 5 ELSE CASE WHEN C5_TIPOCLI=3 THEN 8 ELSE CASE WHEN C5_TIPOCLI=5 THEN 75 ELSE 20 END END END END))/CASE WHEN C5_TIPOCLI=1 THEN 2 ELSE CASE WHEN C5_TIPOCLI=2 THEN 3 ELSE CASE WHEN C5_TIPOCLI=3 OR C5_TIPOCLI=5 THEN 5 ELSE 20 END END END) THEN 1 ELSE 0 END ELSE 1 END=1 OR CASE WHEN (C5_TIPOCLI=4 OR C5_FPROG>0) AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END=1) AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME ORDER BY C5_NUM")or die("Error Surt a Tiempo Alm");
			}elseif($_POST['tp']=="sFtA"){
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(noVend) AS 'nVend' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MIN(C6_NOTA) AS fac2,SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP',SUM(CASE WHEN (C6_QTDVEN-C6_QTDENT)>0 THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'noVend' FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND (CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN CASE WHEN C5_TIPOCLI<>4 AND C5_FPROG=0 AND ISNULL(DATEDIFF(mi,C5_FYHRCYC,ZZO_FECFAC),0)>((DATEDIFF(mi,C5_FYHRCYC,C5_FYHSURT)*(CASE WHEN C5_TIPOCLI=1 THEN 4 ELSE CASE WHEN C5_TIPOCLI=2 THEN 5 ELSE CASE WHEN C5_TIPOCLI=3 THEN 8 ELSE CASE WHEN C5_TIPOCLI=5 THEN 75 ELSE 20 END END END END))/CASE WHEN C5_TIPOCLI=1 THEN 2 ELSE CASE WHEN C5_TIPOCLI=2 THEN 3 ELSE CASE WHEN C5_TIPOCLI=3 OR C5_TIPOCLI=5 THEN 5 ELSE 20 END END END) THEN 1 ELSE 0 END ELSE 0 END=1 OR CASE WHEN (C5_TIPOCLI=4 OR C5_FPROG>0) AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 THEN 1 ELSE 0 END=1) AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME ORDER BY C5_NUM")or die("Error sFtA");
			}elseif($_POST['tp']=="stE"){
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(noVend) AS 'nVend' FROM SC5010 SC5 INNER JOIN(SELECT C6_NUM,MIN(C6_NOTA) AS fac2,SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP',SUM(CASE WHEN (C6_QTDVEN-C6_QTDENT)>0 THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'noVend' FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND (CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN CASE WHEN C5_TIPOCLI<>4 AND C5_FPROG=0 AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END ELSE 1 END=1 OR CASE WHEN (C5_TIPOCLI=4 OR C5_FPROG>0) AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END=1) AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME ORDER BY C5_NUM")or die("Error Surt Tiempo Emb");
			}elseif($_POST['tp']=="sFtE"){
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(noVend) AS 'nVend' FROM SC5010 SC5 INNER JOIN(SELECT C6_NUM,MIN(C6_NOTA) AS fac2,SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP',SUM(CASE WHEN (C6_QTDVEN-C6_QTDENT)>0 THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'noVend' FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND (CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN CASE WHEN C5_TIPOCLI<>4 AND C5_FPROG=0 AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 THEN 1 ELSE 0 END ELSE 0 END=1 OR CASE WHEN (C5_TIPOCLI=4 OR C5_FPROG>0) AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 THEN 1 ELSE 0 END=1) AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME ORDER BY C5_NUM")or die("Error sFtE");
			}elseif($_POST['tp']=="st" || $_POST['tp']=="ft"){
				if($_POST['vsta']=="gral"){
					$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(noVend) AS 'nVend' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MAX(C6_NOTA) AS fac2,SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP',SUM(CASE WHEN (C6_QTDVEN-C6_QTDENT)>0 THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'noVend' FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE C5_NOTA<>'' AND (DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0) AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_=''  GROUP BY C5_NUM,A1_NOME ORDER BY C5_NUM")or die("Error st Gral");
				}else
					$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(noVend) AS 'nVend' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MIN(C6_NOTA) AS fac2,SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP',SUM(CASE WHEN (C6_QTDVEN-C6_QTDENT)>0 THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'noVend' FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE (DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)".($_POST['tp']=="st"?"<=0":">0")." OR CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN 0 ELSE 1 END=1) AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME ORDER BY C5_NUM")or die("Error st");
				
			}elseif($_POST['tp']=="sc" || $_POST['tp']=="si"){
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(noVend) AS 'nVend' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,SUM(CASE WHEN C6_PROGALM='T' THEN C6_QTDVEN-C6_QTDENT ELSE 0 END) AS pComp,SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP',SUM(CASE WHEN (C6_QTDVEN-C6_QTDENT)>0 THEN ((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) ELSE 0 END) AS 'noVend' FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME HAVING SUM(pComp)".($_POST['tp']=="sc"?"=":">")."0") or die("ErrorSc");
			}elseif($_POST['tp']=="boP")
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT C5_NUM,A1_NOME,SUM(qVen) AS 'sol',SUM(qEnt) AS 'ent',SUM(valorP) AS 'valP',SUM(bo) AS 'nVend' FROM SC5010 SC5 INNER JOIN(SELECT C6_NUM,SUM((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) AS 'bo',SUM(C6_QTDVEN) AS 'qVen',SUM(C6_QTDENT) AS 'qEnt',SUM(C6_QTDVEN*C6_PRCVEN) AS 'valorP' FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM HAVING SUM(C6_QTDENT)>0) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME HAVING SUM(CASE WHEN SC6.bo>0 THEN 1 ELSE 0 END)>0 ORDER BY C5_NUM")or die("Error BO");
			
			echo "<table class='tDatos'>
				<thead class='fixedHeader'>
					<tr><th>Pedido</th><th class='thDesc'>Cliente</th><th>Pzas Sol</th><th>Pzas Ent</th><th>Imp. Pedido</th><th>Imp. No vendido</th><th>% No Surtido</th></tr>
				</thead><tbody class='scrollContent'>";
			$pedidos=0;
			while($datos=odbc_fetch_array($sql)){
				$pedidos+=1;
				echo "<tr class='fRow' id='$datos[C5_NUM]'><td>[$pedidos] $datos[C5_NUM]</td><td class='tdDesc'>".substr($datos['A1_NOME'],0,30)."</td><td class='tdD'>".number_format($datos["sol"],0)."</td><td class='tdD'>".number_format($datos["ent"],0)."</td><td class='tdD'>$".number_format($datos["valP"],2)."</td><td class='tdD'>$".number_format($datos["nVend"],2)."</td><td class='tdD'>".number_format(($datos["nVend"]/$datos["valP"])*100,2)."%</td></tr>";
			}
			echo "</tbody></table>";
			odbc_free_result($sql);
		}
	}
	odbc_close($conn);
?>