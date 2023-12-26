<?php
	include("conectabd.php");
	echo "<script src='css/jquery.js'></script>
	<script>
		$(document).on('ready',function(){
			$('.osx').on('click',function(){
				$('#osx-modal-title').text('Faltantes del pedido '+$(this).attr('name'));
				$.get('detxPed.php', {ped:$(this).attr('name')},
					  function(data){
						  $('#osx-modal-data').html(data);
						  }
					);
			});
			$('.mTpo').on('click', function(){
				$.get('detxPed.php', {num:$(this).attr('name')}, function(data){ $('#datosP').html(data); }); });
		});
	</script>";

	/*$cSql="SET LANGUAGE 'español'; SELECT C5_NUM,A1_COD,A1_NOME,SC6.valor,(SC6.QV-SC6.QE) AS pzasF,((SC6.QV-SC6.QE)/SC6.QV)*100 AS pPzas,(SC6.QE/SC6.QV)*100 AS porc,SC6.noV FROM SC5010 SC5 LEFT JOIN SA1010 SA1 ON C5_CLIENT=a1_COD INNER JOIN(SELECT C6_NUM,MAX(C6_NOTA) AS fac2,SUM(C6_VALOR) AS valor,SUM(C6_QTDENT) AS QE,SUM(C6_QTDVEN) AS QV,SUM(C6_PRCVEN*(C6_QTDVEN-C6_QTDENT)) AS noV FROM SC6010 WHERE C6_TES BETWEEN 501 AND 502 AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT WHERE CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($_GET['fh'],0,6)."01' AND '$_GET[fh]' ";*/
    // cambio para backorders CHT
	$cSql="
	SET LANGUAGE 'español';
	SELECT 
		C5_NUM,
		ISNULL(Z77_ORDSUR,'') Z77_ORDSUR,
		A1_COD,A1_NOME,SC6.valor,(SC6.QV-SC6.QE) AS pzasF,((SC6.QV-SC6.QE)/SC6.QV)*100 AS pPzas,(SC6.QE/SC6.QV)*100 AS porc,SC6.noV FROM SC5010 SC5 
	LEFT JOIN SA1010 SA1 ON C5_CLIENT=a1_COD 
	INNER JOIN(
		SELECT C6_NUM,MAX(C6_NOTA) AS fac2,SUM(C6_VALOR) AS valor,SUM(C6_QTDENT) AS QE,SUM(C6_QTDVEN) AS QV,SUM(C6_PRCVEN*(C6_QTDVEN-C6_QTDENT)) AS noV 
		FROM SC6010 
		WHERE C6_TES BETWEEN 501 AND 502 AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM 
	LEFT JOIN ZZO010 ZZO ON SC6.fac2=ZZO_FACT 
	LEFT JOIN (SELECT Z77_PEDIDO, Z77_ORDSUR FROM Z77010 WHERE D_E_L_E_T_ = '') AS Z77 ON C5_NUM = Z77.Z77_PEDIDO
	WHERE CONVERT(DATETIME,SUBSTRING(C5_FYHSURT,0,9)) BETWEEN '".substr($_GET['fh'],0,6)."01' AND '$_GET[fh]' ";

	if(substr($_GET['tp'],0,2)=="T")
		$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 AND C5_NOTA<>'' ";
	elseif(substr($_GET['tp'],0,2)=="F")
		$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 AND C5_NOTA<>'' ";
	elseif(substr($_GET['tp'],0,2)=="C")
		$cSql.="AND C5_NOTA<>'' AND C5_NOTA NOT LIKE 'X%' ";
	elseif(substr($_GET['tp'],0,2)=="I")
		$cSql.="AND C5_NOTA LIKE 'X%' ";
	elseif(substr($_GET['tp'],0,2)=="PP")
		$cSql.="AND C5_NOTA='' ";
	elseif(substr($_GET['tp'],0,3)=="Vip")
	{
		$cSql.="AND C5_NOTA<>'' AND A1_TIPO=1 AND C5_FPROG=0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' ";
		if($_GET['tp']=="VipC")
			 $cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="VipI")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="VipT")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,3)=="Loc"){
		$cSql.="AND C5_NOTA<>'' AND A1_TIPO=2 AND C5_FPROG=0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018'";
		if($_GET['tp']=="LocC")
			$cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="LocI")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="LocT")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,3)=="For"){
		$cSql.="AND C5_NOTA<>'' AND A1_TIPO=3 AND C5_FPROG=0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' ";
		if($_GET['tp']=="ForC")
			$cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="ForI")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="ForT")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,3)=="Exp"){
		$cSql.="AND C5_NOTA<>'' AND A1_TIPO=4 AND C5_FPROG=0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' ";
		if($_GET['tp']=="ExpC")
			$cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="ExpI")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="ExpT")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,3)=="Esp"){
		$cSql.="AND C5_NOTA<>'' AND A1_TIPO=5 AND C5_FPROG=0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' ";
		if($_GET['tp']=="EspC")
			$cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="EspI")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="EspT")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,5)=="Cita+"){
		$cSql.="AND C5_NOTA<>'' AND A1_TIPO=7 AND C5_FPROG=0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' ";
		if($_GET['tp']=="Cita+C")
			$cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="Cita+I")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="Cita+T")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,4)=="Cita"){
		$cSql.="AND C5_NOTA<>'' AND A1_TIPO=6 AND C5_FPROG=0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' ";
		if($_GET['tp']=="CitaC")
			$cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="CitaI")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="CitaT")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,3)=="Pro"){
		$cSql.="AND C5_NOTA<>'' AND C5_FPROG>0 AND C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' ";
		if($_GET['tp']=="ProgC")
			$cSql.="AND C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="ProgI")
			$cSql.="AND C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="ProgT")
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="AND DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}elseif(substr($_GET['tp'],0,3)=="Atn"){
		$cSql.="AND C5_NOTA<>'' AND (C5_USER='Atencion a cliente' OR A1_COD='V00018') AND ";
		if($_GET['tp']=="AtnC")
			$cSql.="C5_NOTA NOT LIKE 'X%' ";
		elseif($_GET['tp']=="AtnI")
			$cSql.="C5_NOTA LIKE 'X%' ";
		elseif($_GET['tp']=="AtnT")
			$cSql.="DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)<=0 ";
		else
			$cSql.="DATEDIFF(mi,C5_FYHSURT,ZZO_FEMBAR)>0 ";
	}else{
		$cSql.="AND C5_NOTA='' AND ";
		if($_GET['tp']=="xSAtn")
			$cSql.="C5_FPROG=0 AND (C5_USER='Atencion a cliente' OR A1_COD='V00018') ";
		else{
			$cSql.="C5_USER<>'Atencion a cliente' AND A1_COD<>'V00018' AND ";
			if($_GET['tp']=="xSVip")
				$cSql.="A1_TIPO=1 AND C5_FPROG=0 ";
			elseif($_GET['tp']=="xSLoc")
				$cSql.="A1_TIPO=2 AND C5_FPROG=0 ";
			elseif($_GET['tp']=="xSFor")
				$cSql.="A1_TIPO=3 AND C5_FPROG=0 ";
			elseif($_GET['tp']=="xSExp")
				$cSql.="A1_TIPO=4 AND C5_FPROG=0 ";
			elseif($_GET['tp']=="xSEsp")
				$cSql.="A1_TIPO=5 AND C5_FPROG=0 ";
			elseif($_GET['tp']=="xSProg")
				$cSql.="C5_FPROG>0 ";
			elseif($_GET['tp']=="xSCita")
				$cSql.="A1_TIPO=6 AND C5_FPROG=0 ";
			elseif($_GET['tp']=="xSCita+")
				$cSql.="A1_TIPO=7 AND C5_FPROG=0 ";
		}
	}
	$cSql.="AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' ORDER BY C5_NUM";
	$sql=odbc_exec($conn,$cSql);
	echo "<table id='tDatos'><tr><th>Pedido</th><th>Nombre</th><th>Importe</th><th>No Surtido</th><th> % </th><th>Pzas Falt</th><th> % </th></tr>";
	while($datos=odbc_fetch_array($sql)){
		echo "<tr><td>";
		if($datos['porc']<100){
			echo "<a id='im' class='osx' name='$datos[C5_NUM]' title='Visualizar pedido'>$datos[C5_NUM]</a>";
		}else{
			echo $datos['C5_NUM'];
		}
		echo "</td><td title='$datos[A1_COD]'><a class='mTpo' name='$datos[C5_NUM]'>".substr($datos["A1_NOME"],0,55)."</a></td><td id='tdD'>$".number_format($datos['valor'],2)."</td><td id='tdD'>$".number_format($datos['noV'],2)."</td><td id='tdD'>".number_format($datos['pPzas'],2)."%</td><td id='tdD'>".number_format($datos['pzasF'],0)."</td><td id='tdD'>".number_format($datos['porc'],2)."%</td></tr>";
	}
	echo "</table>
	<script src='jquery.simplemodal.js'></script>
	<script src='osx.js'></script>";
	odbc_free_result($sql);
	odbc_close($conn);
?>