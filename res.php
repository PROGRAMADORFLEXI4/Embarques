<script>
	$(".valor").on("click", function(e){
		$(".valor").removeClass("styleR");
		$(this).addClass("styleR");
		var vsta="gral";
		if($("#pedsGral").text()!="Panorama General")
			vsta="alm";
		$.post("det.php",{tp:$(this).attr("id"),m:$("#txtFec").attr("value"),an:$("#anio").text() /*,vsta:'gral' */}, function(data){$("#detalle").html(data);});
		/*Revisar variable vsta gral*/
		e.preventdefault();
	});
</script>
<?php
	include("conectabd.php");
	if($_POST['opc']=="grT"){
        $meses="&alm=Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre";
		$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds',ISNULL(SUM(CASE WHEN DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END),0) AS 'pT' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MAX(C6_NOTA) AS fac2 FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".$_POST['an']."0101' AND '".$_POST['an']."1231' AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_='' GROUP BY MONTH(C5_FYHSURT)")or die("Error grP");
		$info="?dat=";
		while($datos=odbc_fetch_array($sql)){
			$info.=$datos['peds'].",".$datos['pT'];
		}
		echo "<img src='graficas/graphbarras.php$info$meses' />";
		odbc_free_result($sql);
	}else{
		echo "<input type='hidden' value='".($_POST['m']+1)."' id='txtFec' />";
		$fecha=$_POST['an'];
		if(($_POST['m']+1)<10)
			$fecha.="0";
		$fecha.=($_POST['m']+1).cal_days_in_month(CAL_GREGORIAN,$_POST['m']+1,$_POST['an']);

		if($_POST['opc']=="peds"){
			 $sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds' FROM SC5010 WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHRCYC,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND D_E_L_E_T_=''")or die("Error pedsTot");
			$datos=odbc_fetch_array($sql);
			echo $datos['peds'];
			odbc_free_result($sql);
		}elseif($_POST['opc']=="T"){
			if($_POST['vsta']=="gral")
				$sql=odbc_exec($conn, "SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds',ISNULL(SUM(CASE WHEN DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END),0) AS 'pC' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MAX(C6_NOTA) AS fac2 FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT WHERE C5_NOTA<>'' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_=''")or die("Error totP Gral");
			else
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds',ISNULL(SUM(CASE WHEN DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 OR CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN 0 ELSE CASE WHEN ZVA_STATUS=1 THEN 1 ELSE 0 END END=1 THEN 1 ELSE 0 END),0) AS 'pC' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MIN(C6_NOTA) AS fac2 FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_=''")or die("Error totP");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			if($datos['peds']==0)
				$totP=0;
			else
				$totP=$datos['pC']/$datos['peds'];
			echo "<br/><span class='titulo'>Surtidos a tiempo [$datos[pC]]:</span><span class='valor' id='st'>".number_format($totP*100,2)."%</span>
				<span class='titulo'>Fuera de tiempo [".($datos['peds']-$datos['pC'])."]:</span><span class='valor' id='ft'>".number_format(($totP=="0"?"0":(1-$totP)*100),2)."%</span>";
		}elseif($_POST['opc']=="A"){
			if($_POST['vsta']=="gral")
				$sql=odbc_exec($conn, "SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds',ISNULL(SUM(CASE WHEN pComp=0 THEN 1 ELSE 0 END),0) AS pComp FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,SUM(C6_QTDVEN-C6_QTDENT) AS pComp FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM HAVING SUM(C6_QTDENT)>0) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_=''")or die("Error pComp gral");
			else
				$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds',ISNULL(SUM(CASE WHEN pComp=0 THEN 1 ELSE 0 END),0) AS pComp FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,SUM(CASE WHEN C6_PROGALM='T' THEN C6_QTDVEN-C6_QTDENT ELSE 0 END) AS pComp FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM HAVING SUM(C6_QTDENT)>0) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_=''")or die("Error PComp");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			if($datos['peds']==0)
				$totP=0;
			else
				$totP=$datos['pComp']/$datos['peds'];
			echo "<br/><span class='titulo'>Surtidos Completos [$datos[pComp]]:</span><span class='valor' title='Productos programados por almacen' id='sc'>".number_format($totP*100,2)."%</span>
				<span class='titulo'>Surtidos Incompletos [".($datos['peds']-$datos['pComp'])."]:</span><span class='valor' title='Productos programados por almacen' id='si'>".number_format(($totP=="0"?"0":(1-$totP)*100),2)."%</span>";
		}elseif($_POST['opc']=="E"){
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds',SUM(CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN CASE WHEN C5_TIPOCLI<>4 AND C5_FPROG=0 AND ISNULL(DATEDIFF(mi,C5_FYHRCYC,ZZO_FECFAC),0)<((DATEDIFF(mi,C5_FYHRCYC,C5_FYHSURT)*(CASE WHEN C5_TIPOCLI=1 THEN 4 ELSE CASE WHEN C5_TIPOCLI=2 THEN 5 ELSE CASE WHEN C5_TIPOCLI=3 THEN 8 ELSE CASE WHEN C5_TIPOCLI=5 THEN 75 ELSE 20 END END END END))/CASE WHEN C5_TIPOCLI=1 THEN 2 ELSE CASE WHEN C5_TIPOCLI=2 THEN 3 ELSE CASE WHEN C5_TIPOCLI=3 OR C5_TIPOCLI=5 THEN 5 ELSE 20 END END END) THEN 1 ELSE 0 END ELSE 1 END) AS 'Almacen',SUM(CASE WHEN (C5_TIPOCLI=4 OR C5_FPROG>0) AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END) AS 'progT' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MIN(C6_NOTA) AS fac2 FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_=''")or die("Error Alm");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			echo "<br/><span class='titulo'>A tiempo Almacen [".($datos['Almacen']+$datos['progT'])."]:</span><span class='valor' ";
			if($datos['peds']>0)
				echo "id='stA'>".number_format((($datos['Almacen']+$datos['progT'])/$datos['peds'])*100,2)."%</span><span class='titulo'>Fuera de tiempo [".($datos['peds']-($datos['Almacen']+$datos['progT']))."] :</span><span class='valor' id='sFtA'>".number_format(100-((($datos['Almacen']+$datos['progT'])/$datos['peds'])*100),2)."%</span>";
			else
				echo ">0%</span><span class='titulo'>Fuera de tiempo:</span><span class='valor'>0%</span>";
		}elseif($_POST['opc']=="NS"){
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT ISNULL(SUM(CASE WHEN SC6.bo>0 THEN 1 ELSE 0 END),0) AS 'bo' FROM SC5010 INNER JOIN(SELECT C6_NUM,SUM((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) AS 'bo' FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND (C6_QTDVEN-C6_QTDENT)>0 AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE C5_NOTA<>'' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND D_E_L_E_T_=''")or die("Error bo");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			$pBo=$datos['bo'];			
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT ISNULL(SUM(SC6.bo),0) AS 'bo',ISNULL(SUM(CASE WHEN SC6.bo>0 THEN SC6.val ELSE 0 END),0) AS 'val' FROM SC5010 INNER JOIN(SELECT C6_NUM,SUM((C6_QTDVEN-C6_QTDENT)*C6_PRCVEN) AS 'bo',SUM(C6_VALOR) AS 'val' FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND C6_QTDVEN-C6_QTDENT>0 AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE C5_NOTA<>'' AND C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND D_E_L_E_T_=''")or die("Error noSurt");
			$impT=odbc_fetch_array($sql);
			odbc_free_result($sql);
			if($impT['val']>0)
				echo "<br/><span class='titulo'>BackOrder &nbsp;[".number_format($pBo,0)."]:</span><span class='valor' id='NoSurt'>$".number_format($impT['bo'],2)."</span><span class='valor' id='boP'>".number_format(($impT['bo']/$impT['val'])*100,2)."%</span>
				<span class='titulo'></span><span class='valor' id='ev'>Estimado de ventas</span>";
			else
				echo "<br/><span class='titulo'>BackOrder:</span><span class='valor'>$0.00</span><span class='valor'>0%</span>";
		}elseif($_POST['opc']=="EVTA"){
			$sql=odbc_exec($conn,"SET LANGUAGE 'español'; SELECT COUNT(*) AS 'peds',SUM(CASE WHEN ISNULL(ZVA_NUM,0)=0 THEN CASE WHEN C5_TIPOCLI<>4 AND C5_FPROG=0 AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END ELSE 1 END) AS 'Embarques',SUM(CASE WHEN (C5_TIPOCLI=4 OR C5_FPROG>0) AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 THEN 1 ELSE 0 END) AS 'progT' FROM SC5010 SC5 INNER JOIN (SELECT C6_NUM,MIN(C6_NOTA) AS fac2 FROM SC6010 WHERE C6_NOTA<>'' AND C6_TES BETWEEN '501' AND '502' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT LEFT JOIN ZVA010 ON C5_NUM=ZVA_NUM AND ZVA_STATUS=1 WHERE C5_USER<>'Atencion a Cliente' AND C5_CLIENTE<>'V00018' AND CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($fecha,0,6)."01' AND '$fecha' AND SC5.D_E_L_E_T_='' AND ZZO.D_E_L_E_T_=''")or die("Error Emb");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			echo "<br/><span class='titulo'>A tiempo Emb. [".($datos['Embarques']+$datos['progT'])."]:</span><span class='valor' ";
			if($datos['peds']>0)
				echo "id='stE'>".number_format((($datos['Embarques']+$datos['progT'])/$datos['peds'])*100,2)."%</span><span class='titulo'>Fuera de tiempo [".($datos['peds']-($datos['Embarques']+$datos['progT']))."]:</span><span class='valor' id='sFtE'>".number_format(100-((($datos['Embarques']+$datos['progT'])/$datos['peds'])*100),2)."%</span>";
			else
				echo ">0%</span><span class='titulo'>Fuera de tiempo:</span><span class='valor'>0%</span>";
		}
	}
	odbc_close($conn);
?>