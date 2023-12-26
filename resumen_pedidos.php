<?php
	session_start();
	include("conectabd.php");
	include("conectaMYSQL.php");
	date_default_timezone_set("America/Monterrey");
	setlocale(LC_ALL,"es_ES");

	odbc_exec($conn,"SET LANGUAGE 'español';");
	/*$exce=odbc_exec($conn,"
		SELECT 					
			C5_NUM
			,ISNULL(ZZM_CAJAS,0) AS ZZM_CAJAS
			,ISNULL(ZZM_TINACO,0) AS ZZM_TINACO
			,ISNULL(ZZM_EXHIB,0) AS ZZM_EXHIB
			,ISNULL(ZZM_COSTAL,0) AS ZZM_COSTAL
			,ISNULL(ZZM_FECSUR,'') AS ZZM_FECSUR 
			,ISNULL(ZZM_FATURA,'') AS ZZM_FATURA
			,ISNULL(RTRIM(A2_NREDUZ),'SIN ASIGNAR') AS A2_NREDUZ
		FROM SC5010 SC5
			INNER JOIN SC6010 SC6 ON C5_NUM=C6_NUM AND SC6.D_E_L_E_T_<>'*'
			INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE AND SA1.D_E_L_E_T_<>'*'
			LEFT JOIN (
				SELECT ZZM_PEDIDO
						,ZZN_NOMBRE
						,ZZM_FYHSUR
						,ZZM_CAJAS
						,ZZM_TINACO
						,ZZM_EXHIB
						,ZZM_COSTAL
						,ZZM_FECSUR
						,ZZM_FATURA
					FROM ZZM010 ZZM
						LEFT JOIN ZZN010 ON ZZM_CODALM=ZZN_CODIGO
					WHERE ZZM.D_E_L_E_T_<>'*'
				) AS ZZM ON C5_NUM=ZZM.ZZM_PEDIDO
			LEFT JOIN SC9010 SC9 ON C5_NUM=C9_PEDIDO AND SC9.D_E_L_E_T_<>'*'
			LEFT JOIN SA2010 SA2 ON C5_TRANSP = A2_COD AND SA2.D_E_L_E_T_<>'*'
		WHERE C5_LOJAENT=A1_LOJA AND C5_FYHRCYC<>'' AND C5_NOTA='' AND (C6_TES='501' OR C6_TES='502' OR C6_TES='522' OR C6_TES='523') AND (C6_QTDVEN-C6_QTDENT)>0 AND C6_BLQ='' 
			AND C9_BLCRED<>'09' AND C9_BLCRED<>'01' AND C5_CLIENTE <> 'N00028'
			AND ZZM_FYHSUR <> '' AND SC5.D_E_L_E_T_<>'*'
		GROUP BY C5_NUM, ZZM_CAJAS, ZZM_FECSUR, A2_NREDUZ, ZZM_COSTAL, ZZM_EXHIB, ZZM_TINACO,ZZM_FATURA
		ORDER BY A2_NREDUZ, C5_NUM
	")or die("Error al ejecutar la consulta");*/ //LEG 20210125 agregue ZZM_FACT



	//cambio backorders CHT

	$exce=odbc_exec($conn,"
		SELECT
			C5_NUM
			,ISNULL(Z77_ORDSUR,'') Z77_ORDSUR
			,ISNULL(ZZM_CAJAS,0) AS ZZM_CAJAS
			,ISNULL(ZZM_TINACO,0) AS ZZM_TINACO
			,ISNULL(ZZM_EXHIB,0) AS ZZM_EXHIB
			,ISNULL(ZZM_COSTAL,0) AS ZZM_COSTAL
			,ISNULL(ZZM_FECSUR,'') AS ZZM_FECSUR 
			,ISNULL(ZZM_FATURA,'') AS ZZM_FATURA
			,ISNULL(RTRIM(A2_NREDUZ),'SIN ASIGNAR') AS A2_NREDUZ
		FROM SC5010 SC5
			INNER JOIN SC6010 SC6 ON C5_NUM=C6_NUM AND SC6.D_E_L_E_T_<>'*'
			INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE AND SA1.D_E_L_E_T_<>'*'
			LEFT JOIN (
				SELECT ZZM_PEDIDO
						,ZZN_NOMBRE
						,ZZM_FYHSUR
						,ZZM_CAJAS
						,ZZM_TINACO
						,ZZM_EXHIB
						,ZZM_COSTAL
						,ZZM_FECSUR
						,ZZM_FATURA
					FROM ZZM010 ZZM
						LEFT JOIN ZZN010 ON ZZM_CODALM=ZZN_CODIGO
					WHERE ZZM.D_E_L_E_T_<>'*'
				) AS ZZM ON C5_NUM=ZZM.ZZM_PEDIDO
			LEFT JOIN SC9010 SC9 ON C5_NUM=C9_PEDIDO AND SC9.D_E_L_E_T_<>'*'
			LEFT JOIN SA2010 SA2 ON C5_TRANSP = A2_COD AND SA2.D_E_L_E_T_<>'*'
			LEFT JOIN (SELECT Z77_PEDIDO, Z77_ORDSUR FROM Z77010 WHERE D_E_L_E_T_ = '') AS Z77 ON C5_NUM = Z77.Z77_PEDIDO
		WHERE C5_LOJAENT=A1_LOJA AND C5_FYHRCYC<>'' AND C5_NOTA='' AND (C6_TES='501' OR C6_TES='502' OR C6_TES='522' OR C6_TES='523') AND (C6_QTDVEN-C6_QTDENT)>0 AND C6_BLQ='' 
			AND C9_BLCRED<>'09' AND C9_BLCRED<>'01' AND C5_CLIENTE <> 'N00028'
			AND ZZM_FYHSUR <> '' AND SC5.D_E_L_E_T_<>'*'
		GROUP BY C5_NUM, ZZM_CAJAS, ZZM_FECSUR, A2_NREDUZ, ZZM_COSTAL, ZZM_EXHIB, ZZM_TINACO,ZZM_FATURA, Z77_ORDSUR
		ORDER BY A2_NREDUZ, C5_NUM
	")or die("Error al ejecutar la consulta");


    $pxs="";
    $ppa="";
    $pxf="";
    $rxt="";
    $rxf="";
    $nPedido="";

    $nombreF = array();
    $pedidoF = array();
    $nCajasF = array();
    $nTincaF = array();
    $nExhibF = array();
    $nCostaF = array();

    $nombreT = array("EN PROCESO DE SURTIDO", "PEDIDOS POR AUDITAR", "PEDIDOS POR FACTURAR");
    $pedidoT = array(0, 0, 0);
    $nCajasT = array(0, 0, 0);
    $nTincaT = array(0, 0, 0);
    $nExhibT = array(0, 0, 0);
    $nCostaT = array(0, 0, 0);

    $aux = False;
    $tPed = False;
    $ROWN1 = 0;
    $ROWN2 = 0;
    $ROWN3 = 0;

    $total1 = 0;
	$total2 = 0;
	$total3 = 0;
	$isbko=false; //LEG 20210125 AGREGUE BANDERA PARA QUE NO CUENTE LOS BKO
    while($datos=odbc_fetch_array($exce))
    { 
    	$nPedido = $datos["C5_NUM"];
		$cajasPedido = $datos["ZZM_CAJAS"];
		$tinacPedido = $datos["ZZM_TINACO"];
		$exhibPedido = $datos["ZZM_EXHIB"];
		$costaPedido = $datos["ZZM_COSTAL"];

		// TODOS LOS PEDIDOS
		$isbko=false; //LEG 20210125 AGREGUE ESTE IF PARA QUE DISTINGA LOS BKO Y NO LOS CUENTE EN X FACTURAR
		if(($datos["ZZM_CAJAS"] + $datos["ZZM_TINACO"] + $datos["ZZM_EXHIB"] + $datos["ZZM_COSTAL"]>0) && trim($datos["ZZM_FATURA"])<>"") //LEG
		{
			$isbko=true;
		}
		elseif($datos["ZZM_CAJAS"] + $datos["ZZM_TINACO"] + $datos["ZZM_EXHIB"] + $datos["ZZM_COSTAL"]==0){//LEG 20210125 ERA EL PRIMER IF Y LO PUSE COMO ELSEIF
    		$ROWN1 += 1;
			$tPed = 0;
			$sqlCajas = "SELECT
				    C5_NUM,
				    SUM(CAJAS_CERRADAS) CAJAS_CERRADAS,
				    FLOOR(SUM(VOL_PZ)/0.05166)+1 CAJAS_VOL,
				    FLOOR(SUM(PESO_PZ)/11.5)+1 CAJAS_PESO,
				    SUM(CAJAS_CERRADAS)+ CASE WHEN CEILING(SUM(VOL_PZ)/0.05166) < CEILING(SUM(PESO_PZ)/11.5) THEN CEILING(SUM(PESO_PZ)/11.5) ELSE CEILING(SUM(VOL_PZ)/0.05166) END CAJAS_TOTALES,
				    SUM(COSTAL) COSTALES,
					SUM(TINACO) TINACOS,
					SUM(EXHIBIDOR) EXHIBIDORES
				FROM
				(
				SELECT                    
				    C5_NUM,C6_PRODUTO,
				    C6_QTDVEN,
				    B1_QE,ROUND(Z52_LARGO,3) AS LARGO, ROUND(Z52_ANCHO,3) AS ALTO,
				    ROUND(Z52_ALTO,3) AS ANCHO, ROUND(B1_PESONET,4) B1_PESONET,FLOOR(C6_QTDVEN/CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END) CAJAS_CERRADAS,
				    C6_QTDVEN-(FLOOR(C6_QTDVEN/CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END)*CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END) PIEZAS,
				    CASE WHEN B1_BOLSA='T' OR B1_CLASE='11' OR B1_CLASE='22' THEN 0 ELSE
				    (ROUND(Z52_LARGO,3)*ROUND(Z52_ANCHO,3)*ROUND(Z52_ALTO,3)/CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END)*(C6_QTDVEN-(FLOOR(C6_QTDVEN/CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END)*CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END)) END VOL_PZ,
				    CASE WHEN B1_BOLSA='T' OR B1_CLASE='11' OR B1_CLASE='22' THEN 0 ELSE
				    (C6_QTDVEN-(FLOOR(C6_QTDVEN/CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END)*CASE WHEN B1_QE=0 THEN 1 ELSE B1_QE END))*B1_PESONET END AS PESO_PZ,
				    CASE WHEN B1_BOLSA='T' THEN CEILING(C6_QTDVEN/B1_QE) ELSE 0 END COSTAL,
				    CASE WHEN B1_CLASE='11' THEN C6_QTDVEN ELSE 0 END TINACO,
				    CASE WHEN B1_CLASE='22' THEN C6_QTDVEN ELSE 0 END EXHIBIDOR
				FROM SC5010 SC5
				    INNER JOIN SC6010 SC6 ON C5_NUM=C6_NUM AND SC6.D_E_L_E_T_<>'*'
				    INNER JOIN SB1010 B1
				                ON C6_PRODUTO = B1_COD
				                    AND B1.D_E_L_E_T_ = ''
				        LEFT JOIN Z52010 Z52 
				                ON B1_DESCLAS = Z52_DESCRI AND Z52.D_E_L_E_T_=''
				WHERE C5_NUM='".$datos["C5_NUM"]."'
				) C
				GROUP BY C5_NUM;";
			$resCajas = odbc_exec($conn,$sqlCajas)or die("Error al consultar el numero de cajas ALMACEN");
			$cajasPedido = odbc_fetch_array($resCajas);

			$cajasPedido = $cajasPedido["CAJAS_TOTALES"];
			$tinacPedido = $datos["TINACOS"];
			$exhibPedido = $datos["EXHIBIDORES"];
			$costaPedido = $datos["COSTALES"];

            $pxs.="<tr class='".($ROWN1 % 2 == 0 ? "trPar" : "trImpar")."'>
            		<td class=''>".$datos["C5_NUM"]."</td>
            		<td class=''>".$datos["Z77_ORDSUR"]."</td>
            		<td class=''>".($cajasPedido > 0 ? number_format($cajasPedido,0,'.',',') : '')."</td>
            		<td class=''>".($tinacPedido > 0 ? number_format($tinacPedido,0,'.',',') : '')."</td>
            		<td class=''>".($exhibPedido > 0 ? number_format($exhibPedido,0,'.',',') : '')."</td>
            		<td class=''>".($costaPedido > 0 ? number_format($costaPedido,0,'.',',') : '')."</td>
            		<td class=''>".$datos["A2_NREDUZ"]."</td>
            		<td class=''>".$datos["ZZM_FECSUR"]."</td>
            	</tr>";
        }elseif($datos["ZZM_FECSUR"]==""){
    		$ROWN2 += 1;
			$tPed = 1;
            $ppa.="<tr class='".($ROWN2 % 2 == 0 ? "trPar" : "trImpar")."'>
                	<td class=''>".$datos["C5_NUM"]."</td>
            		<td class=''>".$datos["Z77_ORDSUR"]."</td>
            		<td class=''>".($cajasPedido > 0 ? number_format($cajasPedido,0,'.',',') : '')."</td>
            		<td class=''>".($tinacPedido > 0 ? number_format($tinacPedido,0,'.',',') : '')."</td>
            		<td class=''>".($exhibPedido > 0 ? number_format($exhibPedido,0,'.',',') : '')."</td>
            		<td class=''>".($costaPedido > 0 ? number_format($costaPedido,0,'.',',') : '')."</td>
                	<td class=''>".$datos["A2_NREDUZ"]."</td>
                	<td class=''>".$datos["ZZM_FECSUR"]."</td>
                </tr>";
        }else{
    		$ROWN3 += 1;
			$tPed = 2;
            $pxf.="<tr class='".($ROWN3 % 2 == 0 ? "trPar" : "trImpar")."'>
                	<td class=''>".$datos["C5_NUM"]."</td>
            		<td class=''>".$datos["Z77_ORDSUR"]."</td>
            		<td class=''>".($cajasPedido > 0 ? number_format($cajasPedido,0,'.',',') : '')."</td>
            		<td class=''>".($tinacPedido > 0 ? number_format($tinacPedido,0,'.',',') : '')."</td>
            		<td class=''>".($exhibPedido > 0 ? number_format($exhibPedido,0,'.',',') : '')."</td>
            		<td class=''>".($costaPedido > 0 ? number_format($costaPedido,0,'.',',') : '')."</td>
                	<td class=''>".$datos["A2_NREDUZ"]."</td>
                	<td class=''>".$datos["ZZM_FECSUR"]."</td>
                </tr>";
        }
		if(!$isbko) //LEG 20210125 AGREGUE ESTE IF Y SOLO METÍ EL CODIGO DE VICTOR PARA QUE SOLO CUENTE LOS QUE NO SON BKO
		{
			//PEDIDOS POR FLETERA
			$aux = array_search($datos["A2_NREDUZ"], $nombreF);
			
			if($aux > -1){// Ya existe registro de la fletera, se incrementa pedido y cajas
				$nombreF[$aux] = $datos["A2_NREDUZ"];
				$pedidoF[$aux] = $pedidoF[$aux] + 1;
				$nCajasF[$aux] = $nCajasF[$aux] + $cajasPedido;
				$nFinacT[$aux] = $nTinacF[$aux] + $tinacPedido;
				$nExhibF[$aux] = $nExhibF[$aux] + $exhibPedido;
				$nCostaF[$aux] = $nCostaF[$aux] + $costaPedido;
			}else{//No existe registro de la fletera, se agrega
				$nombreF[] = $datos["A2_NREDUZ"];
				$pedidoF[] = 1;
				$nCajasF[] = $cajasPedido;
				$nFinacT[] = $tinacPedido;
				$nExhibF[] = $exhibPedido;
				$nCostaF[] = $costaPedido;
			}
			//PEDIDOS POR TIPO
			$pedidoT[$tPed] = $pedidoT[$tPed] + 1;
			$nCajasT[$tPed] = $nCajasT[$tPed] + $cajasPedido;
			$nTinacT[$tPed] = $nTinacT[$tPed] + $tinacPedido;
			$nExhibT[$tPed] = $nExhibT[$tPed] + $exhibPedido;
			$nCostaT[$tPed] = $nCostaT[$tPed] + $costaPedido;
		}        
    }

    foreach ($nombreF as $key => $value){
    	$rxf.="<tr class='".(($key + 1) % 2 == 0 ? "trPar" : "trImpar")."'>
            	<td class=''>".$nombreF[$key]."</td>
            	<td class=''>".$pedidoF[$key]."</td>
            	<td class=''>".($nCajasF[$key] > 0 ? number_format($nCajasF[$key],0,'.',',') : '')."</td>
            	<td class=''>".($nTinacF[$key] > 0 ? number_format($nTinacF[$key],0,'.',',') : '')."</td>
            	<td class=''>".($nExhibF[$key] > 0 ? number_format($nExhibF[$key],0,'.',',') : '')."</td>
            	<td class=''>".($nCostaF[$key] > 0 ? number_format($nCostaF[$key],0,'.',',') : '')."</td>
            </tr>";
    }

    $aux = 1;
    foreach ($nombreT as $key => $value){
    	$aux += 1;
    	$rxt.="<tr class='".(($key + 1) % 2 == 0 ? "trPar" : "trImpar")."'>
            	<td class=''>".$nombreT[$key]."</td>
            	<td class=''>".$pedidoT[$key]."</td>
            	<td class=''>".($nCajasT[$key] > 0 ? number_format($nCajasT[$key],0,'.',',') : '')."</td>
            	<td class=''>".($nTinacT[$key] > 0 ? number_format($nTinacT[$key],0,'.',',') : '')."</td>
            	<td class=''>".($nExhibT[$key] > 0 ? number_format($nExhibT[$key],0,'.',',') : '')."</td>
            	<td class=''>".($nCostaT[$key] > 0 ? number_format($nCostaT[$key],0,'.',',') : '')."</td>
            </tr>";
    }
	$rxt.="<tr class='".($aux % 2 == 0 ? "trPar" : "trImpar")."'>
        	<th class=''>TOTAL</th>
        	<th class=''>".array_sum($pedidoT)."</th>
        	<th class=''>".number_format(array_sum($nCajasT),0,'.',',')."</th>
        	<th class=''>".number_format(array_sum($nTinacT),0,'.',',')."</th>
        	<th class=''>".number_format(array_sum($nExhibT),0,'.',',')."</th>
        	<th class=''>".number_format(array_sum($nCostaT),0,'.',',')."</th>
        </tr>";
	$sqlEmabrques = "SELECT COUNT(DISTINCT C5_NUM) AS pedidos
		, SUM(ZZM_CAJAS) AS cajas
		, SUM(ZZM_TINACO) AS tinacos
		, SUM(ZZM_EXHIB) AS exhibidores
		, SUM(ZZM_COSTAL) AS costales
		FROM ZZO010 ZZO
		INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD AND SA1.D_E_L_E_T_=''
		LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD AND SA2.D_E_L_E_T_ = ''
		LEFT JOIN SC5010 SC5 ON ZZO_PEDIDO=C5_NUM  AND SC5.D_E_L_E_T_=''
		INNER JOIN (SELECT SUM(C6_PRCVEN*C6_QTDENT) AS valor,C6_NUM FROM SC6010 WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522' OR C6_TES='523') AND D_E_L_E_T_='' AND C6_LOCAL='01' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM
		LEFT JOIN (
			SELECT ZZM_PEDIDO
					,ZZM_CAJAS
					,ZZM_TINACO
					,ZZM_EXHIB
					,ZZM_COSTAL
				FROM ZZM010 ZZM
					LEFT JOIN ZZN010 ON ZZM_CODALM=ZZN_CODIGO
				WHERE ZZM.D_E_L_E_T_<>'*'
			) AS ZZM ON C5_NUM=ZZM.ZZM_PEDIDO
		WHERE ZZO.D_E_L_E_T_='' AND
		ZZO_CODFLE<>'EXPORT' AND ZZO_CODFLE<>'ACLARA'  AND ZZO_FEMBAR = ''  
		AND ((ZZO_CODFLE<>'' AND A1_COD<>'V00018') OR ZZO_CODFLE='')
		AND ZZO_CHOFER='' AND ZZO_FECHEN=''";
	$resSqlEmabrques = odbc_exec($conn,$sqlEmabrques)or die("Error al consultar el numero de cajas EMBARQUES");
	$totalesEmabrques = odbc_fetch_array($resSqlEmabrques);

	$sqlWalmart = "SELECT COUNT(DISTINCT orden_compra, idCaja) total FROM cajas WHERE validado < 2";
	$resSqlWalmart = $con -> query($sqlWalmart);
	$resSqlWalmart = $resSqlWalmart->fetch_array();
?>
<!DOCTYPE html>
<html>
 <head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
	<link rel="shortcut icon" href="images/icono.ico"/>
 	<link rel="stylesheet" href="css/styles.css"/>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <script src="css/jquery.js"></script>
    <script type="text/javascript">
    	timerID=setTimeout("tempo()", 360000);
		function tempo(){
			clearTimeout(timerID);
			location.reload();
		}
    </script>
 </head>
 <body class="body">
 	<style type="text/css">
 		.title{
 			font-size: 1.2rem;
 		}
 		.subtitle{
 			font-size: 1rem;
 		}

 		.trPar{
    		background-color: rgba(200, 200, 200, 0);
 		}
 		.trImpar{
    		background-color: rgba(200, 200, 200, 0.3);
 		}
 		td{
 			font-size: 0.8rem;
 		}
 		strong{
 			font-family: arial;
 		}
 	</style>
	<div style="font-size: 1.7rem; background: rgba(200, 200, 200, 1); height: 5%;">
		<img class="logo" src="images/logo.png" style="height: 50px" />
		<h1 style='text-align: right; font-size: 2rem; margin: 0px; color: black; display: inline; width: 50%; padding-left: 5rem;'>INFORMACION DE PEDIDOS PROXIMOS A EMBARCAR</h1>
		<h1 style='text-align: right; color: black; display: inline; padding-right: 10rem; float: right;'>
			Pedidos en Embarques <strong><?php echo $totalesEmabrques["pedidos"]; ?></strong>;
			Cajas en Embarques   <strong>
				<?php 

				echo number_format($totalesEmabrques["cajas"],0,'.',',')." CJ; ".number_format($totalesEmabrques["tinacos"],0,'.',',')." T; ".number_format($totalesEmabrques["exhibidores"],0,'.',',')." E; ".number_format($totalesEmabrques["costales"],0,'.',',')." CO;"; 

				?>
					
				</strong>Cajas WAL-MART<strong>

				<?php 
					echo number_format($resSqlWalmart["total"],0,'.',','); 
				?>
					
				</strong>;
		</h1>
		<img src="images/refresh.png" style='width: 40px; padding-top: 8px; position: absolute; right: 1rem; top: 0.25rem;' onclick='location.reload()'/>
	</div>
	<form name="frmPedi2" style="width: 100%; position: absolute; overflow: auto; max-height:95%; top: 4rem; bottom: 1%; height: auto;">
		<table style='text-align: center; font-size: 1rem;'>
	        <tr><th class='title' colspan='6'>RESUMEN POR TIPO</th></tr>			
			<tr>
	        	<th class='subtitle' style='width: 25%'>TIPO</th>
            	<th class='subtitle' style='width: 15%'># PEDIDOS</th>
            	<th class='subtitle' style='width: 15%'>CAJAS</th>
            	<th class='subtitle' style='width: 15%'>TINACOS</th>
            	<th class='subtitle' style='width: 15%'>EXHIBIDORES</th>
            	<th class='subtitle' style='width: 15%'>COSTALES</th>
            </tr>
            <?php echo $rxt; ?>
		</table>
		<hr>
		<table style='text-align: center; font-size: 1rem;'>
	        <tr><th class='title' colspan='6'>RESUMEN POR FLETERA</th></tr>			
			<tr>
	        	<th class='subtitle' style='width: 25%'>FLETERA</th>
            	<th class='subtitle' style='width: 15%'># PEDIDOS</th>
            	<th class='subtitle' style='width: 15%'>CAJAS</th>
            	<th class='subtitle' style='width: 15%'>TINACOS</th>
            	<th class='subtitle' style='width: 15%'>EXHIBIDORES</th>
            	<th class='subtitle' style='width: 15%'>COSTALES</th>
            </tr>
            <?php echo $rxf; ?>
		</table>
		<hr>
	    <table style='text-align: center; font-size: 1rem;'>
	        <tr><th class='title' colspan='7'>DESGLOSE DE PEDIDOS</th></tr>
	        <tr>
            	<th class='subtitle' style='width: 10%'>Pedido</th>
            	<th class='subtitle' style='width: 15%'>Orden Surtido</th>
            	<th class='subtitle' style='width: 6%'>Cajas</th>
            	<th class='subtitle' style='width: 6%'>Tinacos</th>
            	<th class='subtitle' style='width: 6%'>Exhibidores</th>
            	<th class='subtitle' style='width: 6%'>Cosatales</th>
            	<th class='subtitle' style='width: 26%'>FLETERA</th>
            	<th class='subtitle' style='width: 25%'>Fecha de Surtido</th>
            </tr>
	        <tr><th class='subtitle' colspan='7'>PEDIDOS EN PROCESO DE SURTIDO</th></tr>
	        <?php echo $pxs; ?>
	    	<tr><th class='subtitle' colspan='7'>PEDIDOS POR AUDITAR</th></tr>
	        <?php echo $ppa; ?>
	    	<tr><th class='subtitle' colspan='7'>PEDIDOS POR FACTURAR</th></tr>
	        <?php echo $pxf; ?>
	    </table>
	</form>    
 </body>
</html>